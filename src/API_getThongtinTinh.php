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
        else if ($functionname == 'getGeoStatistic' || $functionname == 'getGeoThongkeToAjax'){
            $tinh = $_POST['tinh'];
            if ($functionname == 'getGeoStatistic'){
                $aResult = getGeoStatistic($paPDO,$paSRID,$tinh);
            }
            else if ($functionname == 'getGeoThongkeToAjax'){
                $aResult = getGeoThongkeToAjax($paPDO,$paSRID,$tinh);
            }
        }
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
    
    function getGeoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
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
        $paPoint = str_replace(',', ' ', $paPoint);
        $mySQLStr = "SELECT gadm41_vnm_1.name_1, canhiem, canhiemmoi, catuvong from \"gadm41_vnm_1\", \"dlieu_point\"
         where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,gadm41_vnm_1.geom)";
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
    function getGeoStatistic($paPDO,$paSRID,$tinh)  {
        $mySQLStr = "SELECT x, y from dlieu_point, gadm41_vnm_1 
        where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and name_1 = '".$tinh."'";
        // lấy x, y để biểu diễn bản đồ được click, hai điểm này sẽ đc lấy làm center của map để hiển thị bản đồ
        $result = query($paPDO, $mySQLStr);

        if ($result != null)
        {
            foreach ($result as $item){
                $ketqua = array($item['x'],$item['y']);
            }
            echo json_encode($ketqua);
        }
        else
            return "null 99";
    }

    function getGeoThongkeToAjax($paPDO,$paSRID,$tinh)
    {
        $mySQLStr = "SELECT ST_AsGeoJson(gadm41_vnm_1.geom) as geo from dlieu_point, gadm41_vnm_1 
        where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and name_1 = '".$tinh."'";
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
?>
