<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\DataTransformer;

use Behat\Transliterator\Transliterator;
use Sylius\Component\Core\Formatter\StringInflector;

class StringUtils
{
    public static function stringToCode(string $in): string
    {
        $in = str_replace(['(', ')'], '', $in);

        $in = Transliterator::transliterate($in);

        return StringInflector::nameToLowercaseCode($in);
    }
}
