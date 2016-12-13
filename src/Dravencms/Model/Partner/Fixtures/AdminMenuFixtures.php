<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Partner\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Dravencms\Model\Admin\Entities\Menu;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class AdminMenuFixtures extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $menu = $manager->getRepository(Menu::class);

        $adminMenu = new Menu('Partner', ':Admin:Partner:Partner', 'fa-diamond', $this->getReference('user-acl-operation-partner-edit'));

        if ($parent = $menu->findOneBy(['name' => 'Site items']))
        {
            $menu->persistAsLastChildOf($adminMenu, $parent);
        }
        else
        {
            $manager->persist($adminMenu);
        }

        $manager->flush();
    }
    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getDependencies()
    {
        return ['Dravencms\Model\Partner\Fixtures\AclOperationFixtures', 'Dravencms\Model\Structure\Fixtures\AdminMenuFixtures'];
    }
}