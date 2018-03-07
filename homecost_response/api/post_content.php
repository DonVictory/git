<?php
require_once '../func/request.func.php';

$post_data = get_post_from_client();


/*$xml_content = '<?xml version="1.0" encoding="UTF-8" ?>
<root>
<room name="厅" area="50" perimeter="34.42">
<door name="单开门" long="3.00" width="0.80"/>
<door name="单开门" long="3.00" width="0.80"/>
<window name="标准窗" long="1.20" width="1.20"/>
<door name="推拉门" long="2.00" width="2.40"/>
<door name="单开门" long="6.00" width="0.80"/>
<door name="子母门" long="2.00" width="1.20"/>
<door name="推拉门" long="2.00" width="1.40"/>
<door name="推拉门" long="2.00" width="2.40"/>
</room>
<room name="次卧" area="50" perimeter="30">
<door name="单开门" long="2.00" width="0.80"/>
<window name="标准窗" long="1.20" width="1.50"/>
</room>
<room name="阳台" area="11.45" perimeter="14.80">
<door name="推拉门" long="3.00" width="2.40"/>
<door name="推拉门" long="2.00" width="2.40"/>
</room>
<room name="次卧" area="50" perimeter="30">
<door name="单开门" long="2.00" width="0.80"/>
<window name="标准窗" long="1.20" width="1.50"/>
</room>
<room name="主卧" area="30" perimeter="50">
<door name="单开门" long="2.00" width="0.80"/>
<window name="标准窗" long="1.20" width="1.50"/>
</room>
</root>
';*/
if($post_data){
	$host = 'http://localhost/app/homecost_response/';
	$url = $host.'index.php';
	$filename = get_uniq_filename('html');
	$res = get_html_bycurl($post_data,$url);
	if(storage_html('../html/'.$filename,$res)){
		echo return_json(1,$host.'html/'.$filename);
	}else{
		echo return_json(0);
	}
	
}

