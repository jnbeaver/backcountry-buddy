<?php

namespace App\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\ExpressionLanguage\ParsedExpression;

#[ORM\Embeddable]
readonly class TripCriteria
{
    #[ORM\Column(name: 'expr', type: 'string', length: 1000, nullable: true)]
    private ?string $expression;

    #[ORM\Column(name: 'compiled_expr', type: 'string', length: 1000, nullable: true)]
    private ?string $compiledExpression;

    #[ORM\Column(name: 'serialized_expr', type: 'string', length: 5000, nullable: true)]
    private ?string $serializedExpression;

    public function __construct(
        string $expression,
        string $compiledExpression,
        ParsedExpression $parsedExpression
    ) {
        $this->expression = $expression;
        $this->compiledExpression = $compiledExpression;
        $this->serializedExpression = serialize($parsedExpression);
    }

    public function getExpression(): ?string
    {
        return $this->expression;
    }

    public function getCompiledExpression(): ?string
    {
        return $this->compiledExpression;
    }

    public function getParsedExpression(): ?ParsedExpression
    {
        if ($this->serializedExpression === null) {
            return null;
        }

        return unserialize($this->serializedExpression);
    }
}
