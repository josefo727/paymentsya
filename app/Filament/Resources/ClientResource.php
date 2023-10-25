<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Gestión';

    protected static ?int $navigationSort = 4;

    public static function getLabel(): ?string
    {
        return 'Cliente';
    }

    public static function getNavigationLabel(): string
    {
        return 'Clientes';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('contact_person')
                    ->label('Contacto')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('email')
                    ->label('Correo')
                    ->email()
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('account')
                    ->label('Cuenta Vtex')
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('account', preg_replace('/\s+/', '', $state)))
                    ->required()
                    ->maxLength(30),
                Forms\Components\TextInput::make('store_name')
                    ->label('Tienda')
                    ->required()
                    ->maxLength(30),
                Forms\Components\TextInput::make('vtex_domain')
                    ->label('Dominio en Vtex')
                    ->url()
                    ->regex('/^https:\/\/[a-zA-Z0-9-]+\.myvtex\.com\/$/')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('store_domain')
                    ->label('Dominio de la tienda')
                    ->url()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Group::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('payment_system')
                            ->label('Vtex Payment System')
                            ->regex('/^\d+(,\d+)*$/')
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('payment_system', preg_replace('/\s+/', '', $state)))
                            ->required(),
                        Forms\Components\Toggle::make('is_production')
                            ->label('Producción')
                            ->required(),
                    ]),
				Forms\Components\TextInput::make('app_key')
					->label('PaymentsYa Api Key')
					->disabled()
					->maxLength(30)
					->hidden(fn (string $context): bool => $context === 'create'),
				Forms\Components\TextInput::make('app_token')
					->label('PaymentsYa Api Token')
					->disabled()
					->maxLength(100)
					->hidden(fn (string $context): bool => $context === 'create'),
				Section::make('Credenciales')
					->description('Credenciales y parámetros API de PaymentsWay y Vtex')
                    ->relationship('credential')
					->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('dashboard')
                            ->label('Url Dashboard')
                            ->url()
                            ->required()
                            ->maxLength(128),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo')
                            ->email()
                            ->required()
                            ->maxLength(64),
                        Forms\Components\Group::make()
                            ->columns(4)
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->label('Contraseña')
                                    ->required()
                                    ->maxLength(64),
                                Forms\Components\TextInput::make('merchant_id')
                                    ->label('Merchant ID')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('terminal_id')
                                    ->label('Terminal ID')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('form_id')
                                    ->label('Form ID')
                                    ->numeric()
                                    ->required(),
                            ])->columnSpanFull(),
                        Forms\Components\Textarea::make('payments_way_api_key')
                            ->label('Payments Way Api Key')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('vtex_api_app_key')
                            ->label('Vtex Api App Key')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Textarea::make('vtex_api_app_token')
                            ->label('Vtex Api App Token')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->hidden(fn (string $context): bool => $context === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Contacto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('store_name')
                    ->label('Tienda')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_production')
                    ->label('Producción')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
