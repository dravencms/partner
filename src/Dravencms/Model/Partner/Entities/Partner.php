<?php declare(strict_types = 1);
namespace Dravencms\Model\Partner\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Dravencms\Database\Attributes\Identifier;
use Dravencms\Model\File\Entities\StructureFile;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Nette;

/**
 * Class Partner
 * @package App\Model\Partner\Entities
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\Table(name="partnerPartner")
 */
class Partner
{
    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false,unique=true)
     */
    private $identifier;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(type="string",length=255)
     */
    private $url;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isMain;

    /**
     * @var integer
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @var ArrayCollection|PartnerTranslation[]
     * @ORM\OneToMany(targetEntity="PartnerTranslation", mappedBy="partner",cascade={"persist", "remove"})
     */
    private $translations;

    /**
     * @var StructureFile
     * @ORM\ManyToOne(targetEntity="\Dravencms\Model\File\Entities\StructureFile", inversedBy="articles")
     * @ORM\JoinColumn(name="structure_file_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $structureFile;

    /**
     * Partner constructor.
     * @param string $identifier
     * @param string $url
     * @param bool $isActive
     * @param bool $isMain
     * @param StructureFile $structureFile
     */
    public function __construct(string $identifier, string $url, bool $isActive, bool $isMain, StructureFile $structureFile)
    {
        $this->identifier = $identifier;
        $this->url = $url;
        $this->isActive = $isActive;
        $this->isMain = $isMain;
        $this->structureFile = $structureFile;
        $this->translations = new ArrayCollection();
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }


    /**
     * @param boolean $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @param boolean $isMain
     */
    public function setIsMain(bool $isMain): void
    {
        $this->isMain = $isMain;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @param StructureFile $structureFile
     */
    public function setStructureFile(StructureFile $structureFile): void
    {
        $this->structureFile = $structureFile;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return boolean
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return boolean
     */
    public function isMain(): bool
    {
        return $this->isMain;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return StructureFile
     */
    public function getStructureFile(): StructureFile
    {
        return $this->structureFile;
    }

    /**
     * @return ArrayCollection|PartnerTranslation[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }

}

