<?php
/*
	content:处理算法内容
	author:@Dong
*/
class Instance{

	private static $_instance;
	private static $pdo;

	private function __construct(){
		try{
			require_once dirname(dirname(__FILE__)).'\config\config.php';
			self::$pdo = new PDO("mysql:host=".HOST.";dbname=".DBNAME.";port=".PORT.";charset=".CHARSET,DBUSER,DBPWD);
		}catch(Exception $e){
			file_put_contents("log.txt", '初始化|'.date("Y-m-d H:i:s",$_SERVER['REQUEST_TIME'])."|".$e->getMessage()."\n",FILE_APPEND);
		}
	}

	private function __clone(){}

	public static function getInstance(){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	//XML to Array()
	public function xml_array($xml_content){
		$xml = trim($xml_content);
		try{
			if($xml_content){
				libxml_disable_entity_loader(true);
				$xml = simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA); 
				return json_decode(json_encode($xml),true);
			}
		}catch(Exception $e){
			file_put_contents("log.txt", 'XML转Array|'.date("Y-m-d H:i:s",$_SERVER['REQUEST_TIME'])."|".$e->getMessage()."\n",FILE_APPEND);
		}
		
	}

	//request_method
	public function isPost(){
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			return true;
		}
		return false;
	}

	//select_fetchall
	public function selectAll($sql,$params = array()){
		$stmt = self::$pdo->prepare($sql);
		if($stmt){
			if($params) {
				foreach ($params as $k => $param) {
					$stmt->bindParam($k, $param, PDO::PARAM_STR, strlen($param));
				}
			}
		} else {
			return false;
		}
		$res = $stmt->execute();


		/*if($params[':1'] == 7){
			print_r($params);
			print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
		}*/
		if($res){
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	//insert data
	public function insert($sql,$params = array()){
		$stmt = self::$pdo->prepare($sql);
		if($stmt){
			if($params) {
				foreach ($params as $k => $param) {
					$stmt->bindParam($k, $param, PDO::PARAM_STR, strlen($param));
				}
			}
		} else {
			return false;
		}
		$res = $stmt->execute();
		return $res;
	}

	//select_fetch
	public function select($sql,$params = array()){
		$stmt = self::$pdo->prepare($sql);
		if($stmt){
			if($params) {
				foreach ($params as $k => $param) {
					$stmt->bindParam($k, $param, PDO::PARAM_STR, strlen($param));
				}
			}
		} else {
			return false;
		}
		$res = $stmt->execute();
		if($res){
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}
	}

	//get_room_id
	public function get_room_id($room_name){
		$sql = "select room_id from ys_room where room_name=:1";
		$res = $this->select($sql,array(":1"=>$room_name));
		if($res){
			return $res['room_id'];
		}else{
			return false;
		}
	}

	//jiesuo 
	public function get_jiesuan($params = array()){
		$value = 0;
		foreach($params as $v){
			$value += ($v['main']+$v['assistant']+$v['manpower'])*$v['gcl'];
		}
		return sprintf("%.2f",$value);
	}

	//get  area
	public function get_area($array = array()){
		$build_area = 0;
		foreach ($array['room'] as $v) {
			$build_area += round($v['@attributes']['area'],2);
		}
		return $build_area;
	}

	//House type
	public function house_type($res_all = array()){
		$response = '';
		$master_bedRoom = $second_bedroom = $reception_room = $kitchen = $toilet = $balcony = $other_room = 0;
		foreach ($res_all as $v) {
			if($v['@attributes']['name'] == '主卧' || $v['@attributes']['name'] == '次卧'){
				$master_bedRoom++;
			}else if($v['@attributes']['name'] == '厅'){
				$reception_room++;
			}else if($v['@attributes']['name'] == '阳台'){
				$balcony++;
			}else if($v['@attributes']['name'] == '厨房'){
				$kitchen++;
			}else if($v['@attributes']['name'] == '卫生间'){
				$toilet++;
			}else{
				$other_room++;
			}
		}
		if($master_bedRoom) $response .= $master_bedRoom.'室';
		if($reception_room) $response .= $reception_room.'厅';
		if($balcony)		$response .= $balcony.'阳台';
		if($kitchen)		$response .= $kitchen.'厨';
		if($toilet)			$response .= $toilet.'卫';
		return $response;
	}

	//download statistics
	public function download_stat(){
		//$product_id = isset(trim($_POST['homecost']))?trim($_POST['homecost']):'';
		//$check = isset(trim($_POST['check']))?trim($_POST['check']):'';
		$product_id = 'HOMECOST';
		$check = 'homecost_download';
		if(strcmp($check, 'homecost_download') != 0){
			return false;
		}
		
		$ip = trim($_SERVER['REMOTE_ADDR']);
		$time_at = date("Y-m-d H:i:s",$_SERVER['REQUEST_TIME']);
		$sql = 'insert into ys_download(product_id,ip,time_at)values("'.$product_id.'","'.$ip.'","'.$time_at.'")';
		return self::$_instance->insert($sql);
	}

	//echo rows
	public function get_rows($res_all = array()){
		$tmp_name = '';
		$count = 0;
		$response = '';
		$tr_rows = '';
		$material = $manpower = 0;
		foreach($res_all as $v){
			$material += round(($v['main']+$v['assistant'])*$v['gcl'],2);
			$manpower += round($v['manpower']*$v['gcl'],2);
			//print_r($v);exit();
			if($tmp_name != '' && $tmp_name != $v['construction_name']){
					$tr_first = "<tr><td colspan='1' rowspan='".$count."' style='vertical-align:middle;'>".$tmp_name."</td>".$tr_first;
					$response = $response.$tr_first.$tr_rows;
					$tr_first = $tr_rows = '';
					$tmp_name = '';
					$count = 0;
			}
			if($tmp_name != $v['construction_name'] && $count == 0){
				$tmp_name = $v['construction_name'];
				$tr_first = "<td colspan='1' style='vertical-align:middle;'>".(++$count)."</td><td colspan='4'>".$v['construction_item']."</td><td colspan='12'>"
				.$v['construction_item_description']."</td><td colspan='2'>".$v['unit']."</td><td colspan='2'>".round($v['gcl'],2)."</td>
				<td colspan='2'></td><td colspan='2'>".$v['main']."</td><td colspan='2'></td><td colspan='2'>".$v['assistant']."</td>
				<td colspan='2'></td><td colspan='2'>".$v['manpower']."</td><td colspan='2'></td><td colspan='2'>"
				.round(($v['main']+$v['assistant'])*$v['gcl'],2)."</td><td colspan='2'>".round($v['manpower']*$v['gcl'],2)."</td>
				<td colspan='2'>".round(($v['main']+$v['assistant']+$v['manpower'])*$v['gcl'],2)."</td></tr>";
			}else if($tmp_name == $v['construction_name'] && $count > 0){
				if($v['gcl'] == '0') continue;
				$tr_rows .= "<tr><td colspan='1' style='vertical-align:middle;'>".(++$count)."</td><td colspan='4'>".$v['construction_item']."</td>"."<td colspan='12'>"
				.$v['construction_item_description']."</td><td colspan='2'>".$v['unit']."</td><td colspan='2'>".round($v['gcl'],2)."</td><td colspan='2'></td>
				<td colspan='2'>".$v['main']."</td><td colspan='2'></td><td colspan='2'>".$v['assistant']."</td>
				<td colspan='2'></td></td><td colspan='2'>".$v['manpower']."</td><td colspan='2'></td><td colspan='2'>"
				.round(($v['main']+$v['assistant'])*$v['gcl'],2)."</td><td colspan='2'>".round($v['manpower']*$v['gcl'],2)."</td>
				<td colspan='2'>".round(($v['main']+$v['assistant']+$v['manpower'])*$v['gcl'],2)."</td></tr>";
			}
		}
		return array("response"=>$response,"material"=>$material,"manpower"=>$manpower);
	}
}
?>