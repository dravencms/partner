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

namespace Dravencms\AdminModule\Components\Partner\PartnerGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Model\Partner\Entities\Partner;
use Dravencms\Model\Partner\Repository\PartnerRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\Html;
use Salamek\Files\ImagePipe;

/**
 * Description of PartnerGrid
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class PartnerGrid extends BaseControl
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
     * @return \Dravencms\Components\BaseGrid\BaseGrid
     */
    public function createComponentGrid($name)
    {
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setDataSource($this->partnerRepository->getPartnerQueryBuilder());

        $grid->addColumnText('name', 'Name')
            ->setAlign('center')
            ->setRenderer(function ($row) use($grid){
                /** @var Partner $row */
                if ($haveImage = $row->getStructureFile()) {
                    $img = Html::el('img');
                    $img->src = $this->imagePipe->request($haveImage->getFile(), '200x');
                } else {
                    $img = '';
                }

                $container = Html::el('div');
                $container->addHtml($img);
                $container->addHtml('<br>');
                $container->addText($row->getIdentifier());

                return $container;
            })
            ->setFilterText();


        $grid->addColumnBoolean('isActive', 'Active');
        $grid->addColumnBoolean('isMain', 'Is main');

        $grid->addColumnPosition('position', 'Position', 'up!', 'down!');

        if ($this->presenter->isAllowed('partner', 'edit')) {
            $grid->addAction('edit', '')
                ->setIcon('pencil')
                ->setTitle('Upravit')
                ->setClass('btn btn-xs btn-primary');
        }

        if ($this->presenter->isAllowed('partner', 'delete')) {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('Smazat')
                ->setClass('btn btn-xs btn-danger ajax')
                ->setConfirm('Do you really want to delete row %s?', 'identifier');
            $grid->addGroupAction('Smazat')->onSelect[] = [$this, 'handleDelete'];
        }

        $grid->addExportCsvFiltered('Csv export (filtered)', 'acl_resource_filtered.csv')
            ->setTitle('Csv export (filtered)');
        $grid->addExportCsv('Csv export', 'acl_resource_all.csv')
            ->setTitle('Csv export');

        return $grid;
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

    public function handleUp($id)
    {
        $menuItem = $this->partnerRepository->getOneById($id);
        $this->partnerRepository->moveUp($menuItem, 1);
    }

    public function handleDown($id)
    {
        $menuItem = $this->partnerRepository->getOneById($id);
        $this->partnerRepository->moveDown($menuItem, 1);
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/PartnerGrid.latte');
        $template->render();
    }
}
