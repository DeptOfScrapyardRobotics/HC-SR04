<?php

namespace DeptOfScrapyardRobotics\Sensors\HCSR04\HCSR04\Factory;

use DeptOfScrapyardRobotics\Sensors\HCSR04\HCSR04\HCSR04;
use Exception;
use Waveforms\Carriers\GPIO\Factory\GPIOConnectionBuilder;
use Waveforms\Carriers\GPIO\GPIOPin;

class HCSR04Factory
{
    protected bool $has_echo = false;

    protected bool $has_trig = false;

    public string $consumer = 'hc-sr04';

    public function __construct(
        public GPIOConnectionBuilder $connection,
        public int|string $chip_device
    ) {
        $this->connection = $connection->firstly($this->chip_device);
    }

    /**
     * @throws Exception
     */
    public function echo(int $pin, bool $nonblocking = false): static
    {
        if (! $this->has_echo) {
            $gpio_input = GPIOPin::createInput($this->connection->connection(), $pin, 'echo')
                ->edgeEvents();

            if ($nonblocking) {
                $gpio_input = $gpio_input->nonblocking();
            }

            $this->connection = $this->connection->addInput($gpio_input);
            $this->has_echo = true;
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function trig(int $pin): static
    {
        if (! $this->has_trig) {
            $gpio_output = GPIOPin::createOutput($this->connection->connection(), $pin, 'trig');

            $this->connection = $this->connection->addOutput($gpio_output);
            $this->has_trig = true;
        }

        return $this;
    }

    public function consumer(string $consumer): static
    {
        $this->consumer = $consumer;

        return $this;
    }

    public function create(): HCSR04
    {
        $bus = $this->connection->consumer($this->consumer)->boot();

        return new HCSR04($bus);
    }
}
