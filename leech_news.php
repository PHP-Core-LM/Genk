<?php
/**
 * Created by PhpStorm.
 * Author: Lê Minh Hổ
 * Date: 4/17/2019
 * Time: 12:11 AM
 */

include_once "model/util/Leech.php";


//Khởi tạo một số hằng số trạng thái leech
define("ALL", 0);
define("MOBILE", 1);
define("TABLET", 2);
define("DIGITAL_MARKETING", 3);
define("MEDIA", 4);


//Trạng thái leech và số lượng page mặc định
$opt = ALL;
$pageLeech = 1;


//Kiểm tra request yêu cầu leech
if (isset($_GET['opt'])){
    $opt = $_GET['opt'];
}


//Kiểm tra request yêu cầu số page cần leech
if (isset($_GET['page'])){
    $pageLeech = $_GET['page'];
}


//Tiến hành leech
$leech = new Leech(); //Khởi tạo đối tượng leech
$result = null; //Kết quả leech được
if ($opt == ALL)
{
    $result = $leech->leechAllData($pageLeech);
}
else {
    $uri = $leech->getURIs($opt);
    if ($uri != ""){
        $result = $leech->leechPost($uri, $pageLeech);
    }
}

return json_encode($result); //Mã hóa chuỗi kết quả trả về thành dạng json