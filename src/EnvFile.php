<?php

declare(strict_types=1);

namespace Idiosyncratic\Env;

use function array_diff_assoc;
use function array_intersect_assoc;

final class EnvFile
{
    private string $path;

    /** @var array<string, string> */
    private array $variables = [];

    public function __construct(string $path)
    {
        $this->path = $path;

        $this->variables = Parser::parseFile($this->path);
    }

    /**
     * @return array<string, string>
     */
    public function getAll() : array
    {
        return $this->variables;
    }

    public function get(string $name) : ?string
    {
        return isset($this->variables[$name]) === true ? $this->variables[$name] : null;
    }

    /**
     * @return array<string, string>
     */
    public function diff(EnvFile $otherEnvFile) : array
    {
        $otherVariables = $otherEnvFile->getAll();

        return array_diff_assoc($this->variables, $otherVariables);
    }

    /**
     * @return array<string, string>
     */
    public function intersect(EnvFile $otherEnvFile) : array
    {
        $otherVariables = $otherEnvFile->getAll();

        return array_intersect_assoc($this->variables, $otherVariables);
    }
}
