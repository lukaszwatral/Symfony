<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\LocationNotFoundException;
use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;

class ForecastService {

    public function __construct(
        private LocationRepository $locationRepository,
        private ForecastRepository $forecastRepository,
    ) {
    }

    public function getForecastForLocations(string $countryCode, string $cityName): array {

        $location = $this->locationRepository->findOneBy([
            'countryCode' => $countryCode,
            'name' => $cityName
        ]);

        if (!$location) {
            throw new LocationNotFoundException();
        }

        $forecasts = $this->forecastRepository->findForecast($location);

        return [$location, $forecasts];
    }
}