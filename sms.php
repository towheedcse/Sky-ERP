<?php
// Company's name POST URL
	$postUrl = "http://193.105.74.59/api/sendsms/xml";

	// XML-formatted data
	//$xmlString =
	//"<SMS>
		<authentification>
			<username>parksoft</username>
			<password>S1CCqAPf</password>
		</authentification>
		<message>
			<sender>FBI</sender>
			<text>Don't try to fraud anyone otherwise FBI will take action agaist you. Be careful. </text>
		</message>
	<recipients>
		<gsm>8801822727396</gsm>
		<gsm>8801913918193</gsm>
		<gsm>8801841325588</gsm>
	</recipients>
	</SMS>";

	// previously formatted XML data becomes value of "XML" POST variable
	$fields = "XML=" . urlencode($xmlString);

	// in this example, POST request was made using PHP's CURL
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $postUrl);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	
// response of the POST request
	$response = curl_exec($ch);
	curl_close($ch);

	// write out the response
	echo $response;
?>
