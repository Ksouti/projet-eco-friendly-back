<?php

namespace App\DataFixtures;

use App\Entity\Advice;
use App\Entity\Article;
use App\Entity\Avatar;
use App\Entity\Category;
use App\Entity\User;
use App\Service\CodeGeneratorService;
use App\Service\SluggerService;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    private $slugger;
    private $codeGenerator;

    public function __construct(SluggerService $slugger, UserPasswordHasherInterface $passwordHasher, CodeGeneratorService $codeGenerator)
    {
        $this->passwordHasher = $passwordHasher;
        $this->slugger = $slugger;
        $this->codeGenerator = $codeGenerator;
    }
    public function load(ObjectManager $manager): void
    {
        // ! Instantiation of faker

        $faker = Faker\Factory::create();

        // ! Adding Categories

        $categories = [
            'Mobilité',
            'Maison',
            'Santé',
            'Energie',
        ];

        foreach ($categories as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $category->setTagline($faker->sentence(6, true));
            $category->setSlug($this->slugger->slugify($categoryName));
            $category->setCreatedAt(new DateTimeImmutable());
            $category->setIsActive(true);
            $manager->persist($category);
        }

        $manager->flush();

        echo 'Categories added !' . PHP_EOL;

        // ! Adding Users

        $passwordHasher = $this->passwordHasher;

        $avatars = [
            'Ours' => 'http://vps-79770841.vps.ovh.net//assets/img/avatars/ours.png',
            'Mésange bleu' => 'http://vps-79770841.vps.ovh.net//assets/img/avatars/mesange-bleue.png',
            'Biche' => 'http://vps-79770841.vps.ovh.net//assets/img/avatars/biche.png',
            'Grenouille' => 'http://vps-79770841.vps.ovh.net//assets/img/avatars/grenouille.png',
            'Renard' => 'http://vps-79770841.vps.ovh.net//assets/img/avatars/renard.png',
            'Lièvre' => 'http://vps-79770841.vps.ovh.net//assets/img/avatars/lievre.png',
            'Papillon' => 'http://vps-79770841.vps.ovh.net//assets/img/avatars/papillon.png',
        ];

        $user = new User();
        $user->setEmail('admin@admin.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($passwordHasher->hashPassword($user, 'admin'));
        $user->setFirstname('Admin');
        $user->setLastname('Istrateur');
        $user->setNickname('NoSysAdmin');
        $user->setCode($this->codeGenerator->codeGen());
        $user->setAvatar('http://vps-79770841.vps.ovh.net//uploads/users/nosysadmin63ff3ea8de28a.png');
        $user->setIsActive(1);
        $user->setIsVerified(1);
        $user->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
        $user->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
        $manager->persist($user);

        $user = new User();
        $user->setEmail('author@author.com');
        $user->setRoles(['ROLE_AUTHOR']);
        $user->setPassword($passwordHasher->hashPassword($user, 'author'));
        $user->setFirstname('Milan');
        $user->setLastname('Kundera');
        $user->setNickname('MilKuKu');
        $user->setCode($this->codeGenerator->codeGen());
        $user->setAvatar('http://vps-79770841.vps.ovh.net//uploads/users/chanda-bec6400ed2e2a75b.jpg');
        $user->setIsActive(1);
        $user->setIsVerified(1);
        $user->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
        $user->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
        $manager->persist($user);

        $user = new User();
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, 'user'));
        $user->setFirstname('Jeff');
        $user->setLastname('Lebowski');
        $user->setNickname('The_Dude');
        $user->setCode($this->codeGenerator->codeGen());
        $user->setAvatar('http://vps-79770841.vps.ovh.net//uploads/users/martina-br6400ec427207b.png');
        $user->setIsActive(1);
        $user->setIsVerified(1);
        $user->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
        $user->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
        $manager->persist($user);

        for ($index = 0; $index < 8; $index++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $user->setRoles(['ROLE_AUTHOR']);
            $user->setPassword($faker->password(8, 12));
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setNickname($faker->userName());
            $user->setCode($this->codeGenerator->codeGen());
            $user->setAvatar($avatars[array_rand($avatars)]);
            $user->setIsActive(1);
            $user->setIsVerified(1);
            $user->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $user->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $manager->persist($user);
        }

        for ($index = 0; $index < 35; $index++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($faker->password(8, 12));
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setNickname($faker->userName());
            $user->setCode($this->codeGenerator->codeGen());
            $user->setAvatar($avatars[array_rand($avatars)]);
            $user->setIsActive(1);
            $user->setIsVerified(1);
            $user->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $user->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $manager->persist($user);
        }

        $manager->flush();

        echo 'Users added !' . PHP_EOL;

        // ! Adding default avatars

        foreach ($avatars as $name => $url) {
            $avatar = new Avatar();
            $avatar->setName($name);
            $avatar->setPicture($url);
            $avatar->setIsActive(1);
            $avatar->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $avatar->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $manager->persist($avatar);
        }

        $manager->flush();

        echo 'Avatars added !' . PHP_EOL;

        // ! Catégories & Users list

        $categories = $manager->getRepository(Category::class)->findAll();

        $users = $manager->getRepository(User::class)->findAll();

        // ! Adding Articles

        $authors = array_filter($users, function ($user) {
            return in_array('ROLE_AUTHOR', $user->getRoles());
        });

        for ($index = 0; $index < 50; $index++) {
            $article = new Article();
            $article->setTitle($faker->sentence(6, true));
            $article->setContent("<p><em>" . $faker->paragraph(6, true) . "</em></p><h3>" . $faker->sentence(6, true) . "</h3><p>" . $faker->paragraph(6, true) . "</p><p><b>" . $faker->paragraph(6, true) . "</b></p><p>" . $faker->paragraph(6, true) . "</p><h4>" . $faker->sentence(6, true) . "</h4><ul><li>" . $faker->sentence(6, true) . "</li><li>" . $faker->sentence(6, true) . "</li><li>" . $faker->sentence(6, true) . "</li></ul><h3>" . $faker->sentence(6, true) . "</h3><p>" . $faker->paragraph(6, true) . "</p><p>" . $faker->paragraph(6, true) . "</p><p>" . $faker->paragraph(6, true) . "</p><p><em>" . $faker->paragraph(6, true) . "</em></p>");
            $article->setSlug($this->slugger->slugify($article->getTitle()));
            $article->setPicture('https://picsum.photos/id/' . $faker->numberBetween(1, 200) . '/1000/1000.jpg');
            $article->setStatus($faker->numberBetween(0, 2));
            $article->setAuthor($authors[array_rand($authors)]);
            $article->setCategory($categories[$faker->numberBetween(0, count($categories) - 1)]);
            $article->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $article->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $manager->persist($article);
        }

        $manager->flush();

        for ($index = 0; $index < 50; $index++) {
            $article = new Article();
            $article->setTitle($faker->sentence(6, true));
            $article->setContent("<p><em>" . $faker->paragraph(6, true) . "</em></p><h3>" . $faker->sentence(6, true) . "</h3><p>" . $faker->paragraph(6, true) . "</p><p><b>" . $faker->paragraph(6, true) . "</b></p><p>" . $faker->paragraph(6, true) . "</p><h4>" . $faker->sentence(6, true) . "</h4><ul><li>" . $faker->sentence(6, true) . "</li><li>" . $faker->sentence(6, true) . "</li><li>" . $faker->sentence(6, true) . "</li></ul><h3>" . $faker->sentence(6, true) . "</h3><p>" . $faker->paragraph(6, true) . "</p><p>" . $faker->paragraph(6, true) . "</p><p>" . $faker->paragraph(6, true) . "</p><p><em>" . $faker->paragraph(6, true) . "</em></p>");
            $article->setSlug($this->slugger->slugify($article->getTitle()));
            $article->setPicture('https://picsum.photos/id/' . $faker->numberBetween(1, 200) . '/1000/1000.jpg');
            $article->setStatus(1);
            $article->setAuthor($authors[array_rand($authors)]);
            $article->setCategory($categories[$faker->numberBetween(0, count($categories) - 1)]);
            $article->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $article->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $manager->persist($article);
        }

        $manager->flush();

        echo 'Articles added !' . PHP_EOL;

        // ! Adding Advices

        $contributors = array_filter($users, function ($user) {
            return !in_array('ROLE_AUTHOR', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles());
        });

        for ($index = 0; $index < 70; $index++) {
            $advice = new Advice();
            $advice->setTitle($faker->sentence(6, true));
            $advice->setContent("<p>" . $faker->paragraph(6, true) . "</p><p>" . $faker->paragraph(6, true) . "</p><p>" . $faker->paragraph(6, true) . "</p>");
            $advice->setSlug($this->slugger->slugify($advice->getTitle()));
            $advice->setStatus($faker->numberBetween(0, 2));
            $advice->setContributor($contributors[array_rand($contributors)]);
            $advice->setCategory($categories[$faker->numberBetween(0, count($categories) - 1)]);
            $advice->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $advice->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $manager->persist($advice);
        }

        $manager->flush();

        for ($index = 0; $index < 70; $index++) {
            $advice = new Advice();
            $advice->setTitle($faker->sentence(6, true));
            $advice->setContent("<p>" . $faker->paragraph(6, true) . "</p><p>" . $faker->paragraph(6, true) . "</p><p>" . $faker->paragraph(6, true) . "</p>");
            $advice->setSlug($this->slugger->slugify($advice->getTitle()));
            $advice->setStatus(1);
            $advice->setContributor($contributors[array_rand($contributors)]);
            $advice->setCategory($categories[$faker->numberBetween(0, count($categories) - 1)]);
            $advice->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $advice->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $manager->persist($advice);
        }

        $manager->flush();

        echo 'Advices added !' . PHP_EOL;

        echo 'All done !' . PHP_EOL;
    }
}
