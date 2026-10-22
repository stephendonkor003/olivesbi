<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Tests\TestCase;

class FrameworkUpgradeCompatibilityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertTrue(app()->environment('testing'));
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Mail::fake();
        Notification::fake();
        Http::preventStrayRequests();
    }

    public function test_real_pdf_renderer_produces_a_document_without_delivery(): void
    {
        $directory = storage_path('framework/testing-upgrade-pdf');
        if (! is_dir($directory)) {
            mkdir($directory, 0700, true);
        }
        $renderer = new Mpdf(['tempDir' => $directory]);
        $renderer->WriteHTML('<h1>Synthetic billing document</h1><p>GHS 42.00</p>');
        $document = $renderer->Output('', Destination::STRING_RETURN);
        $this->assertStringStartsWith('%PDF-', $document);
        $this->assertStringContainsString('%%EOF', $document);
        $this->assertGreaterThan(1000, strlen($document));
        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_actual_excel_export_round_trips_rows_and_amounts(): void
    {
        $export = new class implements FromArray, WithStrictNullComparison
        {
            public function array(): array
            {
                return [['Synthetic account', 42.5], ['Synthetic second account', 0]];
            }
        };
        $bytes = ExcelFacade::raw($export, Excel::XLSX);
        $this->assertStringStartsWith('PK', $bytes);
        $path = storage_path('framework/testing-upgrade.xlsx');
        try {
            file_put_contents($path, $bytes);
            $book = (new Xlsx)->load($path);
            $sheet = $book->getActiveSheet();
            $this->assertSame('Synthetic account', $sheet->getCell('A1')->getValue());
            $this->assertSame(42.5, $sheet->getCell('B1')->getValue());
            $this->assertSame('Synthetic second account', $sheet->getCell('A2')->getValue());
            $this->assertSame(0, $sheet->getCell('B2')->getValue());
            $book->disconnectWorksheets();
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    public function test_real_qr_generator_renders_local_svg(): void
    {
        $svg = (string) QrCode::format('svg')->size(160)->generate('https://example.test/synthetic-bill');
        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('width="160"', $svg);
        $this->assertStringContainsString('height="160"', $svg);
        $this->assertStringNotContainsString('<script', $svg);
    }

    public function test_role_middleware_preserves_active_role_authority_and_rejects_disabled_roles(): void
    {
        Route::get('/_private-upgrade-role', fn () => response()->json(['allowed' => true]))
            ->middleware('role:Administrator');
        $role = new Role(['name' => 'Administrator', 'guard_name' => 'web', 'active' => true]);
        $role->setRelation('permissions', collect());
        $actor = new User(['name' => 'Synthetic Actor', 'status' => 'Active', 'access_level' => 'Assembly_Administrator']);
        $actor->setRelation('roles', collect([$role]));
        Auth::guard('web')->setUser($actor);
        $this->getJson('/_private-upgrade-role')->assertOk()->assertJsonPath('allowed', true);
        $role->active = false;
        $this->getJson('/_private-upgrade-role')->assertForbidden();
        $actor->setRelation('roles', collect());
        $this->getJson('/_private-upgrade-role')->assertForbidden();
    }
}
