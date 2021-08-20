<?php
class RaceSalesItem extends DatabaseObject {

	protected static $tablename = 'racesalesitems';
	protected static $classname = 'RaceSalesItem';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAllForRaceForUpdate($raceid) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ?;";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllForRaces($racelist, $languagecode) {

		$sql = "SELECT DISTINCT a.`id`, CASE WHEN ISNULL(c.`languagecode`) THEN a.`name` ELSE c.`name` END AS `name`,
				a.`price`, a.`allowquantity`, b.`raceid`, CASE WHEN ISNULL(e.`languagecode`) THEN d.`name` ELSE e.`name` END AS `racename`
			FROM `". DB_PREFIX. "salesitems` AS a
			LEFT OUTER JOIN `". DB_PREFIX. self::$tablename. "` AS b
			ON a.`id` = b.`salesitemid`
			LEFT OUTER JOIN `". DB_PREFIX. "salesitemlangs` AS c ON a.`id` = c.`salesitemid` AND c.`languagecode` = ?
			LEFT OUTER JOIN `". DB_PREFIX. "races` AS d ON b.`raceid` = d.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "racelangs` AS e ON b.`raceid` = e.`raceid` AND e.`languagecode` = ?
			WHERE b.`raceid` IN ({$racelist})
			ORDER BY d.`date`, d.`sequence`, CASE WHEN ISNULL(c.`languagecode`) THEN a.`name` ELSE c.`name` END;";
		$params[] = $languagecode;
		$params[] = $languagecode;
		return self::findBySQL($sql, $params);
	}

	public static function findAllForRaceId($raceid, $languagecode) {

		$sql = "SELECT DISTINCT a.`id`, CASE WHEN ISNULL(c.`languagecode`) THEN a.`name` ELSE c.`name` END AS `name`,
				a.`price`, a.`allowquantity`, b.`raceid`, CASE WHEN ISNULL(e.`languagecode`) THEN d.`name` ELSE e.`name` END AS `racename`
			FROM `". DB_PREFIX. "salesitems` AS a
			LEFT OUTER JOIN `". DB_PREFIX. self::$tablename. "` AS b
			ON a.`id` = b.`salesitemid`
			LEFT OUTER JOIN `". DB_PREFIX. "salesitemlangs` AS c ON a.`id` = c.`salesitemid` AND c.`languagecode` = ?
			LEFT OUTER JOIN `". DB_PREFIX. "races` AS d ON b.`raceid` = d.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "racelangs` AS e ON b.`raceid` = e.`raceid` AND e.`languagecode` = ?
			WHERE b.`raceid` = ?
			ORDER BY d.`date`, d.`sequence`, CASE WHEN ISNULL(c.`languagecode`) THEN a.`name` ELSE c.`name` END;";
		$params[] = $languagecode;
		$params[] = $languagecode;
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllByPage($raceid, $paging, $searchname = '') {

		$sql = "SELECT a.*, CASE WHEN b.`raceid` IS NOT NULL THEN 1 ELSE 0 END AS `selected`
			FROM `". DB_PREFIX. "salesitems` AS a
			LEFT OUTER JOIN `". DB_PREFIX. self::$tablename. "` AS b
			ON a.`id` = b.`salesitemid` AND
				b.`raceid` = ?";
		$params[] = $raceid;
		if (!empty($searchname)) {
			$sql .= " WHERE a.`name` LIKE ?";
			$params[] = "%". $searchname. "%";
		}
		$sql .= " ORDER BY a.`name` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findById($raceid, $salesitemid) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `salesitemid` = ? LIMIT 1;";
		$params[] = $raceid;
		$params[] = $salesitemid;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($raceid, $searchname = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. "salesitems` AS a
			LEFT OUTER JOIN `". DB_PREFIX. self::$tablename. "` AS b
			ON a.`id` = b.`salesitemid` AND
				b.`raceid` = ?";
		$params[] = $raceid;
		if (!empty($searchname)) {
			$sql .= " WHERE a.`name` LIKE ?";
			$params[] = "%". $searchname. "%";
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
	public function add() {

		return self::create(self::$tablename);
	}

	public function delete() {

		global $pdo;
		$sql = "DELETE FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `salesitemid` = ? LIMIT 1;";
		$params[] = $this->raceid;
		$params[] = $this->salesitemid;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		return true;
	}
}
?>