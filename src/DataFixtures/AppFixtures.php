<?php

namespace App\DataFixtures;

use App\Entity\Forecast;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Location;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $barcelona = $this->addLocation($manager, "Barcelona", "ES", 41.3874, 2.1686);
        $berlin = $this->addLocation($manager, "Berlin", "DE", 52.5200, 13.4050);
        $stettin = $this->addLocation($manager, "Stettin", "PL", 53.4285, 14.5528);
        $paris = $this->addLocation($manager, "Paris", "FR", 48.8647, 2.3490);

        $forecast1 = $this->addForecast($manager, "2025-02-14", $barcelona, 21, 22, 'cloud', 1025, 40, 3.2, 25);
        $forecast1 = $this->addForecast($manager, "2025-02-15", $barcelona, 22, 23, 'sun', 1009, 30, 0.7, 10);
        $forecast1 = $this->addForecast($manager, "2025-02-16", $barcelona, 23, 24, 'rain', 996, 80, 2.5, 90);
        $forecast1 = $this->addForecast($manager, "2025-02-17", $berlin, 21, 22, 'cloud', 1025, 40, 3.2, 25);
        $forecast1 = $this->addForecast($manager, "2025-02-18", $stettin, 22, 23, 'sun', 1009, 30, 0.7, 10);
        $forecast1 = $this->addForecast($manager, "2025-02-19", $paris, 23, 24, 'rain', 996, 80, 2.5, 90);


        $manager->flush();
    }

    private function addLocation(ObjectManager $objectManager, string $name, string $countryCode, float $latitude, float $longitude): Location {
        $location = new Location();
        $location->setName($name)
            ->setCountryCode($countryCode)
            ->setLatitude($latitude)
            ->setLongitude($longitude)
        ;

        $objectManager->persist($location);

        return $location;
    }

    private function addForecast(ObjectManager $objectManager, string $date, Location $location, int $temperature, int $feelsLike, string $icon, int $pressure=null, int $humidity=null, float $windSpeed=null, int $cloudiness=null): Forecast {
         $forecast = new Forecast();
         $forecast->setDate(new \DateTime($date))
             ->setLocation($location)
             ->setTemperature($temperature)
             ->setFeelsLike($feelsLike)
             ->setPressure($pressure)
             ->setHumidity($humidity)
             ->setWindSpeed($windSpeed)
             ->setCloudiness($cloudiness)
             ->setIcon($icon)
             ;

         $objectManager->persist($forecast);

         return $forecast;
    }
}
