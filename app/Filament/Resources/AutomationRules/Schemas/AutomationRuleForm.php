<?php

namespace App\Filament\Resources\AutomationRules\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AutomationRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('keyword')
                    ->required(),
                Textarea::make('reply_message')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('use_ai')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                
            ]);
    }
}
