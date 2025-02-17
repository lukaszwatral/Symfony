<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WeatherController extends AbstractController {
    #[Route('/weather/{countryCode}/{cityName}')]
    public function forecast(
        LocationRepository $locationRepository,
        ForecastRepository $forecastRepository,
        string $countryCode,
        string $cityName,
    ): Response {

        $location = $locationRepository->findOneBy([
            'countryCode' => $countryCode,
            'name' => $cityName
        ]);

        if (!$location) {
            throw $this->createNotFoundException("Location not found");
        }

        $forecasts = $forecastRepository->findForecast($location);


            $response = $this->render('weather/forecast.html.twig', [
                'forecasts' => $forecasts,
                'location' => $location,
            ]);

            return $response;
    }
}