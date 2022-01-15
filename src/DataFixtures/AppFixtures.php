<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $addressRepository = $manager->getRepository(Address::class);

        $addresses = [
            $addressRepository->findOneBy(['streetName' => "Rue de la Paix"]),
            $addressRepository->findOneBy(['postalCode' => "99999999"]),
            $addressRepository->findOneBy(['city' => "Berlin"])
        ];

        for ($i = 1; $i < 30; $i++) {
            $order = new Order();
            $order->setContactEmail($this->faker->randomElement(['', $this->faker->email]));
            $order->setName('#'. mt_rand(10, 100000));
            $order->setShippingAddress($addresses[array_rand($addresses)]);

            $nbLines = mt_rand(1, 10);
            for ($j = 1; $j < $nbLines; $j++) {
                $qty = mt_rand(1, 4);
                $price = mt_rand(100, 5000);

                $orderLine = new OrderLine();
                $orderLine->setQuantity($qty);
                $orderLine->setTotal($qty * $price);

                $product = new Product();
                $product->setName($this->faker->safeColorName . ' nuts');
                $product->setWeight(mt_rand(100, 5000));
                $manager->persist($product);

                $orderLine->setProduct($product);
                $orderLine->setOrder($order);

                $manager->persist($product);
                $manager->persist($orderLine);
            }

            $manager->persist($order);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return array("App\DataFixtures\AddressFixtures");
    }
}