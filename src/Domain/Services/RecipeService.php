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

        Assert::true(
            count($things) === 1 && $things[0] instanceof RecipeMicrodata,
            "Microdata for recipe not found at '$url'."
        );

        return $things[0];
    }
}
