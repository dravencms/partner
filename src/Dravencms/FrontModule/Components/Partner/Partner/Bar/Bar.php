<?php

namespace Dravencms\FrontModule\Components\Partner\Partner\Bar;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Partner\Repository\PartnerRepository;
use Salamek\Cms\ICmsActionOption;

class Bar extends BaseControl
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
        
        $template->overview = $this->partnerRepository->getAllByActive();
        
        $template->setFile(__DIR__.'/bar.latte');
        $template->render();
    }
}
