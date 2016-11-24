<?php
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

use Dravencms\Components\BaseFormFactory;
use Dravencms\Model\Locale\Repository\LocaleRepository;
use Dravencms\File\File;
use Dravencms\Model\Partner\Entities\Partner;
use Dravencms\Model\Partner\Repository\PartnerRepository;
use Dravencms\Model\File\Repository\StructureFileRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

/**
 * Description of PartnerForm
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class PartnerForm extends Control
{
    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var EntityManager */
    private $entityManager;

    /** @var PartnerRepository */
    private $partnerRepository;

    /** @var StructureFileRepository */
    private $structureFileRepository;

    /** @var LocaleRepository */
    private $localeRepository;

    /** @var File */
    private $file;

    /** @var Partner|null */
    private $partner = null;

    /** @var array */
    public $onSuccess = [];

    /**
     * ArticleForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param EntityManager $entityManager
     * @param PartnerRepository $partnerRepository
     * @param StructureFileRepository $structureFileRepository
     * @param LocaleRepository $localeRepository
     * @param File $file
     * @param Partner|null $partner
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        EntityManager $entityManager,
        PartnerRepository $partnerRepository,
        StructureFileRepository $structureFileRepository,
        LocaleRepository $localeRepository,
        File $file,
        Partner $partner = null
    ) {
        parent::__construct();

        $this->partner = $partner;

        $this->baseFormFactory = $baseFormFactory;
        $this->entityManager = $entityManager;
        $this->partnerRepository = $partnerRepository;
        $this->structureFileRepository = $structureFileRepository;
        $this->localeRepository = $localeRepository;
        $this->file = $file;


        if ($this->partner) {

            $defaults = [
                'name' => $this->partner->getName(),
                'url' => $this->partner->getUrl(),
                'description' => $this->partner->getDescription(),
                'structureFile' => ($this->partner->getStructureFile() ? $this->partner->getStructureFile()->getId() : null),
                'position' => $this->partner->getPosition(),
                'isActive' => $this->partner->isActive(),
                'isMain' => $this->partner->isMain()
            ];

            $repository = $this->entityManager->getRepository('Gedmo\Translatable\Entity\Translation');
            $defaults += $repository->findTranslations($this->partner);

            $defaultLocale = $this->localeRepository->getDefault();
            if ($defaultLocale) {
                $defaults[$defaultLocale->getLanguageCode()]['name'] = $this->partner->getName();
                $defaults[$defaultLocale->getLanguageCode()]['url'] = $this->partner->getUrl();
                $defaults[$defaultLocale->getLanguageCode()]['description'] = $this->partner->getDescription();
            }

        }
        else{
            $defaults = [
                'isActive' => true,
                'isShowName' => true,
                'isAutoDetectTags' => true
            ];
        }

        $this['form']->setDefaults($defaults);
    }

    protected function createComponentForm()
    {
        $form = $this->baseFormFactory->create();

        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            $container = $form->addContainer($activeLocale->getLanguageCode());

            $container->addText('name')
                ->setRequired('Please enter partner name.')
                ->addRule(Form::MAX_LENGTH, 'Partner name is too long.', 255);

            $container->addText('url');

            $container->addTextarea('description');
        }

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
     */
    public function editFormValidate(Form $form)
    {
        $values = $form->getValues();
        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            if (!$this->partnerRepository->isNameFree($values->{$activeLocale->getLanguageCode()}->name, $activeLocale, $this->partner)) {
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
    public function editFormSucceeded(Form $form)
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
            /*$partner->setName($values->name);
            $partner->setUrl($values->url);
            $partner->setDescription($values->description);*/
            $partner->setStructureFile($structureFile);
            $partner->setIsActive($values->isActive);
            $partner->setIsMain($values->isMain);
            $partner->setPosition($values->position);
        } else {
            $defaultLocale = $this->localeRepository->getDefault();
            $partner = new Partner($values->{$defaultLocale->getLanguageCode()}->name, $values->{$defaultLocale->getLanguageCode()}->url, $values->{$defaultLocale->getLanguageCode()}->description, $values->isActive, $values->isMain, $structureFile);
        }

        $repository = $this->entityManager->getRepository('Gedmo\\Translatable\\Entity\\Translation');

        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            $repository->translate($partner, 'name', $activeLocale->getLanguageCode(), $values->{$activeLocale->getLanguageCode()}->name)
                ->translate($partner, 'url', $activeLocale->getLanguageCode(), $values->{$activeLocale->getLanguageCode()}->url)
                ->translate($partner, 'description', $activeLocale->getLanguageCode(), $values->{$activeLocale->getLanguageCode()}->description);
        }

        $this->entityManager->persist($partner);

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