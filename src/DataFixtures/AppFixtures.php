<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Customer;
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
        // 1. Création de 12 Marques (Brands)
        for ($i = 1; $i <= 12; $i++) {
            $brand = new Brand();
            $brand->setNomBrand("Brand " . $i);
            $brand->setTelBrand("0102030405");
            $brand->setSiretBrand("123456789000" . $i);
            $brand->setImageBrand("https://picsum.photos/id/" . (10 + $i) . "/200/200");
            $brand->setSiteWebBrand("www.brand-" . $i . ".fr");
            $manager->persist($brand);

            // 2. Création de 10 clients (Customers) par marque
            for ($j = 1; $j <= 10; $j++) {
                $customer = new Customer();
                if ($i === 1 && $j === 1) {
                    $customer->setLastName("KANE");
                    $customer->setFirstName("Moustapha");
                } else {
                    $customer->setLastName("Nom-" . $i . "-" . $j);
                    $customer->setFirstName("Prenom-" . $j);
                }
                $customer->setPhone("0600000000");
                $customer->setRegistrationDate(new \DateTime());
                $customer->setBrand($brand);
                $manager->persist($customer);
            }
        }

        // 3. Création d'un compte utilisateur admin
        $user = new User();
        $user->setEmail("test@otto.fr");
        $user->setPassword($this->hasher->hashPassword($user, "password"));
        $manager->persist($user);

        $manager->flush();
    }
}
