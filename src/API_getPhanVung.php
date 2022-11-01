<?php
    if(isset($_POST['functionname'])) 
    {
        $paPDO = initDB();
        $paSRID = '0'; 
        $paPoint = $_POST['paPoint'];
        $functionname = $_POST['functionname'];
        
        $aResult = "null";
        if ($functionname == 'getGeoCMRToAjax1'){
            $caNhiem = $_POST['caNhiem'];
            $aResult = getGeoCMRToAjax1($paPDO,$caNhiem);
        }
        else if ($functionname == 'getGeoCMRToAjax2'){
            $caNhiem = $_POST['caNhiem'];
            $aResult = getGeoCMRToAjax2($paPDO);
        }
        else if ($functionname == 'getGeoCMRToAjax3'){
            $caNhiem = $_POST['caNhiem'];
            $aResult = getGeoCMRToAjax3($paPDO);
        }
        else if ($functionname == 'getGeoCMRToAjax4'){
            $caNhiem = $_POST['caNhiem'];
            $aResult = getGeoCMRToAjax4($paPDO);
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
    
    // Hàm fill màu theo canhiem
    function getGeoCMRToAjax1($paPDO,$caNhiem)
    {
        $mySQLStr = "SELECT ST_AsGeoJson(gadm41_vnm_1.geom) as geo from dlieu_point, gadm41_vnm_1 
         where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and canhiem <= ".$caNhiem." ";
        $result = query($paPDO, $mySQLStr);
        $ketqua = array();

        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                array_push($ketqua,$item['geo']);
            }
            return json_encode($ketqua);
        }
        else
            return "null";
    }
    getGeoCMRToAjax1(initDB(),50);

    function getGeoCMRToAjax2($paPDO)
    {
        $mySQLStr = "SELECT ST_AsGeoJson(gadm41_vnm_1.geom) as geo from dlieu_point, gadm41_vnm_1 
         where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and canhiem <= 100 and canhiem > 50";
        $result = query($paPDO, $mySQLStr);
        $ketqua = array();
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                array_push($ketqua,$item['geo']);
            }
            return json_encode($ketqua);
        }
        else
            return "null";
    }
    getGeoCMRToAjax2(initDB());

    function getGeoCMRToAjax3($paPDO)
    {
        $mySQLStr = "SELECT ST_AsGeoJson(gadm41_vnm_1.geom) as geo from dlieu_point, gadm41_vnm_1 
         where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and canhiem <= 150 and canhiem >100 ";
        $result = query($paPDO, $mySQLStr);
        $ketqua = array();
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                array_push($ketqua,$item['geo']);
            }
            return json_encode($ketqua);
        }
        else
            return "null";
    }
    getGeoCMRToAjax3(initDB());

    function getGeoCMRToAjax4($paPDO)
    {
        $mySQLStr = "SELECT gadm41_vnm_1.name_1 ,ST_AsGeoJson(gadm41_vnm_1.geom) as geo from dlieu_point, gadm41_vnm_1 
         where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and canhiem > 150 ";
        $result = query($paPDO, $mySQLStr);
        $ketqua = array();
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                array_push($ketqua,$item['geo']);
                

            }
            return json_encode($ketqua);
        }
        else
            return "null";
    }
    getGeoCMRToAjax4(initDB());
    
?>