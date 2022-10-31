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
        <link rel="stylesheet" href="style.css">
        
    </head>
    <body onload="initialize_map();">
        
                    <div id="map" class="map">
                        <div id="info">
                            <div class ="header-infor">
                                <p>Thông tin vùng</p>
                                <i class="fa-solid fa-xmark cancel"></i>
                            </div>
                            <div id="content-infor"></div>
                        </div>
                    </div>
                    <!--<div id="map" style="width: 80vw; height: 100vh;"></div>-->
                    <div class="thongKe" id='thongKe'>
                        <div class ="header-thongKe">
                            <p>Thống kê vùng dịch bệnh</p>
                            <i class="fa-solid fa-xmark cancel"></i>
                        </div>
                        <ul class ="list">
                            <li class ="list-item red">
                                <div class ="dot dot_red"></div>
                                <span class="list-span">Vùng nguy cơ rất cao</span>
                                
                            </li>
                            <div id='thongke_do' class ="content-list-item list-item-red">
                                        <ul>
                                        <li>Đà Nẵng</li><li class ="tinhThanh">Hồ Chí Minh</li>                                          
                                        </ul>
                            </div>
                            <li class ="list-item orange">
                                <div class ="dot dot_orange"></div>
                                <span class="list-span">Vùng nguy cơ cao</span>
                                
                            </li>
                            <div id='thongke_cam' class ="content-list-item list-item">
                                        <ul>
                                        <li>Đà Nẵng</li><li class ="tinhThanh">Hồ Chí Minh</li>
                                        </ul>
                            </div>
                            <li class ="list-item yellow">
                                <div class ="dot dot_yellow"></div>
                                <span class="list-span">Vùng nguy cơ trung bình</span>
                            </li>   
                            <div id='thongke_vang' class ="content-list-item list-item">
                                        <ul>
                                        <li>Đà Nẵng</li><li class ="tinhThanh">Hồ Chí Minh</li>
                                        </ul>
                            </div>
                            <li class ="list-item green">
                                <div class ="dot dot_green"></div>
                                <span class="list-span">Vùng nguy cơ thấp</span>
                            </li>
                            <div id='thongke_xanh' class ="content-list-item list-item">
                                        <ul>
                                        <li>Đà Nẵng</li><li class ="tinhThanh">Hồ Chí Minh</li>
                                        </ul>
                            </div>
                        </ul>
                    </div>

                    
                    <!-- <button>Button</button> -->
              
        <?php include 'thong_ke_API_huong.php' ?>
        <script>
        //$("#document").ready(function () {
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
                //thêm dlieu covid
                var  layerCovid = new ol.layer.Image({
                    source: new ol.source.ImageWMS({
                        ratio: 1,
                        url: 'http://localhost:8080/geoserver/BTL/wms?',
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

                function createJsonObj(result) {                    
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
                                + '"geometry": ' + result
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
                function displayObjInfo(result, coordinate)
                {
                    //alert("result: " + result);
                    //alert("coordinate des: " + coordinate);
					$("#content-infor").html(result);
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
                //    console.log(paObjJson);
                }
                function highLightObj(result) {
                    //alert("result: " + result);
                    var strObjJson = createJsonObj(result);
                    //alert(strObjJson);
                    var objJson = JSON.parse(strObjJson);
                    //alert(JSON.stringify(objJson));
                    //drawGeoJsonObj(objJson);
                    highLightGeoJsonObj(objJson);
                    // console.log(result);
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
                        url: "APIthongke.php",
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
                        url: "APIthongke.php",
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
                    var resultjs = JSON.parse(result)
                    var x = +resultjs[0];   
                    var y = +resultjs[1];
                        var viewMap = new ol.View({
                            center: ol.proj.fromLonLat([x, y]),
                            zoom: 8
                            //projection: projection
                        });
                        map = new ol.Map({
                            target: "map",
                            layers: [layerBG, layerCMR_adm1, layerCovid],
                            //layers: [layerCMR_adm1],
                            view: viewMap
                        });
                }
                
                let clickName = document.querySelector('.tinhThanh');
                clickName.onclick = function (){ 
                    var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                   
                    $.ajax({
                        type: "POST",
                        url: "thong_ke_API_huong.php",
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
                    $.ajax({
                        type: "POST",
                        url: "thong_ke_API_huong.php",
                        //dataType: 'json',
                        data: {functionname: 'getGeoThongkeToAjax', paPoint: myPoint},
                        success : function (result1, status, erro) {
                            highLightObj(result1);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });

                }
                //Kết thúc Thống kê các vùng, khi click vào tên trong list thống kê, hiển thị bản đồ ở đó
            //};
            function getThongTinTinh1() {
                    //alert("coordinate org: " + evt.coordinate);
                    //var myPoint = 'POINT(12,5)';
                   // var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                   var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    //alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "thong_ke_API_huong.php",
                        //dataType: 'json',
                        //data: {functionname: 'reponseGeoToAjax', paPoint: myPoint},
                        data: {functionname: 'getTinh1', paPoint: myPoint},
                        success : function (result, status, erro) {
                            displayThongTinTinh1(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);

                        }
                    });
            };
            function displayThongTinTinh1(result, coordinate)
                {
                    
                    //alert("coordinate des: " + coordinate);
					$("#thongke_xanh").html(result);
                    console.log("result: " + result);
                }
            getThongTinTinh1();

            //Vung vang
            function getThongTinTinh2() {
                   var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    //alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "thong_ke_API_huong.php",
                        //dataType: 'json',
                        //data: {functionname: 'reponseGeoToAjax', paPoint: myPoint},
                        data: {functionname: 'getTinh2', paPoint: myPoint},
                        success : function (result, status, erro) {
                            displayThongTinTinh2(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);

                        }
                    });
            };
            function displayThongTinTinh2(result, coordinate)
                {
                    
                    //alert("coordinate des: " + coordinate);
					$("#thongke_vang").html(result);
                    console.log("result: " + result);
                }
            getThongTinTinh2();

            //Vung cam
            function getThongTinTinh3() {
                   var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    //alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "thong_ke_API_huong.php",
                        //dataType: 'json',
                        //data: {functionname: 'reponseGeoToAjax', paPoint: myPoint},
                        data: {functionname: 'getTinh3', paPoint: myPoint},
                        success : function (result, status, erro) {
                            displayThongTinTinh3(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);

                        }
                    });
            };
            function displayThongTinTinh3(result, coordinate)
                {
                    
                    //alert("coordinate des: " + coordinate);
					$("#thongke_cam").html(result);
                    console.log("result: " + result);
                }
            getThongTinTinh3();

            //Vung do
            function getThongTinTinh4() {
                   var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    //alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "thong_ke_API_huong.php",
                        //dataType: 'json',
                        //data: {functionname: 'reponseGeoToAjax', paPoint: myPoint},
                        data: {functionname: 'getTinh4', paPoint: myPoint},
                        success : function (result, status, erro) {
                            displayThongTinTinh4(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);

                        }
                    });
            };
            function displayThongTinTinh4(result, coordinate)
                {
                    
                    //alert("coordinate des: " + coordinate);
					$("#thongke_do").html(result);
                    console.log("result: " + result);
                }
            getThongTinTinh4();
        };
        
        </script>
    </body>
    <script src="main.js"></script>
</html>