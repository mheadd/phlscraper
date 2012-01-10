<?php

// Include required classes.
require '../classes/limonade.php';
require '../classes/connect.php';
require '../config/config.php';

/**
 *
 * Function to query MySQL instance and retrieve details of a specified flight.
 * @param int $num
 * @param string $type
 */
function lookupFlightDetails($flight_number, $type) {

	try {

		// Connect to MySQL instance.
		$conn = new DBConnect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
		$conn->selectDB(MYSQL_NAME);

		// Build and execute query.
		$flight_number = strtolower($flight_number);
		$flight_number = $conn->escapeInput($flight_number);
		$query = str_replace('%table%', $type, API_SQL_TPL);
		if($flight_number != 'all') {
			$query .= " AND flight_number='$flight_number'";
		}
		$result = $conn->runQuery($query);

		// Format response array.
		$flights = array();
		while ($flight = mysql_fetch_assoc($result)) {
			array_push($flights, $flight);
		}

		return $flights;

	}

	catch (Exception $ex) {
		Logger::writeLog("API: " . $ex->getMessage());
		return false;
	}

}

/**
 * 
 * Formats the response for return to the requestin client.
 * @param array $response
 * @param string $callback
 * @param string $content_type
 */
function formatResponse(Array $response, $callback=NULL, &$content_type) {

	if(is_null($callback)) {
		$content_type = 'application/json';
		return json_encode($response);
	}
	else {
		$content_type = 'application/javascript';
		return "$callback(" . json_encode($response) . ")";
	}

}

// Routes.
dispatch('/', 'returnVersion');
dispatch('/'.API_VERSION.'/:type/:flight_number/:callback', 'getFlightInformation');

/**
 *
 * Function to return basic API information.
 */
function returnVersion() {
	header("Content-type: application/json");
	return json_encode(array("name" => "PHLAIR API", "version" => API_VERSION));
}

/**
 *
 * Function to return flight information.
 */
function getFlightInformation() {
	
	// Content type to return with response.
	$content_type = 'text/plain';


	// Get parameters submitted with request.
	$flight_number = params('flight_number');
	$flight_type = params('type') == 'arrivals' ? 'arrivals' : 'departures';
	$callback = params('callback');

	// Retireve flight details.
	$details = lookupFlightDetails($flight_number, $flight_type);

	// Build response.
	if(!$details) {
		$response = formatResponse(array("message" => "Resource not found."));
		header("HTTP/1.1 404 Not Found");
	}
	elseif(count($details) == 0) {
		$response = formatResponse(array(), $callback, $content_type);
		header("Content-type: $content_type");
	}
	elseif(count($details) > 0) {
		$response = formatResponse($details, $callback, $content_type);
		header("Content-type: $content_type");
	}
	else {
		$response = formatResponse(array("message" => "An error occured."));
		header("HTTP/1.1 500 Internal Server Error");
	}
	
	return $response;

}

run();


?>