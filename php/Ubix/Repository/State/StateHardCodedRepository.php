<?php

declare(strict_types=1);

namespace Ubix\Repository\State;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Model\State;
use Ubix\Repository\State\StateReaderInterface as StateReader;

/**
 * Hard coded repository for reading state data
 *
 * @see \Ubix\Tests\Repository\State\StateHardCodedRepositoryTest PHPUnit test case
 */
final class StateHardCodedRepository implements StateReader
{
    /**
     * @var State[] $statesSingleton
     */
    private static ?array $statesSingleton = null;

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
        //
        //  If the singleton is not yet populated, populate it now
        //
        if (self::$statesSingleton === null) {
            //
            //  Source: https://en.wikipedia.org/wiki/ISO_3166-2:US
            //
            $this->addState('Alabama', 'US-AL');
            $this->addState('Alaska', 'US-AK');
            $this->addState('Arizona', 'US-AZ');
            $this->addState('Arkansas', 'US-AR');
            $this->addState('California', 'US-CA');
            $this->addState('Colorado', 'US-CO');
            $this->addState('Connecticut', 'US-CT');
            $this->addState('Delaware', 'US-DE');
            $this->addState('Florida', 'US-FL');
            $this->addState('Georgia', 'US-GA');
            $this->addState('Hawaii', 'US-HI');
            $this->addState('Idaho', 'US-ID');
            $this->addState('Illinois', 'US-IL');
            $this->addState('Indiana', 'US-IN');
            $this->addState('Iowa', 'US-IA');
            $this->addState('Kansas', 'US-KS');
            $this->addState('Kentucky', 'US-KY');
            $this->addState('Louisiana', 'US-LA');
            $this->addState('Maine', 'US-ME');
            $this->addState('Maryland', 'US-MD');
            $this->addState('Massachusetts', 'US-MA');
            $this->addState('Michigan', 'US-MI');
            $this->addState('Minnesota', 'US-MN');
            $this->addState('Mississippi', 'US-MS');
            $this->addState('Missouri', 'US-MO');
            $this->addState('Montana', 'US-MT');
            $this->addState('Nebraska', 'US-NE');
            $this->addState('Nevada', 'US-NV');
            $this->addState('New Hampshire', 'US-NH');
            $this->addState('New Jersey', 'US-NJ');
            $this->addState('New Mexico', 'US-NM');
            $this->addState('New York', 'US-NY');
            $this->addState('North Carolina', 'US-NC');
            $this->addState('North Dakota', 'US-ND');
            $this->addState('Ohio', 'US-OH');
            $this->addState('Oklahoma', 'US-OK');
            $this->addState('Oregon', 'US-OR');
            $this->addState('Pennsylvania', 'US-PA');
            $this->addState('Rhode Island', 'US-RI');
            $this->addState('South Carolina', 'US-SC');
            $this->addState('South Dakota', 'US-SD');
            $this->addState('Tennessee', 'US-TN');
            $this->addState('Texas', 'US-TX');
            $this->addState('Utah', 'US-UT');
            $this->addState('Vermont', 'US-VT');
            $this->addState('Virginia', 'US-VA');
            $this->addState('Washington', 'US-WA');
            $this->addState('West Virginia', 'US-WV');
            $this->addState('Wisconsin', 'US-WI');
            $this->addState('Wyoming', 'US-WY');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getByName(string $name): array
    {
        //
        //  Search the singleton array for a matching state name
        //
        foreach (self::$statesSingleton ?? [] as $state) {
            if ($state->getName() === $name) {
                return [$state];
            }
        }

        //
        //  If no results are found then return empty array
        //
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getByIso31662(string $iso31662): array
    {
        //
        //  Search the singleton array for a matching ISO 3166-2 code (two letter country code then a dash then two letter state code)
        //
        foreach (self::$statesSingleton ?? [] as $state) {
            if ($state->getIso31662() === $iso31662) {
                return [$state];
            }
        }

        //
        //  If no results are found then return empty array
        //
        return [];
    }

    /**
     * Add a state to the singleton array
     *
     * @param string $name     The state's name
     * @param string $iso31662 The state's ISO 3166-2 code
     *
     * @return void
     */
    private function addState(string $name, string $iso31662): void
    {
        self::$statesSingleton[] = new State(
            name:     $name,
            iso31662: $iso31662,
        );
    }
}
