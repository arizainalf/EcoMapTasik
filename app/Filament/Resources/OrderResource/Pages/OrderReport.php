<?php
namespace App\Filament\Resources\OrderResource\Pages;

use Closure;
use Carbon\Carbon;
use App\Models\Order;
use Filament\Forms\Form;
use Illuminate\Routing\Route;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use App\Filament\Resources\OrderResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;

class OrderReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = OrderResource::class;
    protected static string $route    = 'report'; // Tambahkan ini

// Pastikan view path benar
    protected static string $view = 'filament.resources.order-resource.pages.order-report';

    public ?array $data = [];
    public $totalOrders;
    public $totalRevenue;
    public $avgOrderValue;
    public $ordersByStatus;

    public function mount(): void
    {
        $this->form->fill();
        $this->generateReport();
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Laporan')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai'),
                        DatePicker::make('end_date')
                            ->label('Tanggal Akhir'),
                        Select::make('status')
                            ->options([
                                'pending'    => 'Pending',
                                'paid'       => 'Paid',
                                'processing' => 'Processing',
                                'shipped'    => 'Shipped',
                                'delivered'  => 'Delivered',
                                'cancelled'  => 'Cancelled',
                            ])
                            ->multiple()
                            ->placeholder('Semua Status'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function generateReport(): void
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

        $this->totalOrders    = $query->count();
        $this->totalRevenue   = $query->sum('total_price');
        $this->avgOrderValue  = $this->totalOrders > 0 ? $this->totalRevenue / $this->totalOrders : 0;
        $this->ordersByStatus = Order::groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status');
    }

    public function submit(): void
    {
        $this->generateReport();
    }
}
