<x-filament::page>
    <x-filament::form wire:submit="submit">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-4">
            Generate Laporan
        </x-filament::button>
    </x-filament::form>

    <div class="mt-8 grid gap-6 md:grid-cols-3">
        <x-filament::stats.card
            label="Total Order"
            :value="$totalOrders"
            icon="heroicon-o-shopping-bag"
        />

        <x-filament::stats.card
            label="Total Pendapatan"
            :value="'Rp ' . number_format($totalRevenue, 0, ',', '.')"
            icon="heroicon-o-currency-dollar"
        />

        <x-filament::stats.card
            label="Rata-rata Order"
            :value="'Rp ' . number_format($avgOrderValue, 0, ',', '.')"
            icon="heroicon-o-scale"
        />
    </div>

    <div class="mt-8">
        <x-filament::card>
            <h2 class="text-lg font-medium">Distribusi Status Order</h2>
            <div class="mt-4 space-y-4">
                @foreach($ordersByStatus as $status => $count)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="capitalize">{{ $status }}</span>
                            <span>{{ $count }} ({{ $totalOrders > 0 ? round(($count/$totalOrders)*100, 2) : 0 }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-primary-600 h-2.5 rounded-full"
                                 style="width: {{ $totalOrders > 0 ? ($count/$totalOrders)*100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::card>
    </div>
</x-filament::page>
