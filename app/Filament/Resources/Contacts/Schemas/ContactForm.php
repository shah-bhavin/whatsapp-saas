<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('vendor_id')
                    ->required()
                    ->numeric(),
                TextInput::make('assigned_user_id')
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('mobile')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('status'),
                DateTimePicker::make('follow_up_at'),
            ]);
    }
    
}
