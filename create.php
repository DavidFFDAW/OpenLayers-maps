<?php include dirname(__FILE__).DIRECTORY_SEPARATOR.'includes/headers.php'; ?>

<style>
.w{width: 100% !important}
.flx.btw { display: flex; flex-direction: row; justify-content: space-between; align-items: center; }
.p {box-sizing: border-box; padding: 10px; }
.m {margin: 10px 0; }
.corner.labeled.input.w{ padding: 20px 15px; }
/* #stroke {width: 30px;} */
#map {position: absolute; z-index: 5; width: 100%;top: 0;left: 0;border-radius: 50px;height: 35vw;}
.ol-rotate.ol-unselectable.ol-control.ol-hidden, .ol-zoomslider.ol-unselectable.ol-control {display: none;}
.grrf { display: flex; justify-content: center; align-items: center; flex-wrap: wrap;}
.grrf .color {width: 100%; height: 55px; border-radius: 10px;  transition: all 0.2s; outline: none; cursor:pointer;}
.grrf .colololo {margin: 0 10px;}
.grrf .color:hover {border-radius: 2px; opacity: 0.4;}
.grrf .ceferino {font-size: 12px;color: grey;text-align: center;transition: all 0.2s; cursor: pointer;}
.grrf .ceferino:hover {font-size: 13px;}
</style>

<script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.14.1/build/ol.js"></script>

<div style="box-sizing: border-box; padding: 5%;">
    <!-- <h3 class="ui center aligned header">Creacion de evento</h3> -->
    <div class="ui two column doubling grid">
        <div class="column">
            <h3>Opciones</h3>
            <div class="ui styled fluid accordion p m">
                <div class="title" onclick="openAccordionChangeColor(event,'data')">
                    <i class="dropdown icon"></i>
                    <i class="circular orange info icon"></i>            
                    Datos
                </div>

                <div class="content" data-step="data">
                    <div class="ui corner labeled input w">
                        <label for="" class="ui label orange">Nombre</label>
                        <input type="text" placeholder="Nombre">
                    </div>
                    <div class="ui corner labeled input w">
                        <label for="" class="ui label orange">Fecha de evento</label>
                        <input type="date" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="ui corner labeled input w">
                        <label for="" class="ui label orange">Localización</label>
                        <input type="text" placeholder="Localización">
                    </div>
                </div>
            </div>
            <div class="ui styled fluid accordion p m">
                <div class="title" onclick="openAccordionChangeColor(event,'route')">
                    <i class="dropdown icon"></i>
                    <i class="teal circular truck icon"></i>            
                    Ruta
                </div>

                <div class="content ui form" data-step="route">
                    <div class="field">
                        <label>Ruta</label>
                        <textarea rows="2" id="route" style="height:300px;"></textarea>
                    </div>
                </div>
            </div>
            <div class="ui styled fluid accordion p m">
                <div class="title" onclick="openAccordionChangeColor(event,'map')">
                    <i class="dropdown icon"></i>
                    <i class="teal circular map icon"></i>            
                    Opciones de Mapa
                </div>

                <div class="content" data-step="map">
                    <div class="ui input w p">
                        <label for="" class="ui label dark">Kilómetros</label>
                        <label class="ui label blue"><span id="kms">0</span> kilometros</label>
                    </div>
                    <div class="ui input w p">
                        <label for="" class="ui label dark">Colores usados</label><br/>
                        <div class="grrf" id="used-colors">

                        </div>
                    </div>
                    <div class="ui input w p">
                        <label for="" class="ui label dark">Color</label>
                        <input type="color" placeholder="#5aad6c" value="#5aad6c" id="color" onchange="changeColor(event)">
                    </div>
                    <div class="flx btw">
                        <div class="ui input p">
                            <label for="" class="ui label dark">Stroke</label>
                            <input type="number" inputmode="numeric" placeholder="4" value="4" id="stroke" onchange="changeColor(event)">
                        </div>
                    </div>
                    <div class="flx btw">
                        <div class="ui input p">
                            <label for="" class="ui label dark">Coordenadas</label>
                            <input type="number" inputmode="numeric" placeholder="-494808.6826199734" value="-494808.6826199734" id="xaxis" onchange="moveCoords(true)">
                            <input type="number" inputmode="numeric" placeholder="4400872.161600239" value="4400872.161600239" id="yaxis" onchange="moveCoords(true)">
                            <button class="ui orange button" type="button" onclick="changeType()">Cambiar coordenadas iniciales</button>
                        </div>
                    </div>
                    <div class="flx btw">
                        <div class="ui input p">
                            <button class="ui teal button" type="button">Autocerrar ruta</button>
                        </div>
                    </div>
                    <div class="flx btw">
                        <div class="ui input p">
                            <button class="ui red button" type="button">Deshacer última ruta</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="column" style="position: sticky; top: 50px; left: 50px;">
            <h3>Mapa</h3>
            <div class="ui segment" style="position: relative; width: 100%; min-height: 250px;">
                <div style="width: 100%; min-height: 250px;" id="map"></div>
            </div>
        </div>
    </div>
