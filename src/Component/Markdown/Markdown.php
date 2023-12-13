<?php

namespace App\Component\Markdown;

class Markdown
{
    public static function bold(string $text): string
    {
        return "**$text**";
    }

    public static function header1(string $title): string
    {
        return "# $title\n";
    }

    public static function header2(string $title): string
    {
        return "## $title\n";
    }

    public static function header3(string $title): string
    {
        return "### $title\n";
    }

    public static function italic(string $text): string
    {
        return "*$text*";
    }

    public static function orderedList(array $items): string
    {
        return sprintf(
            "%s\n",
            implode(
                "\n",
                array_map(fn (string $item) => "1. $item", $items)
            )
        );
    }

    public static function table(array $data): string
    {
        $headerData = array_shift($data);

        return sprintf(
            "%s%s\n",
            self::tableHeader($headerData),
            implode('', array_map(fn (array $rowData) => self::tableRow($rowData), $data))
        );
    }

    public static function unorderedList(array $items): string
    {
        return sprintf(
            "%s\n",
            implode(
                "\n",
                array_map(fn (string $item) => "- $item", $items)
            )
        );
    }

    private static function tableHeader(array $headerData): string
    {
        return sprintf(
            "| %s |\n| %s |",
            implode(' | ', $headerData),
            implode(' | ', array_fill(0, count($headerData), '---'))
        );
    }

    private static function tableRow(array $rowData): string
    {
        return sprintf(
            "\n| %s |",
            implode(' | ', $rowData)
        );
    }
}
