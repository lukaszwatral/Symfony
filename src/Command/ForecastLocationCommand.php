<?php

namespace App\Command;

use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;
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
        private LocationRepository $locationRepository,
        private ForecastRepository $forecastRepository,
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

        $location = $this->locationRepository->findOneBy([
            'countryCode' => $countryCode,
            'name' => $cityName
        ]);

        if (!$location) {
            throw new \Exception("Location not found");
        }

        $forecasts = $this->forecastRepository->findForecast($location);

        $io->title("Forecast for $cityName, $countryCode");

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
