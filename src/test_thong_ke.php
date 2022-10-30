<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>OpenStreetMap &amp; OpenLayers - Marker Example</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
        <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
        
        <link rel="stylesheet" href="http://localhost:8081/libs/openlayers/css/ol.css" type="text/css" />
        <script src="http://localhost:8081/libs/openlayers/build/ol.js" type="text/javascript"></script>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
        
        <script src="http://localhost:8081/libs/jquery/jquery-3.4.1.min.js" type="text/javascript"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

        <style>
            /*
            .map, .righ-panel {
                height: 500px;
                width: 80%;
                float: left;
            }
            */
            /* .map, .righ-panel {
                height: 98vh;
                width: 80vw;
                float: left;
            } */
            .map {
                position: relative;
                /* border: 1px solid #000; */
            }
            #info{
                width: 200px;
                position: absolute;
                right: 10px;
                top: 50px;
                z-index: 1;
                background-color: #f2f3f4;
                min-height: 250px;
                border-radius: 5px;
                /* display: none; */
            }
            .header-infor{
                text-align: center;
                background-color: #0892d0;
                color: #f2f3f4;
                margin: 0;
                padding: 0.5px;
                border-radius: 5px 5px 0 0;
                position: relative;
            }
            /* .header-infor > i{
                color: #333;
                position: absolute;
                right: 10px;
                top: 40%;
                
            } */
            .content-info{
                margin-left: 40px;
            }
            .infor-fail > p {
                text-align: center;
            }
        </style>
    </head>
    <body onload="initialize_map();">
        
                    <div id="map" class="map">
                        <div id="info">
                        </div>
                    </div>
                    <!--<div id="map" style="width: 80vw; height: 100vh;"></div>-->
                
                    
                    <!-- <button>Button</button> -->
              
        <?php include 'inforAPItest.php' ?>
        <script>
        //$("#document").ready(function () {
            var format = 'image/png';
            var map;
            var minX = 102.107955932617;
            var minY = 8.30629730224609;
            var maxX = 109.505798339844;
            var maxY = 23.4677505493164;
            var cenX = (minX + maxX) /2;
            var cenY = (minY + maxY) /2;
            var mapLat = cenY;
            var mapLng = cenX;
            var mapDefaultZoom = 6;
            function initialize_map() {
                //*
                layerBG = new ol.layer.Tile({
                    source: new ol.source.OSM({})
                });
                //*/
                var layerCMR_adm1 = new ol.layer.Image({
                    source: new ol.source.ImageWMS({
                        ratio: 1,
                        url: 'http://localhost:8080/geoserver/example/wms?',
                        params: {
                            'FORMAT': format,
                            'VERSION': '1.1.1',
                            STYLES: '',
                            LAYERS: 'gadm41_vnm_1',
                        }
                    })
                });
                //thêm dlieu covid
                var  layerCovid = new ol.layer.Image({
                    source: new ol.source.ImageWMS({
                        ratio: 1,
                        url: 'http://localhost:8080/geoserver/example/wms?',
                        params: {
                            'FORMAT': format,
                            'VERSION': '1.1.1',
                            STYLES: '',
                            LAYERS: 'dlieu_point',
                        }
                    })
                });
                var viewMap = new ol.View({
                    center: ol.proj.fromLonLat([mapLng, mapLat]),
                    zoom: mapDefaultZoom
                    //projection: projection
                });
                map = new ol.Map({
                    target: "map",
                    layers: [layerBG, layerCMR_adm1, layerCovid],
                    //layers: [layerCMR_adm1],
                    view: viewMap
                });
                //map.getView().fit(bounds, map.getSize());
                
                var styles = {
                    'MultiPolygon': new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: '#0892d0  ', 
                            width: 2
                        })
                    })
                };
                var styleFunction = function (feature) {
                    return styles[feature.getGeometry().getType()];
                };
                var vectorLayer = new ol.layer.Vector({
                    //source: vectorSource,
                    style: styleFunction
                });
                map.addLayer(vectorLayer);

        
                function displayObjInfo(result, coordinate)
                {
                    //alert("result: " + result);
                    //alert("coordinate des: " + coordinate);
					$("#info").html(result);
                }
                function displayInfoCovid() {
                    document.getElementById('info').style.display = 'block';
                }
                function highLightGeoJsonObj(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
					vectorLayer.setSource(vectorSource);
                    /*
                    var vectorLayer = new ol.layer.Vector({
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                    */
                }
                function highLightObj(result) {
                    //alert("result: " + result);
                    var strObjJson = createJsonObj(result);
                    //alert(strObjJson);
                    var objJson = JSON.parse(strObjJson);
                    //alert(JSON.stringify(objJson));
                    //drawGeoJsonObj(objJson);
                    highLightGeoJsonObj(objJson);
                }
                map.on('singleclick', function (evt) {
                    //alert("coordinate org: " + evt.coordinate);
                    //var myPoint = 'POINT(12,5)';
                    var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                    var lon = lonlat[0];   
                    var lat = lonlat[1];
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    //alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "inforAPItest.php",
                        //dataType: 'json',
                        //data: {functionname: 'reponseGeoToAjax', paPoint: myPoint},
                        data: {functionname: 'getInfoCMRToAjax', paPoint: myPoint},
                        success : function (result, status, erro) {
                            displayObjInfo(result, evt.coordinate );
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);

                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: "inforAPItest.php",
                        //dataType: 'json',
                        data: {functionname: 'getGeoCMRToAjax', paPoint: myPoint},
                        success : function (result, status, erro) {
                            highLightObj(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                    displayInfoCovid()
                    //*/
                });
               
                    // Thống kê các vùng, khi click vào tên trong list thống kê, hiển thị bản đồ ở đó
                function displayGeoStatistic(result) {
                    layerBG = new ol.layer.Tile({
                        source: new ol.source.OSM({})
                    });
                        //*/
                        var layerCMR_adm1 = new ol.layer.Image({
                            source: new ol.source.ImageWMS({
                                ratio: 1,
                                url: 'http://localhost:8080/geoserver/example/wms?',
                                params: {
                                    'FORMAT': format,
                                    'VERSION': '1.1.1',
                                    STYLES: '',
                                    LAYERS: 'gadm41_vnm_1',
                                }
                            })
                        });
                        //thêm dlieu covid
                        var  layerCovid = new ol.layer.Image({
                            source: new ol.source.ImageWMS({
                                ratio: 1,
                                url: 'http://localhost:8080/geoserver/example/wms?',
                                params: {
                                    'FORMAT': format,
                                    'VERSION': '1.1.1',
                                    STYLES: '',
                                    LAYERS: 'dlieu_point',
                                }
                            })
                        });
                        var viewMap = new ol.View({
                            center: ol.proj.fromLonLat([mapLng, mapLat]),
                            zoom: mapDefaultZoom
                            //projection: projection
                        });
                        map = new ol.Map({
                            target: "map",
                            layers: [layerBG, layerCMR_adm1, layerCovid],
                            //layers: [layerCMR_adm1],
                            view: viewMap
                        });
                        //map.getView().fit(bounds, map.getSize());
                        
                        var styles = {
                            'MultiPolygon': new ol.style.Style({
                                stroke: new ol.style.Stroke({
                                    color: '#0892d0  ', 
                                    width: 2
                                })
                            })
                        };
                        var styleFunction = function (feature) {
                            return styles[feature.getGeometry().getType()];
                        };
                        var vectorLayer = new ol.layer.Vector({
                            //source: vectorSource,
                            style: styleFunction
                        });
                        map.addLayer(vectorLayer);

                }
                }
                let clickName = document.querySelector('#info');
                clickName.onclick = function (){ 
                    var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    $.ajax({
                        type: "POST",
                        url: "inforAPItest.php",
                        //dataType: 'json',
                        //data: {functionname: 'reponseGeoToAjax', paPoint: myPoint},
                        data: {functionname: 'getGeoStatistic', paPoint: myPoint},
                        success : function (result, status, erro) {
                            displayGeoStatistic(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);

                        }
                    });     
                }
            //};
        //});

        </script>
    </body>
    <script src="infor.js"></script>
</html>