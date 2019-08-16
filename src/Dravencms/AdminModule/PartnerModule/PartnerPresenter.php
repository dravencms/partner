<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Dravencms\AdminModule\PartnerModule;

use Dravencms\AdminModule\Components\Partner\PartnerForm\PartnerFormFactory;
use Dravencms\AdminModule\Components\Partner\PartnerGrid\PartnerGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
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
    public function renderDefault()
    {
        $this->template->h1 = 'Partners';
    }

    /**
     * @isAllowed(partner,edit)
     * @param $id
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit($id)
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
     * @return \AdminModule\Components\Partner\PartnerForm
     */
    protected function createComponentFormPartner()
    {
        $control = $this->partnerFormFactory->create($this->partner);
        $control->onSuccess[] = function(){
            $this->flashMessage('Partner has been successfully saved', 'alert-success');
            $this->redirect('Partner:');
        };
        return $control;
    }

    /**
     * @return \AdminModule\Components\Partner\PartnerGrid
     */
    public function createComponentGridPartner()
    {
        $control = $this->partnerGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('Partner has been successfully deleted', 'alert-success');
            $this->redirect('Partner:');
        };
        return $control;
    }
}
