<!DOCTYPE html> 
<html>
    <head>
        <meta charset="utf-8">
        <title>OpenStreetMap &amp; OpenLayers - Marker Example</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
        <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
        
        <!-- <link rel="stylesheet" href="http://localhost:8081/libs/openlayers/css/ol.css" type="text/css" /> -->
        <!-- <script src="http://localhost:8081/libs/openlayers/build/ol.js" type="text/javascript"></script> -->
        <!--  -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
        
        <!-- <script src="http://localhost:8081/libs/jquery/jquery-3.4.1.min.js" type="text/javascript"></script> -->

        <style>
            /*
            .map, .righ-panel {
                height: 500px;
                width: 80%;
                float: left;
            }
            */
            .map, .righ-panel {
                height: 98vh;
                width: 80vw;
                float: left;
            }
            .map {
                border: 1px solid #000;
            }
        </style>
    </head>

    <body onload="initialize_map();">
        <table>
            <tr>
                <td>
                    <div id="map" style="width: 80vw; height: 100vh;"></div>
                </td>
                <td>
                    <div id="info"></div>
                </td>
            </tr>
        </table>
        <?php include 'inforAPI.php' ?>
        
        <?php
            // $myPDO = initDB();
            // $mySRID = '4326';
            // $pointFormat = 'POINT(12,5)';

            // example1($myPDO);
            // example2($myPDO);
            // example3($myPDO,'4326','POINT(12,5)');
            // $result = getResult($myPDO,$mySRID,$pointFormat);

            // closeDB($myPDO);
        
        ?>
        
        <script>
            var format = 'image/png';
            var map;
            var minX = 102.107955932617;
            var minY = 8.30629730224609;
            var maxX = 109.505798339844;
            var maxY = 23.4677505493164;
            var cenX = (minX + maxX) / 2;
            var cenY = (minY + maxY) / 2;
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
                        url: 'http://localhost:8080/geoserver/BTL/wms?',
                        params: {
                            'FORMAT': format,
                            'VERSION': '1.1.1',
                            STYLES: '',
                            LAYERS: 'gadm41_vnm_1',
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
                    layers: [layerBG, layerCMR_adm1],
                    //layers: [layerCMR_adm1],
                    view: viewMap
                });
                //map.getView().fit(bounds, map.getSize());
                
                var styles = {
                    'MultiPolygon': new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'orange'
                        }),
                        stroke: new ol.style.Stroke({
                            color: 'yellow', 
                            width: 2
                        })
                    })

                };
                var styleFunction = function (
                    feature) {
                    return styles[feature.getGeometry().getType()];
                };
                var vectorLayer = new ol.layer.Vector({
                    //source: vectorSource,
                    style: styleFunction
                });
                map.addLayer(vectorLayer);

                function createJsonObj(result1) {                    
                    var geojsonObject = '{'
                            + '"type": "FeatureCollection",'
                            + '"crs": {'
                                + '"type": "name",'
                                + '"properties": {'
                                    + '"name": "EPSG:4326"'
                                + '}'
                            + '},'
                            + '"features": [{'
                                + '"type": "Feature",'
                                + '"geometry": ' + result1
                            + '}]'
                        + '}';
                    return geojsonObject;
                }
              
                function drawGeoJsonObj(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
                    var vectorLayer = new ol.layer.Vector({
                        source: vectorSource
                    });

                    map.addLayer(vectorLayer);
                }
                function highLightGeoJsonObj(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
					vectorLayer.setSource(vectorSource);
                    
                    // var vectorLayer = new ol.layer.Vector({
                    //     source: vectorSource
                    // });
                    // map.addLayer(vectorLayer);
                    
                }
                function highLightObj(result) {
                    var strObjJson = createJsonObj(result);
                    var objJson = JSON.parse(strObjJson);
                    highLightGeoJsonObj(objJson);
                }
                function displayObjInfo(result, coordinate)
                {
                    //alert("result: " + result);
                    //alert("coordinate des: " + coordinate);
					$("#info").html(result);
                }
                // highLightObj(getGeoCMRToAjax(initDB()));
                function Test() {
                    //alert("coordinate: " + evt.coordinate);
                    // var myPoint = 'POINT(12,5)';
                    var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                    var lon = lonlat[0];
                    var lat = lonlat[1];
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    //alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "test.php",
                        //dataType: 'json',
                        data: {functionname: 'getGeoCMRToAjax', paPoint: myPoint},
                        success : function (result, status, erro) {
                            highLightObj(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });};
            Test();
                    
            };
            
        </script>
    </body>
</html>