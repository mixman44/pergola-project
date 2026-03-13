import '../../css/pergola/pergola-editor.css';
import { SceneManager }      from "./SceneManager.js";
import { ScreenshotManager } from "./ScreenshotManager.js";
import { ApiService }        from "./ApiService.js";
import './pergola-accordion.js'

class pergolaEditor {
    constructor()
    {
        this.sceneManager = new SceneManager();
        this.screenshotManager = new ScreenshotManager(this.sceneManager);

        // Stockage de l'image B (pergola) chargée par l'utilisateur
        this.pergolaImageB = null;

        // CHANGEMENT: Instance de ApiService pour gérer les appels backend
        this.apiService = new ApiService();
        this.init()
    }

    init()
    {
        this.sceneManager.init();
        this.#initEvents();
        this.sceneManager.startAnimationLoop();
    }

    #initEvents()
    {
        this.#initSliders();
        this.#initBackgroundUpload();
        this.#initScreenshot();
        this.#initModelSelector();
        this.#initPergolaControls();
    }

    /**
     * Sliders X / Y / Z → mise à jour des dimensions du cube.
     */
    #initSliders()
    {
        const scaleX = document.querySelector('#scaleX');
        const scaleY = document.querySelector('#scaleY');
        const scaleZ = document.querySelector('#scaleZ');

        const onSliderChange = () => {
            this.sceneManager.updateCubeSize(
                parseFloat(scaleX.value),
                parseFloat(scaleY.value),
                parseFloat(scaleZ.value)
            );
        };

        scaleX.addEventListener('input', onSliderChange);
        scaleY.addEventListener('input', onSliderChange);
        scaleZ.addEventListener('input', onSliderChange);
    }

    /**
     * Upload + application d'une image de fond sur le body.
     */
    #initBackgroundUpload()
    {
        const imageUpload = document.getElementById('imageUpload');
        const applyButton = document.getElementById('applyButton');

        applyButton.addEventListener('click', () => {
            const file = imageUpload.files?.[0];
            if (!file) { alert('Veuillez sélectionner une image'); return; }

            const reader = new FileReader();
            reader.onload = (e) => this.#applyBackgroundImage(e.target.result);
            reader.readAsDataURL(file);
        });
    }

    /**
     * Applique une image en fond
     */
    #applyBackgroundImage(imageUrl)
    {
        const canvasContainer = document.querySelector('.canvas-container');
        canvasContainer.style.backgroundImage    = `url(${imageUrl})`;
        canvasContainer.style.backgroundSize     = 'cover';
        canvasContainer.style.backgroundPosition = 'center';
        canvasContainer.style.backgroundRepeat   = 'no-repeat';
    }

    /**
     * Bouton screenshot : compose fond + cube et envoie au backend.
     */
    #initScreenshot()
    {
        const screenshotButton = document.getElementById('screenshotButton');

        screenshotButton.addEventListener('click', () =>
        {
            const canvasContainer = document.querySelector('.canvas-container');
            const bgImage = canvasContainer.style.backgroundImage;

            const bgUrl = (bgImage && bgImage !== 'none')
                ? bgImage.slice(4, -1).replace(/['"]/g, '')
                : null;

            this.screenshotManager.takeScreenshot(bgUrl, (base64Image) => { this.#GeneratedImage(base64Image); });
        });
    }

    /**
     * Reçoit l'image finale et appelle la bonne fonctions back end
     */
    #GeneratedImage(base64Image)
    {
        // On appelle generateImage, qui gère le mode et imageB automatiquement
        this.apiService.generateImage(base64Image, this.pergolaImageB)
            .then(response => {

                console.log('Image générée avec succès:', response);
                //Télécharge l'image IA depuis l'URL renvoyée
                const link = document.createElement('a');
                link.href = 'data:image/png;base64,' + response.base64;  // Recrée le URI avec base64
                link.download = 'generated_pergola.png';
                link.click();

                alert('Génération réussie !');
            })
            .catch(error => {
                console.error('Erreur génération:', error);
                alert('Erreur lors de la génération: ' + error.message);
            });
    }

    /**
     * Sélecteur de modèle IA.
     */
    #initModelSelector()
    {
        const modelSelect = document.getElementById('modelSelect');

        modelSelect.addEventListener('change', (e) => {
            const selectedModel = e.target.value;
            this.apiService.setModel(selectedModel);
            console.log('Modèle sélectionné :', selectedModel);
        });
    }

    /**
     * Contrôles liés à la pergola :
     *   - chargement de l'image B
     *   - bouton "Décrire la pergola"
     *   - sélecteur de mode de génération
     */
    #initPergolaControls()
    {
        this.#initPergolaImageUpload();
        this.#initDescribePergolaButton();
        this.#initModeSelector();
    }

    #initPergolaImageUpload()
    {
        const pergolaImageUpload = document.getElementById('pergolaImageUpload');

        pergolaImageUpload.addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (event) => {
                this.pergolaImageB = event.target.result.split(',')[1];
                console.log('Image B (pergola) chargée et stockée');
            };
            reader.readAsDataURL(file);
        });
    }

    #initDescribePergolaButton()
    {
        const describePergolaButton = document.getElementById('describePergolaButton');
        const pergolaImageUpload    = document.getElementById('pergolaImageUpload');

        describePergolaButton.addEventListener('click', async () => {
            if (!pergolaImageUpload.files?.[0])
            {
                alert('Veuillez sélectionner une image de pergola');
                return;
            }
            try {
                // Appel à describePergola via ApiService
                const description = await this.apiService.describePergola(this.pergolaImageB);
                console.log('Description reçue:', description);
                alert('Description de la pergola générée avec succès : ' + description);
            } catch (error) {
                console.error('Erreur description:', error);
                alert('Erreur lors de la description: ' + error.message);
            }
        });
    }

    #initModeSelector()
    {
        const modeSelect = document.getElementById('modeSelect');

        modeSelect.addEventListener('change', (e) => {
            const mode = e.target.value;
            this.apiService.setMode(mode);
            console.log('Mode sélectionné :', mode);
        });
    }
}
export { pergolaEditor };
new pergolaEditor();
