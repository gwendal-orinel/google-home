<?php
$content = trim(file_get_contents("php://input"));
$decoded = json_decode($content, true);
$intent= $decoded["queryResult"]["action"];

$context = explode(".", $intent)[0];
if($context == "aquarium1"){
	// Aquarium
	$url="https://api.thingspeak.com/channels/526367/feeds.json?api_key=NJV1J5WTMBJ4YB3D&results=1";
	$result = file_get_contents($url);
	$data=json_decode($result, true);
	$hum=round($data["feeds"]["0"]["field1"],0);
	$temp_air=round($data["feeds"]["0"]["field2"],0);
	$temp_eau=round($data["feeds"]["0"]["field3"],0);
	$lum=round($data["feeds"]["0"]["field4"],0);

	if($intent == "aquarium1.request.temperature.water"){
		$textresponse = "La température de l'aquarium est de ".$temp_eau." degrés";
	}
	if($intent == "aquarium1.request.temperature.air"){
		$textresponse = "La température de l'air est de ".$temp_air." degrés";
	}
	if($intent == "aquarium1.request.moisture"){
		$textresponse = "Le taux d'humidité dans l'air est de ".$hum." pourcent";
	}
	if($intent == "aquarium1.request.light-rate"){
		$textresponse = "La pièce est éclairée à ".$lum." pourcent";
	}
}
if($context == "pipeline"){
	// Deploy
	$action='';
	if($intent == "pipeline.deploy"){
		$name=$decoded["queryResult"]["parameters"]["name"];
		$confirm=$decoded["queryResult"]["parameters"]["confirm"];
		if($confirm == "oui"){ 
			if($name == "webhook"){
				$action='update-webhook';
				$textresponse = "Je viens de lancer le déploiement du serveur ".$name;
			}
			if($name == "proxy"){
				$action='update';
				$textresponse = "Je viens de lancer le déploiement du serveur ".$name;
			}
		}else{
			$textresponse = "J'annule le déploiement";	
		}
	}
	if($confirm == "oui"){
		$url="https://gitlab.com/api/v4/projects/8641028/trigger/pipeline";
		$postdata = http_build_query(array(
			'token' => '400b1e0c0ffbdac008c06da4c9d370',
			'variables[CI_COMMIT_MESSAGE]' => $action,
			'ref' => 'master',
		    ));
		$opts = array('http' =>
		    array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata
		    ));
		$body  = stream_context_create($opts);
		$result = file_get_contents($url, false, $body);
	}
}
$dataresponse = array("fulfillmentText" => $textresponse);
echo json_encode($dataresponse);
?>
