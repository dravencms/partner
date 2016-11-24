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

namespace Dravencms\AdminModule\Components\Partner;

use Dravencms\Components\BaseGridFactory;
use App\Model\Partner\Entities\Partner;
use App\Model\Partner\Repository\PartnerRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Utils\Html;
use Salamek\Files\ImagePipe;

/**
 * Description of PartnerGrid
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class PartnerGrid extends Control
{

    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var PartnerRepository */
    private $partnerRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var ImagePipe */
    private $imagePipe;

    /**
     * @var array
     */
    public $onDelete = [];

    /**
     * ArticleGrid constructor.
     * @param PartnerRepository $partnerRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     */
    public function __construct(PartnerRepository $partnerRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager, ImagePipe $imagePipe)
    {
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->partnerRepository = $partnerRepository;
        $this->entityManager = $entityManager;
        $this->imagePipe = $imagePipe;
    }


    /**
     * @param $name
     * @return \Dravencms\Components\BaseGrid
     */
    public function createComponentGrid($name)
    {
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setModel($this->partnerRepository->getPartnerQueryBuilder());

        $grid->addColumnText('name', 'Name')
            ->setCustomRender(function ($row) use($grid){
                /** @var Partner $row */
                if ($haveImage = $row->getStructureFile()) {
                    $img = Html::el('img');
                    $img->src = $this->imagePipe->request($haveImage->getFile(), '200x');
                } else {
                    $img = '';
                }

                return $img . Html::el('br') . $row->getName();
            })
            ->setFilterText()
            ->setSuggestion();

        $grid->getColumn('name')->cellPrototype->class[] = 'center';


        $grid->addColumnBoolean('isActive', 'Active');
        $grid->addColumnBoolean('isMain', 'Is main');

        $grid->addColumnNumber('position', 'Position')
            ->setFilterNumber()
            ->setSuggestion();

        $grid->getColumn('position')->cellPrototype->class[] = 'center';

        if ($this->presenter->isAllowed('partner', 'edit')) {
            $grid->addActionHref('edit', 'Upravit')
                ->setIcon('pencil');
        }

        if ($this->presenter->isAllowed('partner', 'delete')) {
            $grid->addActionHref('delete', 'Smazat', 'delete!')
                ->setCustomHref(function($row){
                    return $this->link('delete!', $row->getId());
                })
                ->setIcon('trash-o')
                ->setConfirm(function ($row) {
                    return ['Opravdu chcete smazat partnera %s ?', $row->getName()];
                });


            $operations = ['delete' => 'Smazat'];
            $grid->setOperation($operations, [$this, 'gridOperationsHandler'])
                ->setConfirm('delete', 'Opravu chcete smazat %i partneru ?');
        }
        $grid->setExport();

        return $grid;
    }

    /**
     * @param $action
     * @param $ids
     */
    public function gridOperationsHandler($action, $ids)
    {
        switch ($action)
        {
            case 'delete':
                $this->handleDelete($ids);
                break;
        }
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function handleDelete($id)
    {
        $partners = $this->partnerRepository->getById($id);
        foreach ($partners AS $partner)
        {
            $this->entityManager->remove($partner);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/PartnerGrid.latte');
        $template->render();
    }
}
