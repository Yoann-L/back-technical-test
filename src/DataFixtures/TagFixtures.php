<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tags = [
            [
                'name' => "heavy",
                'description' => "order weighs more than 40 kg"
            ],
            [
                'name' => "foreignWarehouse",
                'description' => "order is delivered out of France"
            ],
            [
                'name' => "hasIssues",
                'description' => "order includes an anomaly or discrepancy"
            ],
        ];

        foreach($tags as $tagInfos) {
            $tag = new Tag();
            $tag->setName($tagInfos['name']);
            $tag->setDescription($tagInfos['description']);

            $manager->persist($tag);
        }

        $manager->flush();
    }
}