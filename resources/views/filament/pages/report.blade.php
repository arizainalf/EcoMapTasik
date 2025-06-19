    <x-filament::page>

        {{ $this->form }}

        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <!-- Statistik Total Order -->
            <div class="filament-card rounded-xl shadow-sm border border-gray-300 bg-white p-6">
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-shopping-bag class="h-6 w-6 text-primary-500" />
                    <span class="text-sm font-medium text-gray-500">Total Order</span>
                </div>
                <div class="mt-2 text-2xl font-semibold">{{ $totalOrders }}</div>
            </div>

            <!-- Statistik Total Pendapatan -->
            <div class="filament-card rounded-xl shadow-sm border border-gray-300 bg-white p-6">
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-currency-dollar class="h-6 w-6 text-primary-500" />
                    <span class="text-sm font-medium text-gray-500">Total Pendapatan</span>
                </div>
                <div class="mt-2 text-2xl font-semibold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>

            <!-- Statistik Rata-rata Order -->
            <div class="filament-card rounded-xl shadow-sm border border-gray-300 bg-white p-6">
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-scale class="h-6 w-6 text-primary-500" />
                    <span class="text-sm font-medium text-gray-500">Rata-rata Order</span>
                </div>
                <div class="mt-2 text-2xl font-semibold">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="mt-8">
            <div class="filament-card rounded-xl shadow-sm border border-gray-300 bg-white">
                <div class="p-6">
                    <h2 class="text-lg font-medium">Distribusi Status Order</h2>
                    <div class="mt-4 space-y-4">
                        @foreach ($ordersByStatus as $status => $count)
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="capitalize">{{ $status }}</span>
                                    <span>{{ $count }}
                                        ({{ $totalOrders > 0 ? round(($count / $totalOrders) * 100, 2) : 0 }}%)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-primary-600 h-2.5 rounded-full"
                                        style="width: {{ $totalOrders > 0 ? ($count / $totalOrders) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{ $this->table }}
    </x-filament::page>
