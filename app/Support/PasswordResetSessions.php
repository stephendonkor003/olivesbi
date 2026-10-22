<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PasswordResetSessions
{
    public static function revokeFiles(int $userId): void
    {
        if (config('session.driver') !== 'file') {
            return;
        }

        $directory = config('session.files');
        if (! is_string($directory) || ! is_dir($directory) || is_link($directory)
            || str_replace('\\', '/', rtrim($directory, '/\\')) !== str_replace('\\', '/', (string) realpath($directory))
            || realpath($directory) !== realpath(storage_path('framework/sessions'))) {
            self::retry();
        }
        $loginKey = Auth::guard('web')->getName();
        $count = $bytes = 0;
        foreach (new \FilesystemIterator($directory, \FilesystemIterator::SKIP_DOTS) as $file) {
            if (++$count > 10000) {
                self::retry();
            }
            if ($file->isLink() || ! $file->isFile() || ! preg_match('/\A[a-zA-Z0-9]{40}\z/', $file->getFilename())) {
                continue;
            }
            $bytes += $file->getSize();
            if ($bytes > 128 * 1024 * 1024) {
                self::retry();
            }
            $stream = @fopen($file->getPathname(), 'rb');
            if (! $stream || ! flock($stream, LOCK_SH | LOCK_NB)) {
                if (is_resource($stream)) {
                    fclose($stream);
                }
                self::retry();
            }
            try {
                if (! self::belongsTo($stream, $loginKey, $userId)) {
                    continue;
                }
                if (! flock($stream, LOCK_EX | LOCK_NB)) {
                    self::retry();
                }
                rewind($stream);
                $opened = fstat($stream);
                $current = @lstat($file->getPathname());
                if (! $current || $opened['ino'] !== $current['ino'] || $opened['dev'] !== $current['dev']
                    || is_link($file->getPathname()) || ! self::belongsTo($stream, $loginKey, $userId)
                    || ! @unlink($file->getPathname())) {
                    self::retry();
                }
            } finally {
                flock($stream, LOCK_UN);
                fclose($stream);
            }
        }
    }

    private static function belongsTo($stream, string $loginKey, int $userId): bool
    {
        if (fstat($stream)['size'] > 16 * 1024 * 1024) {
            self::retry();
        }
        $payload = stream_get_contents($stream);
        if ($payload === false) {
            self::retry();
        }
        if (config('session.encrypt')) {
            try {
                // Laravel encrypts the serialized session string as a second
                // serialized value. Decode both without instantiating classes.
                $outer = app('encrypter')->decrypt($payload, false);
                $payload = @unserialize($outer, ['allowed_classes' => false]);
                if (! is_string($payload)) {
                    return false;
                }
            } catch (DecryptException $exception) {
                return false;
            }
        }
        $data = config('session.serialization', 'php') === 'json'
            ? json_decode($payload, true)
            : @unserialize($payload, ['allowed_classes' => false]);

        return is_array($data) && isset($data[$loginKey]) && is_scalar($data[$loginKey])
            && (string) $data[$loginKey] === (string) $userId;
    }

    private static function retry(): never
    {
        throw ValidationException::withMessages(['code' => 'Password reset is temporarily unavailable. Please try again.']);
    }
}
