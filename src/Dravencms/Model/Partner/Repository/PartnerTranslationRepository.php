<?php declare(strict_types = 1);
/**
 * Copyright (C) 2019 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Partner\Repository;

use Dravencms\Model\Partner\Entities\Partner;
use Dravencms\Model\Partner\Entities\PartnerTranslation;
use Dravencms\Database\EntityManager;

use Dravencms\Model\Locale\Entities\ILocale;

class PartnerTranslationRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|PartnerTranslation */
    private $partnerTranslationRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * MenuRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->partnerTranslationRepository = $entityManager->getRepository(PartnerTranslation::class);
    }

    /**
     * @param $name
     * @param ILocale $locale
     * @param Partner|null $partnerIgnore
     * @return bool
     */
    public function isNameFree($name, ILocale $locale, Partner $partnerIgnore = null): bool
    {
        $qb = $this->partnerTranslationRepository->createQueryBuilder('pt')
            ->select('pt')
            ->join('pt.partner', 'p')
            ->where('pt.name = :name')
            ->andWhere('pt.locale = :locale')
            ->setParameters([
                'name' => $name,
                'locale' => $locale
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
     * @param Partner $partner
     * @param ILocale $locale
     * @return PartnerTranslation|null
     */
    public function getTranslation(Partner $partner, ILocale $locale): ?PartnerTranslation
    {
        $qb = $this->partnerTranslationRepository->createQueryBuilder('pt')
            ->select('pt')
            ->where('pt.locale = :locale')
            ->andWhere('pt.partner = :partner')
            ->setParameters([
                'partner' => $partner,
                'locale' => $locale
            ]);
        return $qb->getQuery()->getOneOrNullResult();
    }
}