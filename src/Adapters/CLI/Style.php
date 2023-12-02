<?php

namespace App\Adapters\CLI;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use DateTime;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class Style extends SymfonyStyle
{
    public function choiceAssoc(
        string $question,
        array $choices,
        bool $optional = false,
        bool $multiselect = false
    ): mixed {
        $question = new ChoiceQuestion(
            sprintf('%s%s', $question, $optional ? ' (optional)' : ''),
            array_keys($choices)
        );

        $question->setMultiselect($multiselect);

        if ($optional) {
            // override default validator to handle empty answers
            $defaultValidator = $question->getValidator();
            $question->setValidator(function (?string $answer) use ($defaultValidator) {
                if ($answer === null || $answer === '') {
                    return null;
                }

                return $defaultValidator($answer);
            });
        }

        $answer = $this->askQuestion($question);

        if ($optional && empty($answer)) {
            return null;
        }

        if ($multiselect) {
            return array_intersect_key($choices, array_flip($answer));
        }

        return $choices[$answer];
    }

    public function askDate(string $question, ?DateTime $after = null): DateTime
    {
        return $this->ask($question, null, function (string $answer) use ($after) {
            try {
                $answer = Carbon::parse($answer);
            } catch (InvalidFormatException) {
                throw new RuntimeException('Invalid date/time format.');
            }

            if ($after && $answer->startOfDay()->lte(Carbon::instance($after)->startOfDay())) {
                throw new RuntimeException(sprintf('Must be after %s.', $after->format('n/j/y')));
            }

            return $answer;
        });
    }

    public function askInteger(string $question, ?int $greaterThan = null): int
    {
        return parent::ask($question, null, function (?string $answer) use ($greaterThan) {
            if (!is_numeric($answer)) {
                throw new RuntimeException('Must be an integer.');
            }

            $answer = (int) $answer;

            if ($greaterThan && $answer <= $greaterThan) {
                throw new RuntimeException(sprintf('Must be greater than %s.', $greaterThan));
            }

            return $answer;
        });
    }

    /**
     * @param string $question
     * @param callable|null $validator
     * @return string[]
     */
    public function askMany(string $question, callable $validator = null): array
    {
        $answers = [];

        do {
            $answer = parent::ask(
                sprintf(
                    '%s #%s%s',
                    $question,
                    $num = count($answers) + 1,
                    $num === 1 ? ' (leave empty to finish)' : ''
                ),
                null,
                $validator
            );
        } while (!empty($answer) && array_push($answers, $answer));

        return $answers;
    }

    public function askRequired(string $question, ?array $autocompleterValues = null): string
    {
        $question = new Question($question, null);

        $question->setValidator(function (?string $answer) {
            if (empty($answer)) {
                throw new RuntimeException('This value is required.');
            }

            return $answer;
        });

        if (!empty($autocompleterValues)) {
            $question->setAutocompleterValues($autocompleterValues);
        }

        return parent::askQuestion($question);
    }
}
