<?php

namespace App\Filament\Resources\Messages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('whatsapp_message_id'),
                TextInput::make('contact_id')
                    ->numeric(),
                TextInput::make('campaign_id')
                    ->numeric(),
                TextInput::make('mobile')
                    ->required(),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('direction')
                    ->required(),
                TextInput::make('status'),
            ]);
    }
}
