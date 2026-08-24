<?php

namespace App\Filament\Resources\Donations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DonationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('donor_name')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('proof_of_transfer')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('Pending'),
                TextInput::make('verified_by')
                    ->numeric(),
            ]);
    }
}
