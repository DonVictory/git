<?php
	
$postdata = 'homecost_xml='.'<?xml version="1.0" encoding="UTF-8" ?>
<root>
<room name="厅" area="30" perimeter="34.42">
<door name="单开门" long="3.00" width="0.80"/>
<door name="单开门" long="3.00" width="0.80"/>
<window name="标准窗" long="1.20" width="1.20"/>
<door name="推拉门" long="2.00" width="2.40"/>
<door name="单开门" long="6.00" width="0.80"/>
<door name="子母门" long="2.00" width="1.20"/>
<door name="推拉门" long="2.00" width="1.40"/>
<door name="推拉门" long="2.00" width="2.40"/>
</room>
</root>
';
$url = "http://localhost/app/homecost_response/api/post_content.php";
$result = get_html_bycurl($postdata,$url);
print_r($result);


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
?>