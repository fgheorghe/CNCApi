var STLViewer = function(canvas) {
    this.setCanvas(canvas);
    this.init();
};

STLViewer.prototype.setCanvas = function(canvas) {
    this.canvas = canvas;
    return this;
};

STLViewer.prototype.getCanvas = function() {
    return this.canvas;
};

STLViewer.prototype.setLight = function(light) {
    this.light = light;
    return this;
};

STLViewer.prototype.getLight = function() {
    return this.light;
};

STLViewer.prototype.setCamera = function(camera) {
    this.camera = camera;
    return this;
};

STLViewer.prototype.getCamera = function() {
    return this.camera;
};

STLViewer.prototype.setScene = function(scene) {
    this.scene = scene;
    return this;
};

STLViewer.prototype.setEngine = function(engine) {
    this.engine = engine;
    return this;
};

STLViewer.prototype.getScene = function() {
    return this.scene;
};

STLViewer.prototype.getEngine = function() {
    return this.engine;
};

STLViewer.prototype.init = function() {
    this.initEngine();
    // Scene initialisation triggers light set-up and rendering.
    this.initScene();
};

STLViewer.prototype.initEngine = function() {
    this.setEngine(new BABYLON.Engine(this.getCanvas(), true));
};

// As per: https://css-tricks.com/snippets/javascript/get-url-variables/
STLViewer.prototype.getQueryVariable = function(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
    }
    return(false);
};

STLViewer.prototype.initScene = function() {
    // Prepare...
    this.setScene(new BABYLON.Scene(this.getEngine()));
    this.getScene().clearColor = new BABYLON.Color3(0, 0, 0);

    // ... and load STL file data.
    $.ajax({
        url: "/stl/get?id=" + this.getQueryVariable('id') + "&column=" + this.getQueryVariable('column'),
        success: function(response) {
            // Render facets.
            for (var i = 0; i < response["facets"].length; i++) {
                BABYLON.Mesh.CreateLines("par", [
                    new BABYLON.Vector3(response["facets"][i]["vertex"][0][0], response["facets"][i]["vertex"][0][1], response["facets"][i]["vertex"][0][2]),
                    new BABYLON.Vector3(response["facets"][i]["vertex"][1][0], response["facets"][i]["vertex"][1][1], response["facets"][i]["vertex"][1][2]),
                    new BABYLON.Vector3(response["facets"][i]["vertex"][2][0], response["facets"][i]["vertex"][2][1], response["facets"][i]["vertex"][2][2]),
                    new BABYLON.Vector3(response["facets"][i]["vertex"][0][0], response["facets"][i]["vertex"][0][1], response["facets"][i]["vertex"][0][2])
                ], this.getScene());
            }

            // Continue initialization.
            this.initCamera();
            this.initLight();
            // Render scene.
            this.getEngine().runRenderLoop(function () {
                this.getScene().render();
            }.bind(this));

            // Handle window resize events.
            window.addEventListener("resize", function () {
                this.getEngine().resize();
            }.bind(this));
        }.bind(this)
    });
};

STLViewer.prototype.initCamera = function() {
    this.setCamera(new BABYLON.ArcRotateCamera("main-camera",  0, 0, 0, new BABYLON.Vector3(0, 0, 0), this.getScene()));
    this.getCamera().setPosition(new BABYLON.Vector3(
        0,
        40,
        -200));
    this.getCamera().attachControl(this.getCanvas(), true);
};

STLViewer.prototype.initLight = function() {
    this.setLight(new BABYLON.HemisphericLight("main-light", new BABYLON.Vector3(1, 0.5, 0), this.getScene()));
    this.getLight().intensity = 0.7;
};