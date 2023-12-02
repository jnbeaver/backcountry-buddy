<?php

namespace App\Domain\ValueObject;

use App\Domain\Enum\GearInclusionFrequency;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
class GearInclusionStrategy
{
    #[ORM\Column(type: 'string', nullable: true, enumType: GearInclusionFrequency::class)]
    private ?GearInclusionFrequency $frequency;

    #[ORM\Embedded(class: TripCriteria::class, columnPrefix: false)]
    private ?TripCriteria $tripCriteria;

    public function __construct(
        GearInclusionFrequency $frequency,
        ?TripCriteria $tripCriteria = null
    ) {
        if ($frequency === GearInclusionFrequency::Sometimes) {
            Assert::notNull($tripCriteria, 'Trip criteria must be provided.');
        } else {
            Assert::null($tripCriteria, 'Trip criteria must be null.');
        }

        $this->frequency = $frequency;
        $this->tripCriteria = $tripCriteria;
    }

    public function getFrequency(): ?GearInclusionFrequency
    {
        return $this->frequency;
    }

    public function getTripCriteria(): ?TripCriteria
    {
        return $this->tripCriteria;
    }
}
