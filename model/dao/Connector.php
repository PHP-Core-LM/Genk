<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 4/17/2019
 * Time: 1:10 AM
 */

class Connector
{
    private $conn = null;
    private $HOST = "localhost";
    private $USER = "admin";
    private $PASSWORD ="Leminhho98";
    private $DATABASE = "web_tin_tuc";


    /**
     * @todo Connector constructor.
     */
    function __construct()
    {
        $this->initConnector();
    }


    /**
     * @todo Khởi tạo kết nối PDO
     */
    private function initConnector()
    {
        $option = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", //Hiển thị tiếng việt
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //Thông báo lỗi ra exception
        );
        try
        {
            $this->conn = new PDO('mysql:host='.$this->HOST.";dbname=".$this->DATABASE,
                $this->USER, $this->PASSWORD, $option);
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }


    /**
     * @todo Call procedure
     * @param string $query
     * @param $data
     */
    public function callQuery(string $query, $data)
    {
        $proc = ($this->conn)->prepare($query);
        $proc = $this->bindParams($proc, $data);
        $proc->execute();
    }


    /**
     * @todo Binding giá trị và kiểu dữ liệu các biến vào query
     * @param $proc
     * @param $params
     * @return mixed
     */
    private function bindParams($proc, $params){
        foreach ($params as $param) {
            $index = array_search($param, $params) + 1; //Vị trí đối số được binding trong câu query
            $proc->bindParam($index, $param['Value'], $param['Type']);
        }
        return $proc;
    }


    /**
     * @todo Khởi tạo câu truy vấn gọi procedure
     * @param string $nameProc
     * @param int $numParam
     * @return string
     */
    public function initQueryProcedure(string $nameProc, int $numParam)
    {
        $query = "CALL ".$nameProc."(";
        for ($i = 0; $i < ($numParam - 1); $i++) {
            $query = $query."?, ";
        }
        if ($numParam > 0){
            $query = $query."?)";
        }
        //echo $query;
        return $query;
    }


    /**
     * @todo Khởi tạo thông tin đối số
     * @param array $dataValues
     * @param array $dataType
     * @return array|null
     */
    public function initParam($dataValues, $dataType)
    {
        if (count($dataType) != count($dataValues)) return null;
        $params = array();
        for ($i = 0; $i < count($dataValues); $i++){
            $params[] = array("Value" => $dataValues[$i],
                "Type" => $dataType[$i]); //Mỗi phần tử param sẽ có giá trị và kiểu dữ liệu
        }
        return $params;
    }
}