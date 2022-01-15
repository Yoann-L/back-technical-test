<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Address;
use App\Entity\Country;

class AddressFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $countryRepository = $manager->getRepository(Country::class);

        $france = $countryRepository->findOneBy(['iso' => "FR"]);
        $germany = $countryRepository->findOneBy(['iso' => "DE"]);

        $addresses = [
            [
                'city' => "Paris",
                'country' => $france,
                'postal_code' => "75002",
                'state' => "Ile de france",
                'street_name' => "Rue de la Paix",
                'street_number' => "8"
            ],
            [
                'city' => "Paris",
                'country' => $france,
                'postal_code' => "99999999",
                'state' => "Ile de la réunion",
                'street_name' => "invalid street",
                'street_number' => "-1"
            ],
            [
                'city' => "Berlin",
                'country' => $germany,
                'postal_code' => null,
                'state' => null,
                'street_name' => null,
                'street_number' => null
            ],
        ];

        foreach ($addresses as $addressInfos) {
            $address = new Address();
            $address->setCity($addressInfos['city']);
            $address->setCountry($addressInfos['country']);
            $address->setPostalCode($addressInfos['postal_code']);
            $address->setState($addressInfos['state']);
            $address->setStreetName($addressInfos['street_name']);
            $address->setStreetNumber($addressInfos['street_number']);
            
            $manager->persist($address);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return array("App\DataFixtures\CountryFixtures");
    }
}