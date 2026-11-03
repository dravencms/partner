<?php declare(strict_types=1);

namespace Dravencms\Partner;

use Dravencms\Admin\Menu\{AdminMenuItem, AdminMenuProviderInterface};

final class AdminMenuProvider implements AdminMenuProviderInterface
{
    public function getItems(): iterable
    {
        yield new AdminMenuItem('partner.overview', 'partner.menu.overview', destination: ':Admin:Partner:Partner:default', parent: 'content', icon: 'fa-diamond', order: 70, resource: 'partner', privilege: 'edit');
    }
}
