<?php
class Preallocation extends DatabaseObject {

	protected static $tablename = 'preallnos';
	protected static $classname = 'Preallocation';

	public function __clone() {

		unset($this->id);
	}

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAllForRace($raceid) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? ORDER BY `racenumber`;";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllByPage($raceid, $paging, $searchnumber = '') {

		$sql = "SELECT a.*, CASE WHEN ISNULL(b.`id`) THEN 'admin' ELSE b.`name` END AS `clubname`,
			CASE WHEN ISNULL(c.`id`) THEN '*Unallocated' ELSE CONCAT(c.firstname, ' ', c.surname) END AS status
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS b ON a.`clubid` = b.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "entries` AS c ON a.`raceid` = c.`raceid` AND a.`racenumber` = c.`racenumber`
			WHERE a.`raceid` = ?";
		$params[] = $raceid;
		if (!empty($searchnumber)) {
			$sql .= " AND a.`racenumber` >= ?";
			$params[] = $searchnumber;
		}
		$sql .= " ORDER BY a.`racenumber` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
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
	public static function countAll($raceid, $searchnumber = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ?";
		$params[] = $raceid;
		if (!empty($searchnumber)) {
			$sql .= " AND `racenumber` >= ?";
			$params[] = $searchnumber;
		}
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	public static function countAllForClub($raceid, $clubid) {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "entries` AS b ON a.`raceid` = b.`raceid` AND a.`racenumber` = b.`racenumber`
			WHERE a.raceid = ? AND a.`clubid` = ? AND b.`id` IS NULL;";
		$params[] = $raceid;
		$params[] = $clubid;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	public static function countAllUnused($raceid) {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND
				`racenumber` NOT IN (SELECT `racenumber` FROM `". DB_PREFIX. "entries` WHERE `raceid` = ? AND `paid`);";
		$params[] = $raceid;
		$params[] = $raceid;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	// ********************************************************************************
	// Public static functions (other)
	public static function deleteAllForRace($raceid) {

		global $pdo;
		$stmt = $pdo->prepare("DELETE FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ?;");
		$params[] = $raceid;
		$stmt->execute($params);
		return true;
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