<?php declare(strict_types=1);

namespace Dravencms\Partner;

use Dravencms\User\Acl\{AclPermission, AclProviderInterface};

final class AclProvider implements AclProviderInterface
{
    public function getPermissions(): iterable
    {
        yield new AclPermission('partner', 'edit', 'partner.acl.permissions.edit.label', resourceLabel: 'partner.acl.resource');
        yield new AclPermission('partner', 'delete', 'partner.acl.permissions.delete.label', resourceLabel: 'partner.acl.resource');
    }
}
