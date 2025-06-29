<x-filament::page>

    <div class="mt-8">
        <div class="flex border-b border-gray-200">
            <button wire:click="onTabChanged('orders')" wire:loading.attr="disabled" type="button"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium focus:outline-none transition
                {{ $activeTab === 'orders'
                    ? 'border-primary-600 text-primary-600 border-b-2 bg-white'
                    : 'border-transparent text-gray-500 hover:text-primary-600 hover:border-gray-300' }}">
                <span>🛒 Transaksi</span>
            </button>
            <button wire:click="onTabChanged('serapan')" wire:loading.attr="disabled" type="button"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium focus:outline-none transition
                {{ $activeTab === 'serapan'
                    ? 'border-primary-600 text-primary-600 border-b-2 bg-white'
                    : 'border-transparent text-gray-500 hover:text-primary-600 hover:border-gray-300' }}">
                <span>🗑️ Serapan Sampah</span>
            </button>
        </div>
    </div>


    {{ $this->form }}

    @if ($activeTab === 'orders')
        {{-- Metrics Transaksi --}}
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <div class="filament-card rounded-xl shadow-sm border border-gray-300 bg-white p-6">
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-shopping-bag class="h-6 w-6 text-primary-500" />
                    <span class="text-sm font-medium text-gray-500">Total Order</span>
                </div>
                <div class="mt-2 text-2xl font-semibold">{{ $totalOrders }}</div>
            </div>
            <div class="filament-card rounded-xl shadow-sm border border-gray-300 bg-white p-6">
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-currency-dollar class="h-6 w-6 text-primary-500" />
                    <span class="text-sm font-medium text-gray-500">Total Pendapatan</span>
                </div>
                <div class="mt-2 text-2xl font-semibold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="filament-card rounded-xl shadow-sm border border-gray-300 bg-white p-6">
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-scale class="h-6 w-6 text-primary-500" />
                    <span class="text-sm font-medium text-gray-500">Rata-rata Order</span>
                </div>
                <div class="mt-2 text-2xl font-semibold">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Distribusi Status Order --}}
        <div class="mt-8">
            <div class="filament-card rounded-xl shadow-sm border border-gray-300 bg-white">
                <div class="p-6">
                    <h2 class="text-lg font-medium">Distribusi Status Order</h2>
                    <div class="mt-4 space-y-4">
                        @if (!empty($ordersByStatus))
                            @foreach ($ordersByStatus as $status => $count)
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="capitalize">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
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
                        @endif

                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Metrics Serapan Sampah --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 mt-8">
            @php
                $cards = [
                    ['label' => 'Total Sampah Terserap', 'value' => $totalSerapan],
                    ['label' => 'Total Sampah Organik', 'value' => $totalOrganic],
                    ['label' => 'Total Sampah Anorganik', 'value' => $totalAnorganic],
                    ['label' => 'Total Residu Sampah', 'value' => $totalResidu],
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="filament-card bg-white border border-gray-200 shadow-sm rounded-xl p-6">
                    <div class="flex items-center gap-3">
                        <x-heroicon-o-trash class="h-6 w-6 text-primary-600" />
                        <span class="text-sm font-medium text-gray-600">{{ $card['label'] }}</span>
                    </div>
                    <div class="mt-3 text-2xl font-bold text-gray-800">
                        {{ number_format($card['value'], 2, ',', '.') }} kg
                    </div>
                </div>
            @endforeach
        </div>

    @endif

    {{-- Table --}}
    <div class="mt-8" wire:key="table-{{ $activeTab }}-{{ $tableKey }}">
        <div class="filament-table-container overflow-hidden shadow-sm ring-1 ring-gray-950/5 rounded-xl bg-white">
            <div class="overflow-x-auto">
                <table class="w-full table-auto divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if ($activeTab === 'orders')
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pembeli</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                            @else
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tempat</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    KK</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Organik</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Anorganik</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Residu</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if ($activeTab === 'orders')
                            @forelse ($orderResults as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->created_at->format('d F Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->user->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if ($order->status === 'selesai') bg-green-100 text-green-800
                                            @elseif($order->status === 'dikirim') bg-blue-100 text-blue-800
                                            @elseif($order->status === 'dibayar') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data
                                        transaksi</td>
                                </tr>
                            @endforelse
                        @else
                            @forelse ($serapanResults as $serapan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($serapan->tanggal)->format('d F Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $serapan->tempat->tempat ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $serapan->tempat->kk ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($serapan->organic, 2) }} kg
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($serapan->anorganic, 2) }} kg
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($serapan->residu, 2) }} kg
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($serapan->total, 2) }} kg
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data
                                        serapan sampah</td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-filament::page>
