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
namespace HyperfTest\Metric\Cases;

use Hyperf\Config\Config;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Engine\Http\ServerFactory;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Metric\Adapter\Prometheus\Constants;
use Hyperf\Metric\Adapter\Prometheus\MetricFactory as PrometheusFactory;
use Mockery;
use PHPUnit\Framework\TestCase;
use Prometheus\CollectorRegistry;
use ReflectionClass;
use ReflectionMethod;

/**
 * @internal
 * @coversNothing
 */
class MetricFactoryTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testPrometheusGetUri()
    {
        $config = new Config([
            'metric' => [
                'default' => 'prometheus',
                'use_standalone_process' => true,
                'metric' => [
                    'prometheus' => [
                        'driver' => PrometheusFactory::class,
                        'mode' => Constants::SCRAPE_MODE,
                        'namespace' => 'Hello-World!',
                    ],
                ],
            ],
        ]);
        $r = Mockery::mock(CollectorRegistry::class);
        $c = Mockery::mock(ClientFactory::class);
        $l = Mockery::mock(StdoutLoggerInterface::class);
        $p = new PrometheusFactory($config, $r, $c, $l, new ServerFactory($l));
        $ref = new ReflectionClass($p);
        $method = $ref->getMethod('getUri');
        $method->setAccessible(true);
        $this->assertStringContainsString('http://127.0.0.1/metrics/job/metric/ip/', $method->invokeArgs($p, ['127.0.0.1', 'metric']));
        $this->assertStringContainsString('https://127.0.0.1/metrics/job/metric/ip/', $method->invokeArgs($p, ['https://127.0.0.1', 'metric']));
        $this->assertStringContainsString('http://127.0.0.1:8080/metrics/job/metric/ip/', $method->invokeArgs($p, ['127.0.0.1:8080', 'metric']));
    }

    public function testGetNamespace()
    {
        $config = new Config([
            'metric' => [
                'default' => 'prometheus',
                'use_standalone_process' => true,
                'metric' => [
                    'prometheus' => [
                        'driver' => PrometheusFactory::class,
                        'mode' => Constants::SCRAPE_MODE,
                        'namespace' => 'Hello-World!',
                    ],
                ],
            ],
        ]);
        $r = Mockery::mock(CollectorRegistry::class);
        $c = Mockery::mock(ClientFactory::class);
        $l = Mockery::mock(StdoutLoggerInterface::class);
        $p = new PrometheusFactory($config, $r, $c, $l, new ServerFactory($l));
        $method = new ReflectionMethod(PrometheusFactory::class, 'getNamespace');
        $method->setAccessible(true);
        $this->assertEquals('hello__world_', $method->invoke($p));
    }
}
