<?php
class PdoSql{
	private $host = "localhost";
	private $dbname = "owncloud";
	private $dbuser = "root";
	private  $dbpwd = "root";
	private  $affectRowCount = 0;
	private $lastInsertId = 0;
	private  $conn;

	
	public function __construct($host = "localhost",$dbname = "owncloud",$dbuser = "root",$dbpwd = "root"){
		$this->host = $host;
		$this->dbname = $dbname;
		$this->dbuser = $dbuser;
		$this->dbpwd = $dbpwd;
		try {
			$this->conn = new PDO("mysql:host=$this->host;port=3306;dbname=$this->dbname;",$this->dbuser,$this->dbpwd);
			$this->conn->exec('SET NAMES utf8');
			$this->conn->exec("set @@sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
		} catch (Exception $e) {
			//file_put_contents(__DIR__."\log2.txt", date("Y-m-d H:i:s",$_SERVER['REQUEST_TIME']).",wrong_info:".$e->getMessage()."\n",FILE_APPEND);
		}
	}
	public function __destruct(){
		$this->conn = null;
	}
	
	
	//执行sql
	public function execute($sql,$params = array()){
		$stmt = $this->conn->prepare($sql);
		if ($stmt) {
			if($params) {
				foreach ($params as $k => &$param) {
					$stmt->bindParam($k, $param, PDO::PARAM_STR, strlen($param));
				}
			}
		} else {
			return false;
		}
		$res = $stmt->execute();
		if(!$res){
			$error = $stmt->errorInfo();
			if (isset($error[2]) && $error[2]) {
				file_put_contents(__DIR__."\log.txt", date("Y-m-d H:i:s").",execute_wrong_info:".$error[2]."\n",FILE_APPEND);
			}
		}
		$this->affectRowCount = $res ? $stmt->rowCount() : 0;
		return $stmt;
	}
	//获取一列数据
	public function fetchColumn($sql, $params = array()) {
		$stmt = $this->execute($sql, $params);
		return $stmt->fetchColumn();
	}
	//获取行数
	public function getRowCount() {
		return $this->affectRowCount;
	}
	//获得结果集
	public function fetchAll($sql, $params = array()) {
		$stmt = $this->execute($sql, $params);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	//获得一行数据
	public function fetchRow($sql, $params = array()) {
		$stmt = $this->execute($sql, $params);
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	//插入一行
	public function insert($sql,$params = array()){
		$stmt = $this->execute($sql,$params);
		return $this->affectRowCount;
		//return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	//更新
	public function update($sql,$params = array()){
		$stmt = $this->execute($sql,$params);
		return $this->affectRowCount;
	}
}