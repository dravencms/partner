<?php declare(strict_types = 1);
namespace Dravencms\Model\Partner\Entities;

use Doctrine\ORM\Mapping as ORM;
use Dravencms\Database\Attributes\Identifier;
use Dravencms\Model\Locale\Entities\Locale;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Nette;

/**
 * Class PartnerTranslation
 * @package App\Model\Partner\Entities
 * @ORM\Entity
 * @ORM\Table(name="partnerPartnerTranslation", uniqueConstraints={@UniqueConstraint(name="partner_translation_name_unique", columns={"partner_id", "locale_id", "name"})})
 */
class PartnerTranslation
{
    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique=true,nullable=false)
     */
    private $slug;

    /**
     * @var string
     * @ORM\Column(type="text",nullable=false)
     */
    private $description;

    /**
     * @var Partner
     * @ORM\ManyToOne(targetEntity="Partner", inversedBy="translations")
     * @ORM\JoinColumn(name="partner_id", referencedColumnName="id")
     */
    private $partner;

    /**
     * @var Locale
     * @ORM\ManyToOne(targetEntity="Dravencms\Model\Locale\Entities\Locale")
     * @ORM\JoinColumn(name="locale_id", referencedColumnName="id")
     */
    private $locale;

    /**
     * PartnerTranslation constructor.
     * @param Partner $partner
     * @param Locale $locale
     * @param $name
     * @param $description
     */
    public function __construct(Partner $partner, Locale $locale, $name, $description)
    {
        $this->name = $name;
        $this->description = $description;
        $this->partner = $partner;
        $this->locale = $locale;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param Partner $partner
     */
    public function setGallery(Partner $partner): void
    {
        $this->partner = $partner;
    }

    /**
     * @param Locale $locale
     */
    public function setLocale(Locale $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Partner
     */
    public function getPartner(): Partner
    {
        return $this->partner;
    }

    /**
     * @return Locale
     */
    public function getLocale(): Locale
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }
}

