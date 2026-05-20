<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Message;
use App\Models\Contact;
use App\Models\Campaign;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Messages',
                Message::count()
            ),

            Stat::make(
                'Contacts',
                Contact::count()
            ),

            Stat::make(
                'Campaigns',
                Campaign::count()
            ),
        ];
    }
}
