<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReportUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'description',
        'file_path',
        'created_by',
    ];

    public function task()
    {
        return $this->belongsTo(TaskAssignment::class, 'task_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateFileName()
    {
        $fileName = Str::random();
        while (self::where('file_path', $fileName)->first()) {
            self::generateFileName();
        }

        return $fileName;
    }
}
