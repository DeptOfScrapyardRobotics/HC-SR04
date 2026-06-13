## Basic Usage

### Native (POSIX) driver. (Single Board Computers)

```php

use DeptOfScrapyardRobotics\Sensors\HCSR04;
use RealityInterface\Sensors\Enums\LengthUnit;

$native_sensor = HCSR04::connection('native', 0)
    ->echo(22)
    ->trig(24)
    ->create();
     

// Trigger a Pulse
$native_sensor->fire();

// Pull a reading
$distance = $native_sensor->distance(LengthUnit::CM);  

```

### USB (MPSSE) driver. (Linux and MacOS)

<p>Not Recommended or Supported.</p>

## Alternative Usage

### Using Through the Sensor Library (as an Ultrasonic Distance Sensor)

```php

use RealityInterface\Sensors\Enums\LengthUnit;
use DeptOfScrapyardRobotics\Sensors\HCSR04\HCSR04;
use RealityInterface\Sensors\Applied\Distance\UltrasonicDistanceSensor;

$hc_sr04 = HCSR04::connection('native', 0)
    ->echo(22)
    ->trig(24)
    ->create();

$sensor = UltrasonicDistanceSensor::as($hc_sr04)->units(LengthUnit::FT);

// Get a distance measurement
$sensor->pulse();
$feet_away = $sensor->readDistance();

// Get a measurement object
$event = $sensor->measure();

```
### Using Through the Sensor Library (as a Generic Distance Sensor) 

```php

use RealityInterface\Sensors\Enums\LengthUnit;
use DeptOfScrapyardRobotics\Sensors\HCSR04\HCSR04;
use RealityInterface\Sensors\Applied\Distance\DistanceSensor;

$hc_sr04 = HCSR04::connection('native', 0)
    ->echo(22)
    ->trig(24)
    ->create();

$sensor = DistanceSensor::as($hc_sr04)->units(LengthUnit::FT);

// Get a distance measurement
$feet_away = $sensor->getDistance();

// Get a measurement object
$event = $sensor->measure();

```

### Using Through the Sensor Framework (with an autoloaded config) (as an Ultrasonic Distance Sensor)
```php

use RealityInterface\Sensors\Applied\Distance\UltrasonicDistanceSensor;

$sensor = UltrasonicDistanceSensor::using('hc-sr04')

// Get a distance measurement
$sensor->pulse();
$feet_away = $sensor->readDistance();

// Get a measurement object
$event = $sensor->measure();

```

### Using Through the Sensor Framework (with an autoloaded config) (as a Generic Distance Sensor)
```php

use RealityInterface\Sensors\Applied\Distance\DistanceSensor;

$sensor = DistanceSensor::using('hc-sr04')

// Get a distance measurement
$sensor->pulse();
$feet_away = $sensor->readDistance();

// Get a measurement object
$event = $sensor->measure();

```



