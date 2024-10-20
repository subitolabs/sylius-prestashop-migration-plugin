<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Model;

trait TranslationModelTrait
{
    private function getTranslation(array $field, string $locale): ?string
    {
        return array_key_exists($locale, $field) ? $field[$locale] : null;
    }
}
