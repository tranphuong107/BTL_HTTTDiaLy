<?php
    if(isset($_POST['functionname'])) 
    {
        $paPDO = initDB();
        $paSRID = '4326'; 
        $paPoint = $_POST['paPoint'];
        $functionname = $_POST['functionname'];
        
        $aResult = "null";
        if ($functionname == 'getGeoCMRToAjax')
        $aResult = getGeoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getInfoCMRToAjax')
            $aResult = getInfoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getGeoStatistic')
            $aResult = getGeoStatistic($paPDO, $paSRID);
        else if ($functionname == 'getGeoThongkeToAjax')
            $aResult = getGeoThongkeToAjax($paPDO, $paSRID);
        else if ($functionname == 'getTinh1')
            $aResult = getTinh1($paPDO);
        else if ($functionname == 'getTinh2')
            $aResult = getTinh2($paPDO);
        else if ($functionname == 'getTinh3')
            $aResult = getTinh3($paPDO);
        else if ($functionname == 'getTinh4')
            $aResult = getTinh4($paPDO);
        echo $aResult;
    
        closeDB($paPDO);
    } 

    function initDB()
    {
        // Kết nối CSDL
        $paPDO = new PDO('pgsql:host=localhost;dbname=BTL;port=5432', 'postgres', '123456789');
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
    function getGeoCMRToAjax($paPDO,$paSRID,$paPoint)
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
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = '<div class ="infor-success">';
                $resFin = $resFin.'<p>Tỉnh: '.$item['name_1'].'</p>';
                $resFin = $resFin.'<p>Ca nhiễm: '.$item['canhiem'].'</p>';
                $resFin = $resFin.'<p>Ca nhiễm mới: '.$item['canhiemmoi'].'</p>';
                $resFin = $resFin.'<p>Ca tử vong: '.$item['catuvong'].'</p>';
                $resFin = $resFin.'</div>';

                break;
            }
            return $resFin;
        }
        else{
            $resFin = '<div class ="infor-fail">';
            $resFin = $resFin.'<p>Vui lòng chọn vùng đất liền Việt Nam !</p>';
            $resFin = $resFin.'</div>';
        }
            return $resFin;
    }


    // API cho thống kê
    function getGeoStatistic($paPDO)  {
        $mySQLStr = "SELECT x, y, geom  from dlieu_point  where  varname_1 = 'Ho Chi Minh' ";
        // echo $mySQLStr;
        // lấy x, y để biểu diễn bản đồ được click, hai điểm này sẽ đc lấy làm center của map để hiển thị bản đồ
        $result = query($paPDO, $mySQLStr);

        if ($result != null)
        {
            foreach ($result as $item){
                $ketqua = array($item['x'],$item['y'],$item['geom']);
            }
            return json_encode($ketqua);
        }
        else
            return "null 99";
    }

    function getGeoThongkeToAjax($paPDO,$paSRID)
    {
        //echo $paPoint;
        //echo "<br>";
        // $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT ST_AsGeoJson(gadm41_vnm_1.geom) as geo from dlieu_point, gadm41_vnm_1 
        where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and dlieu_point.varname_1 = 'Ho Chi Minh'";
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
     //Vung xanh
     function getTinh1($paPDO)
    {
        $mySQLStr = "SELECT gadm41_vnm_1.name_1 from \"gadm41_vnm_1\", \"dlieu_point\"
         where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and canhiem <= 50 ";
        
        //echo $mySQLStr; 
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        //echo json_encode($result);

        $ketqua = array();
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                array_push($ketqua,$item['name_1']);
            }
            $resFin = '<ul>';
            foreach ($ketqua as $item){
                $resFin = $resFin.'<li class="tinhThanh>'.$item.'</li>';
                //echo $item;
            }
            //$resFin = $resFin.'</ul>';
            //echo json_encode($resFin);
            return $resFin;
            //echo json_encode($ketqua);

        }
        else
            return "null";
    }
    getTinh1(initDB());

    //Vung vang
    function getTinh2($paPDO)
    {
        $mySQLStr = "SELECT gadm41_vnm_1.name_1 from \"gadm41_vnm_1\", \"dlieu_point\"
         where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and canhiem <= 100 and canhiem > 50 ";
        
        //echo $mySQLStr; 
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        //echo json_encode($result);

        $ketqua = array();
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                array_push($ketqua,$item['name_1']);
            }
            $resFin = '<ul>';
            foreach ($ketqua as $item){
                $resFin = $resFin.'<li class="tinhThanh>'.$item.'</li>';
                //echo $item;
            }
            //$resFin = $resFin.'</ul>';
            //echo json_encode($resFin);
            return $resFin;
            //echo json_encode($ketqua);

        }
        else
            return "null";
    }
    getTinh2(initDB());

    //Vung cam
    function getTinh3($paPDO)
    {
        $mySQLStr = "SELECT gadm41_vnm_1.name_1 from \"gadm41_vnm_1\", \"dlieu_point\"
         where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and canhiem <= 150 and canhiem >100 ";
        
        //echo $mySQLStr; 
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        //echo json_encode($result);

        $ketqua = array();
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                array_push($ketqua,$item['name_1']);
            }
            $resFin = '<ul>';
            foreach ($ketqua as $item){
                $resFin = $resFin.'<li class="tinhThanh>'.$item.'</li>';
                //echo $item;
            }
            //$resFin = $resFin.'</ul>';
            //echo json_encode($resFin);
            return $resFin;
            //echo json_encode($ketqua);

        }
        else
            return "null";
    }
    getTinh3(initDB());

    //Vung do
    function getTinh4($paPDO)
    {
        $mySQLStr = "SELECT gadm41_vnm_1.name_1 from \"gadm41_vnm_1\", \"dlieu_point\"
         where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and canhiem > 150 ";
        
        //echo $mySQLStr; 
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        //echo json_encode($result);

        $ketqua = array();
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                array_push($ketqua,$item['name_1']);
            }
            $resFin = '<ul>';
            foreach ($ketqua as $item){
                $resFin = $resFin.'<li class="tinhThanh>'.$item.'</li>';
                //echo $item;
            }
            //$resFin = $resFin.'</ul>';
            //echo json_encode($resFin);
            return $resFin;
            //echo json_encode($ketqua);

        }
        else
            return "null";
    }
    getTinh4(initDB());

?>
