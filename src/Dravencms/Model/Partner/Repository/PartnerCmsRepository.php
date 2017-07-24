<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Partner\Repository;

use Dravencms\Locale\TLocalizedRepository;
use Dravencms\Model\Partner\Entities\Partner;
use Gedmo\Translatable\TranslatableListener;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Salamek\Cms\CmsActionOption;
use Salamek\Cms\ICmsActionOption;
use Salamek\Cms\ICmsComponentRepository;
use Salamek\Cms\Models\ILocale;

class PartnerCmsRepository implements ICmsComponentRepository
{
    /** @var PartnerRepository */
    private $partnerRepository;

    public function __construct(PartnerRepository $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
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
                foreach ($this->partnerRepository->getActive() AS $partner) {
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
    public function getActionOption($componentAction, array $parameters)
    {
        //$found = $this->partnerRepository->findTranslatedOneBy($this->partnerRepository, $locale, $parameters + ['isActive' => true]);
        $found = null; //!FIXME
        if ($found)
        {
            return new CmsActionOption(($found->getLead() ? $found->getLead() . ' ' : '') . $found->getName(), $parameters);
        }

        return null;
    }
}
