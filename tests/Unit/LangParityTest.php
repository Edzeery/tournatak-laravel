<?php

$langRoot = __DIR__.'/../../resources/lang';

$locales = ['ar', 'en', 'fr', 'es'];

$files = ['actions.php', 'app.php', 'attributes.php', 'auth.php', 'pagination.php', 'validation.php'];

$flatLangKeys = static function (string $path): array {
    $data = require $path;
    $flat = [];
    $walk = function (array $node, string $prefix) use (&$flat, &$walk): void {
        foreach ($node as $key => $value) {
            $flatKey = $prefix === '' ? (string) $key : $prefix.'.'.$key;
            if (is_array($value)) {
                $walk($value, $flatKey);
            } else {
                $flat[$flatKey] = (string) $value;
            }
        }
    };
    $walk($data, '');
    ksort($flat);

    return $flat;
};

foreach ($locales as $locale) {
    foreach ($files as $file) {
        test("lang parity: {$locale}/{$file} matches ar key set and has no empty values", function () use ($langRoot, $locale, $file, $flatLangKeys): void {
            $reference = $flatLangKeys("{$langRoot}/ar/{$file}");
            $target = $flatLangKeys("{$langRoot}/{$locale}/{$file}");

            expect(array_keys($target))->toBe(array_keys($reference));

            foreach ($target as $key => $value) {
                $this->assertNotSame('', trim($value), "{$locale}/{$file} [{$key}] must not be empty");
            }
        });
    }
}
