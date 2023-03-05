<?php

namespace App\DataFixtures\Providers\advices;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdviceFixtureProvider extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $advices = [
            [
                'title' => 'Comprendre la consommation de carburant',
                'category' => 'mobilité',
                'content' => "Chaque voiture a une consommation de carburant différente. Il est donc important de savoir combien de carburant votre voiture consomme en moyenne pour pouvoir économiser de l'essence.",
            ],
            [
                'title' => 'Accélérer progressivement',
                'category' => 'mobilité',
                'content' => "Évitez d'accélérer trop brusquement, cela peut faire augmenter la consommation de carburant",
            ],
            [
                'title' => 'Éviter les accélérations inutiles',
                'category' => 'mobilité',
                'content' => "Évitez d'accélérer brusquement ou de freiner trop rapidement. Conduisez en douceur et anticipez les arrêts et les feux rouges.",
            ],
            [
                'title' => 'Éviter les vitesses trop élevées',
                'category' => 'mobilité',
                'content' => "Conduire à des vitesses trop élevées peut faire augmenter la consommation de carburant. Essayez donc de conduire à une vitesse plus modérée.",
            ],
            [
                'title' => 'Éviter les poids superflus',
                'category' => 'mobilité',
                'content' => "Le poids de votre voiture peut influencer la quantité de carburant consommée. Ainsi, en évitant de transporter des objets superflus, vous pouvez économiser de l'essence.",
            ],
            [
                'title' => 'Garder les pneus bien gonflés',
                'category' => 'mobilité',
                'content' => "Des pneus bien gonflés peuvent améliorer l'efficacité de votre voiture et ainsi économiser de l'essence.",
            ],
            [
                'title' => 'Éviter de laisser tourner le moteur',
                'category' => 'mobilité',
                'content' => "Laisser tourner le moteur de votre voiture alors qu'elle est à l'arrêt peut faire augmenter la consommation de carburant. Éteignez donc votre moteur lorsque vous êtes à l'arrêt.",
            ],
            [
                'title' => 'Garder votre voiture en bon état',
                'category' => 'mobilité',
                'content' => "Assurez-vous que votre voiture est bien entretenue, notamment en vérifiant régulièrement la pression des pneus, en changeant les filtres à air et en faisant l'entretien régulier de votre voiture.",
            ]
        ];
    }
}
