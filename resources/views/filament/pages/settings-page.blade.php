<x-filament::page>
    <x-filament::card>
        {{ $this->form }}
    </x-filament::card>
    <x-filament::button wire:click="save" class="mt-4">
        Simpan Pengaturan
    </x-filament::button>

</x-filament::page>
