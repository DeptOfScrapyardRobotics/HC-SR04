<?php

namespace ScrapyardIO\Sensors\Distance\HCSR04\Concerns;

use ScrapyardIO\Transports\Concerns\EchoPin;
use ScrapyardIO\Transports\Concerns\TrigPin;


trait HCSR04GPIOChip
{
    use EchoPin, TrigPin;

    public function trigger(): void
    {
        $this->trigLow();
        usleep(2);
        $this->trigHigh();
        usleep(10);
        $this->trigLow();
    }

    public function echoReady(int $timeout_ms = 0): bool
    {
        $low_time = microtime(true);
        while(!$this->isLow())
        {
            usleep(1000);

            if ($timeout_ms > 0) {
                $elapsed_ms = (microtime(true) - $low_time) * 1000;
                if ($elapsed_ms >= $timeout_ms) {
                    return false; // Timeout reached
                }
            }
        }

        return true; // Device is ready
    }

    public function echoWait(int $timeout_ms = 0): array|bool
    {
        $timeout_start = microtime(true);

        // Wait for echo pin to go HIGH
        while (!$this->isHigh()) {
            if ($timeout_ms > 0) {
                $elapsed_ms = (microtime(true) - $timeout_start) * 1000;
                if ($elapsed_ms >= $timeout_ms) {
                    return false;
                }
            }
        }

        $pulse_start = microtime(true);

        // Wait for echo pin to go LOW
        while ($this->isHigh()) {
            if ($timeout_ms > 0) {
                $elapsed_ms = (microtime(true) - $timeout_start) * 1000;
                if ($elapsed_ms >= $timeout_ms) {
                    return false;
                }
            }
        }

        $pulse_end = microtime(true);

        return [$pulse_start, $pulse_end];
    }

    public function isLow(): bool
    {
        return $this->echoRead() === 0;
    }

    public function isHigh(): bool
    {
        return $this->echoRead() === 1;
    }
}
