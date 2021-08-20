<?php
class Result extends DatabaseObject {

	protected static $tablename = 'results';
	protected static $classname = 'Result';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `pos`;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($raceid, $paging, $searchname = '', $searchagecategory = '', $searchclubid = '', $searchracenumber = '', $searchsort = '') {

		$sql = "SELECT a.*, b.`racenumber`, b.`firstname`, b.`surname`, b.`agecategorycode`, IFNULL(c.`name`, 'Unattached') AS `clubname`, b.`team`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "entries` AS b ON a.`entryid` = b.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS c ON b.`clubid` = c.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "agecategories` AS d ON b.`agecategorycode` = d.`code`
			WHERE a.`raceid` = ?";
		$params[] = $raceid;
		if (!empty($searchname)) {
			$sql .= " AND (b.`surname` LIKE ? OR
				b.`firstname` LIKE ?)";
			$params[] = "%". $searchname. "%";
			$params[] = "%". $searchname. "%";
		}
		if (!empty($searchagecategory)) {
			if ($searchagecategory == 'M') {
				$sql .= " AND NOT d.`femaleonly`";
			} elseif ($searchagecategory == 'F') {
				$sql .= " AND NOT d.`maleonly`";
			} else {
				$sql .= " AND b.`agecategorycode` LIKE ?";
				$params[] = $searchagecategory. "%";
			}
		}
		if (!empty($searchclubid)) {
			$sql .= " AND IFNULL(c.`id`, '--') = ?";
			$params[] = $searchclubid;
		}
		if (!empty($searchracenumber)) {
			$sql .= " AND b.`racenumber` = ?";
			$params[] = $searchracenumber;
		}
		if (!empty($searchsort)) {
			if ($searchsort == 'name') {
				$sql .= " ORDER BY b.`surname`, b.`firstname`, a.`pos`";
			} elseif ($searchsort == 'racenumber') {
				$sql .= " ORDER BY b.`racenumber`, a.`pos`";
			} elseif ($searchsort == 'agegrade') {
				$sql .= " ORDER BY a.`agegrade` DESC, a.`pos`";
			} elseif ($searchsort == 'chiptime') {
				$sql .= " ORDER BY a.`chiptime`, a.`pos`";
			} elseif ($searchsort == 'chiptime2') {
				$sql .= " ORDER BY a.`chiptime2`, a.`pos`";
			} elseif ($searchsort == 'chiptime3') {
				$sql .= " ORDER BY a.`chiptime3`, a.`pos`";
			} elseif ($searchsort == 'chiptime4') {
				$sql .= " ORDER BY a.`chiptime4`, a.`pos`";
			} elseif ($searchsort == 'chiptime5') {
				$sql .= " ORDER BY a.`chiptime5`, a.`pos`";
			} elseif ($searchsort == 'chiptime6') {
				$sql .= " ORDER BY a.`chiptime6`, a.`pos`";
			} elseif ($searchsort == 'pos') {
				$sql .= " ORDER BY a.`pos`";
			}
		} else {
			$sql .= " ORDER BY a.`pos`";
		}
		$sql .= " LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		return self::findBySQL($sql, $params);
	}

	public static function findAllClubs($raceid) {

		$sql = "SELECT DISTINCT IFNULL(c.`id`, '--') AS `clubid`, IFNULL(c.`name`, 'Unattached') AS `clubname`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "entries` AS b ON a.`entryid` = b.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS c ON b.`clubid` = c.`id`
			WHERE a.`raceid` = ?
			ORDER BY c.`name`;";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllAgeCategories($raceid) {

		$sql = "SELECT DISTINCT b.`agecategorycode`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "entries` AS b ON a.`entryid` = b.`id`
			WHERE a.`raceid` = ?
			ORDER BY b.`agecategorycode`;";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findByPosition($raceid, $position) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND
				`pos` = ? LIMIT 1;";
		$params[] = $raceid;
		$params[] = $position;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($raceid, $searchname = '', $searchagecategory = '', $searchclubid = '', $searchracenumber = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "entries` AS b ON a.`entryid` = b.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS c ON b.`clubid` = c.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "agecategories` AS d ON b.`agecategorycode` = d.`code`
			WHERE a.`raceid` = ?";
		$params[] = $raceid;
		if (!empty($searchname)) {
			$sql .= " AND (b.`surname` LIKE ? OR
				b.`firstname` LIKE ?)";
			$params[] = "%". $searchname. "%";
			$params[] = "%". $searchname. "%";
		}
		if (!empty($searchagecategory)) {
			if ($searchagecategory == 'M') {
				$sql .= " AND NOT d.`femaleonly`";
			} elseif ($searchagecategory == 'F') {
				$sql .= " AND NOT d.`maleonly`";
			} else {
				$sql .= " AND b.`agecategorycode` LIKE ?";
				$params[] = $searchagecategory. "%";
			}
		}
		if (!empty($searchclubid)) {
			$sql .= " AND IFNULL(c.`id`, '--') = ?";
			$params[] = $searchclubid;
		}
		if (!empty($searchracenumber)) {
			$sql .= " AND b.`racenumber` = ?";
			$params[] = $searchracenumber;
		}
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	public static function gotChipTimes($raceid) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `chiptime` != '' LIMIT 1;";
		$params[] = $raceid;
		$resultArray = self::findBySQL($sql, $params);
		return empty($resultArray) ? false : true;
	}

	public static function gotAgeGrades($raceid) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `agegrade` != 0 AND `agegrade` IS NOT NULL LIMIT 1;";
		$params[] = $raceid;
		$resultArray = self::findBySQL($sql, $params);
		return empty($resultArray) ? false : true;
	}

	// ********************************************************************************
	// Public static functions (other)
	public static function clearRace($raceid) {

		global $pdo;
		$sql = "DELETE FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ?";
		$params[] = $raceid;
		$stmt = $pdo->prepare($sql);
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