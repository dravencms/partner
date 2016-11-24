<?php

namespace Dravencms\FrontModule\Components\Partner\Partner;

use Dravencms\Components\BaseControl;
use Dravencms\Components\BaseFormFactory;
use App\Model\Partner\Repository\PartnerRepository;
use Salamek\Cms\ICmsActionOption;

class Overview extends BaseControl
{
    /** @var PartnerRepository */
    private $partnerRepository;

    /** @var ICmsActionOption */
    private $cmsActionOption;

    /** @var BaseFormFactory */
    private $baseFormFactory;

    /**
     * Overview constructor.
     * @param ICmsActionOption $cmsActionOption
     * @param PartnerRepository $partnerRepository
     * @param BaseFormFactory $baseFormFactory
     */
    public function __construct(ICmsActionOption $cmsActionOption, PartnerRepository $partnerRepository, BaseFormFactory $baseFormFactory)
    {
        parent::__construct();
        $this->cmsActionOption = $cmsActionOption;
        $this->partnerRepository = $partnerRepository;
        $this->baseFormFactory = $baseFormFactory;
    }

    public function render()
    {
        $template = $this->template;

        $template->main = $this->partnerRepository->getMain();

        $template->overview = $this->partnerRepository->getSecondary();
        
        $template->setFile(__DIR__.'/overview.latte');
        $template->render();
    }
}
