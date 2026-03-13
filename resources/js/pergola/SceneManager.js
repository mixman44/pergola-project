import  * as THREE from "three";
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { cubeController } from "./cube-controller.js";

class SceneManager
{
    constructor()
    {
        this.scene    = null;
        this.camera   = null;
        this.renderer = null;
        this.controls = null;
        this.cubeControleur = null;
    }

    init()
    {
        const canvasContainer = document.querySelector('.canvas-container');
        const iw = canvasContainer.clientWidth;
        const ih = canvasContainer.clientHeight;
        const canvas = document.querySelector('#canvas');

        this.scene = new THREE.Scene();

        // Caméra perspective
        this.camera = new THREE.PerspectiveCamera(70, iw / ih, 0.1, 1000);
        this.camera.position.set(2, 2, 3);
        this.camera.lookAt(0, 0, 0);

        // Renderer avec fond transparent
        this.renderer = new THREE.WebGLRenderer({
            canvas,
            alpha: true,
            preserveDrawingBuffer: true   // nécessaire pour les screenshots
        });

        this.renderer.setSize(iw, ih, false);
        this.renderer.setClearColor(0x000000, 0);
        this.renderer.render(this.scene, this.camera);

        // Contrôles orbitaux (rotation, zoom à la souris)
        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping  = true;
        this.controls.dampingFactor  = 0.05;

        // Ajout du cube à la scène
        this.cubeControleur = new cubeController();
        this.cubeControleur.addToScene(this.scene);

        window.addEventListener('resize', () => {
            const iw = canvasContainer.clientWidth;
            const ih = canvasContainer.clientHeight;
            this.camera.aspect = iw / ih;
            this.camera.updateProjectionMatrix();
            this.renderer.setSize(iw, ih, false);
        });
    }

    /**
     * Met à jour les dimensions du cube via le CubeControleur.
     * Appelé par Vue lors d'un changement de slider.
     */
    updateCubeSize(x, y, z)
    {
        this.cubeControleur.updateSize(x, y, z);
        this.render();
    }
    /**
     * Effectue un rendu
     */
    render()
    {
        this.renderer.render(this.scene, this.camera);
    }
    /**
     * Lance la boucle d'animation principale.
     * À appeler une seule fois après init().
     */
    startAnimationLoop()
    {
        const animate = () => {
            requestAnimationFrame(animate);
            this.controls.update();
            this.renderer.render(this.scene, this.camera);
        };
        animate();
    }

    /**
     * Retourne l'élément DOM canvas du renderer.
     */
    getCanvasElement()
    {
        return this.renderer.domElement;
    }
}

export { SceneManager };
