<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\LocationNotFoundException;
use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;
use App\Service\ForecastService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WeatherController extends AbstractController {
    #[Route('/weather/{countryCode}/{cityName}')]
    public function forecast(
        ForecastService $forecastService,
        string $countryCode,
        string $cityName,
    ): Response {

        try {
            list($location, $forecasts) = $forecastService->getForecastForLocations($countryCode, $cityName);

        } catch (LocationNotFoundException $e) {
            throw $this->createNotFoundException('Location not found');
        }
            $response = $this->render('weather/forecast.html.twig', [
                'forecasts' => $forecasts,
                'location' => $location,
            ]);

            return $response;
    }
}