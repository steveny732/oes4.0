<?php
class Entrant extends DatabaseObject {

	protected static $tablename = 'entrants';
	protected static $classname = 'Entrant';
	
	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `surname`, `firstname`;";
		return self::findBySQL($sql);
	}

	public static function findAllBySurname($paging, $surname) {

		$sql = "SELECT MAX(`id`) AS `id`, `firstname`, `surname`, DATE_FORMAT(`dob`, '%e %b %Y') AS `dob`, `postcode`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `surname` LIKE ?
			GROUP BY `surname`, `firstname`, DATE_FORMAT(`dob`, '%e %b %Y'), `postcode`
			ORDER BY `surname`, `firstname`, `dob`, `postcode` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		$params[] = $surname. "%";
		return self::findBySQL($sql, $params);
	}

	public static function findByRegisteredEntrant($id) {

		if (!is_numeric($id)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `id` = ? OR `mainentrantid` = ?
			ORDER BY `surname`, `firstname`";
		$params[] = $id;
		$params[] = $id;
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findById($id) {

		if (!is_numeric($id)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `id` = ? LIMIT 1;";
		$params[] = $id;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findByIdWithClub($id) {

		if (!is_numeric($id)) die();
		$sql = "SELECT a.*, b.`name` AS `clubname`, b.`affiliated` AS `memberaffil`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS b ON a.`clubid` = b.`id`
			WHERE a.`id` = ? LIMIT 1;";
		$params[] = $id;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findByDetails($surname, $firstname, $postcode, $dob) {

		$sql = "SELECT * FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `surname` = ? AND
				`firstname` = ? AND
				`postcode` = ? AND
				`dob` = ?
			ORDER BY `id` DESC LIMIT 1";
		$params[] = $surname;
		$params[] = $firstname;
		$params[] = $postcode;
		$params[] = $dob;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findByPasswordRef($passwordref) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `passwordref` = ?
			LIMIT 1;";
		$params[] = $passwordref;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findByEmailWithUsername($email) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `email` = ? AND
				`username` IS NOT NULL AND `username` <> ''
			LIMIT 1;";
		$params[] = $email;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findByUsername($username) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `username` = ?
			LIMIT 1;";
		$params[] = $username;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function authenticate($username, $password) {

		$sql = "SELECT * FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `username` = ? AND
				`password` = ?
				LIMIT 1;";
		$params[] = $username;
		$params[] = $password;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAllBySurname($surname = '') {

		global $pdo;
		$params = Array();
		$sql = "SELECT COUNT(*) AS `count` FROM `". DB_PREFIX. self::$tablename. "` ";
		if (!empty($surname)) {
			$sql .= "WHERE `surname` LIKE ?;";
			$params[] = $surname. "%";
		}
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	// ********************************************************************************
	// Protected static functions
	protected static function findBySQL($sql, $params = Array()) {

		global $pdo;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll(PDO::FETCH_CLASS, self::$classname);
	}

	// ********************************************************************************
	// Public instance functions
	public function save() {

		return isset($this->id) ? self::update(self::$tablename) : self::create(self::$tablename);
	}

	public function delete() {

		parent::deleteObject(self::$tablename);
	}
}
?>