<?php

namespace App\DataFixtures;

use App\Entity\Grossiste;
use App\Entity\Medecin;
use App\Entity\Medicament;
use App\Entity\Pharmacie;
use App\Entity\User;
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

        //création des médecins
        $specialite = ['MI','CARDIO','DIABETO/ENDO','NEPHRO','MG'];
        $secteurs = ['privé', 'publique'];
        for($i = 1; $i <= 3 ; $i++){
            $doc = new Medecin();
            $doc->setNom("Médecin");
            $doc->setPrenom( $i);
            $doc->setCommune("Lyon $i");
            $doc->setDepartement("Rhone");
            $doc->setSecteur($secteurs[array_rand($secteurs)]);
            $doc->setSpecialite($specialite[array_rand($specialite)]);

            $manager->persist($doc);
        }


        // medocs
        for($i = 1; $i <= 5; $i++) {
            $medoc = new Medicament();
            $medoc->setNom("Medoc $i");
            $manager->persist($medoc);
        }

        $user = new User();
        $user->setNom('Bouazabia');
        $user->setPrenom('Mehdi');
        $user->setMail("mehdi@mail.fr");
        $user->setMdp('mehdi');

        $manager->persist($user);

        $manager->flush();
    }
}
