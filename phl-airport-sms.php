<?php

// Constants used to access Scraperwiki API.
define("SCRAPERWIKI_API_URL", "http://api.scraperwiki.com/api/1.0/datastore/sqlite");
define("SCRAPERWIKI_FORMAT", "jsondict");
define("SCRAPERWIKI_NAME", "phl-flight-scraperphp");
define("SCRAPERWIKI_QUERY", "select%20*%20from%20%60swdata%60%20where%20%60flight_num%60%20%3D%20%22[[flight_num]]%22%20and%20date%20%3D%20%22[[date]]%22%20and%20%60flight_type%60%20%3D%20%22[[direction]]%22");

// Function to fetch JSON for a specific flight from Scrapewiki.
function getFlightInfo($flight_num, $date, $direction) {
	$direction = (strtolower($direction) == "d") ? "DEPARTURE" : "ARRIVAL";
	$query = str_replace(array("[[flight_num]]", "[[date]]", "[[direction]]"), array($flight_num, $date, $direction), SCRAPERWIKI_QUERY);
	$url = SCRAPERWIKI_API_URL."?format=".SCRAPERWIKI_FORMAT."&name=".SCRAPERWIKI_NAME."&query=".$query;
	_log("*** $url ***");
	return json_decode(file_get_contents($url));
}

// Set the date.
$date = date("m.d.y");

if($currentCall->channel == "VOICE") {
	say("Thank you for caling my test app.");
	$flight = ask("Please say or enter your numeric flight number.", array("choices" => "[1-4 DIGITS]", "attempts" => 3, "timeout" => 5));
	$flight_type = ask("Is your flight an arrival or departure?", array("choices" => "arrival, departure", "attempts" => 3, "timeout" => 5));
	
	$flight_num = $flight->value;
	$direction = $flight_type->value;
}
else {
	// Get flight number entered by user.
	$message = explode(" ", $currentCall->initialText);
	$flight_num = $message[0];
	$direction = $message[1];
}

try {
	$flight_info = getFlightInfo($flight_num, $date, $direction);
	if(count($flight_info) == 0) {
		say("No information found for flight $flight_num on $date.");
	}
	else {
		$leaveorarrive = (strtolower($direction) == "d") ? "leaving for" : "arriving from";
		$say = $flight_info[0]->airline . " Flight " . $flight_info[0]->flight_num . " $leaveorarrive " . ucwords(strtolower($flight_info[0]->destination));
		$say .= " at " . $flight_info[0]->time . " at Gate " . $flight_info[0]->gate . ": " . ucwords(strtolower($flight_info[0]->remarks));
		say($say);
	}
}

catch (Exception $ex) {
	say("Sorry, could not look up flight info. Please try again later.");
}

?>
