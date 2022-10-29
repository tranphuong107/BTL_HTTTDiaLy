<?php
    if(isset($_POST['functionname'])) 
    {
        $paPDO = initDB();
        $paSRID = '4326'; 
        $paPoint = $_POST['paPoint'];
        $functionname = $_POST['functionname'];
        
        $aResult = "null";
        if ($functionname == 'getGeoCMRToAjax'){
            $caNhiem = $_POST['caNhiem'];
            $aResult = getGeoCMRToAjax($paPDO,$caNhiem);
        }
        else if ($functionname == 'getInfoCMRToAjax')
            $aResult = getInfoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getGeoFillColorToAjax')
            $aResult = getGeoFillColorToAjax($paPDO, $paSRID);
        echo $aResult;
    
        closeDB($paPDO);
    }

    function initDB()
    {
        // Kết nối CSDL
        $paPDO = new PDO('pgsql:host=localhost;dbname=BTL;port=5433', 'postgres', '123456');
        return $paPDO;
    }
    function query($paPDO, $paSQLStr)
    {
        try
        {
            // Khai báo exception
            $paPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Sử đụng Prepare 
            $stmt = $paPDO->prepare($paSQLStr);
            // Thực thi câu truy vấn
            $stmt->execute();
            
            // Khai báo fetch kiểu mảng kết hợp
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            
            // Lấy danh sách kết quả
            $paResult = $stmt->fetchAll();   
            return $paResult;                 
        }
        catch(PDOException $e) {
            echo "Thất bại, Lỗi: " . $e->getMessage();
            return null;
        }       
    }
    function closeDB($paPDO)
    {
        // Ngắt kết nối
        $paPDO = null;
    }
    function getResult($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }
    // Hàm fill màu theo canhiem
    function getGeoCMRToAjax($paPDO,$caNhiem)
    {
        //echo $paPoint;
        //echo "<br>";
        // $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        // $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where varname_1 = 'Ha Noi'";
        $mySQLStr = "SELECT ST_AsGeoJson(gadm41_vnm_1.geom) as geo from dlieu_point, gadm41_vnm_1 
         where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and canhiem <= ".$caNhiem." ";
        // echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }
    // getGeoCMRToAjax(initDB());
    function getInfoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        $mySQLStr = "SELECT gadm41_vnm_1.name_1, canhiem, canhiemmoi, catuvong from \"gadm41_vnm_1\", \"dlieu_point\"
         where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,gadm41_vnm_1.geom)";
        //echo $mySQLStr; 
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            $resFin = '<div class ="header-infor">';
            $resFin = $resFin.'<p>Thông tin vùng</p>';
            // $resFin = $resFin.'<i class="fa-solid fa-xmark"></i>';
            $resFin = $resFin.'</div>';
            $resFin = $resFin.'<div class ="content-info">';
            // Lặp kết quả
            foreach ($result as $item){
            
                $resFin = $resFin.'<p>Tỉnh: '.$item['name_1'].'</p>';
                $resFin = $resFin.'<p>Ca nhiễm: '.$item['canhiem'].'</p>';
                $resFin = $resFin.'<p>Ca nhiễm mới: '.$item['canhiemmoi'].'</p>';
                $resFin = $resFin.'<p>Ca tử vong: '.$item['catuvong'].'</p>';

                break;
            }
            $resFin = $resFin.'</div>';
            
            return $resFin;
        }
        else{
            $resFin = '<div class ="header-infor">';
            $resFin = $resFin.'<p>Thông tin vùng</p>';
            // $resFin = $resFin.'<i class="fa-solid fa-xmark"></i>';
            $resFin = $resFin.'</div>';
            $resFin = $resFin.'<div class ="infor-fail">';
            $resFin = $resFin.'<p>Vui lòng chọn vùng đất liền Việt Nam !</p>';
            $resFin = $resFin.'</div>';
        }
            return $resFin;
    }

    // function getGeoFillColorToAjax($paPDO,$paSRID)
    // {
    //     //echo $paPoint;
    //     //echo "<br>";
    //     // $paPoint = str_replace(',', ' ', $paPoint);
    //     //echo $paPoint;
    //     //echo "<br>";
    //     //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
    //     $mySQLStr = "SELECT (gadm41_vnm_1.geom) as geo from dlieu_point, gadm41_vnm_1 
    //     where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and canhiem <= 50 ";
    //     // echo $mySQLStr;
    //     //echo "<br><br>";
        
    //     $result = query($paPDO, $mySQLStr);
    //     if ($result != null)
    //     {
    //         // Lặp kết quả
    //         echo json_encode($result);
    //         foreach ($result as $item){
    //             echo $item['geo'];

    //             // $mygeo = "SELECT ST_AsGeoJson(".$item['geo']."::geometry)";
    //             // $myresult = query($paPDO, $mygeo);
    //             // echo $myresult;
    //             // if ($myresult != null)
    //             // {
    //             //     // Lặp kết quả
    //             //     // echo json_encode($result);
    //             //     foreach ($myresult as $myitem){
    //             //         echo json_encode($myitem);
    //             //     }
    //             // }
    //         }
    //     }
    //     else
    //         return "null";
    // }
    // getGeoFillColorToAjax(initDB(),'4326');
?>