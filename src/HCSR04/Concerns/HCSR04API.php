<?php

namespace DeptOfScrapyardRobotics\Sensors\HCSR04\HCSR04\Concerns;

trait HCSR04API
{
    use HCSR04InternalAPI;

    public function readDistance(): float
    {
        $this->fire();
        $duration_us = ($this->end_time - $this->start_time) / 1_000;

        if ($duration_us <= 0) {
            return 0.0;
        }

        return $duration_us;
    }
}
