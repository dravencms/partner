<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Partner\Repository;


use Dravencms\Model\Partner\Entities\Partner;
use Dravencms\Database\EntityManager;


class PartnerRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|Partner */
    private $partnerRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * MenuRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->partnerRepository = $entityManager->getRepository(Partner::class);
    }

    /**
     * @param $id
     * @return mixed|null|Partner
     */
    public function getOneById($id): ?Partner
    {
        return $this->partnerRepository->find($id);
    }

    /**
     * @param $id
     * @return Partner[]
     */
    public function getById($id)
    {
        return $this->partnerRepository->findBy(['id' => $id]);
    }

    /**
     * @return array|mixed
     */
    public function getActive()
    {
        return $this->partnerRepository->findBy(['isActive' => true]);
    }

    /**
     * @param $identifier
     * @param Partner|null $partnerIgnore
     * @return bool
     */
    public function isIdentifierFree($identifier, Partner $partnerIgnore = null): bool
    {
        $qb = $this->partnerRepository->createQueryBuilder('p')
            ->select('p')
            ->where('p.identifier = :identifier')
            ->setParameters([
                'identifier' => $identifier
            ]);
        if ($partnerIgnore)
        {
            $qb->andWhere('p != :partnerIgnore')
                ->setParameter('partnerIgnore', $partnerIgnore);
        }
        $query = $qb->getQuery();
        return (is_null($query->getOneOrNullResult()));
    }

    /**
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function getPartnerQueryBuilder()
    {
        $qb = $this->partnerRepository->createQueryBuilder('p')
            ->select('p');
        return $qb;
    }

    /**
     * @param integer $id
     * @param bool $isActive
     * @return mixed|null|Partner
     */
    public function getOneByIdAndActive($id, $isActive = true): ?Partner
    {
        return $this->partnerRepository->findOneBy(['id' => $id, 'isActive' => $isActive]);
    }

    /**
     * @param bool $isActive
     * @return Partner[]
     */
    public function getAllByActive($isActive = true)
    {
        return $this->partnerRepository->findBy(['isActive' => $isActive], ['isMain' => 'DESC']);
    }

    /**
     * @return Partner[]
     */
    public function getMain(): ?Partner
    {
        return $this->partnerRepository->findOneBy(['isMain' => true]);
    }

    /**
     * @return Partner[]
     */
    public function getSecondary()
    {
        return $this->partnerRepository->findBy(['isMain' => false]);
    }

    /**
     * @param array $parameters
     * @return Partner|null
     */
    public function getOneByParameters(array $parameters = []): ?Partner
    {
        return $this->partnerRepository->findOneBy($parameters);
    }
}
