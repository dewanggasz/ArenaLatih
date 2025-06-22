<?php

namespace App\Filament\Admin\Widgets; // Pastikan namespace-nya benar

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Test;
use App\Models\TestResult;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pengguna', User::count())
                ->description('Jumlah semua akun terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Total Paket Latihan', Test::count())
                ->description('Jumlah semua paket latihan yang tersedia')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),
            
            Stat::make('Total Latihan Dikerjakan', TestResult::where('status', 'completed')->count())
                ->description('Jumlah semua sesi latihan yang telah diselesaikan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('warning'),
        ];
    }
}