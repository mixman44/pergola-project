<?php

namespace App\Ai\Services;

use App\Ai\Agents\PergolaAnalyzer;
use App\Ai\Prompts\PromptsManageur;
use Laravel\Ai\Exceptions\FailoverableException;
use Laravel\Ai\Image;
use Laravel\Ai\Responses\ImageResponse;

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
     * Agent responsable de l'analyse et de la description textuelle des pergolas.
     */
    private PergolaAnalyzer $agent;
    /**
     * Gestionnaire des prompts utilisés par le service.
     */
    private PromptsManageur $prompts;

    /**
     * Modèle Gemini utilisé pour la génération d'images.
     */
    private string $currentImageModel = 'gemini-2.5-flash-image';

    /**
     * Initialise le service avec l'agent et le gestionnaire de prompts.
     */
    public function __construct()
    {
        $this->agent = new PergolaAnalyzer();
        $this->prompts = new PromptsManageur();
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


    /**
     * Génère une image de pergola à partir d'une images de référence.
     *
     * @param  array  $images L'images de référence a fournir au modèle
     * @return ImageResponse La réponse contenant l'image générée
     *
     * @throws FailoverableException Si le provider Gemini est indisponible
     */
    public function generateImage(array $images):  ImageResponse
    {
        return Image::of($this->prompts->generatePergolaPrompt())
            ->attachments($images)
            ->quality('high')
            ->landscape()
            ->generate(
                provider: 'gemini',
                model: $this->currentImageModel,
            );
    }

    /**
     * Génère une image de pergola en combinant deux images de référence.
     *
     * @param  array  $images Les deux images de référence à combiner
     * @return ImageResponse La réponse contenant l'image générée
     *
     * @throws FailoverableException Si le provider Gemini est indisponible
     */
    public function generatePergolaFrom2Images(array $images):  ImageResponse
    {
        return Image::of($this->prompts->generatePergolaFrom2ImagesPrompt())
            ->attachments($images)
            ->quality('high')
            ->landscape()
            ->generate(
                provider: 'gemini',
                model: $this->currentImageModel,
            );
    }

    /**
     * Analyse et décrit textuellement une pergola à partir d'images.
     *
     * @param  array  $images L'image de la pergola à analyser
     */
    public function describePergola(array $images) :void
    {
        $rep = $this->agent->promptModel($this->prompts->describePergolaPrompt(), $images);
        $this->prompts->chengePergolaDescription((string) $rep);
    }

    public function resetPergolaDescription(): void
    {
        $this->prompts->resetPergolaDescription();
    }
}
