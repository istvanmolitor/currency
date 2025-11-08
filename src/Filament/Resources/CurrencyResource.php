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
                Forms\Components\Toggle::make('enabled')
                    ->label(__('currency::currency.form.enabled'))
                    ->default(true),
                Forms\Components\TextInput::make('code')
                    ->label(__('currency::currency.form.code'))
                    ->required()
                    ->maxLength(3)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name')
                    ->label(__('currency::currency.form.name'))
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('symbol')
                    ->label(__('currency::currency.form.symbol'))
                    ->maxLength(8),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('enabled')
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
