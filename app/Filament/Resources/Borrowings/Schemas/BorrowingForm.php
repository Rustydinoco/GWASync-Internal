<?php

namespace App\Filament\Resources\Borrowings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BorrowingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('item_code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('category')
                    ->required(),
                TextInput::make('condition')
                    ->required()
                    ->default('Baik'),
                TextInput::make('status')
                    ->required()
                    ->default('Tersedia'),
                TextInput::make('approved_by')
                    ->numeric(),
            ]);
    }
}
