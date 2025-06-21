<?php
namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SettingsPage extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-cog';
    protected static string $view             = 'filament.pages.settings-page';
    protected static ?string $navigationLabel = 'Pengaturan Aplikasi';
    protected static ?string $title           = 'Pengaturan Aplikasi';
    protected static ?string $navigationGroup = 'Pengaturan';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = Setting::first();
        if ($setting) {
            $this->form->fill($setting->toArray());
        } else {
            $this->form->fill();
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Aplikasi')
                    ->description('Data dasar seperti nama, deskripsi, dan kontak.')
                    ->schema([
                        Forms\Components\TextInput::make('app_name')
                            ->label('Nama Aplikasi')
                            ->required(),

                        Forms\Components\Textarea::make('app_description')
                            ->label('Deskripsi')
                            ->rows(3),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('No. Telepon'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Alamat')
                    ->description('Alamat lengkap dan wilayah administratif.')
                    ->schema([
                        Forms\Components\TextInput::make('province')
                            ->label('Provinsi')
                            ->live()
                            ->required(),

                        Forms\Components\TextInput::make('city')
                            ->label('Kota/Kabupaten')
                            ->live()
                            ->required(),

                        Forms\Components\TextInput::make('district')
                            ->label('Kecamatan')
                            ->live()
                            ->required(),

                        Forms\Components\TextInput::make('subdistrict')
                            ->label('Kelurahan')
                            ->live()
                            ->required(),

                        Forms\Components\Select::make('postal_code')
                            ->label('Kode Pos')
                            ->options(function (callable $get) {
                                $search = trim(
                                    ($get('province') ?? '') . ' ' .
                                    ($get('city') ?? '') . ' ' .
                                    ($get('district') ?? '') . ' ' .
                                    ($get('subdistrict') ?? '')
                                );

                                if (empty($search)) {
                                    return [];
                                }

                                try {
                                    $courierService = app(\App\Services\CourirService::class);

                                    $result = $courierService->getDestinationId($search);

                                    if (isset($result) && is_array($result)) {
                                        return collect($result)
                                            ->pluck('label', 'zip_code')
                                            ->toArray();
                                    }
                                } catch (\Throwable $e) {
                                    \Log::error('Gagal memuat kode pos: ' . $e->getMessage());
                                }

                                return [];
                            })
                            ->loadingMessage('Memuat kode pos...')
                            ->noSearchResultsMessage('Tidak ada hasil ditemukan.')
                            ->disablePlaceholderSelection()
                            ->required(),

                        Forms\Components\TextInput::make('address')
                            ->label('Alamat Lengkap'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Media')
                    ->description('Unggah logo dan gambar slider untuk tampilan aplikasi.')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->label('Logo')
                            ->directory('settings')
                            ->getUploadedFileNameForStorageUsing(fn($file) => $file->getClientOriginalName())
                            ->disk('public'),

                        Forms\Components\FileUpload::make('slider_1')
                            ->image()
                            ->label('Slider 1')
                            ->directory('settings')
                            ->disk('public'),

                        Forms\Components\FileUpload::make('slider_2')
                            ->image()
                            ->label('Slider 2')
                            ->directory('settings')
                            ->disk('public'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data')
            ->model(Setting::class);
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $setting = Setting::first();

            if ($setting) {
                $setting->update($data);
            } else {
                Setting::create($data);
            }

            Notification::make()
                ->title('Pengaturan berhasil disimpan')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Terjadi kesalahan saat menyimpan pengaturan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
