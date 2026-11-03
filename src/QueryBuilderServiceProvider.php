<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database;

use Tavaan\Core\Contracts\Container\ContainerInterface;
use Tavaan\Core\Contracts\Config\RepositoryInterface as ConfigRepository;
use Tavaan\Engine\Database\Contracts\QueryBuilderInterface;
use Tavaan\Core\Provider\ServiceProvider;

/**
 * QueryBuilderServiceProvider — ثبت سرویس‌های موتور QueryBuilder.
 */
final class QueryBuilderServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->singleton(QueryBuilderInterface::class, function (ContainerInterface $app) {
            /** @var ConfigRepository $config */
            $config = $app->get(ConfigRepository::class);
            return new QueryBuilder($config);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(): void
    {
        // ثبت Facade
        $this->app->instance('db', $this->app->get(QueryBuilderInterface::class));

    }

    /**
     * {@inheritdoc}
     */
    public function provides(): array
    {
        return [
            QueryBuilderInterface::class,
            'db',
        ];
    }
}