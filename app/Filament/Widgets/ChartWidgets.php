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
        $attractionsData = Ticket::selectRaw('AttractionId, COUNT(*) as VisitCount')
            ->groupBy('AttractionId')
            ->orderByDesc('VisitCount') // Order by most visited
            ->limit(10) // Get the top 10 attractions
            ->pluck('VisitCount', 'AttractionId')
            ->toArray();

        // Get attraction names for labels
        $attractionNames = Attraction::whereIn('id', array_keys($attractionsData))
            ->pluck('AttractionName', 'id')
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
            'labels' => array_values($attractionNames), // Attraction names
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
