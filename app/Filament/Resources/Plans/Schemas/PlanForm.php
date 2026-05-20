<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                TextInput::make('monthly_message_limit')
                    ->required()
                    ->numeric()
                    ->default(1000),
                TextInput::make('agent_limit')
                    ->required()
                    ->numeric()
                    ->default(1),
                Toggle::make('has_ai_access')
                    ->required(),
                Toggle::make('has_campaign_access')
                    ->required(),
            ]);
    }
}
