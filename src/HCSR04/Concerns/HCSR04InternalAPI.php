<?php

namespace DeptOfScrapyardRobotics\Sensors\HCSR04\HCSR04\Concerns;

use DeptOfScrapyardRobotics\Sensors\src2\Exceptions\HCSR04Exception;
use RealityInterface\Sensors\Enums\LengthUnit;
use Waveforms\Carriers\GPIO\Events\EdgeFallingEvent;
use Waveforms\Carriers\GPIO\Events\EdgeRisingEvent;

trait HCSR04InternalAPI
{
    private float $start_time = 0;

    private float $end_time = 0;

    /**
     * Reset the device's state
     */
    protected function reset(): void
    {
        $this->start_time = 0;
        $this->end_time = 0;
        $this->gpio->trig()->low();
        usleep(2);

        // Discard any leftover/spurious edges so this capture starts from an
        // empty FIFO. Otherwise a stale edge shifts every rising/falling pair.
        $this->gpio->echo()->flush();
    }

    protected function trigger(): void
    {
        $this->gpio->trig()->high();
        usleep(10);
        $this->gpio->trig()->low();
    }

    /**
     * @throws HCSR04Exception
     */
    protected function fire(int $deadline_ns = -1): void
    {
        $this->reset();
        $this->trigger();
        $this->wait($deadline_ns);
        $this->echo($deadline_ns);
    }

    protected function divisor(LengthUnit $unit): float
    {
        return match ($unit) {
            LengthUnit::MM => 5.8,
            LengthUnit::CM => 58.0,
            LengthUnit::M => 5800.0,
            LengthUnit::IN => 148.0,
            LengthUnit::FT => 1776.0,
            LengthUnit::YD => 5328.0,
        };
    }

    /**
     * After firing the trigger pulse, wait for the echo pin to go high.
     *
     * @throws HCSR04Exception
     */
    protected function wait(int $deadline_ns = -1): void
    {
        if ($deadline_ns == -1) {
            do {
                $event = $this->gpio->echo()->listen();
            } while (! $event instanceof EdgeRisingEvent);

            $this->start_time = $event->timestamp_ns;
        } else {
            $wait_start = hrtime(true);
            while ($this->gpio->echo()->read() == 0) {
                if ((hrtime(true) - $wait_start) >= $deadline_ns) {
                    throw HCSR04Exception::echoTimeout($deadline_ns);
                }
            }
            $this->start_time = hrtime(true);
        }
    }

    /**
     * After the echo pin goes high, wait for the echo pin to go low.
     *
     * @throws HCSR04Exception
     */
    protected function echo(int $deadline_ns = -1): void
    {
        if ($deadline_ns == -1) {
            do {
                $event = $this->gpio->echo()->listen();
            } while (! $event instanceof EdgeFallingEvent);

            $this->end_time = $event->timestamp_ns;
        } else {
            $wait_start = hrtime(true);
            while ($this->gpio->echo()->read() == 1) {
                if ((hrtime(true) - $wait_start) >= $deadline_ns) {
                    throw HCSR04Exception::echoTimeout($deadline_ns);
                }
            }
            $this->end_time = hrtime(true);
        }
    }
}
