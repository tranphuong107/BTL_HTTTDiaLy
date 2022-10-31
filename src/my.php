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
        <?php include 'myAPI.php' ?>
        
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
                
                var style1 = {
                    'MultiPolygon': new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'green'
                        }),
                        stroke: new ol.style.Stroke({
                            color: 'green', 
                            width: 1
                        })
                    })

                };
                var styleFunction1 = function (
                    feature) {
                    return style1[feature.getGeometry().getType()];
                };
                var vectorLayer = new ol.layer.Vector({
                    //source: vectorSource,
                    style: styleFunction1
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
                        // source: vectorSource,
                        style: styleFunction1,
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
					// vectorLayer.setSource(vectorSource);

                    // style lại geo 
                    var vectorLayer = new ol.layer.Vector({
                        style: styleFunction1,
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                    // console.log(paObjJson);
                }
                function highLightObj1(result) {
                    // console.log(typeof result);
                    var resultjs = JSON.parse(result)
                    //  Duyệt lấy ra từng geo và tô màu 
                    for(let geo of resultjs){
                        var strObjJson = createJsonObj(geo);
                        var objJson = JSON.parse(strObjJson);
                        highLightGeoJsonObj(objJson);
                    }
                }
                function displayObjInfo(result, coordinate)
                {
                    //alert("result: " + result);
                    //alert("coordinate des: " + coordinate);
                    
					$("#info").html(result);
                }
                // highLightObj(getGeoCMRToAjax(initDB()));
                function Test1() {
                    //alert("coordinate: " + evt.coordinate);
                    // var myPoint = 'POINT(12,5)';
                    var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(106.630784879871996 10.757754740205399)';
                    // soca là ca nhiễm t test, b tạo 1 mảng ở đây và truyền hai biến giới hạn ca nhiem  để truy vấn
                    // Thêm hai biến ở dòng 210, tương tự như t truyền soCa nhé,
                    // hoặc copy chạy lại nhiều lần đoạn bên dưới cũng đc , vậy cho dễ
                    var soCa = 50;
                    //alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "myAPI.php",
                        //dataType: 'json',
                        data: {functionname: 'getGeoCMRToAjax1', paPoint: myPoint, caNhiem: soCa},
                        success : function (result, status, erro) {
                            highLightObj1(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    
                    });
                };
                Test1();

                var style2 = {
                    'MultiPolygon': new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'yellow'
                        }),
                        stroke: new ol.style.Stroke({
                            color: 'yellow', 
                            width: 1
                        })
                    })

                };
                var styleFunction2 = function (
                    feature) {
                    return style2[feature.getGeometry().getType()];
                };
                var vectorLayer = new ol.layer.Vector({
                    //source: vectorSource,
                    style: styleFunction2
                });
                map.addLayer(vectorLayer);

                             
                function drawGeoJsonObj(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
                    var vectorLayer = new ol.layer.Vector({
                        // source: vectorSource,
                        style: styleFunction2,
                    });

                    map.addLayer(vectorLayer);
                }
                function highLightGeoJsonObj2(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
					// vectorLayer.setSource(vectorSource);

                    // style lại geo 
                    var vectorLayer = new ol.layer.Vector({
                        style: styleFunction2,
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                    // console.log(paObjJson);
                }
                function highLightObj2(result) {
                    // console.log(typeof result);
                    var resultjs = JSON.parse(result)
                    //  Duyệt lấy ra từng geo và tô màu 
                    for(let geo of resultjs){
                        var strObjJson = createJsonObj(geo);
                        var objJson = JSON.parse(strObjJson);
                        highLightGeoJsonObj2(objJson);
                    }
                }
                
                // highLightObj(getGeoCMRToAjax(initDB()));
                function Test2() {
                    //alert("coordinate: " + evt.coordinate);
                    // var myPoint = 'POINT(12,5)';
                    var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(106.630784879871996 10.757754740205399)';
                    // soca là ca nhiễm t test, b tạo 1 mảng ở đây và truyền hai biến giới hạn ca nhiem  để truy vấn
                    // Thêm hai biến ở dòng 210, tương tự như t truyền soCa nhé,
                    // hoặc copy chạy lại nhiều lần đoạn bên dưới cũng đc , vậy cho dễ
                    var soCa = 50;
                    //alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "myAPI.php",
                        //dataType: 'json',
                        data: {functionname: 'getGeoCMRToAjax2', paPoint: myPoint, caNhiem: soCa},
                        success : function (result, status, erro) {
                            highLightObj2(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    
                    });
                };
                Test2();

                var style3 = {
                    'MultiPolygon': new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'orange'
                        }),
                        stroke: new ol.style.Stroke({
                            color: 'orange', 
                            width: 1
                        })
                    })

                };
                var styleFunction3 = function (
                    feature) {
                    return style3[feature.getGeometry().getType()];
                };
                var vectorLayer = new ol.layer.Vector({
                    //source: vectorSource,
                    style: styleFunction3
                });
                map.addLayer(vectorLayer);

                function createJsonObj3(result1) {                    
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
              
                function drawGeoJsonObj3(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
                    var vectorLayer = new ol.layer.Vector({
                        // source: vectorSource,
                        style: styleFunction3,
                    });

                    map.addLayer(vectorLayer);
                }
                function highLightGeoJsonObj3(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
					// vectorLayer.setSource(vectorSource);

                    // style lại geo 
                    var vectorLayer = new ol.layer.Vector({
                        style: styleFunction3,
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                    // console.log(paObjJson);
                }
                function highLightObj3(result) {
                    // console.log(typeof result);
                    var resultjs = JSON.parse(result)
                    //  Duyệt lấy ra từng geo và tô màu 
                    for(let geo of resultjs){
                        var strObjJson = createJsonObj(geo);
                        var objJson = JSON.parse(strObjJson);
                        highLightGeoJsonObj3(objJson);
                    }
                }
                
                // highLightObj(getGeoCMRToAjax(initDB()));
                function Test3() {
                    //alert("coordinate: " + evt.coordinate);
                    // var myPoint = 'POINT(12,5)';
                    var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(106.630784879871996 10.757754740205399)';
                    // soca là ca nhiễm t test, b tạo 1 mảng ở đây và truyền hai biến giới hạn ca nhiem  để truy vấn
                    // Thêm hai biến ở dòng 210, tương tự như t truyền soCa nhé,
                    // hoặc copy chạy lại nhiều lần đoạn bên dưới cũng đc , vậy cho dễ
                    var soCa = 50;
                    //alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "myAPI.php",
                        //dataType: 'json',
                        data: {functionname: 'getGeoCMRToAjax3', paPoint: myPoint, caNhiem: soCa},
                        success : function (result, status, erro) {
                            highLightObj3(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    
                    });
                };
                Test3();

                var style4 = {
                    'MultiPolygon': new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'red'
                        }),
                        stroke: new ol.style.Stroke({
                            color: 'red', 
                            width: 1
                        })
                    })

                };
                var styleFunction4 = function (
                    feature) {
                    return style4[feature.getGeometry().getType()];
                };
                var vectorLayer = new ol.layer.Vector({
                    //source: vectorSource,
                    style: styleFunction4
                });
                map.addLayer(vectorLayer);

                function createJsonObj4(result1) {                    
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
              
                function drawGeoJsonObj4(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
                    var vectorLayer = new ol.layer.Vector({
                        // source: vectorSource,
                        style: styleFunction4,
                    });

                    map.addLayer(vectorLayer);
                }
                function highLightGeoJsonObj4(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
					// vectorLayer.setSource(vectorSource);

                    // style lại geo 
                    var vectorLayer = new ol.layer.Vector({
                        style: styleFunction4,
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                    // console.log(paObjJson);
                }
                function highLightObj4(result) {
                    // console.log(typeof result);
                    var resultjs = JSON.parse(result)
                    //  Duyệt lấy ra từng geo và tô màu 
                    for(let geo of resultjs){
                        var strObjJson = createJsonObj4(geo);
                        var objJson = JSON.parse(strObjJson);
                        highLightGeoJsonObj4(objJson);
                    }
                }
               
                // highLightObj(getGeoCMRToAjax(initDB()));
                function Test4() {
                    //alert("coordinate: " + evt.coordinate);
                    // var myPoint = 'POINT(12,5)';
                    var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(106.630784879871996 10.757754740205399)';
                    // soca là ca nhiễm t test, b tạo 1 mảng ở đây và truyền hai biến giới hạn ca nhiem  để truy vấn
                    // Thêm hai biến ở dòng 210, tương tự như t truyền soCa nhé,
                    // hoặc copy chạy lại nhiều lần đoạn bên dưới cũng đc , vậy cho dễ
                    var soCa = 50;
                    //alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "myAPI.php",
                        //dataType: 'json',
                        data: {functionname: 'getGeoCMRToAjax4', paPoint: myPoint, caNhiem: soCa},
                        success : function (result, status, erro) {
                            highLightObj4(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    
                    });
                };
                Test4();
            };
                    
               
            
        </script>
    </body>
</html>