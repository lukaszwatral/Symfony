<?php

namespace App\Controller;

use App\Exception\LocationNotFoundException;
use App\Service\ForecastService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/weather')]
final class WeatherApiController extends AbstractController
{
    #[Route('/forecast')]
    public function forecast(
        ForecastService $forecastService,
        #[MapQueryParameter] string $name,
        #[MapQueryParameter] string $country
    ): JsonResponse
    {
        try {
            /** @var $location Location */
            /** @var $forecasts Forecast[] */

            list($location, $forecasts) = $forecastService->getForecastForLocations($country, $name);
        } catch (LocationNotFoundException $e) {
            return new JsonResponse(['succes' => false, 'error' => 'Location not found'], Response::HTTP_FOUND);
        }

        $json = [
            'location_name' => $location->getName(),
            'location_country' => $location->getCountryCode(),
            'forecasts' => []
        ];

        foreach ($forecasts as $forecast) {
            $row = [
                'date' => $forecast->getDate()->format('Y-m-d'),
                'temperature' => $forecast->getTemperature(),
                'feels_like' => $forecast->getFeelsLike(),
                'pressure' => $forecast->getPressure(),
                'humidity' => $forecast->getHumidity(),
                'wind_speed' => $forecast->getWindSpeed(),
                'cloudiness' => $forecast->getCloudiness(),
                'icon' => $forecast->getIcon(),
            ];

            $json['forecasts'][$forecast->getDate()->format('Y-m-d')] = $row;
        }

        return new JsonResponse($json);
    }
}
