<?php

namespace App\Domain\Services;

use GuzzleHttp\Client;
use Brick\Schema\Interfaces\Recipe as RecipeMicrodata;
use Brick\Schema\SchemaReader;
use Webmozart\Assert\Assert;

class RecipeService
{
    public function __construct(
        private readonly Client $httpClient
    ) {
    }

    public function readMicrodata(string $url): RecipeMicrodata
    {
        $html = $this->httpClient
            ->get($url)
            ->getBody()
            ->getContents();

        $things = SchemaReader::forAllFormats()->readHtml($html, $url);
        $recipe = null;

        foreach ($things as $thing) {
            if ($thing instanceof RecipeMicrodata) {
                $recipe = $thing;
                break;
            }
        }

        Assert::notNull($recipe, "Microdata for recipe not found at '$url'.");

        return $recipe;
    }
}
