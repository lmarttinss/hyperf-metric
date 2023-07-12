<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf + OpenCodeCo
 *
 * @link     https://opencodeco.dev
 * @document https://hyperf.wiki
 * @contact  leo@opencodeco.dev
 * @license  https://github.com/opencodeco/hyperf-metric/blob/main/LICENSE
 */
namespace Hyperf\Metric\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\DbConnection\Pool\PoolFactory;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * A simple mysql connection watcher served as an example.
 * This listener is not auto enabled.Tweak it to fit your
 * own need.
 */
class DBPoolWatcher extends PoolWatcher implements ListenerInterface
{
    public function getPrefix()
    {
        return 'mysql';
    }

    public function process(object $event): void
    {
        $config = $this->container->get(ConfigInterface::class);
        $poolNames = array_keys($config->get('databases', ['default' => []]));
        foreach ($poolNames as $poolName) {
            $workerId = (int) ($event->workerId ?? 0);
            $pool = $this
                ->container
                ->get(PoolFactory::class)
                ->getPool($poolName);
            $this->watch($pool, $poolName, $workerId);
        }
    }
}
