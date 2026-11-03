<?php declare(strict_types=1);
namespace Dravencms\Partner\Access;
use Salamek\Files\Access\{IFileAccessProvider, FileAccess};
use Dravencms\Model\Partner\Repository\PartnerRepository;
use Nette\Security\User;

final class PartnerFileProvider implements IFileAccessProvider
{
    public function __construct(private PartnerRepository $repository, private User $user) {}
    public static function getId(): string { return 'partner.image'; }
    public function resolve(string $reference): ?FileAccess
    {
        $id = filter_var($reference, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false || !($entity = $this->repository->getOneById($id))) { return null; }
        $public = $entity->isActive();
        if (!$public && !$this->user->isAllowed('partner', 'edit')) { return null; }
        $attachment = $entity->getStructureFile();
        return $attachment ? new FileAccess($attachment->getFile(), $public, $attachment->getBasename()) : null;
    }
}
