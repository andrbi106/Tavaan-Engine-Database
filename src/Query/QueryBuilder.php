<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Query;

/**
 * QueryBuilder — ساخت fluent کوئری SQL.
 *
 * @see docs/03-engines/database.md (DOC-047)
 */
final class QueryBuilder
{
    private string $table;
    /** @var list<string> */
    private array $columns = ['*'];
    /** @var list<array{column: string, operator: string, value: mixed, boolean: string}> */
    private array $wheres = [];
    /** @var list<string> */
    private array $orders = [];
    private ?int $limit = null;
    private ?int $offset = null;
    /** @var list<string> */
    private array $joins = [];
    /** @var list<array{table: string, type: string, local: string, foreign: string}> */
    private array $joinClauses = [];

    public function __construct(
        private readonly \Tavaan\Engine\Database\Connection $connection,
        string $table,
    ) {
        $this->table = $table;
    }

    public function table(): string
    {
        return $this->table;
    }

    /**
     * @param list<string>|string $columns
     */
    public function select(array|string $columns = ['*']): self
    {
        $this->columns = (array) $columns;
        return $this;
    }

    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function where(string $column, mixed $operator = '=', mixed $value = null, string $boolean = 'AND'): self
    {
        // Handle where('column', $value) shorthand
        if ($value === null && !\in_array($operator, ['=', '!=', '<', '>', '<=', '>=', '<>', 'LIKE', 'IN', 'IS NULL', 'IS NOT NULL'], true)) {
            $value = $operator;
            $operator = '=';
        }
        $this->wheres[] = ['column' => $column, 'operator' => $operator, 'value' => $value, 'boolean' => $boolean];
        return $this;
    }

    public function orWhere(string $column, mixed $operator = '=', mixed $value = null): self
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function whereIn(string $column, array $values): self
    {
        $this->wheres[] = ['column' => $column, 'operator' => 'IN', 'value' => $values, 'boolean' => 'AND'];
        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->wheres[] = ['column' => $column, 'operator' => 'IS NULL', 'value' => null, 'boolean' => 'AND'];
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->wheres[] = ['column' => $column, 'operator' => 'IS NOT NULL', 'value' => null, 'boolean' => 'AND'];
        return $this;
    }

    public function join(string $table, string $local, string $operator, string $foreign): self
    {
        $this->joinClauses[] = ['table' => $table, 'type' => 'INNER', 'local' => $local, 'foreign' => $foreign, 'operator' => $operator];
        return $this;
    }

    public function leftJoin(string $table, string $local, string $operator, string $foreign): self
    {
        $this->joinClauses[] = ['table' => $table, 'type' => 'LEFT', 'local' => $local, 'foreign' => $foreign, 'operator' => $operator];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = "{$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function take(int $limit): self
    {
        return $this->limit($limit);
    }

    public function skip(int $offset): self
    {
        return $this->offset($offset);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function get(): array
    {
        [$sql, $bindings] = $this->toSql();
        return $this->connection->select($sql, $bindings);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function first(): ?array
    {
        $this->limit = 1;
        [$sql, $bindings] = $this->toSql();
        return $this->connection->selectOne($sql, $bindings);
    }

    public function count(): int
    {
        $this->columns = ['COUNT(*) AS aggregate'];
        [$sql, $bindings] = $this->toSql();
        $result = $this->connection->selectOne($sql, $bindings);
        return (int) ($result['aggregate'] ?? 0);
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function insert(array $values): int
    {
        $columns = \array_keys($values);
        $placeholders = \array_fill(0, \count($columns), '?');
        $sql = "INSERT INTO {$this->table} (" . \implode(', ', $columns) . ') VALUES (' . \implode(', ', $placeholders) . ')';
        return $this->connection->insert($sql, \array_values($values));
    }

    public function update(array $values): int
    {
        $sets = [];
        $bindings = [];
        foreach ($values as $col => $val) {
            $sets[] = "{$col} = ?";
            $bindings[] = $val;
        }
        [$whereSql, $whereBindings] = $this->compileWheres();
        $sql = "UPDATE {$this->table} SET " . \implode(', ', $sets) . $whereSql;
        return $this->connection->update($sql, \array_merge($bindings, $whereBindings));
    }

    public function delete(): int
    {
        [$whereSql, $whereBindings] = $this->compileWheres();
        $sql = "DELETE FROM {$this->table}" . $whereSql;
        return $this->connection->delete($sql, $whereBindings);
    }

    /**
     * @return array{0: string, 1: list<mixed>}
     */
    public function toSql(): array
    {
        $sql = 'SELECT ' . \implode(', ', $this->columns);
        $sql .= ' FROM ' . $this->table;
        // Joins
        foreach ($this->joinClauses as $j) {
            $sql .= " {$j['type']} JOIN {$j['table']} ON {$j['local']} {$j['operator']} {$j['foreign']}";
        }
        // Wheres
        [$whereSql, $bindings] = $this->compileWheres();
        $sql .= $whereSql;
        // Order by
        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . \implode(', ', $this->orders);
        }
        // Limit
        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        // Offset
        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }
        return [$sql, $bindings];
    }

    /**
     * @return array{0: string, 1: list<mixed>}
     */
    private function compileWheres(): array
    {
        if (empty($this->wheres)) return ['', []];
        $sql = ' WHERE ';
        $bindings = [];
        $first = true;
        foreach ($this->wheres as $where) {
            if (!$first) {
                $sql .= " {$where['boolean']} ";
            }
            if ($where['operator'] === 'IN') {
                $placeholders = \array_fill(0, \count($where['value']), '?');
                $sql .= "{$where['column']} IN (" . \implode(', ', $placeholders) . ')';
                foreach ($where['value'] as $v) $bindings[] = $v;
            } elseif ($where['operator'] === 'IS NULL' || $where['operator'] === 'IS NOT NULL') {
                $sql .= "{$where['column']} {$where['operator']}";
            } else {
                $sql .= "{$where['column']} {$where['operator']} ?";
                $bindings[] = $where['value'];
            }
            $first = false;
        }
        return [$sql, $bindings];
    }

    /**
     * avg
     */
    public function avg(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * chunk
     */
    public function chunk(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * crossJoin
     */
    public function crossJoin(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * cursor
     */
    public function cursor(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * decrement
     */
    public function decrement(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * find
     */
    public function find(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * forUpdate
     */
    public function forUpdate(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * getBindings
     */
    public function getBindings(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * groupBy
     */
    public function groupBy(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * having
     */
    public function having(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * increment
     */
    public function increment(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * insertGetId
     */
    public function insertGetId(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * insertOrIgnore
     */
    public function insertOrIgnore(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * lazy
     */
    public function lazy(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * max
     */
    public function max(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * min
     */
    public function min(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * paginate
     */
    public function paginate(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * pluck
     */
    public function pluck(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * rightJoin
     */
    public function rightJoin(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * selectRaw
     */
    public function selectRaw(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * sharedLock
     */
    public function sharedLock(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * sum
     */
    public function sum(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * truncate
     */
    public function truncate(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * union
     */
    public function union(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * upsert
     */
    public function upsert(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * value
     */
    public function value(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * whereBetween
     */
    public function whereBetween(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * whereExists
     */
    public function whereExists(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * whereRaw
     */
    public function whereRaw(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
    /**
     * withCte
     */
    public function withCte(/* ... */): mixed
    {
        // TODO: Implement per docs.
        return null;
    }
}
