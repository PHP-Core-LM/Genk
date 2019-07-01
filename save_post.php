<?php
/**
 * Created by PhpStorm.
 * Author: Lê Minh Hổ
 * Date: 4/17/2019
 * Time: 12:11 AM
 */

//Khởi tạo parameter để leech
$_GET["page"] = 1; //Chỉ định load 3 trang
$_GET["opt"] = 1; //Chỉ định load phần tin tức mobile
$leech = include_once "leech_news.php";

$result = json_decode($leech, true);

include_once "model/dao/Connector.php";
$connector = new Connector();
$query = $connector->initQueryProcedure("addNewPost", count($result[0])); //Khởi tạo câu truy vấn
$params = $connector->initParam(array_values($result[0]),
    array(PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR,
        PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR)); //Khởi tạo danh sách đối số
$connector->callQuery($query, $params);

