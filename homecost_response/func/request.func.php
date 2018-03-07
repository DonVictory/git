<?php
/*
	content：处理方法
	author：@Dong
*/

//get post content
function get_post_from_client(){
	$post_data = isset($_POST['homecost_xml'])? trim($_POST['homecost_xml']):'';
	if(isset($post_data)&&$_SERVER['REQUEST_METHOD'] === 'POST' && $post_data != ''){
		return $post_data;
	}
	return false;
}
//get uinq_filename
function get_uniq_filename($ext){
	return date("YmdHis",$_SERVER['REQUEST_TIME']).md5(uniqid(microtime(true))).'.'.$ext;
}
//curl_post
function get_html_bycurl($postdata,$url){
	$ch = curl_init();

	$header[] = 'Content-type: text/xml'; 
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);

	$result = curl_exec($ch);
	return $result;
}
//create url
function create_url($host,$dir,$script_file){
	return $host.'/'.$dir.'/'.$script_file;
}
//storage html file
function storage_html($dir,$content){
	file_put_contents($dir, $content);
	if(file_exists($dir)){
		return true;
	}
	return false;
}
//return json_package
function return_json($status,$url = ''){
	$xml = "<?xml version='1.0' encoding='UTF-8'><root>";
	$xml .= '<status>'.$status.'</status>';
	$xml .= '<url>'.$url.'</url>';
	$xml .= '</root>';
	return $xml;
	//$json = array("status"=>$status,"url"=>$url);
	//return json_encode($json);
}