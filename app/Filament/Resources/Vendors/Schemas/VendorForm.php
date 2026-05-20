<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            TextInput::make('business_name')
                ->required(),

            TextInput::make('email')
                ->email()
                ->required(),

            TextInput::make('phone'),

            Textarea::make('address')->columnSpan(3),

            Toggle::make('is_active')
                ->default(true),
            ])
            ->columns();
    }
}
