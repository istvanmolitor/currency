<?php

namespace Molitor\Currency\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Molitor\Currency\Filament\Resources\ExchangeRateResource\Pages;
use Molitor\Currency\Models\ExchangeRate;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static \BackedEnum|null|string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    public static function getNavigationGroup(): string
    {
        return __('currency::common.group');
    }

    public static function getNavigationLabel(): string
    {
        return 'Exchange rates';
    }

    public static function canAccess(): bool
    {
        return Gate::allows('acl', 'currency');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('currency_1_id')
                    ->relationship('currency1', 'code')
                    ->label('From currency')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('currency_2_id')
                    ->relationship('currency2', 'code')
                    ->label('To currency')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->numeric()
                    ->step('0.00000001')
                    ->label('Rate')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('currency1.code')
                    ->label('From')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency2.code')
                    ->label('To')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Rate')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created at')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExchangeRates::route('/'),
            'create' => Pages\CreateExchangeRate::route('/create'),
            'edit' => Pages\EditExchangeRate::route('/{record}/edit'),
        ];
    }
}
