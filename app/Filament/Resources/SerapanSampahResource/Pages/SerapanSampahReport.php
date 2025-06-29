<?php
namespace App\Filament\Resources\SerapanSampahResource\Pages;

use App\Filament\Resources\SerapanSampahResource;
use App\Models\SerapanSampah;
use Filament\Resources\Pages\Page;

class SerapanSampahReport extends Page
{
    protected static string $resource = SerapanSampahResource::class;

    protected static string $view = 'filament.resources.serapan-sampah-resource.pages.serapan-sampah-report';

    protected static ?string $title = 'Report Sampah';

    protected static ?string $navigationGroup = 'Laporan Sampah';

    public $startDate;
    public $endDate;

    public $results;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');
        $this->generateReport();
    }

    public function generateReport()
    {
        $this->results = SerapanSampah::with('tempat')
            ->whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->get();
    }
}
