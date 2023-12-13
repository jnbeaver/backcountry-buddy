<?php

namespace App\Component\Markdown;

class Markdown
{
    public static function bold(string $text): string
    {
        return "**$text**";
    }

    public static function tasklist(array $items): string
    {
        return sprintf(
            "%s\n",
            implode(
                "\n",
                array_map(fn (string $item) => "- [ ] $item", $items)
            )
        );
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

    public static function table(array $header, array $rows): string
    {
        return sprintf(
            "%s%s\n",
            self::tableHeader($header),
            implode('', array_map(fn (array $row) => self::tableRow($row), $rows))
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

    private static function tableHeader(array $header): string
    {
        return sprintf(
            "| %s |\n| %s |",
            implode(' | ', $header),
            implode(' | ', array_fill(0, count($header), '---'))
        );
    }

    private static function tableRow(array $row): string
    {
        return sprintf(
            "\n| %s |",
            implode(' | ', $row)
        );
    }
}
