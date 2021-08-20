<?php
include_once("pdo_mysql.php");
class MySQLDatabase {

	private $connection;

	function __construct() {
		$this->openConnection();
	}

	public function openConnection() {
		$this->connection = pdo_connect(DB_SERVER, DB_USER, DB_PASS) or die("Database connection failed: ". pdo_error());
		if ($this->connection) {
			$db_select = pdo_select_db(DB_NAME, $this->connection) or die("Database selection failed: ". pdo_error());
			pdo_query('SET NAMES "utf8"');
			pdo_query('SET sql_mode = "";');
		}
	}

	public function closeConnection() {
		if (isset($this->connection)) {
			unset($this->connection);
		}
	}

	public function query($sql) {
		$result = pdo_query($sql, $this->connection) or die("Database query failed: ". $sql);
		return $result;
	}
	
	public function escapeValue($value) {
		if (REAL_ESCAPE_STRING_EXISTS) {
			if (MAGIC_QUOTES_ACTIVE) {
				$value = stripslashes($value);
			}
			$value = pdo_real_escape_string($value);
		} else {
			if (!MAGIC_QUOTES_ACTIVE) {
				$value = addslashes($value);
			}
		}
		return $value;
	}
	
	public function fetchArray($resultSet) {
		return pdo_fetch_array($resultSet, MYSQL_ASSOC);
	}

	public function numRows($resultSet) {
		return pdo_num_rows($resultSet);
	}

	public function insertId() {
		return pdo_insert_id($this->connection);
	}

	public function affectedRows() {
		return pdo_affected_rows($this->connection);
	}
}

$db = new MySQLDatabase();
?>