# Tavaan Engine — Database

> **دیتابیس** — موتور Database در فریمورک تَوان

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.database` |
| نام | `Database` |
| عنوان فارسی | دیتابیس |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Database` |
| نوع | `engine` |
| Facade | `DB` (accessor: `db`) |

## قراردادها (Contracts)

این موتور قراردادهای زیر را ارائه می‌دهد:

- `Tavaan\Engine\Database\Contracts\ConnectionInterface`
- `Tavaan\Engine\Database\Contracts\GrammarInterface`
- `Tavaan\Engine\Database\Contracts\MigrationInterface`
- `Tavaan\Engine\Database\Contracts\PaginatorInterface`
- `Tavaan\Engine\Database\Contracts\QueryBuilderInterface`
- `Tavaan\Engine\Database\Contracts\SchemaBuilderInterface`
- `Tavaan\Engine\Database\Contracts\TransactionManagerInterface`

## خطاها (Exceptions)

- `Tavaan\Engine\Database\Exceptions\ConnectionException`
- `Tavaan\Engine\Database\Exceptions\DatabaseException`
- `Tavaan\Engine\Database\Exceptions\DeadlockException`
- `Tavaan\Engine\Database\Exceptions\MixedBindingException`
- `Tavaan\Engine\Database\Exceptions\QueryException`
- `Tavaan\Engine\Database\Exceptions\RecordNotFoundException`
- `Tavaan\Engine\Database\Exceptions\UnsafeQueryException`

## Facadeها

- `Tavaan\Engine\Database\Facades\DB`
- `Tavaan\Engine\Database\Facades\Schema`

## کلاس‌های اصلی

- `src/Connection.php`
- `src/ConnectionManager.php`
- `src/Connections/MySqlConnection.php`
- `src/Connections/PostgresConnection.php`
- `src/Connections/SqliteConnection.php`
- `src/DatabaseServiceProvider.php`
- `src/Expression.php`
- `src/Grammars/Grammar.php`
- `src/Grammars/MySqlGrammar.php`
- `src/Grammars/PostgresGrammar.php`
- `src/Grammars/SqliteGrammar.php`
- `src/Migration/Exceptions/MigrationException.php`
- `src/Migration/Exceptions/MigrationLockedException.php`
- `src/Migration/FileMigrationLock.php`
- `src/Migration/MigrateResult.php`
- `src/Migration/Migration.php`
- `src/Migration/MigrationRepository.php`
- `src/Migration/MigrationServiceProvider.php`
- `src/Migration/Migrator.php`
- `src/ORM/Casts/CastHandler.php`
- ... و 56 فایل دیگر

## Service Providerها

- `Tavaan\Engine\Database\DatabaseServiceProvider`

## وابستگی‌ها

**موتورهای مورد‌نیاز:**

- `config`
- `event`
- `log`

## پیکربندی

- فایل پیکربندی: `config/database.php`
- کلید: `database`

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Database.git
```

این موتور بخشی از فریمورک تَوان است و معمولاً به‌صورت مستقیم نصب نمی‌شود،
بلکه توسط [Tavaan-Core](https://github.com/andrbi106/Tavaan-Core) مدیریت می‌شود.

## روابط با سایر مخازن

| مخزن | نقش |
|---|---|
| **`andrbi106/Tavaan-Engine-Database`** (این مخزن) | موتور Database |
| `andrbi106/Tavaan-Engine-Core` | ساختار پایه + درگاه ارتباط موتورها |
| `andrbi106/Tavaan-Core` | اسکلت اپلیکیشن + همهٔ موتورها |
| `Derogaramin/Tavaan-Framework` | فریمورک مادر (monorepo) |

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
