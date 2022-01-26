<?php declare(strict_types = 1);
/*
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace Dravencms\AdminModule\Components\Partner\PartnerForm;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Locale\Repository\LocaleRepository;
use Dravencms\Locale\CurrentLocaleResolver;
use Dravencms\File\File;
use Dravencms\Model\Partner\Entities\Partner;
use Dravencms\Model\Partner\Entities\PartnerTranslation;
use Dravencms\Model\Partner\Repository\PartnerRepository;
use Dravencms\Model\File\Repository\StructureFileRepository;
use Dravencms\Model\Partner\Repository\PartnerTranslationRepository;
use Dravencms\Database\EntityManager;
use Dravencms\Components\BaseForm\Form;

/**
 * Description of PartnerForm
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class PartnerForm extends BaseControl
{
    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var EntityManager */
    private $entityManager;

    /** @var PartnerRepository */
    private $partnerRepository;

    /** @var PartnerTranslationRepository */
    private $partnerTranslationRepository;

    /** @var StructureFileRepository */
    private $structureFileRepository;

    /** @var LocaleRepository */
    private $localeRepository;

    /** @var File */
    private $file;

    /** @var \Dravencms\Model\Locale\Entities\Locale|null */
    private $currentLocale;

    /** @var Partner|null */
    private $partner = null;

    /** @var array */
    public $onSuccess = [];

    /**
     * PartnerForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param EntityManager $entityManager
     * @param PartnerRepository $partnerRepository
     * @param PartnerTranslationRepository $partnerTranslationRepository
     * @param StructureFileRepository $structureFileRepository
     * @param LocaleRepository $localeRepository
     * @param CurrentLocaleResolver $currentLocaleResolver
     * @param File $file
     * @param Partner|null $partner
     * @throws \Exception
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        EntityManager $entityManager,
        PartnerRepository $partnerRepository,
        PartnerTranslationRepository $partnerTranslationRepository,
        StructureFileRepository $structureFileRepository,
        LocaleRepository $localeRepository,
        CurrentLocaleResolver $currentLocaleResolver,
        File $file,
        Partner $partner = null
    ) {
        $this->partner = $partner;

        $this->baseFormFactory = $baseFormFactory;
        $this->entityManager = $entityManager;
        $this->partnerRepository = $partnerRepository;
        $this->partnerTranslationRepository = $partnerTranslationRepository;
        $this->structureFileRepository = $structureFileRepository;
        $this->currentLocale = $currentLocaleResolver->getCurrentLocale();
        $this->localeRepository = $localeRepository;
        $this->file = $file;


        if ($this->partner) {

            $defaults = [
                'identifier' => $this->partner->getIdentifier(),
                'url' => $this->partner->getUrl(),
                'structureFile' => ($this->partner->getStructureFile() ? $this->partner->getStructureFile()->getId() : null),
                'position' => $this->partner->getPosition(),
                'isActive' => $this->partner->isActive(),
                'isMain' => $this->partner->isMain()
            ];

            foreach ($this->partner->getTranslations() AS $translation)
            {
                $defaults[$translation->getLocale()->getLanguageCode()]['name'] = $translation->getName();
                $defaults[$translation->getLocale()->getLanguageCode()]['description'] = $translation->getDescription();
            }
        }
        else{
            $defaults = [
                'isActive' => true
            ];
        }

        $this['form']->setDefaults($defaults);
    }

    /**
     * @return Form
     */
    protected function createComponentForm(): Form
    {
        $form = $this->baseFormFactory->create();

        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            $container = $form->addContainer($activeLocale->getLanguageCode());
            $container->addText('name')
                ->setRequired('Please enter partner name.')
                ->addRule(Form::MAX_LENGTH, 'Partner name is too long.', 255);
            $container->addTextArea('description');
        }

        $form->addText('identifier')
            ->setRequired('Please enter identifier');

        $form->addText('url');

        $form->addText('structureFile');

        $form->addText('position')
            ->setDisabled((is_null($this->partner)));

        $form->addCheckbox('isActive');
        $form->addCheckbox('isMain');

        $form->addSubmit('send');

        $form->onValidate[] = [$this, 'editFormValidate'];
        $form->onSuccess[] = [$this, 'editFormSucceeded'];

        return $form;
    }

    /**
     * @param Form $form
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function editFormValidate(Form $form): void
    {
        $values = $form->getValues();

        if (!$this->partnerRepository->isIdentifierFree($values->identifier, $this->partner)) {
            $form->addError('Tento identifier je již zabrán.');
        }


        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            if (!$this->partnerTranslationRepository->isNameFree($values->{$activeLocale->getLanguageCode()}->name, $activeLocale, $this->partner)) {
                $form->addError('Tento název je již zabrán.');
            }
        }

        if (!$this->presenter->isAllowed('partner', 'edit')) {
            $form->addError('Nemáte oprávění editovat article.');
        }
    }

    /**
     * @param Form $form
     * @throws \Exception
     */
    public function editFormSucceeded(Form $form): void
    {
        $values = $form->getValues();

        if ($values->structureFile)
        {
            $structureFile = $this->structureFileRepository->getOneById($values->structureFile);
        }
        else
        {
            $structureFile = null;
        }

        if ($this->partner) {
            $partner = $this->partner;
            $partner->setIdentifier($values->identifier);
            $partner->setStructureFile($structureFile);
            $partner->setIsActive($values->isActive);
            $partner->setIsMain($values->isMain);
            $partner->setPosition($values->position);
            $partner->setUrl($values->url);
        } else {
            $partner = new Partner(
                $values->identifier,
                $values->url,
                $values->isActive,
                $values->isMain,
                $structureFile
            );
        }

        $this->entityManager->persist($partner);

        $this->entityManager->flush();

        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            if ($partnerTranslation = $this->partnerTranslationRepository->getTranslation($partner, $activeLocale))
            {
                $partnerTranslation->setName($values->{$activeLocale->getLanguageCode()}->name);
                $partnerTranslation->setDescription($values->{$activeLocale->getLanguageCode()}->description);
            }
            else
            {
                $partnerTranslation = new PartnerTranslation(
                    $partner,
                    $activeLocale,
                    $values->{$activeLocale->getLanguageCode()}->name,
                    $values->{$activeLocale->getLanguageCode()}->description
                );
            }
            $this->entityManager->persist($partnerTranslation);
        }
        $this->entityManager->flush();

        $this->onSuccess();
    }

    public function render()
    {
        $template = $this->template;
        $template->fileSelectorPath = $this->file->getFileSelectorPath();
        $template->activeLocales = $this->localeRepository->getActive();
        $template->setFile(__DIR__ . '/PartnerForm.latte');
        $template->render();
    }
}
