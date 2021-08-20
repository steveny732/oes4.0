<?php
class RaceNo extends DatabaseObject {

	protected static $tablename = 'racenos';
	protected static $classname = 'RaceNo';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `racenumber`;";
		return self::findBySQL($sql);
	}

	public static function findAllUnused($raceid) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT CASE WHEN ISNULL(c.`raceid`) THEN '' ELSE 'P' END AS `preall`, a.`racenumber`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "entries` AS b ON a.`raceid` = b.`raceid` AND a.`racenumber` = b.`racenumber`
			LEFT OUTER JOIN `". DB_PREFIX. "preallnos` AS c ON a.`raceid` = c.`raceid` AND a.`racenumber` = c.`racenumber`
			WHERE a.`raceid` = ? AND
			b.`raceid` IS NULL
			ORDER BY a.`racenumber`";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findByNumber($raceid, $racenumber) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `racenumber` = ? LIMIT 1;";
		$params[] = $raceid;
		$params[] = $racenumber;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findWithoutPreall($raceid, $racenumber) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT * FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `racenumber` = ? AND 
			`racenumber` NOT IN (SELECT `racenumber` FROM `". DB_PREFIX. "preallnos` WHERE `raceid` = ?) LIMIT 1;";
		$params[] = $raceid;
		$params[] = $racenumber;
		$params[] = $raceid;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function deleteAllForRace($raceid) {

		if (!is_numeric($raceid)) die();
		global $pdo;
		$sql = "DELETE FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ?;";
		$params[] = $raceid;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		return true;
	}

	public static function getNextFreeNumber($raceid, $seriesid, $seriesracenumbers, $clubid) {

		$sql = "SELECT `racenumber`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `racenumber` NOT IN (SELECT `racenumber` FROM `". DB_PREFIX. "entries` WHERE ";
		$params[] = $raceid;
		if (!empty($seriesid) && $seriesracenumbers) {
			$sql .= "`raceid` IN (SELECT `id` FROM `". DB_PREFIX. "races` WHERE `seriesid` = ?) AND ";
			$params[] = $seriesid;
		} else {
			$sql .= "`raceid` = ? AND ";
			$params[] = $raceid;
		}
		$sql .= "`racenumber` IS NOT NULL) AND
			(`racenumber` NOT IN (SELECT `racenumber` FROM `". DB_PREFIX. "preallnos` WHERE `raceid` = ?)";
		$params[] = $raceid;
		if (!empty($clubid)) {
			$sql .= " OR `racenumber` IN (SELECT `racenumber` FROM `". DB_PREFIX. "preallnos` WHERE `raceid` = ? AND
				`clubid` = ?)";
			$params[] = $raceid;
			$params[] = $clubid;
		}
		$sql .= ") ORDER BY `racenumber` LIMIT 1;";
		$racenumber = '';
		$resultArray = self::findBySQL($sql, $params);
		if (!empty($resultArray)) {
			$row = array_shift($resultArray);
			$racenumber = $row->racenumber;
		}
		return $racenumber;
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