<?php
    if(isset($_POST['functionname'])) 
    {
        $paPDO = initDB();
        $functionname = $_POST['functionname'];
        
        $aResult = "null";
         if ($functionname == 'getTinh1')
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
   
     //Vung xanh
     function getTinh1($paPDO)
    {
        $mySQLStr = "SELECT gadm41_vnm_1.name_1 from \"gadm41_vnm_1\", \"dlieu_point\"
         where \"gadm41_vnm_1\".gid_1 = \"dlieu_point\".gid_1 and canhiem <= 50 ";
        $result = query($paPDO, $mySQLStr);
        $ketqua = array();

        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                array_push($ketqua,$item['name_1']);
            }
            $resFin = '<ul>';
            foreach ($ketqua as $item){
                $resFin = $resFin.'<li class ="tinhThanh">'.$item.'</li>';
            }
            $resFin = $resFin.'</ul>';
            return $resFin;

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
        $result = query($paPDO, $mySQLStr);

        $ketqua = array();
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                array_push($ketqua,$item['name_1']);
            }
            $resFin = '<ul>';
            foreach ($ketqua as $item){
                $resFin = $resFin.'<li class ="tinhThanh">'.$item.'</li>';
            }
            $resFin = $resFin.'</ul>';
            return $resFin;

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
        $result = query($paPDO, $mySQLStr);
        $ketqua = array();

        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                array_push($ketqua,$item['name_1']);
            }
            $resFin = '<ul>';
            foreach ($ketqua as $item){
                $resFin = $resFin.'<li class ="tinhThanh">'.$item.'</li>';
            }
            $resFin = $resFin.'</ul>';
            return $resFin;

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
        $result = query($paPDO, $mySQLStr);

        $ketqua = array();
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                array_push($ketqua,$item['name_1']);
            }
            $resFin = '<ul>';
            foreach ($ketqua as $item){
                $resFin = $resFin.'<li class ="tinhThanh">'.$item.'</li>';
            }
            $resFin = $resFin.'</ul>';
            return $resFin;

        }
        else
            return "null";
    }
    getTinh4(initDB());

?>
