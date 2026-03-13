/**
 * Service responsable de la communication avec l'API Laravel
 * pour la génération et l'analyse d'images de pergolas.
 */
class ApiService {
    /**
     * Initialise le service API.
     * - Définit les valeurs par défaut du modèle IA et du mode de génération.
     *
     * @constructor
     */
    constructor()
    {
        this.baseUrl = '/pergola';

        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!this.csrfToken)
        {
            console.warn('CSRF token non trouvé. Assure-toi d\'avoir <meta name="csrf-token" content="{{ csrf_token() }}"> dans ton layout HTML.');
        }

        this.currentModel = 'gemini-2.5-flash-image';
        this.currentMode = 'default';
    }

    /**
     * Définit le modèle d'IA utilisé pour la génération d'image.
     *
     * @param {string} model - Nom du modèle IA (ex: "gemini-2.5-flash-image")
     *
     * @example
     * apiService.setModel("gemini-2.5-flash-image");
     */
    setModel(model)
    {
        this.currentModel = model;
        console.log('Modèle stocké :', this.currentModel);
    }

    /**
     * Définit le mode de fonctionnement de la génération.
     *
     * Modes possibles :
     * - default : génération classique
     * - 2image : génération à partir de deux images
     * - custom : génération personnalisée
     *
     * @param {string} mode - Mode de génération
     *
     * @example
     * apiService.setMode("2image");
     */
    setMode(mode)
    {
        this.currentMode = mode;
        console.log('Mode stocké :', this.currentMode); // Pour debug
    }

    /**
     * Méthode privée permettant d'envoyer une requête HTTP vers l'API Laravel.
     * @private
     *
     * @param {string} endpoint - Endpoint API (ex: "/generate")
     * @param {Object|FormData} data - Données à envoyer
     * @param {string} [method='POST'] - Méthode HTTP utilisée
     *
     * @returns {Promise<Object>} Réponse JSON retournée par l'API
     *
     * @throws {Error} Si la requête HTTP échoue
     */
    async #fetchApi(endpoint, data, method = 'POST')
    {
        const url = `${this.baseUrl}${endpoint}`;

        const options = {
            method,
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,  // Token CSRF pour Laravel (évite erreur 419)
                'Accept': 'application/json',    // Demande une réponse en JSON du serveur
            },
        };

        // Adapte le body selon le type de data
        if (data instanceof FormData) // Pour images/fichiers (efficace pour uploads)
        {
            options.body = data;
        }
        else // Pour objets simples
        {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(data);  // Convertit {key: value} en string JSON
        }

        try {  // Bloc pour catcher les erreurs
            const response = await fetch(url, options);  // Envoie la requête et attend
            if (!response.ok) {  // Si erreur HTTP (ex. 400, 500)
                throw new Error(`Erreur HTTP: ${response.status} - ${await response.text()}`);  // Lit le message Laravel
            }
            return await response.json();  // Parse la réponse en objet JS (ex. { success: true, url: 'image.jpg' })
        } catch (error) {  // Capture erreurs réseau ou autres
            console.error('Erreur API:', error);  // Log pour debug
            throw error;  // Relance pour que l'appelant (ex. generateImage) gère (ex. alert)
        }
    }


    /**
     * Envoie une requête à l'API pour générer une image de pergola.
     *
     * L'image A est obligatoire.
     * L'image B est requise uniquement si le mode est "2image".
     *
     * Étapes :
     * - validation du base64
     * - conversion en Blob
     * - création du FormData
     * - envoi à l'API
     *
     * @param {string} imageA - Image principale en base64
     * @param {string|null} imageB - Deuxième image optionnelle en base64
     *
     * @returns {Promise<Object>} Réponse de l'API contenant l'image générée
     *
     * @throws {Error} Si les images sont invalides
     *
     * @example
     * const result = await apiService.generateImage(base64Image);
     */
    async generateImage(imageA, imageB = null)
    {
        const formData = new FormData();
        formData.append('model', this.currentModel);
        formData.append('mode', this.currentMode);

        if (typeof imageA !== 'string' || imageA.trim() === '' || !/^[A-Za-z0-9+/=]+$/.test(imageA))
        {
            throw new Error('Image A invalide ou manquante (doit être un base64 valide)');
        }

        const base64A = imageA;
        const imageABlob = this.base64ToBlob(base64A);
        formData.append('image', imageABlob, 'imageA.png');

        // mode est '2image' et imageB fournie, ajoute-la
        if ((this.currentMode === '2image' || this.currentMode === 'custom') && imageB)
        {
            const imageBBlob = this.base64ToBlob(imageB);
            formData.append('second_image', imageBBlob, 'imageB.png');
        }
        else if (this.currentMode === '2image' && !imageB)
        {
            throw new Error('Image B requise pour mode 2image');  // Erreur si manquante
        }

        //Appelle la méthode privée pour envoyer la requête
        return this.#fetchApi('/generate', formData);
    }

    /**
     * Analyse une image de pergola afin de générer une description.
     *
     * Cette méthode :
     * - convertit l'image base64 en Blob
     * - envoie l'image à l'endpoint /describe
     * - récupère la description générée par l'IA
     *
     * @param {string} imageB - Image en base64 à analyser
     *
     * @returns {Promise<string>} Description générée par l'IA
     *
     * @throws {Error} Si l'image est invalide
     *
     * @example
     * const description = await apiService.describePergola(base64Image);
     */
    async describePergola(imageB)
    {

        const formData = new FormData();
        formData.append('mode', 'custom');  // Fixed, comme requis par ton contrôleur

        if (typeof imageB !== 'string' || imageB.trim() === '' || !/^[A-Za-z0-9+/=]+$/.test(imageB))
        {
            throw new Error('Image B invalide ou manquante (doit être un base64 valide)');
        }

        //Convertit imageB
        const imageBBlob = this.base64ToBlob(imageB);
        formData.append('second_image', imageBBlob, 'imageB.png');


        const response = await this.#fetchApi('/describe', formData);  // Appelle privée
        return response.description;
    }

    /**
     * Convertit une image encodée en base64 en objet Blob.
     *
     * Cette conversion est nécessaire pour pouvoir envoyer l'image
     * dans un FormData vers l'API.
     *
     * Étapes :
     * - nettoyage du base64
     * - correction du padding
     * - décodage en bytes
     * - création d'un Blob
     *
     * @param {string} base64 - Image encodée en base64
     * @param {string} [mimeType='image/png'] - Type MIME du fichier
     *
     * @returns {Blob} Blob utilisable dans FormData
     *
     * @example
     * const blob = apiService.base64ToBlob(base64Image);
     */
    base64ToBlob(base64, mimeType = 'image/png')
    {
        base64 = base64.replace(/[^A-Za-z0-9+/=]/g, '');

        const mod = base64.length % 4;

        if (mod !== 0) {
            base64 += '='.repeat(4 - mod);
        }

        const byteCharacters = atob(base64);
        const byteArrays = [];
        for (let offset = 0; offset < byteCharacters.length; offset += 512) {
            const slice = byteCharacters.slice(offset, offset + 512);
            const byteNumbers = new Array(slice.length);
            for (let i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            byteArrays.push(byteArray);
        }
        return new Blob(byteArrays, { type: mimeType });
    }
}
export {ApiService}
