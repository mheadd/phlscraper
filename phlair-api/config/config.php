<?php 

// MySQL constants
define("MYSQL_HOST", "");
define("MYSQL_USER", "");
define("MYSQL_PASS", "");
define("MYSQL_NAME", "phlair");

// API version
define("API_VERSION", "1.0");

// SQL templates
define("API_SQL_TPL", "SELECT * FROM %table% WHERE date = DATE(NOW())");
define("SCRAPER_SQL_TPL", "REPLACE INTO %table% SET time = '%time%', gate = '%gate%', remarks = '%remarks%', date = DATE(NOW()), airline = '%airline%', flight_number = '%flight_number%'");

// Base URL for PHL flight info.
define("BASE_URL", "http://www.phl.org/cgi-bin/fidscooltext.pl");

class Logger {
	
	public static function writeLog($message) {
		openlog("PHLAIR", LOG_ODELAY, LOG_USER);
		syslog(LOG_ERR, $message);
		closelog();
	}
	
}

?>