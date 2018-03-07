<?php
	require_once '../class/Instance.class.php';
	$demo = Instance::getInstance();
	if($demo->download_stat()){
		echo true;
	}else{
		echo false;
	}
?>