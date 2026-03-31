<?php

namespace App\DataFixtures;

use App\Entity\AutoEcole;
use App\Entity\Eleve;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Création de 12 Auto-écoles
        for ($i = 1; $i <= 12; $i++) {
            $ae = new AutoEcole();
            $ae->setNomAutoEcole("Auto-Ecole " . $i);
            $ae->setTelAutoEcole("0102030405");
            $ae->setSiretAutoEcole("123456789000" . $i);
            $ae->setImageAutoEcole("https://picsum.photos/id/" . (10 + $i) . "/200/200");
            $ae->setLienWebAutoEcole("www.ae-" . $i . ".fr");
            $manager->persist($ae);

            // 2. Création de 10 élèves par auto-école
            for ($j = 1; $j <= 10; $j++) {
                $eleve = new Eleve();
                // Un des élèves doit porter votre nom
                if ($i === 1 && $j === 1) {
                    $eleve->setNomEleve("MOUSTAPHA");
                    $eleve->setPrenomEleve("Moustapha");
                } else {
                    $eleve->setNomEleve("Nom-" . $i . "-" . $j);
                    $eleve->setPrenomEleve("Prenom-" . $j);
                }
                $eleve->setTelEleve("0600000000");
                $eleve->setDateInscription(new \DateTime());
                $eleve->setAutoEcole($ae);
                $manager->persist($eleve);
            }
        }

        // 3. Création d'un compte utilisateur pour tester le login
        $user = new User();
        $user->setEmail("test@otto.fr");
        $user->setPassword($this->hasher->hashPassword($user, "password"));
        $manager->persist($user);

        $manager->flush();
    }
}