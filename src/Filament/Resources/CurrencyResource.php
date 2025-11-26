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
use Molitor\Currency\Filament\Resources\CurrencyResource\Pages;
use Molitor\Currency\Models\Currency;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static \BackedEnum|null|string $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationGroup(): string
    {
        return __('currency::common.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('currency::currency.title');
    }

    public static function canAccess(): bool
    {
        return Gate::allows('acl', 'currency');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Toggle::make('is_enabled')
                    ->label(__('currency::common.enabled'))
                    ->default(true),
                Forms\Components\Toggle::make('is_default')
                    ->label(__('currency::common.default'))
                    ->default(false),
                Forms\Components\TextInput::make('code')
                    ->label(__('currency::common.code'))
                    ->required()
                    ->maxLength(3)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name')
                    ->label(__('currency::common.name'))
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('symbol')
                    ->label(__('currency::common.symbol'))
                    ->maxLength(8),
                Forms\Components\Toggle::make('is_symbol_first')
                    ->label(__('currency::common.symbol_first'))
                    ->default(false),
                Forms\Components\TextInput::make('decimals')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(4)
                    ->label(__('currency::common.decimals'))
                    ->required(),
                Forms\Components\TextInput::make('decimal_separator')
                    ->label(__('currency::common.decimal_separator'))
                    ->maxLength(1),
                Forms\Components\TextInput::make('thousands_separator')
                    ->label(__('currency::common.thousands_separator'))
                    ->maxLength(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean()
                    ->label(__('currency::common.default')),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->boolean()
                    ->label(__('currency::currency.table.enabled')),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('currency::currency.table.code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('currency::currency.table.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->label(__('currency::currency.table.symbol'))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_enabled')
                    ->label(__('currency::common.enabled'))
                    ->boolean(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (?Currency $record) => $record?->is_default === false),
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
