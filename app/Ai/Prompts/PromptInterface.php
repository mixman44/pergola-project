<?php

namespace App\Ai\Prompts;

interface PromptInterface
{
    /**
     * done le promt que on va utilisé
     */
    public static function getPrompt(): string;
}
