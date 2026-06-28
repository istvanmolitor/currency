<?php

declare(strict_types=1);

namespace Molitor\Currency\DataTables;

use Molitor\Admin\DataTables\DataTable;
use Molitor\Currency\Http\Resources\CurrencyResource;
use Molitor\Currency\Models\Currency;

class CurrencyDataTable extends DataTable
{
    protected function getModelClass(): string
    {
        return Currency::class;
    }

    protected function getResourceClass(): string
    {
        return CurrencyResource::class;
    }

    protected function initColumns(): void
    {
        $this->addColumn('code')
            ->setLabel('Kód')
            ->setSearchable()
            ->setOrderable();

        $this->addColumn('name')
            ->setLabel('Név')
            ->setSearchable()
            ->setOrderable();

        $this->addColumn('symbol')
            ->setLabel('Szimbólum');

        $this->addColumn('is_default')
            ->setLabel('Alapértelmezett');

        $this->addColumn('is_enabled')
            ->setLabel('Engedélyezett');
    }
}
