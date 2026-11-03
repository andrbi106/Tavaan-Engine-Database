<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Contracts;

/**
 * قرارداد QueryBuilder.
 *
 * @see \Tavaan\Engine\Database\QueryBuilder
 * @api 1.0
 */
interface QueryBuilderInterface
{
    /**
     * متد table.
     *
     * @return mixed
     */
    public function table(/* ... */): mixed;

    /**
     * متد select.
     *
     * @return mixed
     */
    public function select(/* ... */): mixed;

    /**
     * متد selectRaw.
     *
     * @return mixed
     */
    public function selectRaw(/* ... */): mixed;

    /**
     * متد from.
     *
     * @return mixed
     */
    public function from(/* ... */): mixed;

    /**
     * متد join.
     *
     * @return mixed
     */
    public function join(/* ... */): mixed;

    /**
     * متد leftJoin.
     *
     * @return mixed
     */
    public function leftJoin(/* ... */): mixed;

    /**
     * متد rightJoin.
     *
     * @return mixed
     */
    public function rightJoin(/* ... */): mixed;

    /**
     * متد crossJoin.
     *
     * @return mixed
     */
    public function crossJoin(/* ... */): mixed;

    /**
     * متد where.
     *
     * @return mixed
     */
    public function where(/* ... */): mixed;

    /**
     * متد orWhere.
     *
     * @return mixed
     */
    public function orWhere(/* ... */): mixed;

    /**
     * متد whereIn.
     *
     * @return mixed
     */
    public function whereIn(/* ... */): mixed;

    /**
     * متد whereNull.
     *
     * @return mixed
     */
    public function whereNull(/* ... */): mixed;

    /**
     * متد whereBetween.
     *
     * @return mixed
     */
    public function whereBetween(/* ... */): mixed;

    /**
     * متد whereExists.
     *
     * @return mixed
     */
    public function whereExists(/* ... */): mixed;

    /**
     * متد whereRaw.
     *
     * @return mixed
     */
    public function whereRaw(/* ... */): mixed;

    /**
     * متد groupBy.
     *
     * @return mixed
     */
    public function groupBy(/* ... */): mixed;

    /**
     * متد having.
     *
     * @return mixed
     */
    public function having(/* ... */): mixed;

    /**
     * متد orderBy.
     *
     * @return mixed
     */
    public function orderBy(/* ... */): mixed;

    /**
     * متد limit.
     *
     * @return mixed
     */
    public function limit(/* ... */): mixed;

    /**
     * متد offset.
     *
     * @return mixed
     */
    public function offset(/* ... */): mixed;

    /**
     * متد forUpdate.
     *
     * @return mixed
     */
    public function forUpdate(/* ... */): mixed;

    /**
     * متد sharedLock.
     *
     * @return mixed
     */
    public function sharedLock(/* ... */): mixed;

    /**
     * متد union.
     *
     * @return mixed
     */
    public function union(/* ... */): mixed;

    /**
     * متد withCte.
     *
     * @return mixed
     */
    public function withCte(/* ... */): mixed;

    /**
     * متد toSql.
     *
     * @return mixed
     */
    public function toSql(/* ... */): mixed;

    /**
     * متد getBindings.
     *
     * @return mixed
     */
    public function getBindings(/* ... */): mixed;

    /**
     * متد get.
     *
     * @return mixed
     */
    public function get(/* ... */): mixed;

    /**
     * متد cursor.
     *
     * @return mixed
     */
    public function cursor(/* ... */): mixed;

    /**
     * متد first.
     *
     * @return mixed
     */
    public function first(/* ... */): mixed;

    /**
     * متد find.
     *
     * @return mixed
     */
    public function find(/* ... */): mixed;

    /**
     * متد value.
     *
     * @return mixed
     */
    public function value(/* ... */): mixed;

    /**
     * متد pluck.
     *
     * @return mixed
     */
    public function pluck(/* ... */): mixed;

    /**
     * متد count.
     *
     * @return mixed
     */
    public function count(/* ... */): mixed;

    /**
     * متد sum.
     *
     * @return mixed
     */
    public function sum(/* ... */): mixed;

    /**
     * متد avg.
     *
     * @return mixed
     */
    public function avg(/* ... */): mixed;

    /**
     * متد min.
     *
     * @return mixed
     */
    public function min(/* ... */): mixed;

    /**
     * متد max.
     *
     * @return mixed
     */
    public function max(/* ... */): mixed;

    /**
     * متد exists.
     *
     * @return mixed
     */
    public function exists(/* ... */): mixed;

    /**
     * متد insert.
     *
     * @return mixed
     */
    public function insert(/* ... */): mixed;

    /**
     * متد insertGetId.
     *
     * @return mixed
     */
    public function insertGetId(/* ... */): mixed;

    /**
     * متد insertOrIgnore.
     *
     * @return mixed
     */
    public function insertOrIgnore(/* ... */): mixed;

    /**
     * متد upsert.
     *
     * @return mixed
     */
    public function upsert(/* ... */): mixed;

    /**
     * متد update.
     *
     * @return mixed
     */
    public function update(/* ... */): mixed;

    /**
     * متد increment.
     *
     * @return mixed
     */
    public function increment(/* ... */): mixed;

    /**
     * متد decrement.
     *
     * @return mixed
     */
    public function decrement(/* ... */): mixed;

    /**
     * متد delete.
     *
     * @return mixed
     */
    public function delete(/* ... */): mixed;

    /**
     * متد truncate.
     *
     * @return mixed
     */
    public function truncate(/* ... */): mixed;

    /**
     * متد paginate.
     *
     * @return mixed
     */
    public function paginate(/* ... */): mixed;

    /**
     * متد chunk.
     *
     * @return mixed
     */
    public function chunk(/* ... */): mixed;

    /**
     * متد lazy.
     *
     * @return mixed
     */
    public function lazy(/* ... */): mixed;
}