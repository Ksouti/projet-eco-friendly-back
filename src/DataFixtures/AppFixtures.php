<?php

namespace App\DataFixtures;

use App\Entity\Advice;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    private $slugger;

    public function __construct(UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger)
    {
        $this->passwordHasher = $passwordHasher;
        $this->slugger = $slugger;
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
            $category->setSlug($categoryName);
            $manager->persist($category);
        }

        // ! Adding Users

        $passwordHasher = $this->passwordHasher;

        $roles = [
            'ROLE_AUTHOR',
            'ROLE_ADMIN',
            '',
            '',
        ];

        $avatars = [
            'avatar-bear.png',
            'avatar-bluetit.png',
            'avatar-deer.png',
            'avatar-fox.png',
            'avatar-frog.png',
            'avatar-hare.png',
            'avatar-tortoiseshell.png',
        ];

        $user = new User();
        $user->setEmail('admin@admin.com');
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user->setPassword($passwordHasher->hashPassword($user, 'admin'));
        $user->setFirstname('Adminfirstname');
        $user->setLastname('Adminlastname');
        $user->setNickname('Adminnickname');
        $user->setAvatar('https://picsum.photos/id/' . $faker->numberBetween(1, 200) . '/100/100.jpg');
        $user->setIsActive(rand(0, 1));
        $user->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
        $user->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
        $manager->persist($user);

        $user = new User();
        $user->setEmail('author@author.com');
        $user->setRoles(['ROLE_USER', 'ROLE_AUTHOR']);
        $user->setPassword($passwordHasher->hashPassword($user, 'author'));
        $user->setFirstname('Authorfirstname');
        $user->setLastname('Authorlastname');
        $user->setNickname('Authornickname');
        $user->setAvatar('https://picsum.photos/id/' . $faker->numberBetween(1, 200) . '/100/100.jpg');
        $user->setIsActive(rand(0, 1));
        $user->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
        $user->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
        $manager->persist($user);

        $user = new User();
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, 'user'));
        $user->setFirstname('Userfirstname');
        $user->setLastname('Userlastname');
        $user->setNickname('Usernickname');
        $user->setAvatar('https://picsum.photos/id/' . $faker->numberBetween(1, 200) . '/100/100.jpg');
        $user->setIsActive(rand(0, 1));
        $user->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
        $user->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
        $manager->persist($user);

        for ($index = 0; $index < 25; $index++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $user->setRoles(['ROLE_USER', $roles[$faker->numberBetween(0, count($roles) - 1)]]);
            $user->setPassword($passwordHasher->hashPassword($user, $faker->password(8, 12)));
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setNickname($faker->userName());
            $user->setAvatar('https://picsum.photos/id/' . $faker->numberBetween(1, 200) . '/100/100.jpg');
            $user->setIsActive(rand(0, 1));
            $user->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $user->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $manager->persist($user);
        }

        $manager->flush();

        // ! Catégories & Users list

        $categories = $manager->getRepository(Category::class)->findAll();

        $users = $manager->getRepository(User::class)->findAll();

        // ! Instantiation of slugger

        $slugger = $this->slugger;

        // ! Adding Articles

        $authors = array_filter($users, function ($user) {
            return in_array('ROLE_AUTHOR', $user->getRoles());
        });

        for ($index = 0; $index < 30; $index++) {
            $article = new Article();
            $article->setTitle($faker->sentence(6, true));
            $article->setContent($faker->paragraph(6, true));
            $article->setSlug($slugger->slug($article->getTitle(), '-'));
            $article->setPicture('https://picsum.photos/id/' . $faker->numberBetween(1, 200) . '/300/450.jpg');
            $article->setStatus($faker->numberBetween(0, 2));
            $article->setAuthor($authors[array_rand($authors)]);
            $article->setCategory($categories[$faker->numberBetween(0, count($categories) - 1)]);
            $article->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $article->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $manager->persist($article);
        }

        // ! Adding Advices

        $contributors = array_filter($users, function ($user) {
            return !in_array('ROLE_AUTHOR', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles());
        });

        for ($index = 0; $index < 60; $index++) {
            $advice = new Advice();
            $advice->setTitle($faker->sentence(6, true));
            $advice->setContent($faker->paragraph(6, true));
            $advice->setSlug($slugger->slug($advice->getTitle(), '-'));
            $advice->setStatus($faker->numberBetween(0, 2));
            $advice->setContributor($contributors[array_rand($contributors)]);
            $advice->setCategory($categories[$faker->numberBetween(0, count($categories) - 1)]);
            $advice->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $advice->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $manager->persist($advice);
        }

        $manager->flush();
    }
}
