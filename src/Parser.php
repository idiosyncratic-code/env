<?php

declare(strict_types=1);

namespace Idiosyncratic\Env;

use RuntimeException;
use function count;
use function explode;
use function file_exists;
use function file_get_contents;
use function implode;
use function is_file;
use function is_readable;
use function preg_match_all;
use function sprintf;
use function strpos;
use function trim;

final class Parser
{
    /**
     * @return array<string, string>
     */
    public static function parseFile(string $path) : array
    {
        if (file_exists($path) === false) {
            throw new RuntimeException(sprintf('File %s does not exist', $path));
        }

        if (is_file($path) === false) {
            throw new RuntimeException(sprintf('%s is not a file', $path));
        }

        if (is_readable($path) === false) {
            throw new RuntimeException(sprintf('%s is not readable', $path));
        }

        $content =  file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException(sprintf('Could not read %s', $path));
        }

        return self::parseString($content);
    }

    /**
     * @return array<string, string>
     */
    public static function parseString(string $content) : array
    {
        $variables = [];

        $lines = explode("\n", $content);

        $cleanLines = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }
            $cleanLines[] = $line;
        }

        $content = implode("\n", $cleanLines);

        $pattern = '/([^=]+)=([\',"]?)(.+)\2\n?/';

        preg_match_all($pattern, $content, $matches);

        $count = count($matches[0]);

        for ($index = 0; $index < $count; $index++) {
            $variables[(string) $matches[1][$index]] = (string) $matches[3][$index];
        }

        return $variables;
    }
}
