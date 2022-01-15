<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $countries = [
            [
                'iso' => "FR",
                'iso3' => "FRA",
                'name' => "France"
            ],
            [
                'iso' => "BE",
                'iso3' => "BEL",
                'name' => "Belgium"
            ],
            [
                'iso' => "DE",
                'iso3' => "DEU",
                'name' => "Germany"
            ],
            [
                'iso' => "PL",
                'iso3' => "POL",
                'name' => "Poland"
            ]
        ];

        foreach ($countries as $countryInfos) {
            $country = new Country();
            $country->setIso($countryInfos['iso']);
            $country->setIso3($countryInfos['iso3']);
            $country->setName($countryInfos['name']);

            $manager->persist($country);
        }

        $manager->flush();
    }
}