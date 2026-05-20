<?php

namespace App\Filament\Resources\AiSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AiSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('provider')
                ->options([
                    'gemini' => 'Gemini',
                    'openai' => 'OpenAI',
                    'groq' => 'Groq',
                    'openrouter' => 'OpenRouter',
                ])->required()->default('gemini'),

                Textarea::make('api_key')
                    ->required()
                    ->columnSpanFull(),
                    
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
