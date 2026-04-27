<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Dashboard as FilamentDashboard;

class Dashboard extends FilamentDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament-panels::pages.dashboard';

    public function getWidgets(): array
    {
        return [
            \App\Filament\User\Widgets\StatsOverview::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}