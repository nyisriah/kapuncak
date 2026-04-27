<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        return [
            Stat::make('Total Bookings', $user->bookings()->count())
                ->description('Total bookings you have made')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Active Bookings', $user->getActiveBookings()->count())
                ->description('Bookings that are not completed or cancelled')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Completed Bookings', $user->getCompletedBookingsCount())
                ->description('Successfully completed bookings')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Spent', 'Rp ' . number_format($user->getTotalRevenue(), 0, ',', '.'))
                ->description('Total amount spent on bookings')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}