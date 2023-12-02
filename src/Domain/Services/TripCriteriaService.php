<?php

namespace App\Domain\Services;

use App\Domain\Entity\Trip;
use App\Domain\ValueObject\TripCriteria;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

class TripCriteriaService
{
    private const TYPE = 'type';

    private const LOW_TEMP = 'lowTemp';

    private const HIGH_TEMP = 'highTemp';

    public const TOKENS = [
        self::TYPE,
        self::LOW_TEMP,
        self::HIGH_TEMP,
    ];

    public function __construct(
        private readonly ExpressionLanguage $expressionLanguage,
    ) {
    }

    public function create(string $expression): TripCriteria
    {
        $parsedExpression = $this->expressionLanguage->parse($expression, self::TOKENS);

        return new TripCriteria(
            $expression,
            $this->expressionLanguage->compile($parsedExpression, self::TOKENS),
            $parsedExpression
        );
    }

    public function evaluate(TripCriteria $criteria, Trip $trip): bool
    {
        return $this->expressionLanguage->evaluate(
            $criteria->getParsedExpression(),
            array_combine(
                self::TOKENS,
                [
                    $trip->getType()->name,
                    $trip->getLowTemp(),
                    $trip->getHighTemp(),
                ]
            )
        );
    }

    public function lint(string $expression, ?string &$errorMessage): bool
    {
        try {
            $this->expressionLanguage->lint($expression, self::TOKENS);
        } catch (SyntaxError $e) {
            $errorMessage = $e->getMessage();

            return false;
        }

        return true;
    }
}
