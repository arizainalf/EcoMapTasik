<?php
namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilePage extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Profil Saya';
    protected static ?string $title           = 'Profil Saya';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $slug             = 'profile';

    protected static string $view             = 'filament.pages.profile-page';

    public $data = [];

    public function mount(): void
    {
        $user       = auth()->user();
        $this->data = $user->only(['name', 'email', 'profile_photo']);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Informasi Akun')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required(),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Ganti Password')
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label('Password Baru')
                        ->password()
                        ->minLength(6)
                        ->maxLength(255)
                        ->nullable()
                        ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                        ->same('password_confirmation'),

                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Konfirmasi Password')
                        ->password()
                        ->dehydrated(false),
                ]),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function save(): void
    {
        $user = auth()->user();
        $data = $this->data;

        // Cek dan proses upload profile_photo
        if (isset($data['profile_photo']) && $data['profile_photo'] instanceof \Livewire\TemporaryUploadedFile) {
            $data['profile_photo'] = $data['profile_photo']->store('profiles', 'public');
        }

        // Hapus field password jika kosong
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        Notification::make()
            ->title('Profil berhasil diperbarui.')
            ->success()
            ->send();
    }
}
