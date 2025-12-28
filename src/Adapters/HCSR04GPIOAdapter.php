<?php

namespace ScrapyardIO\Sensors\Distance\HCSR04\Adapters;

use ScrapyardIO\Sensors\Enums\SensorType;
use ScrapyardIO\Support\Attributes\Sensor;
use ScrapyardIO\Sensors\Distance\Adapters\DistanceSensorAdapter;
use ScrapyardIO\Sensors\Distance\HCSR04\Concerns\HCSR04GPIOChip;

#[Sensor('HC-SR04', 1, SensorType::PROXIMITY)]
class HCSR04GPIOAdapter extends DistanceSensorAdapter
{
    use HCSR04GPIOChip;

    public function trigPin(int $chip, int $line): static
    {
        $this->trig_chip($chip);
        $this->trig_line($line);
        $this->trig_gpio();

        return $this;
    }

    public function echoPin(int $chip, int $line): static
    {
        $this->echo_chip($chip);
        $this->echo_line($line);
        $this->echo_gpio();

        return $this;
    }

    public function rawMillimeters(): int
    {
        $ready = $this->echoReady(2000);

        if ($ready) {
            $this->trigger();
            $result = $this->echoWait(2000);

            if ($result === false) {
                return -1;
            }

            [$start, $end] = $result;
            $time_diff = $end - $start;
            $distance_mm = ($time_diff * 343000) / 2;

            return intval($distance_mm);
        }

        return -1;
    }

    /**
     * @return $this
     */
    public function boot(): static
    {

        return $this;
    }
}
