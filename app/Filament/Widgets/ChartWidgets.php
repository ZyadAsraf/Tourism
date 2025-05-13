<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;
use App\Models\Attraction;

class ChartWidgets extends ChartWidget
{
    protected static ?string $heading = 'Most Visited Attractions';

    protected function getData(): array
    {
        // Count the number of tickets per attraction
        $attractionsData = Ticket::selectRaw('attraction, COUNT(*) as VisitCount')
            ->groupBy('attraction')
            ->orderByDesc('VisitCount') // Order by most visited
            ->limit(10) // Get the top 10 attractions
            ->pluck('VisitCount', 'attraction')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Visits',
                    'data' => array_values($attractionsData), // Ticket counts
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)', // Red color
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => array_keys($attractionsData), // Attraction names from tickets
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
