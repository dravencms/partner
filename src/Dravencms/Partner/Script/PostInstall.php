<?php

namespace Dravencms\Partner\Script;

use Dravencms\Model\Admin\Entities\Menu;
use Dravencms\Model\Admin\Repository\MenuRepository;
use Dravencms\Model\User\Entities\AclOperation;
use Dravencms\Model\User\Entities\AclResource;
use Dravencms\Model\User\Repository\AclOperationRepository;
use Dravencms\Model\User\Repository\AclResourceRepository;
use Dravencms\Packager\IPackage;
use Dravencms\Packager\IScript;
use Kdyby\Doctrine\EntityManager;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class PostInstall implements IScript
{
    /** @var MenuRepository */
    private $menuRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var AclOperationRepository */
    private $aclOperationRepository;

    /** @var AclResourceRepository */
    private $aclResourceRepository;

    /**
     * PostInstall constructor.
     * @param MenuRepository $menuRepository
     * @param EntityManager $entityManager
     * @param AclOperationRepository $aclOperationRepository
     * @param AclResourceRepository $aclResourceRepository
     */
    public function __construct(MenuRepository $menuRepository, EntityManager $entityManager, AclOperationRepository $aclOperationRepository, AclResourceRepository $aclResourceRepository)
    {
        $this->menuRepository = $menuRepository;
        $this->entityManager = $entityManager;
        $this->aclOperationRepository = $aclOperationRepository;
        $this->aclResourceRepository = $aclResourceRepository;
    }

    /**
     * @param IPackage $package
     * @throws \Exception
     */
    public function run(IPackage $package)
    {
        if (!$aclResource = $this->aclResourceRepository->getOneByName('partner')) {
            $aclResource = new AclResource('partner', 'Partner');
            $this->entityManager->persist($aclResource);
        }

        if (!$aclOperationEdit = $this->aclOperationRepository->getOneByName('edit')) {
            $aclOperationEdit = new AclOperation($aclResource, 'edit', 'Allows editation of partner');
            $this->entityManager->persist($aclOperationEdit);
        }

        if (!$aclOperationDelete = $this->aclOperationRepository->getOneByName('delete')) {
            $aclOperationDelete = new AclOperation($aclResource, 'delete', 'Allows deletion of partner');
            $this->entityManager->persist($aclOperationDelete);
        }

        if (!$this->menuRepository->getOneByPresenter(':Admin:Partner:Partner')) {
            $adminMenu = new Menu('Partner', ':Admin:Partner:Partner', 'fa-diamond', $aclOperationEdit);

            $foundRoot = $this->menuRepository->getOneByName('Site items');

            if ($foundRoot) {
                $this->menuRepository->getMenuRepository()->persistAsLastChildOf($adminMenu, $foundRoot);
            } else {
                $this->entityManager->persist($adminMenu);
            }
        }

        $this->entityManager->flush();

    }
}