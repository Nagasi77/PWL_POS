@extends('layouts.template')

@section('content')
<section class="content">
    <div class="container-fluid">
        {{-- Ringkasan Total --}}
        <div class="row mb-4">
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #4e73df, #224abe); color: white;">
                    <div class="card-body text-center p-4">
                        <h5 class="text-uppercase mb-2">Total Stok Masuk</h5>
                        <h3 class="mb-0 font-weight-bold">{{ \App\Helpers\Helper::ribuan($totalStokMasuk) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #f445dd, #ee497b); color: white;">
                    <div class="card-body text-center p-4">
                        <h5 class="text-uppercase mb-2">Total Barang Terjual</h5>
                        <h3 class="mb-0 font-weight-bold">{{ \App\Helpers\Helper::ribuan($totalStokTerjual) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
    

        {{-- Grafik --}}
        <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="card-header bg-white text-white d-flex align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-chart-area mr-2"></i> Statistik Barang Masuk dan Terjual
                </h3>
            </div>
            <div class="card-body bg-light p-4">
                <div class="chart-container" style="position: relative; width: 100%; height: 400px;">
                    @if ($ringkasan->count() > 0)
                        <canvas id="stokChart"></canvas>
                    @else
                        <div class="text-center text-muted p-5">
                            <i class="fas fa-info-circle fa-3x mb-3 text-info"></i><br>
                            <span class="font-weight-bold">Data Tidak Tersedia untuk Ditampilkan Saat Ini</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
</section>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if ($ringkasan->count() > 0)
        const ctx = document.getElementById('stokChart').getContext('2d');
        const stokChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($ringkasan->pluck('barang_nama')) !!},
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: {!! json_encode($ringkasan->pluck('total_masuk')) !!},
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    },
                    {
                        label: 'Barang Terjual',
                        data: {!! json_encode($ringkasan->pluck('total_terjual')) !!},
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    @endif
</script>
@endpush