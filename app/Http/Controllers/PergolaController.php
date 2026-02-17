<?php

namespace App\Http\Controllers;

use App\Ai\Services\PergolaAIService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Ai\Exceptions\FailoverableException;
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
     * Service métier responsable des interactions avec l’IA.
     *
     * @var PergolaAIService
     */
    private PergolaAIService $service;

    /**
     * Constructeur du contrôleur.
     *
     * Initialise le service PergolaAIService utilisé
     * pour toutes les opérations liées à l’IA.
     */
    public function __construct()
    {
        $this->service = new PergolaAIService();
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
    public function generate(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'mode' => 'required|in:default,custom,2image',
            'image' => 'required|image',
            'second_image' => 'required_if:mode,2image,custom|image'
        ]);

        $model = $request->input('model');
        $image = $request->file('image');
        $mode = $request->input('mode');

        $this->service->changeModel($model);

        if ($mode === '2image')
        {
            $secondImage = $request->file('second_image');
            $images = [$image, $secondImage];
            $result = $this->service->generatePergolaFrom2Images($images);
        }
        else
        {
            $images = [$image];
            if ($mode === 'default')
            {
                $this->service->resetPergolaDescription();
            }
            $result = $this->service->generateImage($images);
        }


        return view('pergola.index', compact('result'));
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
    public function describe(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:custom',
            'second_image' => 'required|image'
        ]);

        $secondImage = $request->file('second_image');
        $images = [$secondImage];
        $this->service->describePergola($images);
        return redirect()->back()->with('success', 'Description mise à jour.');
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
        // Liste des modèles Gemini disponibles
        $models = [
            'gemini-2.5-flash-image',
            'gemini-3-pro-image-preview'
        ];

        // Modes de génération
        $modes = [
            'default' => 'Description par défaut',
            'custom' => 'Description personnalisée',
            '2image' => '2 images'
        ];

        return view('pergola.index', compact('models', 'modes'));
    }
}
