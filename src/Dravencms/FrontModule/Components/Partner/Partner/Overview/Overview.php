<?php declare(strict_types = 1);

namespace Dravencms\FrontModule\Components\Partner\Partner\Overview;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Partner\Repository\PartnerRepository;
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
        $this->cmsActionOption = $cmsActionOption;
        $this->partnerRepository = $partnerRepository;
        $this->baseFormFactory = $baseFormFactory;
    }

    public function render(): void
    {
        $template = $this->template;

        $template->main = $this->partnerRepository->getMain();

        $template->overview = $this->partnerRepository->getSecondary();
        
        $template->setFile(__DIR__.'/overview.latte');
        $template->render();
    }
}
