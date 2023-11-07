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
namespace Hyperf\Metric\Adapter\Prometheus;

use Hyperf\Metric\Contract\GaugeInterface;
use Prometheus\RegistryInterface;
use Prometheus\Exception\MetricsRegistrationException;

class Gauge implements GaugeInterface
{
    protected \Prometheus\Gauge $gauge;

    /**
     * @var string[]
     */
    protected array $labelValues = [];

    /**
     * @throws MetricsRegistrationException
     */
    public function __construct(protected RegistryInterface $registry, string $namespace, string $name, string $help, array $labelNames)
    {
        $this->gauge = $registry->getOrRegisterGauge($namespace, $name, $help, $labelNames);
    }

    public function with(string ...$labelValues): static
    {
        $this->labelValues = $labelValues;
        return $this;
    }

    public function set(float $value): void
    {
        $this->gauge->set($value, $this->labelValues);
    }

    public function add(float $delta): void
    {
        $this->gauge->incBy($delta, $this->labelValues);
    }
}
