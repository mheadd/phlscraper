<?php

// Include required classes.
require '../classes/simple_html_dom.php';
require '../classes/connect.php';
require '../config/config.php';

/**
 * 
 * Scrape a URL.
 * @param string $url
 */
function scrape($url) {
	@$file = file_get_contents($url);
	return $file;
}

/**
 * 
 * Get rid of whitespace and spaces.
 * @param string $text
 */
function removeSpaces($text) {
	return trim(str_replace("&nbsp;", "", $text));
}

/**
 *
 * Function to save data in MySQL instance.
 * @param array $data
 * @param string $type
 */
function saveData(Array $data, $type) {

	$table = $type == "DEPARTURE" ? "departures" : "arrivals";

	try {

		// Set up MySQL connection.
		$conn = new DBConnect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
		$conn->selectDB(MYSQL_NAME);

		// Update / insert record.

		$query = str_replace(array('%table%', '%time%', '%gate%', '%remarks%', '%airline%', '%flight_number%'),
		array($table, $data['time'], $data['gate'], ucwords(strtolower($data['remarks'])), $data['airline'], $data['flight_number']),
		SCRAPER_SQL_TPL);

		// Field name differs in arrival & departure tables.
		if($type == "DEPARTURE") {
			$query .= ", destination='";
		}
		else {
			$query .=", origin='";
		}
		$query .= $data['destination']."'";

		$result = $conn->runQuery($query);

		if(!$result) {
			Logger::writeLog("SCRAPER: Could not update record: " . mysql_error($conn) . " Query: " . $query);
		}
		else {
			if($conn->getNumRowsAffected() == 0) {
				Logger::writeLog("SCRAPER: Could not update record. Query: " . $query);
			}
		}
		return;

	}
	catch (Exception $ex) {
		Logger::writeLog("SCRAPER: " . $ex->getMessage());
		return;
	}

}

/**
 *
 * Function to process HTML scraped from web page.
 * @param string $param
 * @param string $type
 * @param object $conn
 */
function processHTML($param, $type) {

	$dom = new simple_html_dom();
	$dom->load(scrape(BASE_URL."?type=$param"));

	foreach($dom->find("TR[@HEIGHT='25']") as $data) {

		// Flight details.
		$tds = $data->find("td");

		$airline = removeSpaces($tds[0]->plaintext);
		$flight_number = removeSpaces($tds[1]->plaintext);
		$destination = removeSpaces($tds[2]->plaintext);
		$time = removeSpaces($tds[3]->plaintext);
		$gate = removeSpaces($tds[4]->plaintext);
		$remarks = removeSpaces($tds[5]->plaintext);

		// Skip header row. Cheesy, but effective.
		if($airline == "Airline") {
			continue;
		}

		// Build up record to store.
		$flight_data = array("airline"=>$airline, "flight_number"=>$flight_number, "destination"=>$destination, "time"=>$time, "gate"=>$gate, "remarks"=>$remarks);

		// Save record.
		saveData($flight_data, $type);

	}

	$dom->clear();

}

// Process departures.
processHTML("dflno", "DEPARTURE");

// Process arrivals.
processHTML("aflno", "ARRIVAL");

echo date(DATE_RFC822) . ": Flight information update complete.";

?>