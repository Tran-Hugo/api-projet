<?php

namespace App\DataPersister;

use App\Entity\Dependency;
use App\Repository\DependencyRepository;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

class DependencyDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private DependencyRepository $repository)
    {  
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Dependency;
    }

    public function persist($data, array $context = [])
    {
        $this->repository->persist($data);
    }

    public function remove($data, array $context = [])
    {
        $this->repository->remove($data);
    }
}