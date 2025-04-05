<?php

namespace App\Filament\Widgets;
use App\Models\Admin;
use App\Models\Ticket;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class test extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat ::make('Admins',Admin::count())
            ->description('all admin use the system')
            ->descriptionIcon('heroicon-o-users',IconPosition::Before)
            ->chart([10,5,20,6,40])
            ->color('info'),

            Stat::make('Total Bookings',Ticket::count())
            ->description('the total number of bookings')
            ->descriptionIcon('heroicon-o-qr-code',IconPosition::Before)
            ->chart([50,5,20,60,40])
            ->color('info'),
        ];
    }
}
