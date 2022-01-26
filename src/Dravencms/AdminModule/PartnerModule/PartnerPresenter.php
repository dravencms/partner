<?php declare(strict_types = 1);

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Dravencms\AdminModule\PartnerModule;

use Dravencms\AdminModule\Components\Partner\PartnerForm\PartnerForm;
use Dravencms\AdminModule\Components\Partner\PartnerForm\PartnerFormFactory;
use Dravencms\AdminModule\Components\Partner\PartnerGrid\PartnerGrid;
use Dravencms\AdminModule\Components\Partner\PartnerGrid\PartnerGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Flash;
use Dravencms\Model\Partner\Entities\Partner;
use Dravencms\Model\Partner\Repository\PartnerRepository;

/**
 * Description of PartnerPresenter
 *
 * @author Adam Schubert
 */
class PartnerPresenter extends SecuredPresenter
{

    /** @var PartnerRepository @inject */
    public $partnerRepository;

    /** @var PartnerGridFactory @inject */
    public $partnerGridFactory;

    /** @var PartnerFormFactory @inject */
    public $partnerFormFactory;

    /** @var Partner|null */
    private $partner = null;

    /**
     * @isAllowed(partner,edit)
     */
    public function renderDefault(): void
    {
        $this->template->h1 = 'Partners';
    }

    /**
     * @isAllowed(partner,edit)
     * @param $id
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit(int $id = null): void
    {
        if ($id) {
            $partner = $this->partnerRepository->getOneById($id);

            if (!$partner) {
                $this->error();
            }

            $this->partner = $partner;

            $this->template->h1 = sprintf('Edit partner „%s“', $partner->getIdentifier());
        } else {
            $this->template->h1 = 'New partner';
        }
    }

    /**
     * @return PartnerForm
     */
    protected function createComponentFormPartner(): PartnerForm
    {
        $control = $this->partnerFormFactory->create($this->partner);
        $control->onSuccess[] = function(){
            $this->flashMessage('Partner has been successfully saved', Flash::SUCCESS);
            $this->redirect('Partner:');
        };
        return $control;
    }

    /**
     * @return PartnerGrid
     */
    public function createComponentGridPartner(): PartnerGrid
    {
        $control = $this->partnerGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('Partner has been successfully deleted', Flash::SUCCESS);
            $this->redirect('Partner:');
        };
        return $control;
    }
}
