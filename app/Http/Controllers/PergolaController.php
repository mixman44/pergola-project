<?php

namespace App\Http\Controllers;

use App\Ai\Services\PergolaAIService;
use App\Enums\PergolaModeEnum;
use App\Enums\PergolaModelEnum;
use App\Http\Requests\Pergola\DescribePergolaRequest;
use App\Http\Requests\Pergola\GeneratePergolaRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Ai\Exceptions\FailoverableException;
use Laravel\Ai\Files\Image;
use Illuminate\Http\JsonResponse;
/**
 * Contrôleur principal de gestion de l’application Pergola IA.
 *
 * Responsabilités :
 * - Afficher la page principale de l’interface pergola
 * - Valider les données envoyées par l’utilisateur
 * - Gérer la génération d’images de pergola via l’IA
 * - Gérer l’analyse d’image pour mise à jour de description (mode custom)
 *
 * Ce contrôleur sert de couche HTTP entre les requêtes utilisateur
 * et la logique métier contenue dans PergolaAIService.
 */
class PergolaController extends Controller
{

    /**
     * Constructeur du contrôleur.
     *
     * Initialise le service PergolaAIService utilisé
     * pour toutes les opérations liées à l’IA.
     */
    public function __construct(private PergolaAIService $service)
    {

    }

    /**
     * Génère une image de pergola selon les paramètres utilisateur.
     *
     * Règles de validation :
     * - model : obligatoire
     * - mode : doit être "default", "custom" ou "2image"
     * - image : obligatoire
     * - second_image : obligatoire si mode = custom ou 2image
     *
     * Fonctionnement :
     * - Change le modèle IA sélectionné
     * - Selon le mode :
     *      - default : réinitialise la description puis génère
     *      - custom : génère avec la description personnalisée
     *      - 2image : génère à partir de deux images
     *
     * @param Request $request Requête HTTP contenant les données du formulaire
     * @return \Illuminate\View\View Vue contenant le résultat généré
     *
     * @throws FailoverableException En cas d’échec du provider IA
     */
    public function generate(GeneratePergolaRequest $request): JsonResponse
    {
        $model = $request->input('model');

        $imageFile = $request->file('image');
        $image = Image::fromPath($imageFile->getRealPath());

        $mode = $request->input('mode');

        $this->service->changeModel($model);

        if ($mode === PergolaModeEnum::TWO_IMAGE->value)
        {
            $secondImageFile = $request->file('second_image');
            $secondImage = Image::fromPath($secondImageFile->getRealPath());

            $base64 = $this->service->generate($image, $secondImage);
        }
        elseif ($mode === PergolaModeEnum::CUSTOM->value)
        {
            // On récupère la description stockée en session
            $description = session('pergola_description');
            $base64 = $this->service->generate($image, null, $description);
        }
        else // default
        {
            // Pas de description custom, on vide la session au cas où
            session()->forget('pergola_description');
            $base64 = $this->service->generate($image);
        }

        return response()->json([
            'success' => true,
            'base64' => $base64,  // Renvoie le base64 pour le JS
        ]);
    }

    /**
     * Analyse une image afin de mettre à jour la description de la pergola.
     *
     * Disponible uniquement en mode "custom".
     *
     * Règles de validation :
     * - mode : doit être "custom"
     * - second_image : obligatoire
     *
     * Cette méthode met à jour l’état interne de la description
     * dans le service PergolaAIService.
     *
     * @param Request $request Requête HTTP contenant l’image à analyser
     * @return \Illuminate\Http\RedirectResponse Redirection vers la page précédente
     */
    public function describe(DescribePergolaRequest $request): JsonResponse
    {
        $secondImageFile = $request->file('second_image');
        $secondImage = Image::fromPath($secondImageFile->getRealPath());

        $description = $this->service->describePergola($secondImage);

        session(['pergola_description' => $description]);

        return response()->json([
            'success' => true,
            'description' => $description,
        ]);
    }




    /**
     * Affiche la page principale de l’application.
     *
     * Fournit :
     * - La liste des modèles IA disponibles
     * - Les différents modes de génération
     *
     * @return \Illuminate\View\View Vue principale de l’interface pergola
     */
    public function index()
    {
        return view('pergola.index');
    }
}
