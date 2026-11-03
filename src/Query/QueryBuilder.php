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
        return (float) ($this->connection->selectOne('SELECT COALESCE(AVG(' . $column . '), 0) AS aggregate FROM ' . $this->table . $this->compileWheres()[0], $this->compileWheres()[1])['aggregate'] ?? 0);
    }
    /**
     * chunk
     */
    public function chunk(/* ... */): mixed
    {
        $results = $this->skip(0)->take($count)->get(); while (count($results) > 0) { foreach ($results as $r) $callback($r); $results = $this->skip(0)->take($count)->get(); } return true;
    }
    /**
     * crossJoin
     */
    public function crossJoin(/* ... */): mixed
    {
        return $this;
    }
    /**
     * cursor
     */
    public function cursor(/* ... */): mixed
    {
        return $this->get();
    }
    /**
     * decrement
     */
    public function decrement(/* ... */): mixed
    {
        $this->connection->update('UPDATE ' . $this->table . ' SET ' . $column . ' = ' . $column . ' - ?', [$amount]); return $this;
    }
    /**
     * find
     */
    public function find(/* ... */): mixed
    {
        return [];
    }
    /**
     * forUpdate
     */
    public function forUpdate(/* ... */): mixed
    {
        return null;
    }
    /**
     * getBindings
     */
    public function getBindings(/* ... */): mixed
    {
        return array_keys($this->bindings);
    }
    /**
     * groupBy
     */
    public function groupBy(/* ... */): mixed
    {
        return $this;
    }
    /**
     * having
     */
    public function having(/* ... */): mixed
    {
        return $this;
    }
    /**
     * increment
     */
    public function increment(/* ... */): mixed
    {
        $this->connection->update('UPDATE ' . $this->table . ' SET ' . $column . ' = ' . $column . ' + ?', [$amount]); return $this;
    }
    /**
     * insertGetId
     */
    public function insertGetId(/* ... */): mixed
    {
        return $this->connection->insert($sql, $bindings);
    }
    /**
     * insertOrIgnore
     */
    public function insertOrIgnore(/* ... */): mixed
    {
        return $this->connection->insert($sql, $bindings);
    }
    /**
     * lazy
     */
    public function lazy(/* ... */): mixed
    {
        return null;
    }
    /**
     * max
     */
    public function max(/* ... */): mixed
    {
        return $this->connection->selectOne('SELECT MAX(' . $column . ') AS aggregate FROM ' . $this->table)['aggregate'] ?? null;
    }
    /**
     * min
     */
    public function min(/* ... */): mixed
    {
        return $this->connection->selectOne('SELECT MIN(' . $column . ') AS aggregate FROM ' . $this->table)['aggregate'] ?? null;
    }
    /**
     * paginate
     */
    public function paginate(/* ... */): mixed
    {
        return $this;
    }
    /**
     * pluck
     */
    public function pluck(/* ... */): mixed
    {
        return array_column($this->get(), $column, $key ?? null);
    }
    /**
     * rightJoin
     */
    public function rightJoin(/* ... */): mixed
    {
        return $this;
    }
    /**
     * selectRaw
     */
    public function selectRaw(/* ... */): mixed
    {
        return [];
    }
    /**
     * sharedLock
     */
    public function sharedLock(/* ... */): mixed
    {
        return $this;
    }
    /**
     * sum
     */
    public function sum(/* ... */): mixed
    {
        return (float) ($this->connection->selectOne('SELECT COALESCE(SUM(' . $column . '), 0) AS aggregate FROM ' . $this->table . $this->compileWheres()[0], $this->compileWheres()[1])['aggregate'] ?? 0);
    }
    /**
     * truncate
     */
    public function truncate(/* ... */): mixed
    {
        $this->connection->exec('DELETE FROM ' . $this->table); return $this;
    }
    /**
     * union
     */
    public function union(/* ... */): mixed
    {
        return $this;
    }
    /**
     * upsert
     */
    public function upsert(/* ... */): mixed
    {
        return null;
    }
    /**
     * value
     */
    public function value(/* ... */): mixed
    {
        return null;
    }
    /**
     * whereBetween
     */
    public function whereBetween(/* ... */): mixed
    {
        return $this;
    }
    /**
     * whereExists
     */
    public function whereExists(/* ... */): mixed
    {
        return $this;
    }
    /**
     * whereRaw
     */
    public function whereRaw(/* ... */): mixed
    {
        return $this;
    }
    /**
     * withCte
     */
    public function withCte(/* ... */): mixed
    {
        return null;
    }
}
