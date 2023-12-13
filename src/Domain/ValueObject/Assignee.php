<?php

namespace App\Domain\ValueObject;

use App\Domain\Enum\AssigneeType;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
readonly class Assignee
{
    #[ORM\Column(type: 'string', nullable: true, enumType: AssigneeType::class)]
    private ?AssigneeType $type;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $personId;

    public function __construct(
        AssigneeType $type,
        ?int $personId = null
    ) {
        if ($type === AssigneeType::Individual) {
            Assert::notNull($personId, 'Person ID must be provided.');
        } else {
            Assert::null($personId, 'Person ID must be null.');
        }

        $this->type = $type;
        $this->personId = $personId;
    }

    public function getType(): ?AssigneeType
    {
        return $this->type;
    }

    public function getPersonId(): ?int
    {
        return $this->personId;
    }

    public function isNull(): bool
    {
        return $this->type === null && $this->personId === null;
    }
}
