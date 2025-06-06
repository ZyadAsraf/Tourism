<?php

namespace App\Filament\Widgets;

use App\Models\Ticket_Type;
use Filament\Widgets\ChartWidget;
use App\Models\Ticket;
use App\Models\TicketType;

class BieWidgets extends ChartWidget
{
    protected static ?string $heading = 'Ticket Type Distribution';

    protected static ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 1;
    protected function getData(): array
    {
        // Get total number of tickets
        $totalTickets = Ticket::count();

        if ($totalTickets === 0) {
            return [
                'datasets' => [
                    [
                        'label' => 'No Data Available',
                        'data' => [50], // Placeholder data
                        'backgroundColor' => ['#CCCCCC'],
                    ],
                ],
                'labels' => ['No Tickets'],
            ];
        }

        // Count occurrences of each ticket type using the foreign key
        $ticketTypeCounts = Ticket::selectRaw('TicketTypesId, COUNT(*) as count')
            ->groupBy('TicketTypesId')
            ->pluck('count', 'TicketTypesId')
            ->toArray();

        // Get ticket type names
        $ticketTypeNames = TicketType::whereIn('id', array_keys($ticketTypeCounts))
            ->pluck('Title', 'id')
            ->toArray();

        // Calculate percentages
        $percentages = [];
        foreach ($ticketTypeCounts as $typeId => $count) {
            $percentages[$ticketTypeNames[$typeId] ?? 'Unknown'] = round(($count / $totalTickets) * 100, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Percentage of Ticket Types',
                    'data' => array_values($percentages), // Percentage values
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'], // Pie colors
                ],
            ],
            'labels' => array_keys($percentages), // Ticket type names
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
    
}
