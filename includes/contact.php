<?php
class Contact extends DatabaseObject {

	protected static $tablename = 'contacts';
	protected static $classname = 'Contact';
	
	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `role`;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($paging, $searchrole = '') {

		$params = Array();
		$sql = "SELECT a.*, b.`name` AS `racename`, DATE_FORMAT(b.`date`, '%e %b %Y') AS `racedate`
			FROM `". DB_PREFIX. self::$tablename. "` AS a 
			LEFT OUTER JOIN `". DB_PREFIX. "races` AS b ON a.`raceid` = b.`id`";
		if (!empty($searchrole)) {
			$sql .= " WHERE a.`role` LIKE ?";
			$params[] = "%". $searchrole. "%'";
		}
		$sql .= " ORDER BY b.`date` DESC, a.`role` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		return self::findBySQL($sql, $params);
	}

	public static function findAllForOpenRaces($siteid) {

		if (!is_numeric($siteid)) die();
		$sql = "SELECT a.*, b.`name` FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "races` AS b ON a.`raceid` = b.`id`
			WHERE b.`siteid` = ? OR b.`siteid` IS NULL
			ORDER BY b.`date` DESC, b.`name`, a.`role`;";
		$params[] = $siteid;
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

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($searchrole = '') {

		global $pdo;
		$params = Array();
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`";
		if (!empty($searchrole)) {
			$sql .= " WHERE `role` LIKE ?";
			$params[] = "%". $searchrole. "%";
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