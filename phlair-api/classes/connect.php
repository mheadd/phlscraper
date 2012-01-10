<?php

/**
 *
 * MySQL Connection Class.
 * @author mheadd
 *
 */
class DBConnect {

	// Private class members.
	private $host;
	private $user;
	private $password;
	private $dbConnection;
	private $dbName;
	private $rowsAffected;
	private $debug;
	private $errorMessage;

	/**
	 *
	 * Class constructor.
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param boolean $debug
	 * @throws Exception
	 */
	public function __construct($host, $user, $password, $debug=false) {

		$this->debug = $debug;
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;

		if(!$this->dbConnection = @mysql_connect($this->host, $this->user, $this->password)) {

			$this->errorMessage = 'Could not establish connection. ';
			if($this->debug) {
				$this->errorMessage .= mysql_error();
			}

			throw new Exception($this->errorMessage);

		}
	}

	/**
	 *
	 * Select a database to use.
	 * @param string $name
	 * @throws Exception
	 */
	public function selectDB($name) {
			
		if(!mysql_select_db($name, $this->dbConnection)) {

			$this->errorMessage = 'Could not connect to database. ';
			if($this->debug) {
				$this->errorMessage .= mysql_error();
			}

			throw new Exception($this->errorMessage);

		}
	}

	/**
	 *
	 * Escape input prior to using it in a SQL query.
	 * @param mixed $value
	 */
	public function escapeInput($value) {

		return mysql_real_escape_string($value, $this->dbConnection);

	}

	/**
	 *
	 * Run a query against a database table and return the result set.
	 * @param string $query
	 * @throws Exception
	 */
	public function runQuery($query) {
			
		$result = mysql_query($query, $this->dbConnection);
		$this->rowsAffected = mysql_affected_rows();
			
		if(!$result) {

			$this->errorMessage = 'Could not execute query. ';
			if($this->debug) {
				$this->errorMessage .= mysql_error();
			}
			throw new Exception($this->errorMessage);

		}

		return $result;

	}

	/**
	 *
	 * Determine the number of rows affected in the last operation.
	 */
	public function getNumRowsAffected() {
		return $this->rowsAffected;
	}

	/**
	 *
	 * Close the database connection.
	 */
	public function __destruct() {
		mysql_close($this->dbConnection);
	}
}

?>