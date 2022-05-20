class MapWrapper { 
    constructor(coords, zoom = 16, zoomSlider = true) { 
        this.coords = coords;
        this.zoom = zoom;
        this.map = [];
        this.zoomSlider = zoomSlider;
        this.globalStrokeWidth = '4';
        this.globalColor = '#5aad6c';
        this.coordinates = [];
        this.icon_url = './marker.svg';
    }

    init() {
        this.vectorSource = new ol.source.Vector();
        this.vectorLayer = new ol.layer.Vector({
            source: this.vectorSource
        });

        this.map = new ol.Map({
            target: 'map',
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                }),
                this.vectorLayer
            ],
            view: new ol.View({
                center: this.coords,
                zoom: this.zoom,
                maxZoom: 20,
                minZoom: 10,
                zoomAnimation: true
            })
        });
        
        if (this.zoomSlider) {            
            this.addZoomSlider();        
        }

        return this.map;
    }

    addZoomSlider() { 
        this.map.addControl(new ol.control.ZoomSlider());
    }

    createLineStringBetweenTwoPoints (pointsArray, stroke, color) {
        const finalColor = color || this.globalColor;
        const finalStroke = stroke || this.globalStrokeWidth;

        const featureLine = new ol.Feature({
            geometry: new ol.geom.LineString(pointsArray)
        });

        const vectorLine = new ol.source.Vector({});
        vectorLine.addFeature(featureLine);
        
        const vectorLineLayer = new ol.layer.Vector({
            source: vectorLine,
            style: new ol.style.Style({
                fill: new ol.style.Fill({ color: finalColor, weight: finalStroke }),
                stroke: new ol.style.Stroke({ color: finalColor, width: finalStroke })
            }),
            name: 'direction',
            type: 'DirectionLine'
        });

        return vectorLineLayer;
    }

    addVectorLayer(...vectorLayer) {
        for (const layer of vectorLayer) {
            this.map.addLayer(layer);
        }
    }

    createMarkerAt(coords) {
        // coords[1] = coords[1] + 0.00002;
        // coords[0] = coords[0] - 0.00001;
        const circle = new ol.Feature({
            geometry : new ol.geom.Point(ol.proj.fromLonLat(coords)),
            labelPoint: new ol.geom.Point(ol.proj.fromLonLat(coords)),
            name: 'Marker',
            size : 10
        });

        const vectorMarker = new ol.source.Vector({});
        vectorMarker.addFeature(circle);

        const vectorMarkerLayer = new ol.layer.Vector({
            source: vectorMarker,
            style: new ol.style.Style({
                image: new ol.style.Icon({
                    anchor: [0.2, 0.5],
                    src: this.icon_url
                })
            }),
            name: 'marker',
            type: 'Marker'
        });
        return vectorMarkerLayer;
    }

    
}