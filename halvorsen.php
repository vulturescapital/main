<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halvorsen Attractor Visualization</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.0/p5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dat-gui/0.7.6/dat.gui.min.js"></script>
    <style>
        body {
            margin: 0;
            overflow: hidden;
        }

        canvas {
            display: block;
        }
    </style>
</head>
<body>
<script>
    let particles = [];
    let points = [];
    let attractor;
    let NUM_POINTS = 3500; // Number of points in the curve

    let parDef = {
        Speed: 1.0,
        Particles: true,
        Preset: function () {
            this.Speed = 1.0;
            this.Particles = true;
            attractor.a = 1.89;
            attractor.x = -1.48;
            attractor.y = -1.51;
            attractor.z = 2.04;
            initSketch();
        },
        Randomize: randomCurve,
    };

    function setup() {
        attractor = new HalvorsenAttractor();
        let gui = new dat.GUI();
        gui.add(parDef, 'Speed', 0, 3, 0.01).listen();
        gui.add(parDef, 'Particles');
        gui.add(parDef, 'Preset');
        gui.add(parDef, 'Randomize');

        createCanvas(windowWidth, windowHeight, WEBGL);
        colorMode(HSB);
        initSketch();
    }

    function windowResized() {
        resizeCanvas(windowWidth, windowHeight);
    }

    function randomCurve() {
        attractor.randomize();
        initSketch();
    }

    function initSketch() {
        points = [];
        for (let i = 0; i < NUM_POINTS; i++) {
            let p = attractor.generatePoint();
            points.push(new p5.Vector(p.x, p.y, p.z));
        }
    }

    function draw() {
        background(0);
        translate(0, 0, 750);

        beginShape();
        noFill();
        stroke(255);
        strokeWeight(0.1);
        for (let v of points) {
            vertex(v.x, v.y, v.z);
        }
        endShape();
    }

    class HalvorsenAttractor {
        constructor() {
            this.a = 1.89;
            this.x = -1.48;
            this.y = -1.51;
            this.z = 2.04;
            this.h = 0.009;
            this.speed = 1.0;
        }

        generatePoint() {
            let nx = this.speed * (-this.a * this.x - 4 * this.y - 4 * this.z - this.y ** 2);
            let ny = this.speed * (-this.a * this.y - 4 * this.z - 4 * this.x - this.z ** 2);
            let nz = this.speed * (-this.a * this.z - 4 * this.x - 4 * this.y - this.x ** 2);
            this.x += this.h * nx;
            this.y += this.h * ny;
            this.z += this.h * nz;
            return {x: this.x, y: this.y, z: this.z}
        }

        randomize() {
            this.a = random(0.1, 3);
            this.x = random(-5, 5);
            this.y = random(-5, 5);
            this.z = random(-5, 5);
        }
    }
</script>
</body>
</html>