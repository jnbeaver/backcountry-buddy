<?php

namespace App\Adapters\CLI;

use Symfony\Component\Console\Style\SymfonyStyle;

class Style extends SymfonyStyle
{
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
}
