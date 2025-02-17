<?php

namespace App\Command;

use App\Entity\Forecast;
use App\Exception\LocationNotFoundException;
use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;
use App\Service\ForecastService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'forecast:location',
    description: 'Fetching forecast for specified location.',
)]
class ForecastLocationCommand extends Command
{
    public function __construct(
        private ForecastService $forecastService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
           ->addArgument('countryCode', InputArgument::REQUIRED, 'Country code of the location')
            ->addArgument('cityName', InputArgument::REQUIRED, 'City name of the location')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $countryCode = $input->getArgument('countryCode');
        $cityName = $input->getArgument('cityName');

        try {
            /** @var $location Location */
            /** @var $forecasts Forecast[] */
            list($location, $forecasts) = $this->forecastService->getForecastForLocations($countryCode, $cityName);
        } catch (LocationNotFoundException $e) {
            $io->error("Location $cityName, $countryCode not found");
            return Command::FAILURE;
        }
        $io->title("Forecast for {$location->getName()}, {$location->getCountryCode()}");

        $forecastsArray = [];
        foreach ($forecasts as $forecast) {
            $forecastsArray[] = [
                $forecast->getDate()->format('Y-m-d'),
                $forecast->getTemperature(),
                $forecast->getFeelsLike(),
                $forecast->getPressure(),
                $forecast->getHumidity(),
                $forecast->getWindSpeed(),
                $forecast->getIcon(),
                $forecast->getCloudiness(),
            ];
        }
        $io->horizontalTable([
            'Date',
            'Temperature',
            'Feels like',
            'Pressure',
            'Humidity',
            'Wind speed',
            'Icon',
            'Cloudiness'
        ], $forecastsArray);


        return Command::SUCCESS;
    }
}
