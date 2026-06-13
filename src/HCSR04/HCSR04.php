<?php

namespace DeptOfScrapyardRobotics\Sensors\HCSR04\HCSR04;

use DeptOfScrapyardRobotics\Sensors\HCSR04\HCSR04\Concerns\HCSR04API;
use DeptOfScrapyardRobotics\Sensors\HCSR04\HCSR04\Exceptions\HCSR04Exception;
use DeptOfScrapyardRobotics\Sensors\HCSR04\HCSR04\Factory\HCSR04Factory;
use Exception;
use RealityInterface\Sensors\Attributes\MeasuresDistance;
use RealityInterface\Sensors\Contracts\Applied\Distance\PulseDerivedDistanceSensor;
use RealityInterface\Sensors\Enums\LengthUnit;
use RealityInterface\Sensors\Enums\SensorType;
use RealityInterface\Sensors\SensorChip;
use Waveforms\Carriers\GPIO\GPIO;
use Waveforms\Carriers\GPIO\GPIOBus;

#[MeasuresDistance(SensorType::ULTRASONIC)]
class HCSR04 extends SensorChip implements PulseDerivedDistanceSensor
{
    use HCSR04API;

    public function __construct(
        protected readonly GPIOBus $gpio
    ) {}

    public function getDistance(LengthUnit $unit = LengthUnit::CM): float
    {
        return $this->readDistance() / $this->divisor($unit);
    }

    /**
     * @throws HCSR04Exception
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'distance' => $this->readDistance(),
            default => throw HCSR04Exception::invalidProperty($name)
        };
    }

    /**
     * @throws Exception
     */
    public static function connection(string $driver, int|string $chip_device): HCSR04Factory
    {
        return new HCSR04Factory(
            GPIO::connection($driver),
            $chip_device
        );
    }
}
