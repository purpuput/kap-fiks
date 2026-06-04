<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AuditReport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$pimpinan = User::where('role', 'pimpinan')->first();
Auth::login($pimpinan);

view()->share('errors', new \Illuminate\Support\ViewErrorBag);

$r = AuditReport::first();
$html = view('audit_reports.show', [
    'auditReport' => $r,
    'documentTypes' => [
        'spk' => 'Surat Penugasan / Perikatan',
    ],
    'filesByType' => $r->files->where('is_active', true)->groupBy('document_type')->map->first(),
])->render();

preg_match_all('/<button[^>]*>.*?<\/button>/is', $html, $matches);
foreach ($matches[0] as $idx => $btn) {
    if (strpos($btn, 'Lihat') !== false || strpos($btn, 'Preview') !== false || strpos($btn, 'btn-review') !== false) {
        echo "Button {$idx}:\n" . $btn . "\n\n";
    }
}
