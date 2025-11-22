<?php

namespace Molitor\Currency\Filament\Resources\ExchangeRateResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Molitor\Currency\Filament\Resources\ExchangeRateResource;
use Molitor\Currency\Repositories\ExchangeRateRepositoryInterface;

class ListExchangeRates extends ListRecords
{
    protected static string $resource = ExchangeRateResource::class;

    public function getBreadcrumb(): string
    {
        return __('currency::common.list');
    }

    public function getTitle(): string
    {
        return 'Exchange rates';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Frissítés')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    try {
                        app(ExchangeRateRepositoryInterface::class)->update();
                        Notification::make()
                            ->title('Sikeres frissítés')
                            ->success()
                            ->send();
                        $this->dispatch('$refresh');
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Hiba frissítés közben')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            CreateAction::make()
                ->label('Create exchange rate')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function table(Table $table): Table
    {
        return ExchangeRateResource::table($table)
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
}
