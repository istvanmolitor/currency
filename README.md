# Molitor Currency

A Molitor Currency egy Laravel csomag devizák (pénznemek) és árfolyamok kezeléséhez. Tartalmaz:

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
composer require molitor/currency
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

### Filament integráció

A csomag biztosít egy Filament Resource-t a devizák kezeléséhez:

- Molitor\Currency\Filament\Resources\CurrencyResource

Ez megjelenik a Filament adminban (Settings / Currencies csoportban) és lehetővé teszi a devizák listázását és létrehozását. A táblázat oszlopai: Enabled, Code, Name, Symbol, időbélyegek.

Megjegyzés: A konkrét Filament verzió (v4) támogatott. Ha ikonok/akciók hiányoznak, ellenőrizd a filament/filament csomag verzióját és a kompatibilitást.

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
