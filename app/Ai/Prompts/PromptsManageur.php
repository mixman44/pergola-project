<?php

namespace App\Ai\Prompts;

use App\Ai\Prompts\GeneratePergolaPrompt;
use App\Ai\Prompts\GeneratePergola2ImagesPrompt;
use App\Ai\Prompts\DescribePergolaPrompt;

class PromptsManageur
{
    private GeneratePergolaPrompt $generatePergola;
    private GeneratePergola2ImagesPrompt $generatePergola2Images;
    private DescribePergolaPrompt $describePergola;

    public function __construct()
    {
        $this->generatePergola = new GeneratePergolaPrompt();
        $this->generatePergola2Images = new GeneratePergola2ImagesPrompt();
        $this->describePergola = new DescribePergolaPrompt();
    }

    /**
     * Prompt for
     * Generate pergola from RED shape and descriptions
     */
    public function generatePergolaPrompt(): string
    {
        return $this->generatePergola->getPrompt();
    }

    /**
     * Prompt for
     * Generate pergola using 2 images
     */
    public function generatePergolaFrom2ImagesPrompt(): string
    {
        return $this->generatePergola2Images->getPrompt();
    }

    /**
     * Prompt for
     * Describe pergola from image
     */
    public function describePergolaPrompt(): string
    {
        return $this->describePergola->getPrompt();
    }

    /**
     * cehangé la decriptons de la pergola dans le pronpt generatePergolaPrompt
     * @param string $description
     * @return void
     */
    public function chengePergolaDescription(string $description): void
    {
        $this->generatePergola->setDescription($description);
    }

    /**
     * remais la decriptons de la pergola dans le pronpt generatePergolaPrompt
     * @return void
     */
    public function resetPergolaDescription(): void
    {
        $this->generatePergola = new GeneratePergolaPrompt();
    }

}
