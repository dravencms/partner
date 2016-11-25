<?php

namespace Dravencms\Partner\Script;

use Dravencms\Model\Admin\Entities\Menu;
use Dravencms\Model\Admin\Repository\MenuRepository;
use Dravencms\Model\User\Entities\AclOperation;
use Dravencms\Model\User\Entities\AclResource;
use Dravencms\Packager\IPackage;
use Dravencms\Packager\IScript;
use Kdyby\Doctrine\EntityManager;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class PostInstall implements IScript
{
    private $menuRepository;
    private $entityManager;

    public function __construct(MenuRepository $menuRepository, EntityManager $entityManager)
    {
        $this->menuRepository = $menuRepository;
        $this->entityManager = $entityManager;
    }

    public function run(IPackage $package)
    {
        $aclResource = new AclResource('partner', 'Partner');

        $this->entityManager->persist($aclResource);

        $aclOperationEdit = new AclOperation($aclResource, 'edit', 'Allows editation of partner');
        $this->entityManager->persist($aclOperationEdit);
        $aclOperationDelete = new AclOperation($aclResource, 'delete', 'Allows deletion of partner');
        $this->entityManager->persist($aclOperationDelete);

        $adminMenu = new Menu('Partner', ':Admin:Partner:Partner', 'fa-diamond', $aclOperationEdit);

        $foundRoot = $this->menuRepository->getOneByName('Site items');

        if ($foundRoot)
        {
            $this->menuRepository->getMenuRepository()->persistAsLastChildOf($adminMenu, $foundRoot);
        }
        else
        {
            $this->entityManager->persist($adminMenu);
        }

        $this->entityManager->flush();

    }
}