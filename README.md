# Molitor Currency

A Molitor Currency egy Laravel csomag devizák (pénznemek) és árfolyamok kezeléséhez.

## Függőségek

- `istvanmolitor/language` (composer `require`)

Tartalmaz:

- Eloquent modelleket (Currency, ExchangeRate)
- Migrációkat a currencies és exchange_rates táblákhoz
- Seeder-t az ISO valutalistához
- Repository-kat devizákhoz és árfolyam-számításhoz
- Konzol parancsot árfolyamok letöltéséhez
- Filament (v3/4) Resource-t a devizák admin felülethez

## Telepítés

1) Telepítés Composerrel

Ha önálló csomagként használod:

```
composer require istvanmolitor/currency
```

Monorepo/fejlesztői környezetben (path repository-val) már be van húzva.

2) Autodiscovery

A csomag Laravel Package Discovery-val regisztrálja a szolgáltatót:
- Molitor\Currency\Providers\CurrencyServiceProvider

Ez automatikusan:
- betölti a migrációkat (src/database/migrations)
- betölti a nézeteket (views) és fordításokat (translations), ha szükséges

3) Migrációk futtatása

```
php artisan migrate
```

4) Alap devizák feltöltése (opcionális, ajánlott)

A csomag tartalmaz egy seeder-t, amely több tucat ISO pénznemet tölt fel, és pár alapértelmezett devizát engedélyez.

```
php artisan db:seed --class="Molitor\\Currency\\database\\seeders\\CurrencySeeder"
```

Megjegyzés: a CurrencySeeder csak akkor fut be, ha a currencies tábla üres.

## Használat

### Modellek

- Molitor\Currency\Models\Currency
- Molitor\Currency\Models\ExchangeRate

### Repository-k

A csomag interfészeken keresztül köt be implementációkat a Laravel konténerbe.

- Molitor\Currency\Repositories\CurrencyRepositoryInterface
- Molitor\Currency\Repositories\ExchangeRateRepositoryInterface

Példa dependency injection-re (szervizekben, kontrollerekben, jobokban):

```php
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;
use Molitor\Currency\Repositories\ExchangeRateRepositoryInterface;

class PriceService
{
    public function __construct(
        private CurrencyRepositoryInterface $currencies,
        private ExchangeRateRepositoryInterface $rates,
    ) {}

    public function convert(float $amount, string $from, string $to): float
    {
        $source = $this->currencies->getByCode($from);
        $target = $this->currencies->getByCode($to);

        return $this->rates->exchange($amount, $source, $target);
    }
}
```

Hasznos metódusok:
- CurrencyRepositoryInterface
  - getDefault(): ?Currency — alapértelmezett deviza (HUF)
  - getByCode(string $code): ?Currency
  - getEnabledCurrencies(): Collection
  - getAll(): Collection
  - getDefaultId(): int
- ExchangeRateRepositoryInterface
  - update(): void — árfolyamok frissítése (jelenleg HUF alapú letöltés)
  - getRate(Currency $from, Currency $to): float
  - exchange(float $price, Currency $from, Currency $to): float

### Price helper

A csomag globális `price()` helper függvényt biztosít, amely egy `Molitor\Currency\Services\Price` objektumot ad vissza az alapértelmezett devizanemben:

```php
$price = price(1234.5); // Price objektum, currency = getDefault()

echo (string) $price; // pl. "1 234,50 Ft"

$usd = $price->exchange('USD'); // átváltás másik devizára
```

### Árfolyam frissítés parancs

A csomag tartalmaz egy artisan parancsot:

```
php artisan exchange-rate:update
```

Jelenleg a http://api.napiarfolyam.hu/?bank=kh végpontról tölt HUF alapú árfolyamokat, és az exchange_rates táblába menti.

Cron példa (Laravel Scheduler):

```php
$schedule->command('exchange-rate:update')->hourly();
```

### Admin integráció

A csomag nem Filament-et használ, hanem a `istvanmolitor/admin` csomag `DataTable`/`HasAdminFilters` alapjaira épülő saját admin API-t biztosít:

- Molitor\Currency\DataTables\CurrencyDataTable — szerver oldali kereső/rendező táblázat (kód, név, szimbólum, alapértelmezett, engedélyezett oszlopok)
- Molitor\Currency\Http\Controllers\CurrencyController — REST erőforrás-kontroller (`index`, `select`, `store`, `show`, `edit`, `update`, `destroy`)

Az admin route-ok a `routes/api.php`-ban vannak, `/api/admin/currency/currencies` alatt, `auth:sanctum` és `permission:currency` middleware-rel védve.

## Adatbázis séma

- currencies: id, enabled (bool), code (string, max 3), name (string), symbol (string), timestamps
- exchange_rates: id, currency_1_id (fk currencies.id), currency_2_id (fk currencies.id), value (float), created_at

## Testreszabás

- Alapértelmezett deviza: a CurrencyRepository getDefault() jelenleg HUF-ot ad vissza. Szükség esetén módosítsd az implementációt.
- Árfolyam forrás: az ExchangeRateRepository::downloadHuf() HUF alapú adatokat tölt. Más API-ra/forrásra cserélhető.

## Fejlesztői megjegyzések

- A provider bindolja az interfészeket az implementációkhoz, így DI-vel bárhol használhatók.
- A migrációk automatikusan betöltődnek a csomagból; külön publish nem szükséges.

## Licenc

MIT
