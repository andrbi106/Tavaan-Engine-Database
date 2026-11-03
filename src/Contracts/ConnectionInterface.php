<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Contracts;

/**
 * قرارداد Connection.
 *
 * @see \Tavaan\Engine\Database\Connection
 * @api 1.0
 */
interface ConnectionInterface
{
    /**
     * متد select.
     *
     * @return mixed
     */
    public function select(/* ... */): mixed;

    /**
     * متد selectOne.
     *
     * @return mixed
     */
    public function selectOne(/* ... */): mixed;

    /**
     * متد cursor.
     *
     * @return mixed
     */
    public function cursor(/* ... */): mixed;

    /**
     * متد insert.
     *
     * @return mixed
     */
    public function insert(/* ... */): mixed;

    /**
     * متد update.
     *
     * @return mixed
     */
    public function update(/* ... */): mixed;

    /**
     * متد delete.
     *
     * @return mixed
     */
    public function delete(/* ... */): mixed;

    /**
     * متد statement.
     *
     * @return mixed
     */
    public function statement(/* ... */): mixed;

    /**
     * متد affectingStatement.
     *
     * @return mixed
     */
    public function affectingStatement(/* ... */): mixed;

    /**
     * متد lastInsertId.
     *
     * @return mixed
     */
    public function lastInsertId(/* ... */): mixed;

    /**
     * متد transaction.
     *
     * @return mixed
     */
    public function transaction(/* ... */): mixed;

    /**
     * متد beginTransaction.
     *
     * @return mixed
     */
    public function beginTransaction(/* ... */): mixed;

    /**
     * متد commit.
     *
     * @return mixed
     */
    public function commit(/* ... */): mixed;

    /**
     * متد rollBack.
     *
     * @return mixed
     */
    public function rollBack(/* ... */): mixed;

    /**
     * متد transactionLevel.
     *
     * @return mixed
     */
    public function transactionLevel(/* ... */): mixed;

    /**
     * متد pdo.
     *
     * @return mixed
     */
    public function pdo(/* ... */): mixed;

    /**
     * متد queryLog.
     *
     * @return mixed
     */
    public function queryLog(/* ... */): mixed;
}