import { SceneManager } from "./SceneManager.js";

class ScreenshotManager {

    constructor(sceneManager)
    {
        this.sceneManager = sceneManager;
    }


    /**
     * Compose l'image (fond + canvas 3D) et déclenche le téléchargement,
     * @param {string} backgroundImageUrl - URL de l'image de fond
     * @param {function}    onImageReady
     */
    takeScreenshot(backgroundImageUrl, onImageReady)
    {
        // Force un rendu à jour avant de capturer
        this.sceneManager.render();

        const tempCanvas = document.createElement('canvas');
        const ctx = tempCanvas.getContext('2d');
        const domCanvas  = this.sceneManager.getCanvasElement();
        this.#composeWithBackground(tempCanvas, ctx, domCanvas, backgroundImageUrl, onImageReady);
    }


    /**
     * Compose le canvas 3D par-dessus l'image de fond en respectant les ratios,
     * puis appelle #finalize.
     */
    #composeWithBackground(tempCanvas, ctx, domCanvas, backgroundImageUrl, onImageReady)
    {
        // Crée une image vide en mémoire
        const img = new Image();

        // Quand l’image est complètement chargée
        img.onload = () =>
        {
            // Ajuste la taille du canvas temporaire à la taille réelle de l’image de fond
            tempCanvas.width  = img.width;
            tempCanvas.height = img.height;

            // Dessine l’image de fond sur le canvas temporaire
            ctx.drawImage(img, 0, 0);

            // Calcule où et comment placer le canvas 3D pour qu’il soit centré et bien dimensionné
            const { offsetX, offsetY, drawWidth, drawHeight } = this.#computeOverlayBounds(domCanvas, img);

            // Dessine le contenu du canvas 3D par-dessus l’image de fond
            ctx.drawImage(domCanvas, offsetX, offsetY, drawWidth, drawHeight);

            this.#finalize(tempCanvas, onImageReady);
        };

        img.src = backgroundImageUrl;
    }

    /**
     * Calcule les dimensions et la position du canvas 3D superposé sur l'image de fond,
     * de façon à le centrer en conservant son ratio d'aspect.
     */
    #computeOverlayBounds(domCanvas, img)
    {
        const canvasAspect = domCanvas.width / domCanvas.height;
        const imgAspect    = img.width / img.height;

        let drawWidth, drawHeight, offsetX, offsetY;

        if (canvasAspect > imgAspect)
        {
            // Canvas plus large que l'image de fond
            drawHeight = img.height;
            drawWidth  = drawHeight * canvasAspect;
            offsetX    = (img.width - drawWidth) / 2;
            offsetY    = 0;
        }
        else
        {
            // Canvas plus haut que l'image de fond
            drawWidth  = img.width;
            drawHeight = drawWidth / canvasAspect;
            offsetX    = 0;
            offsetY    = (img.height - drawHeight) / 2;
        }

        return { offsetX, offsetY, drawWidth, drawHeight };
    }

    /**
     * Télécharge l'image composée
     */
    #finalize(canvas, onImageReady)
    {
        const imageData  = canvas.toDataURL('image/png');
        const base64     = imageData.split(',')[1];

        this.#downloadImage(imageData);

        if (typeof onImageReady === 'function')
        {
            onImageReady(base64);
        }
    }

    /**
     * Crée un lien temporaire et déclenche le téléchargement du PNG.
     */
    #downloadImage(imageDataUrl)
    {
        const link      = document.createElement('a');
        link.download   = 'Image_A_target.png';
        link.href       = imageDataUrl;
        link.click();
    }
}

export { ScreenshotManager };
