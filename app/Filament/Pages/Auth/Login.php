<?php

namespace App\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin; 
use Filament\Schemas\Schema; 
use Filament\Forms\Components\TextInput;

class Login extends BaseLogin
{
    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nia';
        
        return [
            $login_type => $data['login'],
            'password'  => $data['password'],
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('login')
                ->required()
                ->label('Email atau NIA'),

            TextInput::make('password')
                ->password()
                ->required(),
        ]);
    }
}