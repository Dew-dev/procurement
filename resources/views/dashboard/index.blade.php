@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
        <p class="text-slate-500 mt-1">Ringkasan kontrak dan progress pengerjaan</p>
    </div>
    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <!-- Total Kontrak -->
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">Total Kontrak</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $totalContracts }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total PO rilis -->
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">PO yang sudah dirilis</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $totalPurchaseOrders }}</p>
                    <p class="text-md text-slate-500 mt-1">Jumlah PO terbit di semua kontrak</p>
                </div>
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Item Semua Kontrak -->
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">Total Item Semua Kontrak</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $totalContractItems }}</p>
                    <p class="text-md text-slate-500 mt-1">Jumlah total kuantitas item pada semua kontrak</p>
                </div>
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Item yang sudah PO -->
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">Total Item yang sudah PO</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $totalPoItems }}</p>
                    <p class="text-md text-slate-500 mt-1">{{ $totalItemsWithPoPercent }}% dari total item kontrak</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Item belum PO -->
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">Total Item belum PO</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $totalItemsWithoutPo }}</p>
                    <p class="text-md text-slate-500 mt-1">{{ $totalItemsWithoutPoPercent }}% dari total item kontrak</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Item belum sampai PT PAL -->
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">Total Item belum sampai PT PAL</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $totalItemsUnarrived }}</p>
                    <p class="text-md text-slate-500 mt-1">{{ $totalItemsUnarrivedPercentOfPo }}% dari item yang sudah PO</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M12 3v18"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Item sudah sampai PT PAL -->
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">Total Item sudah sampai PT PAL</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $totalDeliveredItems }}</p>
                    <p class="text-md text-slate-500 mt-1">{{ $totalDeliveredItemsPercentOfPo }}% dari item yang sudah PO</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Delivery Status Chart -->
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Status Pengiriman</h2>
            <div class="relative h-64">
                <canvas id="deliveryStatusChart"></canvas>
            </div>
            <div class="mt-4 space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="flex items-center"><span class="w-3 h-3 bg-emerald-500 rounded-full mr-2"></span> Sudah Dikirim</span>
                    <span class="font-medium">{{ $deliveryStatus['delivered'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center"><span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span> On Track</span>
                    <span class="font-medium">{{ $deliveryStatus['on_track'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center"><span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span> At Risk</span>
                    <span class="font-medium">{{ $deliveryStatus['at_risk'] }}</span>
                </div>
            </div>
        </div>

        <!-- Progress Distribution Chart -->
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Distribusi Progress WIP</h2>
            <div class="relative h-64">
                <canvas id="progressDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Delivery by Contract and Top Progress -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Items Delivered Per Contract -->
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Item yang sudah sampai ke PT PAL per Kontrak</h2>
            @if($deliveredByContract->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @foreach($deliveredByContract as $contract)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-sm text-slate-900">{{ $contract->contract_number }}</p>
                                <p class="text-xs text-slate-500">{{ $contract->buyer_name }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-sm font-medium">
                                {{ $contract->delivered_items }} item
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center text-slate-500">
                    <p class="text-sm">Belum ada item yang terkirim</p>
                </div>
            @endif
        </div>

        <!-- Top item yang sudah PO by Progress -->
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Top 5 item yang sudah PO dengan Progress Tertinggi</h2>
            @if($topPosByProgress->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @foreach($topPosByProgress as $po)
                        <div class="p-3 bg-slate-50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <p class="font-medium text-sm text-slate-900">{{ $po->po_number }}</p>
                                    <p class="text-xs text-slate-500">{{ $po->contract_number }}</p>
                                </div>
                                <span class="font-semibold text-lg" :style="{color: '{{ $po->percentage >= 80 ? '#059669' : ($po->percentage >= 50 ? '#2563eb' : '#f59e0b') }}'}">
                                    {{ $po->percentage }}%
                                </span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2">
                                <div class="h-2 rounded-full" 
                                     :style="{
                                        width: '{{ $po->percentage }}%',
                                        backgroundColor: '{{ $po->percentage >= 80 ? '#059669' : ($po->percentage >= 50 ? '#2563eb' : '#f59e0b') }}'
                                     }"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center text-slate-500">
                    <p class="text-sm">Belum ada data progress</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Performance Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Highest Progress Contract -->
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-xl p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-emerald-700 text-sm font-medium">Kontrak dengan Progress Tertinggi</p>
                        @if($highProgress)
                        <p class="text-2xl font-bold text-emerald-900 mt-3">{{ $highProgress['contract_number'] }}</p>
                        <p class="text-sm text-emerald-700 mt-1">{{ $highProgress['buyer_name'] }}</p>
                        <p class="text-lg font-semibold text-emerald-900 mt-3">{{ $highProgress['items_in_po_percent'] }}%</p>
                        <p class="text-xs text-emerald-700 mt-1">{{ $highProgress['items_in_po'] }} item yang sudah PO</p>
                    @else
                        <p class="text-slate-500 mt-3">Belum ada data</p>
                    @endif
                </div>
                <svg class="w-12 h-12 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
        </div>

        <!-- Lowest Progress Contract -->
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-amber-700 text-sm font-medium">Kontrak dengan Progress Terendah</p>
                    @if($lowProgress)
                        <p class="text-2xl font-bold text-amber-900 mt-3">{{ $lowProgress['contract_number'] }}</p>
                        <p class="text-sm text-amber-700 mt-1">{{ $lowProgress['buyer_name'] }}</p>
                        <p class="text-lg font-semibold text-amber-900 mt-3">{{ $lowProgress['items_in_po_percent'] }}%</p>
                        <p class="text-xs text-amber-700 mt-1">{{ $lowProgress['items_in_po'] }} item yang sudah PO</p>
                    @else
                        <p class="text-slate-500 mt-3">Belum ada data</p>
                    @endif
                </div>
                <svg class="w-12 h-12 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- AI Insights -->
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-xl p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-indigo-700 text-sm font-medium">Insight AI</p>
                    <div class="mt-3 space-y-2">
                        @if($deliveryStatus['at_risk'] > 0)
                            <p class="text-sm text-indigo-900"><span class="font-semibold">{{ $deliveryStatus['at_risk'] }} item yang sudah PO</span> berisiko keterlambatan</p>
                        @endif
                        @if($avgProgress > 75)
                            <p class="text-sm text-indigo-900">Progress secara keseluruhan <span class="font-semibold">sangat baik</span> ({{ round($avgProgress, 1) }}%)</p>
                        @elseif($avgProgress > 50)
                            <p class="text-sm text-indigo-900">Progress <span class="font-semibold">sedang berjalan</span> ({{ round($avgProgress, 1) }}%)</p>
                        @else
                            <p class="text-sm text-indigo-900">Progress <span class="font-semibold">perlu dipercepat</span> ({{ round($avgProgress, 1) }}%)</p>
                        @endif
                        <p class="text-sm text-indigo-900"><span class="font-semibold">{{ $totalContractItems ? round(($totalDeliveredItems / $totalContractItems) * 100, 1) : 0 }}%</span> item sudah selesai pengiriman</p>
                    </div>
                </div>
                <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5.36 6.364l-.707.707M9 19.071V20m0-11.071V4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Contract Status Table -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 mb-8">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Status Pengiriman Kontrak</h2>
        @if($contractStatusTable->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-slate-600 font-medium">No Kontrak</th>
                            <th class="text-left px-4 py-3 text-slate-600 font-medium">Buyer</th>
                            <th class="text-center px-4 py-3 text-slate-600 font-medium">Status</th>
                            <th class="text-center px-4 py-3 text-slate-600 font-medium">Target Delivery</th>
                            <th class="text-right px-4 py-3 text-slate-600 font-medium">Delta Hari</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contractStatusTable as $contract)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $contract->contract_number }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $contract->buyer_name }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $contract->status === 'Delivered' ? 'bg-emerald-100 text-emerald-700' : ($contract->status === 'At Risk' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ $contract->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-slate-600">
                                    @if($contract->target_delivery_date)
                                        {{ \Carbon\Carbon::parse($contract->target_delivery_date)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-slate-900 font-medium">
                                    @if($contract->day_delta > 0)
                                        +{{ $contract->day_delta }} hari
                                    @elseif($contract->day_delta < 0)
                                        {{ $contract->day_delta }} hari
                                    @else
                                        Hari ini
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $contractStatusTable->links() }}
            </div>
        @else
            <div class="py-8 text-center text-slate-500">
                <p class="text-sm">Belum ada data status kontrak</p>
            </div>
        @endif
    </div>

    <!-- Contract Progress Overview -->
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Ringkasan Progress Kontrak</h2>
        @if($contracts->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-slate-600 font-medium">No Kontrak</th>
                            <th class="text-left px-4 py-3 text-slate-600 font-medium">Buyer</th>
                            <th class="text-center px-4 py-3 text-slate-600 font-medium">Jumlah Item</th>
                            <th class="text-center px-4 py-3 text-slate-600 font-medium">Item yang sudah PO</th>
                            <th class="text-center px-4 py-3 text-slate-600 font-medium">Item sudah sampai</th>
                            <th class="text-right px-4 py-3 text-slate-600 font-medium">Progress (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contracts as $contract)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $contract->contract_number }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $contract->buyer_name }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs font-medium">
                                        {{ $contract->total_items ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs font-medium">
                                        {{ $contract->items_in_po ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 bg-emerald-50 text-emerald-700 rounded text-xs font-medium">
                                        {{ $contract->delivered_items ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <div class="w-36 bg-slate-200 rounded-full h-2">
                                            <div class="h-2 rounded-full bg-blue-500" style="width: {{ $contract->progress_percent ?? 0 }}%"></div>
                                        </div>
                                        <span class="font-medium text-slate-900 w-12 text-right">{{ $contract->progress_percent ?? 0 }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $contracts->links() }}
            </div>
        @else
            <div class="py-8 text-center text-slate-500">
                <p>Belum ada data kontrak</p>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Delivery Status Chart
            const deliveryCtx = document.getElementById('deliveryStatusChart').getContext('2d');
            new Chart(deliveryCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Sudah Dikirim', 'On Track', 'At Risk'],
                    datasets: [{
                        data: [
                            {{ $deliveryStatus['delivered'] }},
                            {{ $deliveryStatus['on_track'] }},
                            {{ $deliveryStatus['at_risk'] }}
                        ],
                        backgroundColor: [
                            '#10b981',
                            '#3b82f6',
                            '#ef4444'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Progress Distribution Chart
            const progressCtx = document.getElementById('progressDistributionChart').getContext('2d');
            new Chart(progressCtx, {
                type: 'bar',
                data: {
                    labels: ['0-20%', '21-40%', '41-60%', '61-80%', '81-100%'],
                    datasets: [{
                        label: 'Jumlah item yang sudah PO',
                        data: [
                            {{ $progressBuckets['0-20%'] }},
                            {{ $progressBuckets['21-40%'] }},
                            {{ $progressBuckets['41-60%'] }},
                            {{ $progressBuckets['61-80%'] }},
                            {{ $progressBuckets['81-100%'] }}
                        ],
                        backgroundColor: [
                            '#ef4444',
                            '#f97316',
                            '#eab308',
                            '#84cc16',
                            '#10b981'
                        ],
                        borderRadius: 4,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
