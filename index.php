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
	// Folio
	if($intent == "pipeline.folio.deploy"){
		$url="https://gitlab.example.com/api/v4/projects/8641028/ref/master/trigger/pipeline?token=400b1e0c0ffbdac008c06da4c9d370&variables[CI_COMMIT_MESSAGE]=update-folio";
		$textresponse = "Je viens de lancer le déploiement";
	}
	if($intent == "pipeline.services-cloud"){
		$url="https://gitlab.example.com/api/v4/projects/8641028/ref/master/trigger/pipeline?token=400b1e0c0ffbdac008c06da4c9d370&variables[CI_COMMIT_MESSAGE]=update";
		$textresponse = "Je viens de lancer le déploiement";
	}
	
	$result = file_get_contents($url, false, '');

}
$dataresponse = array("fulfillmentText" => $textresponse);
echo json_encode($dataresponse);
?>
