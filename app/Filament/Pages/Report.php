<?php
namespace App\Filament\Pages;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;


class Report extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view            = 'filament.pages.report';
    protected static ?string $title          = 'Laporan';

    public ?array $data         = [];
    public int $totalOrders     = 0;
    public float $totalRevenue  = 0;
    public float $avgOrderValue = 0;
    public Collection $ordersByStatus;

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
                        DatePicker::make('start_date')->label('Tanggal Mulai')->live(),
                        DatePicker::make('end_date')->label('Tanggal Akhir')->live(),
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
                            ->placeholder('Semua Status')
                            ->live(),
                    ])->columns(3),
            ])->statePath('data');
    }

    public function updated($property): void
    {
        $this->generateReport();
    }

    public function generateReport(): void
    {
        $query = $this->getFilteredQuery();

        $this->totalOrders    = $query->count();
        $this->totalRevenue   = $query->sum('total_price');
        $this->avgOrderValue  = $this->totalOrders > 0 ? $this->totalRevenue / $this->totalOrders : 0;
        $this->ordersByStatus = $query->groupBy('status')->selectRaw('status, count(*) as count')->pluck('count', 'status');
    }

    protected function getFilteredQuery()
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

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(fn() => $this->getFilteredQuery())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d F Y'),

                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable(),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'pending'                 => 'gray',
                        'paid'                    => 'success',
                        'processing'              => 'warning',
                        'shipped'                 => 'info',
                        'delivered'               => 'success',
                        'cancelled'               => 'danger',
                        default                   => 'secondary',
                    }),
            ])
            ->paginated()
            ->defaultSort('created_at', 'desc');
    }
}
