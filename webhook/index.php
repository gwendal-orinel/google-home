<?php
$content = trim(file_get_contents("php://input"));
$decoded = json_decode($content, true);
$intent= $decoded["queryResult"]["action"];

$url="https://api.thingspeak.com/channels/526367/feeds.json?api_key=APIKEY&results=1";
$result = file_get_contents($url);
$data=json_decode($result, true);
$hum=round($data["feeds"]["0"]["field1"],0);
$temp_air=round($data["feeds"]["0"]["field2"],0);
$temp_eau=round($data["feeds"]["0"]["field3"],0);
$lum=round($data["feeds"]["0"]["field4"],0);

if($intent == "request.temperature.water"){
	$textresponse = "La température de l'aquarium est de ".$temp_eau." degrés";
}
if($intent == "request.temperature.air"){
	$textresponse = "La température de l'air est de ".$temp_air." degrés";
}
if($intent == "request.moisture"){
	$textresponse = "Le taux d'humidité dans l'air est de ".$hum." pourcent";
}
if($intent == "request.light-rate"){
	$textresponse = "La pièce est éclairée à ".$lum." pourcent";
}

$dataresponse = array("fulfillmentText" => $textresponse);
echo json_encode($dataresponse);
?>
