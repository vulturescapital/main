<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Three.js</title>
    <style>
        body { margin: 0; }
        canvas { display: block; }
    </style>
</head>
<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        // Création de la scène
        var scene = new THREE.Scene();

        // Création de la caméra
        var camera = new THREE.PerspectiveCamera(75, window.innerWidth/window.innerHeight, 0.1, 1000);

        // Création du rendu
        var renderer = new THREE.WebGLRenderer();
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);

        // Création d'une géométrie cube
        var geometry = new THREE.BoxGeometry(1, 1, 1);
        var material = new THREE.MeshBasicMaterial({color: 0x00ff00});
        var cube = new THREE.Mesh(geometry, material);

        // Ajout du cube à la scène
        scene.add(cube);

        // Positionnement de la caméra
        camera.position.z = 5;

        // Fonction d'animation
        var animate = function () {
            requestAnimationFrame(animate);

            // Rotation du cube
            cube.rotation.x += 0.01;
            cube.rotation.y += 0.01;

            // Rendu de la scène avec la caméra
            renderer.render(scene, camera);
        };

        // Appel de la fonction d'animation
        animate();
    </script>
</body>
</html>

