<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardStatistics extends ChartWidget
{
    // protected static ?string $heading = 'Chart';
    protected static ?string $heading = 'Statistik Penjualan Bulanan';

    protected function getData(): array
    {
        $salesData = [];

        // Ambil data penjualan (pembayaran) dalam 12 bulan terakhir
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            
            // Hitung total penjualan (pembayaran) per bulan
            $monthlySales = DB::table('payments')
                ->whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                // ->where('status' == 'completed')
                ->sum('amount');

            $salesData[] = [
                'month' => $month->format('M Y'),
                'sales' => $monthlySales,
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Penjualan (Rp)',
                    'data' => array_column($salesData, 'sales'),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => array_column($salesData, 'month'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    // Optional: Tambahkan filter untuk rentang waktu
    public function getFilters(): ?array
    {
        return [
            '1_month' => '1 Bulan',
            '3_months' => '3 Bulan',
            '6_months' => '6 Bulan',
            '12_months' => '1 Tahun',
        ];
    }

    // Optional: Method untuk menghandle filter
    public function updateFilter($filter)
    {
        switch ($filter) {
            case '1_month':
                // Logic untuk 1 bulan
                break;
            case '3_months':
                // Logic untuk 3 bulan
                break;
            case '6_months':
                // Logic untuk 6 bulan
                break;
            case '12_months':
                // Logic untuk 1 tahun
                break;
        }
    }
    
}
