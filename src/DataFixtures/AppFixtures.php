<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Color;
use App\Entity\Price;
use App\Entity\Reference;
use App\Entity\Size;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();

        $colors =['black', 'white', 'red', 'blue', 'green'];
        $dataColors = [];
        for ($i = 0; $i < count($colors); $i++) {
            $color = new Color();
            $color->setName($colors[$i]);
            $dataColors[] = $color;
            $manager->persist($color);
        }

        $sizes = ['xs','s', 'm', 'l', 'xl'];
        $dataSizes = [];
        for ($i = 0; $i < count($sizes); $i++) {
            $size = new Size();
            $size->setName($sizes[$i]);
            $dataSizes[] = $size;
            $manager->persist($size);

        }

        $prices = ['29', '39', '49'];
        $dataPrices = [];
        for ($i = 0; $i < count($prices); $i++) {
            $price = new Price();
            $price->setAmount($prices[$i]);
            $dataPrices[] = $price;
            $manager->persist($price);
        }

        $references = ['classic', 'comfy', 'rock', 'punk'];
        $images = [
            'https://img.ltwebstatic.com/images3_pi/2021/01/25/1611570961c433df8e89c9ca1ee4b7e50784b3b110_thumbnail_600x.webp',
            'https://img.ltwebstatic.com/images3_pi/2022/02/16/1644978324fc8d75b8b626829c5088b907becb4edc_thumbnail_600x.webp',
            'https://img.ltwebstatic.com/images3_pi/2021/05/25/162192337877ef491acffc88c829328cdb1b24064a_thumbnail_600x.webp',
            'https://img.ltwebstatic.com/images3_pi/2021/04/01/16172608950bbf916711c3d6c5bbc46bfaf50765b1_thumbnail_600x.webp'
        ];
        //we could set here a specific description if we want: $description = "lorem ipsum... "
        $dataRefs = [];
        for ($i = 0; $i < count($references); $i++) {
            $reference = new Reference();
            $reference
                ->setTitle($references[$i])
                ->setSlug(strtolower($references[$i]))
                ->setImage($images[$i])
                ->setPrice($faker->randomElement($dataPrices))
                ->setDescription($faker->text(200)); 
                //in case we set a defined description instead of faker: ->setDescription($description)
            $dataRefs[] = $reference;
            $manager->persist($reference);
        }

        //loop articles
        $dataArticles = [];
        for ($i = 0; $i < 20; $i++) {
        $article = new Article();
        $article->setReference($dataRefs[array_rand($dataRefs)])
            ->setColor($dataColors[array_rand($dataColors)])
            ->setSize($dataSizes[array_rand($dataSizes)])
            ->setQty(rand(0,10));
        $dataArticles[] = $article;  
        $manager->persist($article);
        }

        $manager->flush();
    }
}
