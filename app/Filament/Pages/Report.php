<?php
namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\SerapanSampah;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class Report extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view            = 'filament.pages.report';
    protected static ?string $title          = 'Laporan';

    public ?array $filters = [];
    public Collection $orderResults;
    public Collection $serapanResults;

    public float $totalRevenue   = 0;
    public int $totalOrders      = 0;
    public float $avgOrderValue  = 0;
    public float $totalSerapan   = 0;
    public float $totalResidu    = 0;
    public float $totalOrganic   = 0;
    public float $totalAnorganic = 0;

    public ?string $activeTab = 'orders';
    public string $tableKey   = '';

    public function onTabChanged($tab)
    {
        $this->activeTab = $tab;
        // Generate unique key untuk memaksa table refresh
        $this->tableKey = uniqid();
        $this->generateReport();
    }

    public function mount(): void
    {
        $this->form->fill();
        $this->tableKey = uniqid();
        $this->generateReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Filter Laporan')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->live()
                            ->afterStateUpdated(fn() => $this->generateReport()),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Akhir')
                            ->live()
                            ->afterStateUpdated(fn() => $this->generateReport()),

                        Forms\Components\Select::make('status')
                            ->label('Status Transaksi')
                            ->multiple()
                            ->options([
                                'belum_dibayar' => 'Belum Dibayar',
                                'dibayar'       => 'Dibayar',
                                'dikirim'       => 'Dikirim',
                                'selesai'       => 'Selesai',
                            ])
                            ->placeholder('Semua Status')
                            ->live()
                            ->afterStateUpdated(fn() => $this->handleFilterUpdated())
                            ->visible(fn() => $this->activeTab === 'orders'),

                    ])->columns(3),
            ])
            ->statePath('filters');
    }

    public function generateReport(): void
    {
        $this->loadOrders();
        $this->loadSerapan();
    }

    protected function handleFilterUpdated()
    {
        $this->generateReport();
    }

    protected function getOrderQuery()
    {
        $data  = $this->form->getState();
        $query = Order::query();

        if (! empty($data['start_date'])) {
            $query->where('created_at', '>=', Carbon::parse($data['start_date'])->startOfDay());
        }
        if (! empty($data['end_date'])) {
            $query->where('created_at', '<=', Carbon::parse($data['end_date'])->endOfDay());
        }
        if (! empty($data['status'])) {
            $query->whereIn('status', $data['status']);
        }

        return $query;
    }

    protected function getSerapanQuery()
    {
        $data  = $this->form->getState();
        $query = SerapanSampah::query();

        if (! empty($data['start_date'])) {
            $query->where('tanggal', '>=', Carbon::parse($data['start_date'])->startOfDay());
        }
        if (! empty($data['end_date'])) {
            $query->where('tanggal', '<=', Carbon::parse($data['end_date'])->endOfDay());
        }

        return $query;
    }

    protected function loadOrders(): void
    {
        $query = $this->getOrderQuery();

        $this->orderResults = $query->with('user')->get();

        $this->totalOrders   = $this->orderResults->count();
        $this->totalRevenue  = $this->orderResults->sum('total_price');
        $this->avgOrderValue = $this->totalOrders > 0 ? $this->totalRevenue / $this->totalOrders : 0;
    }

    protected function loadSerapan(): void
    {
        $query = $this->getSerapanQuery();

        $this->serapanResults = $query->with('tempat')->get();
        $this->totalSerapan   = $this->serapanResults->sum('total');
        $this->totalOrganic   = $this->serapanResults->sum('organic');
        $this->totalAnorganic = $this->serapanResults->sum('anorganic');
        $this->totalResidu    = $this->serapanResults->sum('residu');
    }

    protected function getTableQuery()
    {
        return $this->activeTab === 'orders'
        ? $this->getOrderQuery()->with('user')
        : $this->getSerapanQuery()->with('tempat');
    }

    public function getTableData()
    {
        $query      = $this->getTableQuery();
        $sortColumn = $this->activeTab === 'orders' ? 'created_at' : 'tanggal';

        return $query->orderBy($sortColumn, 'desc')->paginate(10);
    }

    // Method untuk refresh table ketika tab berubah
    public function updatedActiveTab()
    {
        // Method ini akan dipanggil setelah onTabChanged
        // Tidak perlu action tambahan karena sudah di-handle di onTabChanged
    }
}
