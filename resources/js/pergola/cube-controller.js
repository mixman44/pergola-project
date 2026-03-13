import * as THREE from 'three';

// Classe pour le cube en fil de fer
export class cubeController {

    constructor(color = 0xff0000 , thickness = 0.02) {
        this.color = color;
        this.thickness = thickness;
        this.originalPoints = [
            // Dessus du cube (rectangle)
            new THREE.Vector3(-0.5, 0.5, -0.5),
            new THREE.Vector3(0.5, 0.5, -0.5),

            new THREE.Vector3(0.5, 0.5, -0.5),
            new THREE.Vector3(0.5, 0.5, 0.5),

            new THREE.Vector3(0.5, 0.5, 0.5),
            new THREE.Vector3(-0.5, 0.5, 0.5),

            new THREE.Vector3(-0.5, 0.5, 0.5),
            new THREE.Vector3(-0.5, 0.5, -0.5),

            // Les quatre pieds (verticales)
            new THREE.Vector3(-0.5, 0.5, -0.5),
            new THREE.Vector3(-0.5, -0.5, -0.5),

            new THREE.Vector3(0.5, 0.5, -0.5),
            new THREE.Vector3(0.5, -0.5, -0.5),

            new THREE.Vector3(0.5, 0.5, 0.5),
            new THREE.Vector3(0.5, -0.5, 0.5),

            new THREE.Vector3(-0.5, 0.5, 0.5),
            new THREE.Vector3(-0.5, -0.5, 0.5),
        ];
        this.mesh = new THREE.Group();  // Groupe pour contenir les cylindres
        this.#createMeshes(this.originalPoints);
    }

    //constui le cube
    #createMeshes(points)
    {
        // Supprime les anciens enfants si existants
        while (this.mesh.children.length > 0)
        {
            const child = this.mesh.children[0];
            child.geometry.dispose();
            child.material.dispose();
            this.mesh.remove(child);
        }

        const material = new THREE.MeshBasicMaterial({ color: this.color });

        for (let i = 0; i < points.length; i += 2)
        {
            const start = points[i];
            const end = points[i + 1];
            const direction = new THREE.Vector3().subVectors(end, start);
            const length = direction.length();

            const geometry = new THREE.CylinderGeometry(this.thickness, this.thickness, length, 8);
            const cylinder = new THREE.Mesh(geometry, material);

            // Position au milieu et orientation
            const midpoint = new THREE.Vector3().addVectors(start, end).multiplyScalar(0.5);
            cylinder.position.copy(midpoint);
            const quaternion = new THREE.Quaternion().setFromUnitVectors(new THREE.Vector3(0, 1, 0), direction.clone().normalize());
            cylinder.quaternion.copy(quaternion);

            this.mesh.add(cylinder);
        }
    }

    // Met à jour la taille du cube
    updateSize(scaleX = 1, scaleY = 1, scaleZ = 1) {
        const newPoints = this.originalPoints.map(point => {
            return new THREE.Vector3(
                point.x * scaleX,  // Largeur (axe X)
                point.y * scaleY,  // Hauteur (axe Y)
                point.z * scaleZ   // Profondeur (axe Z)
            );
        });

        this.#createMeshes(newPoints);
    }

    //ajoute le cube a la saine
    addToScene(scene)
    {
        scene.add(this.mesh);
    }

    //retire le cube de la sain
    dispose()
    {
        this.geometry.dispose();
        this.material.dispose();
    }
}
