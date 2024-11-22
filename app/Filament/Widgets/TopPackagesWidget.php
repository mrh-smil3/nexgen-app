<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TopPackagesWidget extends ChartWidget
{
    protected static ?string $heading = 'Paket Paling Laris';

    protected function getData(): array
    {
        // Ambil top 5 paket berdasarkan total penjualan
        $topPackages = DB::table('packages')
            ->join('subscriptions', 'packages.id', '=', 'subscriptions.package_id')
            ->join('payments', 'subscriptions.id', '=', 'payments.subscription_id')
            ->select('packages.name', 
                DB::raw('COUNT(subscriptions.id) as total_subscriptions'),
                DB::raw('SUM(payments.amount) as total_revenue')
            )
            ->whereYear('payments.payment_date', Carbon::now()->year)
            ->groupBy('packages.id', 'packages.name')
            ->orderBy('total_subscriptions', 'desc')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Berlangganan',
                    'data' => $topPackages->pluck('total_subscriptions')->toArray(),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                ],
                [
                    'label' => 'Total Pendapatan (Rp)',
                    'data' => $topPackages->pluck('total_revenue')->toArray(),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                ]
            ],
            'labels' => $topPackages->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    // Tambahkan filter periode
    public function getFilters(): ?array
    {
        return [
            'current_year' => 'Tahun Berjalan',
            'last_year' => 'Tahun Lalu',
            'all_time' => 'Sepanjang Waktu'
        ];
    }

    // Method untuk mengupdate filter
    public function updateFilter($filter)
    {
        $query = DB::table('packages')
            ->join('subscriptions', 'packages.id', '=', 'subscriptions.package_id')
            ->join('payments', 'subscriptions.id', '=', 'payments.subscription_id')
            ->select('packages.name', 
                DB::raw('COUNT(subscriptions.id) as total_subscriptions'),
                DB::raw('SUM(payments.amount) as total_revenue')
            );

        switch ($filter) {
            case 'current_year':
                $query->whereYear('payments.payment_date', Carbon::now()->year);
                break;
            case 'last_year':
                $query->whereYear('payments.payment_date', Carbon::now()->subYear()->year);
                break;
            case 'all_time':
                // Tidak perlu filter tambahan
                break;
        }

        $topPackages = $query->groupBy('packages.id', 'packages.name')
            ->orderBy('total_subscriptions', 'desc')
            ->limit(5)
            ->get();

        // Update chart data
        $this->data = [
            'datasets' => [
                [
                    'label' => 'Jumlah Berlangganan',
                    'data' => $topPackages->pluck('total_subscriptions')->toArray(),
                ],
                [
                    'label' => 'Total Pendapatan (Rp)',
                    'data' => $topPackages->pluck('total_revenue')->toArray(),
                ]
            ],
            'labels' => $topPackages->pluck('name')->toArray(),
        ];
    }

    // Informasi tambahan di bawah chart
    public function getDescription(): ?string
    {
        $topPackage = DB::table('packages')
            ->join('subscriptions', 'packages.id', '=', 'subscriptions.package_id')
            ->join('payments', 'subscriptions.id', '=', 'payments.subscription_id')
            ->select('packages.name', 
                DB::raw('COUNT(subscriptions.id) as total_subscriptions'),
                DB::raw('SUM(payments.amount) as total_revenue')
            )
            ->whereYear('payments.payment_date', Carbon::now()->year)
            ->groupBy('packages.id', 'packages.name')
            ->orderBy('total_subscriptions', 'desc')
            ->first();

        return $topPackage 
            ? "Paket terlaris tahun ini: {$topPackage->name} dengan {$topPackage->total_subscriptions} berlangganan" 
            : "Tidak ada data penjualan";
    }
}