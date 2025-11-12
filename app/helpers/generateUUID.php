<?php
/**
 * Genereert een willekeurige UUID (Universally Unique Identifier) versie 4.
 *
 * Deze functie maakt gebruik van `mt_rand()` om willekeurige bits te genereren,
 * die vervolgens worden samengesteld volgens de UUID v4-specificatie.
 *
 * Voorbeeld uitvoer:
 * ```
 * 3f29c9d1-7b8a-4d3f-a8d1-2e73a6c81b3e
 * ```
 *
 * Gebruik:
 * ```php
 * $uuid = generateUUID();
 * echo $uuid;
 * ```
 *
 * @return string Een geldige UUIDv4 in de vorm van 8-4-4-4-12 hexadecimale tekens.
 */
function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}


?>