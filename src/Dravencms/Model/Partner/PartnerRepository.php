<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace App\Model\Partner\Repository;

use App\Model\BaseRepository;
use App\Model\Partner\Entities\Partner;
use Gedmo\Translatable\TranslatableListener;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Salamek\Cms\CmsActionOption;
use Salamek\Cms\ICmsActionOption;
use Salamek\Cms\ICmsComponentRepository;
use Salamek\Cms\Models\ILocale;

class PartnerRepository extends BaseRepository implements ICmsComponentRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
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
    public function getOneById($id)
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
     * @param $name
     * @param ILocale $locale
     * @param Partner|null $partnerIgnore
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isNameFree($name, ILocale $locale, Partner $partnerIgnore = null)
    {
        $qb = $this->partnerRepository->createQueryBuilder('a')
            ->select('a')
            ->where('a.name = :name')
            ->setParameters([
                'name' => $name
            ]);

        if ($partnerIgnore)
        {
            $qb->andWhere('a != :partnerIgnore')
                ->setParameter('partnerIgnore', $partnerIgnore);
        }

        $query = $qb->getQuery();
        $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale->getLanguageCode());
        
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
    public function getOneByIdAndActive($id, $isActive = true)
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
     * @return mixed|null|Partner
     */
    public function getMain()
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
     * @param bool $isActive
     * @param array $parameters
     * @return Partner
     * @deprecated
     */
    public function getOneByActiveAndParameters($isActive = true, array $parameters = [])
    {
        $parameters['isActive'] = $isActive;
        return $this->partnerRepository->findOneBy($parameters);
    }

    /**
     * @param string $componentAction
     * @return ICmsActionOption[]
     */
    public function getActionOptions($componentAction)
    {
        switch ($componentAction)
        {
            case 'Main':
                $return = [];
                /** @var Partner $partner */
                foreach ($this->partnerRepository->findBy(['isActive' => true]) AS $partner) {
                    $return[] = new CmsActionOption($partner->getName(), ['id' => $partner->getId()]);
                }
                break;

            case 'Overview':
            case 'Bar':
                return null;
                break;

            default:
                return false;
                break;
        }
        

        return $return;
    }

    /**
     * @param string $componentAction
     * @param array $parameters
     * @param ILocale $locale
     * @return null|CmsActionOption
     */
    public function getActionOption($componentAction, array $parameters, ILocale $locale)
    {
        $found = $this->findTranslatedOneBy($this->partnerRepository, $locale, $parameters + ['isActive' => true]);

        if ($found)
        {
            return new CmsActionOption(($found->getLead() ? $found->getLead() . ' ' : '') . $found->getName(), $parameters);
        }

        return null;
    }
}