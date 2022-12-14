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
        <link rel="stylesheet" href="styleHienThi.css">
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
                        <div class="thongKeVung" id='thongKeVung'>
                            <div class ="header-thongKeVung">
                                <p>Hiển thị</p>
                            </div>
                            <ul class ="list">
                                <li class ="list-item red">
                                    <div class ="dot dot_red"></div>
                                    <span class="list-span-red">Vùng đỏ</span>
                                </li>
                                <div id='thongke' class ="content-list-item list-item-red">
                                </div>
                                <li class ="list-item orange">
                                    <div class ="dot dot_orange"></div>
                                    <span class="list-span-orange">Vùng cam</span>
                                    
                                </li>
                                <div class ="content-list-item list-item">
                                </div>
                                <li class ="list-item yellow">
                                    <div class ="dot dot_yellow"></div>
                                    <span class="list-span-yellow">Vùng vàng</span>
                                </li>   
                                <div class ="content-list-item list-item">
                                </div>
                                <li class ="list-item green">
                                    <div class ="dot dot_green"></div>
                                    <span class="list-span-green">Vùng xanh</span>
                                </li>
                                <div class ="content-list-item list-item">
                                </div>
                            </ul>
                        </div>  
                    </div>
                    
                    <div class="thongKe" id='thongKe'>
                        <div class ="header-thongKe">
                            <p>Thống kê vùng dịch bệnh</p>
                            <i class="fa-solid cancel"></i>
                        </div>
                        <ul class ="list">
                            <li class ="list-item red">
                                <div class ="dot dot_red"></div>
                                <span class="list-span">Vùng nguy cơ rất cao</span>
                            </li>
                            <div id='thongke_do' class ="content-list-item list-item-red">
                                        <ul>
                                        <li class ="tinhThanh">Đà Nẵng</li>
                                        </ul>
                            </div>
                            <li class ="list-item orange">
                                <div class ="dot dot_orange"></div>
                                <span class="list-span">Vùng nguy cơ cao</span>
                                
                            </li>
                            <div id='thongke_cam' class ="content-list-item list-item">
                                        <ul>
                                        <li class ="tinhThanh">An Giang</li>
                                        </ul>
                            </div>
                            <li class ="list-item yellow">
                                <div class ="dot dot_yellow"></div>
                                <span class="list-span">Vùng nguy cơ trung bình</span>
                            </li>   
                            <div id='thongke_vang' class ="content-list-item list-item">
                                        <ul>
                                        <li class ="tinhThanh">Hồ Chí Minh</li>
                                        </ul>
                            </div>
                            <li class ="list-item green">
                                <div class ="dot dot_green"></div>
                                <span class="list-span">Vùng nguy cơ thấp</span>
                            </li>
                            <div id='thongke_xanh' class ="content-list-item list-item">
                                        <ul>
                                        <li class ="tinhThanh">Cà Mau</li>
                                        </ul>
                            </div>
                        </ul>
                    </div>
              
        <?php include 'API_getThongtinTinh.php' ?>
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
                });
                map = new ol.Map({
                    target: "map",
                    layers: [layerBG, layerCMR_adm1, layerCovid],
                    view: viewMap
                });
                
                var styles = {
                    'MultiPolygon': new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: '#778899'
                        }),
                        stroke: new ol.style.Stroke({
                            color: '#778899', 
                            width: 2
                        })
                    })
                };
                var styleFunction = function (feature) {
                    return styles[feature.getGeometry().getType()];
                };
                var vectorLayer = new ol.layer.Vector({
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
                }

                function highLightObj(result) {
                    var strObjJson = createJsonObj(result);
                    var objJson = JSON.parse(strObjJson);
                    highLightGeoJsonObj(objJson);
                }
                // Phương
                //Thông tin dịch bệnh tại vùng khi click
                map.on('singleclick', function (evt) {
                    var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                    var lon = lonlat[0];   
                    var lat = lonlat[1];
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    $.ajax({
                        type: "POST",
                        url: "API_getThongtinTinh.php",
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
                        url: "API_getThongtinTinh.php",
                        data: {functionname: 'getGeoCMRToAjax', paPoint: myPoint},
                        success : function (result, status, erro) {
                            highLightObj(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                    displayInfoCovid()
                });
               

                // Thống kê các vùng, khi click vào tên trong list thống kê, hiển thị bản đồ ở đó
                function displayGeoStatistic(result) {
                    var resultjs = JSON.parse(result)
                    var x = +resultjs[0];   
                    var y = +resultjs[1];
                        var viewMap = new ol.View({
                            center: ol.proj.fromLonLat([x, y]),
                            zoom: 8
                        });
                        map = new ol.Map({
                            target: "map",
                            layers: [layerBG, layerCMR_adm1, layerCovid],
                            view: viewMap
                        });
                }
                // kết thúc thống kê các vùng, khi click vào tên trong list thống kê, hiển thị bản đồ ở đó

                // Thêm màu các vùng
                //Đổ màu vùng xanh
                function highLightGeoJsonObj1(paObjJson) {
                    var style1 = {
                    'MultiPolygon': new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'rgba(103,228,78,0.4)'
                        }),
                        stroke: new ol.style.Stroke({
                            color: 'rgba(103,228,78,1)', 
                            width: 0.5
                        })
                    })

                    };
                    var styleFunction1 = function (
                        feature) {
                        return style1[feature.getGeometry().getType()];
                    };
                    var vectorLayer = new ol.layer.Vector({
                        style: styleFunction1
                    });

                    map.addLayer(vectorLayer);


                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });

                    var vectorLayer = new ol.layer.Vector({
                        style: styleFunction1,
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                }
                function highLightObj1(result) {
                    var resultjs = JSON.parse(result)
                    for(let geo of resultjs){
                        var strObjJson = createJsonObj(geo);
                        var objJson = JSON.parse(strObjJson);
                        highLightGeoJsonObj1(objJson);
                    }
                }
                
                function Test1() {
                    var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(106.630784879871996 10.757754740205399)';
                    var soCa = 50;
                    $.ajax({
                        type: "POST",
                        url: "API_getPhanVung.php",
                        data: {functionname: 'getGeoCMRToAjax1', paPoint: myPoint, caNhiem: soCa},
                        success : function (result, status, erro) {
                            highLightObj1(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    
                    });
                };

                //Đổ màu vùng vàng
                
                
                function highLightGeoJsonObj2(paObjJson) {

                    var style2 = {
                    'MultiPolygon': new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'rgba(244,243,61,0.4)'
                        }),
                        stroke: new ol.style.Stroke({
                            color: 'rgba(244,243,61,1)', 
                            width: 0.5
                        })
                    })

                    };
                    var styleFunction2 = function (
                        feature) {
                        return style2[feature.getGeometry().getType()];
                    };
                    var vectorLayer = new ol.layer.Vector({
                        style: styleFunction2
                    });
                    map.addLayer(vectorLayer);


                           
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });

                    var vectorLayer = new ol.layer.Vector({
                        style: styleFunction2,
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                }
                function highLightObj2(result) {
                    var resultjs = JSON.parse(result)
                    for(let geo of resultjs){
                        var strObjJson = createJsonObj(geo);
                        var objJson = JSON.parse(strObjJson);
                        highLightGeoJsonObj2(objJson);
                    }
                }
                
                function Test2() {
                    var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(106.630784879871996 10.757754740205399)';
                    var soCa = 50;

                    $.ajax({
                        type: "POST",
                        url: "API_getPhanVung.php",
                        data: {functionname: 'getGeoCMRToAjax2', paPoint: myPoint, caNhiem: soCa},
                        success : function (result, status, erro) {
                            highLightObj2(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    
                    });
                };

                //Đổ màu vùng cam
                function highLightGeoJsonObj3(paObjJson) {
                    var style3 = {
                    'MultiPolygon': new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'rgba(255,147,22,0.4)'
                        }),
                        stroke: new ol.style.Stroke({
                            color: 'rgb(255,147,22,1)', 
                            width: 0.5
                        })
                    })

                    };
                    var styleFunction3 = function (
                        feature) {
                        return style3[feature.getGeometry().getType()];
                    };
                    var vectorLayer = new ol.layer.Vector({
                        style: styleFunction3
                    });
                    map.addLayer(vectorLayer);


                    
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
					
                    var vectorLayer = new ol.layer.Vector({
                        style: styleFunction3,
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                                 }
                function highLightObj3(result) {
                    var resultjs = JSON.parse(result)
                    for(let geo of resultjs){
                        var strObjJson = createJsonObj(geo);
                        var objJson = JSON.parse(strObjJson);
                        highLightGeoJsonObj3(objJson);
                    }
                }
                
                function Test3() {
                    var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(106.630784879871996 10.757754740205399)';
                    var soCa = 50;
                   
                    $.ajax({
                        type: "POST",
                        url: "API_getPhanVung.php",
                        data: {functionname: 'getGeoCMRToAjax3', paPoint: myPoint, caNhiem: soCa},
                        success : function (result, status, erro) {
                            highLightObj3(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    
                    });
                };

                function highLightGeoJsonObj4(paObjJson) {

                    var style4 = {
                    'MultiPolygon': new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'rgba(255,22,22,0.4)'
                        }),
                        stroke: new ol.style.Stroke({
                            color: 'rgba(255,22,22,1)', 
                            width: 0.5
                        })
                    })

                    };
                    var styleFunction4 = function (
                        feature) {
                        return style4[feature.getGeometry().getType()];
                    };
                    var vectorLayer = new ol.layer.Vector({
                        style: styleFunction4
                    });
                    map.addLayer(vectorLayer);


                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
					
                    var vectorLayer4 = new ol.layer.Vector({
                        style: styleFunction4,
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer4);
                                 }
                function highLightObj4(result) {
                    var resultjs = JSON.parse(result)
                    for(let geo of resultjs){
                        var strObjJson = createJsonObj(geo);
                        var objJson = JSON.parse(strObjJson);
                        highLightGeoJsonObj4(objJson);
                    }
                }
               
                function Test4() {                    
                    var lon = 105.142431745547000;
                    var lat = 10.572287031767900;
                    var myPoint = 'POINT(106.630784879871996 10.757754740205399)';
                    var soCa = 50;
                    $.ajax({
                        type: "POST",
                        url: "API_getPhanVung.php",
                        data: {functionname: 'getGeoCMRToAjax4', paPoint: myPoint, caNhiem: soCa},
                        success : function (result, status, erro) {
                            highLightObj4(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    
                    });
                };

         
            // Thống kê vùng 
            //vung xanh
            function getThongTinTinh1() {
                    $.ajax({
                        type: "POST",
                        url: "API_getThongke.php",
                        data: {functionname: 'getTinh1'},
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
					document.getElementById('thongke_xanh').innerHTML = result;
                    const clickName = document.querySelector('#thongke_xanh');
                    const clickNameUl = clickName.querySelector('ul');
                    const ClickNameLi = clickNameUl.querySelectorAll('.tinhThanh');
                    for (const Tinh of ClickNameLi) {
                        Tinh.onclick = function (){ 
                        var tenTinh =  Tinh.innerText;
                        var lon = 105.142431745547000;
                        var lat = 10.572287031767900;
                        var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    
                            $.ajax({
                                type: "POST",
                                url: "API_getThongtinTinh.php",
                                //dataType: 'json',
                                data: {functionname: 'getGeoThongkeToAjax', paPoint: myPoint, tinh: tenTinh},
                                success : function (result1, status, erro) {
                                    highLightObj(result1);
                                },
                                error: function (req, status, error) {
                                    alert(req + " " + status + " " + error);
                                }
                            });

                        }
                    }  
                }
            getThongTinTinh1();

            //Vung vang
            function getThongTinTinh2() {
                    $.ajax({
                        type: "POST",
                        url: "API_getThongke.php",
                        data: {functionname: 'getTinh2'},
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
					$("#thongke_vang").html(result);
                    const clickName = document.querySelector('#thongke_vang');
                    const clickNameUl = clickName.querySelector('ul');
                    const ClickNameLi = clickNameUl.querySelectorAll('.tinhThanh');
                    for (const Tinh of ClickNameLi) {
                        Tinh.onclick = function (){ 
                        var tenTinh =  Tinh.innerText;
                        var lon = 105.142431745547000;
                        var lat = 10.572287031767900;
                        var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    
                            $.ajax({
                                type: "POST",
                                url: "API_getThongtinTinh.php",
                                //dataType: 'json',
                                data: {functionname: 'getGeoThongkeToAjax', paPoint: myPoint, tinh: tenTinh},
                                success : function (result1, status, erro) {
                                    highLightObj(result1);
                                },
                                error: function (req, status, error) {
                                    alert(req + " " + status + " " + error);
                                }
                            });

                        }
                    }  
                }
            getThongTinTinh2();

            //Vung cam
            function getThongTinTinh3() {
                    $.ajax({
                        type: "POST",
                        url: "API_getThongke.php",
                        data: {functionname: 'getTinh3'},
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
					$("#thongke_cam").html(result);
                    const clickName = document.querySelector('#thongke_cam');
                    const clickNameUl = clickName.querySelector('ul');
                    const ClickNameLi = clickNameUl.querySelectorAll('.tinhThanh');
                    for (const Tinh of ClickNameLi) {
                        Tinh.onclick = function (){ 
                        var tenTinh =  Tinh.innerText;
                        var lon = 105.142431745547000;
                        var lat = 10.572287031767900;
                        var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    
                            $.ajax({
                                type: "POST",
                                url: "API_getThongtinTinh.php",
                                data: {functionname: 'getGeoThongkeToAjax', paPoint: myPoint, tinh: tenTinh},
                                success : function (result1, status, erro) {
                                    highLightObj(result1);
                                },
                                error: function (req, status, error) {
                                    alert(req + " " + status + " " + error);
                                }
                            });

                        }
                    }  
                }
            getThongTinTinh3();

            //Vung do
            function getThongTinTinh4() {
                    $.ajax({
                        type: "POST",
                        url: "API_getThongke.php",
                        data: {functionname: 'getTinh4'},
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
					$("#thongke_do").html(result);
                    const clickName = document.querySelector('#thongke_do');
                    const clickNameUl = clickName.querySelector('ul');
                    const ClickNameLi = clickNameUl.querySelectorAll('.tinhThanh');
                    for (const Tinh of ClickNameLi) {
                        Tinh.onclick = function (){ 
                        var tenTinh =  Tinh.innerText;
                        var lon = 105.142431745547000;
                        var lat = 10.572287031767900;
                        var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    
                            $.ajax({
                                type: "POST",
                                url: "API_getThongtinTinh.php",
                                data: {functionname: 'getGeoThongkeToAjax', paPoint: myPoint, tinh: tenTinh},
                                success : function (result1, status, erro) {
                                    highLightObj(result1);
                                },
                                error: function (req, status, error) {
                                    alert(req + " " + status + " " + error);
                                }
                            });

                        }
                    }  
                }
            getThongTinTinh4();

            // Hien thi mau cho vung
            let HienThiVungXanh = document.querySelector(".list-span-green");
                HienThiVungXanh.onclick=function(){
                    Test1();
            }
            let HienThiVungVang = document.querySelector(".list-span-yellow");
                HienThiVungVang.onclick=function(){
                    Test2();
            }
            let HienThiVungCam = document.querySelector(".list-span-orange");
                HienThiVungCam.onclick=function(){
                    Test3();
            }
            let HienThiVungDo = document.querySelector(".list-span-red");
                HienThiVungDo.onclick=function(){
                    Test4();
            }
            };
        </script>
    </body>
    <script src="main.js"></script>
</html>