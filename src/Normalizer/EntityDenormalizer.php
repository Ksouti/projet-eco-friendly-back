<?php

namespace App\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Entity denormalizer
 */
class EntityDenormalizer implements DenormalizerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supportsDenormalization($data, $class, $format = null)
    {
        return strpos($class, 'App\\Entity\\') === 0 && (is_numeric($data));
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return $this->entityManager->find($class, $data);
    }
}
