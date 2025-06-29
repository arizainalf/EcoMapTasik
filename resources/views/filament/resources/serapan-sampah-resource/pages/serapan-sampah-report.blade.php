<x-filament::page>
    <div class="space-y-4">
        <form wire:submit.prevent="generateReport" class="flex items-center space-x-4">
            <x-filament::input.wrapper>
                <x-filament::input.label for="startDate" value="Tanggal Mulai" />
                <x-filament::input id="startDate" type="date" wire:model.defer="startDate" />
            </x-filament::input.wrapper>

            <x-filament::input.wrapper>
                <x-filament::input.label for="endDate" value="Tanggal Selesai" />
                <x-filament::input id="endDate" type="date" wire:model.defer="endDate" />
            </x-filament::input.wrapper>

            <x-filament::button type="submit">Lihat Laporan</x-filament::button>
        </form>

        <table class="min-w-full border mt-4">
            <thead>
                <tr class="border-b">
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-left">Tempat</th>
                    <th class="px-4 py-2 text-left">KK</th>
                    <th class="px-4 py-2 text-left">Total Sampah (kg)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results as $item)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $item->tanggal->format('d M Y') }}</td>
                        <td class="px-4 py-2">{{ $item->tempat->tempat }}</td>
                        <td class="px-4 py-2">{{ $item->kk }}</td>
                        <td class="px-4 py-2">{{ $item->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament::page>
