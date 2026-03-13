<?php

namespace App\Ai\Services;
use App\Ai\Agents\PergolaAnalyzer;
use Laravel\Ai\Image;
use Laravel\Ai\Responses\ImageResponse;
use App\Ai\Prompts\GeneratePergolaPrompt;
use App\Ai\Prompts\GeneratePergola2ImagesPrompt;
use App\Ai\Prompts\DescribePergolaPrompt;
use Laravel\Ai\Files\Image as AiImage;
use Illuminate\Support\Facades\Storage;

/**
 * Service de gestion de l'IA pour l'analyse et la génération de pergolas.
 *
 * Ce service orchestre deux types d'opérations :
 * - La génération d'images de pergolas via le modèle image Gemini
 * - La description textuelle de pergolas via l'agent PergolaAnalyzer
 */
class PergolaAIService
{


    /**
     * Modèle Gemini utilisé pour la génération d'images.
     */
    private string $currentImageModel = 'gemini-2.5-flash-image';

    /**
     * Initialise le service avec l'agent et le gestionnaire de prompts
     * @param PergolaAnalyzer $agent
     */
    public function __construct(private PergolaAnalyzer $agent)
    {

    }

    /**
     * Change le modèle utilisé pour la génération d'images.
     *
     * @param string $nauveauModel Le nom du nouveau modèle Gemini à utiliser
     */
    public function changeModel(string $nauveauModel) : void
    {
        $this->currentImageModel = $nauveauModel;
    }

    /**
     * Retourne le modèle actuellement utilisé pour la génération d'images.
     *
     * @return string Le nom du modèle Gemini actuel
     */
    public function getImageModel(): string
    {
        return $this->currentImageModel;
    }

    public function generate( AiImage $imageA, ?AiImage $imageB = null, ?string $description = null ): string
    {
        if ($imageB !== null)
        {
            // Mode 2 images
            $prompt = GeneratePergola2ImagesPrompt::getPrompt();
            $images = [$imageA, $imageB];

        } else
        {
            // Mode 1 image
            $prompt = GeneratePergolaPrompt::getPrompt($description);
            $images = [$imageA];
        }

        $response = Image::of($prompt)
            ->attachments($images)
            ->quality('high')
            ->landscape()
            ->generate(
                provider: 'gemini',
                model: $this->currentImageModel,
            );

        $imageContent = (string) $response;  // Bytes de l'image (méthode correcte du SDK)
        return base64_encode($imageContent);  // Retourne le base64 pur
    }

    /**
     * Analyse et décrit textuellement une pergola à partir d'images.
     *
     * @param  array  $images L'image de la pergola à analyser
     */
    public function describePergola(AiImage $image) : string
    {
        $images = [$image];
        $rep = $this->agent->promptModel(DescribePergolaPrompt::getPrompt(), $images);
        return (string) $rep;
    }

}
