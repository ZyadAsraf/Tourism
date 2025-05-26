<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;
use App\Models\Attraction;

class ChartWidgets extends ChartWidget
{
    protected static ?string $heading = 'Most Visited Attractions';

    protected static ?string $pollingInterval = null;


protected function getData(): array
{
    // Join tickets with attractions to get the attraction names
    $attractionsData = Ticket::join('attractions', 'tickets.attraction', '=', 'attractions.id')
        ->selectRaw('attractions.AttractionName, COUNT(*) as VisitCount')
        ->groupBy('attractions.AttractionName')
        ->orderByDesc('VisitCount')
        ->limit(10)
        ->pluck('VisitCount', 'AttractionName')
        ->toArray();

    return [
        'datasets' => [
            [
                'label' => 'Number of Visits',
                'data' => array_values($attractionsData),
                'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                'borderColor' => 'rgba(255, 99, 132, 1)',
                'borderWidth' => 1,
            ],
        ],
        'labels' => array_keys($attractionsData),
    ];
}


    protected function getType(): string
    {
        return 'bar';
    }
}
