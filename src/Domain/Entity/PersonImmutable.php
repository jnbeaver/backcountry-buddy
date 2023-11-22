<?php

namespace App\Domain\Entity;

interface PersonImmutable
{
    public function getId(): int;

    public function getName(): string;

    public function isChild(): bool;
}