</div>

<script src="./Colors.js"></script>
<script src="./Globals.js"></script>
<script src="./MapWrapper.js"></script>
<script>    
    function openAccordionChangeColor(ev, step) {
        ev.preventDefault();
        const allIcons = [...document.querySelectorAll('div.ui.styled.fluid.accordion .title .circular')];
        const allContents = [...document.querySelectorAll('div.ui.styled.fluid.accordion .content.active')];

        allIcons.forEach(icon => {
            icon.classList.remove('orange');
            icon.classList.add('teal');
        });

        
        allContents.filter(it => it.dataset.step !== step).forEach(content => {
                content.classList.remove('active');
        });

        const current = ev.target.children[1];
        const currentContent = ev.target.nextElementSibling;
        // console.log(currentContent);
        current.classList.remove('teal');
        current.classList.add('orange');
    }


    // var points = [],
    //     msg_el = document.getElementById('msg'),
    //     url_osrm_nearest = 'https://router.project-osrm.org/nearest/v1/driving/',
    //     url_osrm_route = 'https://router.project-osrm.org/route/v1/driving/',
    //     icon_url = './arrow-down-circle-fill.svg',
    //     vectorSource = new ol.source.Vector(),
    //     vectorLayer = new ol.layer.Vector({
    //         source: vectorSource
    //     }),
    //     styles = {
    //         route: new ol.style.Style({
    //             stroke: new ol.style.Stroke({
    //                 width: 6, color: [40, 40, 40, 0.8]
    //         })
    //     }),
    //     icon: new ol.style.Style({
    //         image: new ol.style.Icon({
    //             anchor: [0.5, 1],
    //         src: icon_url
    //         })
    //     })
    // };

    const globals = new Globals();
    const usedColors = new UsedColors('used-colors');

    const textarea = document.getElementById('route');
    const kilometers = document.getElementById('kms');
    var initialCoords = [-494808.6826199734, 4400872.161600239];
    var previousCoords = [];
    var globalColor = '#5aad6c';
    var globalStrokeWidth = 4;
    var coordinates = [];

    usedColors.add(globalColor);

    const wrapper = new MapWrapper(initialCoords, 17, true);
    const map = wrapper.init();
    
    const changeColorCb = col=> { wrapper.globalColor = col; }
    usedColors.setChangeColorCallback(changeColorCb);


    map.getControls().forEach(control => {
        map.removeControl(control);        
    });

    function changeType() {
        globals.set('move', true);
        console.log(globals.globals);
    }

    map.on('click', async event => {
        if (globals.get('routing')) {

            console.log(event.coordinate);
            const coords = event.coordinate;    
            const lastCoords = previousCoords[previousCoords.length - 1] || coords;
            const points = [lastCoords, coords];

            coordinates = [...coordinates, {
                line: points,
                color: wrapper.globalColor,
                stroke: wrapper.globalStrokeWidth,
                street: await getRoadName(coords),
            }];

            previousCoords = [...previousCoords, coords ];

            // const marker = wrapper.createMarkerAt(ol.proj.toLonLat(coords));
            const line = wrapper.createLineStringBetweenTwoPoints(points);
            // const newKilometerCalculation = wrapper.calculateKilometersBasedOnCoordinates(points);
            // kilometers.innerText = Number(kilometers.innerText) + newKilometerCalculation;
            
            wrapper.addVectorLayer(line);
            textarea.value = JSON.stringify(coordinates, null, 2);

        } else 
            if (globals.get('move')) {
                moveCoords(false, event.coordinate);
                document.getElementById('xaxis').value = event.coordinate[0];
                document.getElementById('yaxis').value = event.coordinate[1];     
        }
        // textarea.parentElement.parentElement.classList.add('active');
        // if (textarea.style.height !== '500px') {
            //     textarea.style.height = Number(textarea.style.height.replace('px', '')) + 20 + 'px';
        // }
    });

    function moveCoords (coordsFromInputs, coords = []) {
        const x = coordsFromInputs ? document.getElementById('xaxis').value : coords[0];
        const y = coordsFromInputs ? document.getElementById('yaxis').value : coords[1];
        const finalNewCoords = [Number(x), Number(y)];
        map.getView().setCenter(finalNewCoords);
    }

    // const createMarkerAt = (coords) => {
    //     coords[1] = coords[1] + 0.00001;
    //     const circle = new ol.Feature({
    //         geometry : new ol.geom.Point(ol.proj.fromLonLat(coords)),
    //         labelPoint: new ol.geom.Point(ol.proj.fromLonLat(coords)),
    //         name: 'My Point',
    //         size : 10
    //     });

    //     const vectorMarker = new ol.source.Vector({});
    //     vectorMarker.addFeature(circle);

    //     const vectorMarkerLayer = new ol.layer.Vector({
    //         source: vectorMarker,
    //         style: new ol.style.Style({
    //             image: new ol.style.Icon({
    //                 anchor: [0.2, 0.5],
    //                 src: icon_url
    //             })
    //         }),
    //         name: 'marker',
    //         type: 'Marker'
    //     });
    //     return vectorMarkerLayer;
    // }


    function changeColor (ev) {
        const color = document.getElementById('color').value;
        const stroke = Number(document.getElementById('stroke').value);

        wrapper.globalColor = color;
        console.log(wrapper.globalColor);
        wrapper.globalStrokeWidth = stroke;

        usedColors.add(color);
    }

    // const layers = [
    //     new ol.layer.Tile({
    //         source: new ol.source.OSM()
    //     }),
    //     vectorLayer,
    // ];
    // const viewOptions = {
    //     center: initialCoords,
    //     zoom: 16,
    //     // minZoom: 14,
    //     maxZoom: 20,
    //     zoomAnimation: true,
    //     zoomFactor: 2,
        
    // };
    // const mapOptions = { target: 'map', layers: layers, view: new ol.View(viewOptions) };


    // // Añade linea en el mapa.
    // map.on('click', async function(evt) {
    //     const coords = evt.coordinate;
    //     // moveCoords(coords);
    //     const lastCoords = previousCoords[previousCoords.length - 1] || coords;
    //     const points = [lastCoords, coords];       
    //     console.log(lastCoords);
    //     console.log(previousCoords);


    //     coordinates = [...coordinates, {
    //         line: points,
    //         color: globalColor,
    //         stroke: globalStrokeWidth,
    //         street: await getRoadName(coords),
    //     }];

    //     previousCoords = [...previousCoords, coords ];

    //     const marker = createMarkerAt(ol.proj.toLonLat(coords));
    //     const line = createLineStringBetweenTwoPoints(points, globalStrokeWidth, globalColor);
        
    //     map.addLayer(line);
    //     map.addLayer(marker);
    // }); 

    async function getRoadName (coords) {
        const [lon,lat] = ol.proj.toLonLat(coords);
        const url = `http://nominatim.openstreetmap.org/reverse?format=json&lon=${lon}&lat=${lat}`;
        const response = await fetch(url);
        const data = await response.json();
        return { fullName: data.display_name || 'Probably water', road: data.address.road || 'Probably water' };
    }

    
    // function loadRoute () {// window.addEventListener('load', function (evt) {
    //     const loadedRoute = JSON.parse(window.localStorage.getItem('route'));
        
    //     loadedRoute.forEach( 
    //         ({ line, stroke, color }) => {
    //             const finalLine = createLineStringBetweenTwoPoints(line, stroke, color);
    //             map.addLayer(finalLine);
    //         }
    //     );
    // }
    // // window.onload = loadRoute;

    // function saveRoute () {
    //     // window.localStorage.setItem('route', JSON.stringify(coordinates));

    //     const form = document.createElement('form');
    //     form.setAttribute('method', 'POST');
    //     // form.setAttribute('action', '/');
    //     const inpt = document.createElement('input');
    //     inpt.setAttribute('type', 'hidden');
    //     inpt.setAttribute('name', 'route');
    //     inpt.setAttribute('value', JSON.stringify(coordinates));

    //     form.appendChild(inpt);
    //     document.body.appendChild(form);
    //     form.submit();
    // }

    // function previousMove () {
    //     coordinates = coordinates.slice(0,-1);
    //     previousCoords = previousCoords.slice(0, -1);
    //     const layersArray = map.getLayers().getArray();
    //     const reversed = [...layersArray].reverse();
    //     // will get the first direction of the array reversed which is the actual last direction
    //     const foundLayer = reversed.find( layer => layer.get('name') === 'direction');
        
    //     if (foundLayer) map.removeLayer(foundLayer);
    // }

    // function deleteEntireRoute () {
    //     coordinates = [];
    //     previousCoords = [];
    //     map.getLayers().getArray()
    //         .filter( layer => layer.get('name') === 'direction')
    //         .forEach( item => {
    //             map.removeLayer(item);
    //         }
    //     );
    // }

    // function moveCoords (coords) {
    //     console.log(coords);
    //     console.log(map.getProjection());
    //     var olCoordinates = ol.proj.transform(coords, 'EPSG:4326', 'EPSG:3857')
    //     console.log(olCoordinates);
    //     map.getView().setCenter(olCoordinates);
    //     // map.getView().setZoom(10);
    // }
    
</script>

<?php include dirname(__FILE__).DIRECTORY_SEPARATOR.'includes/footer.php'; ?>