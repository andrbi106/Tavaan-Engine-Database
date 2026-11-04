<?php
declare(strict_types=1);

namespace Tavaan\Engine\Database;

use Tavaan\Engine\Database\Contracts\QueryBuilderInterface;

/**
 * Query Builder — سازندهٔ پرس‌وجوی SQL.
 *
 * این کلاس پایهٔ QueryBuilderInterface را پیاده‌سازی می‌کند
 * و توسط ConnectionManager و ORM استفاده می‌شود.
 */
final class QueryBuilder implements QueryBuilderInterface
{
    private string $table = '';
    private array $wheres = [];
    private array $orders = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $selects = ['*'];
    private array $joins = [];
    private array $groups = [];
    private array $havings = [];

    public function from(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    public function table(string $table): static
    {
        return $this->from($table);
    }

    public function select(string ...$columns): static
    {
        $this->selects = $columns;
        return $this;
    }

    public function where(string $column, mixed $operator = '=', mixed $value = null): static
    {
        if ($value === null && $operator !== '=') {
            $value = $operator;
            $operator = '=';
        }
        $this->wheres[] = ['type' => 'basic', 'column' => $column, 'operator' => $operator, 'value' => $value];
        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        $this->wheres[] = ['type' => 'in', 'column' => $column, 'values' => $values];
        return $this;
    }

    public function whereNull(string $column): static
    {
        $this->wheres[] = ['type' => 'null', 'column' => $column];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc'): static
    {
        $this->orders[] = ['column' => $column, 'direction' => $direction];
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): static
    {
        $this->joins[] = ['type' => 'inner', 'table' => $table, 'first' => $first, 'operator' => $operator, 'second' => $second];
        return $this;
    }

    public function groupBy(string ...$groups): static
    {
        $this->groups = $groups;
        return $this;
    }

    public function get(): array
    {
        // Returns array of rows — implementation depends on Connection
        return [];
    }

    public function first(): mixed
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function count(): int
    {
        return count($this->get());
    }

    public function insert(array $data): int
    {
        // Returns last insert ID
        return 0;
    }

    public function update(array $data): int
    {
        // Returns affected rows
        return 0;
    }

    public function delete(): int
    {
        // Returns affected rows
        return 0;
    }

    /** @return array<string, mixed> */
    public function toSql(): array
    {
        return [
            'table' => $this->table,
            'selects' => $this->selects,
            'wheres' => $this->wheres,
            'joins' => $this->joins,
            'orders' => $this->orders,
            'groups' => $this->groups,
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];
    }
}
