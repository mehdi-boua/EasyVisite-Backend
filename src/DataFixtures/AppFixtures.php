<?php

namespace App\DataFixtures;

use App\Entity\Grossiste;
use App\Entity\Pharmacie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // création de la liste des grossistes
        $listeGrossistes = [];
        for($i = 0; $i < 5; $i++) {
            $grossiste = new Grossiste();
            $grossiste->setNom("Grossiste $i");
            $manager->persist($grossiste);

            $listeGrossistes[] = $grossiste;
        }

        //création de la liste des pharmacies
        for($i = 1; $i <= 7 ; $i++){
            $pharma = new Pharmacie();
            $pharma->setOfficine("Pharmacie $i");
            $pharma->setCommune("Lyon $i");
            $pharma->setDepartement("Rhone");
            $pharma->addListeGrossiste($listeGrossistes[array_rand($listeGrossistes)]);
            $pharma->addListeGrossiste($listeGrossistes[array_rand($listeGrossistes)]);

            $manager->persist($pharma);
        }


        $manager->flush();
    }
}
