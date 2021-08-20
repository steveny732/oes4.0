<?php
class OldResult extends DatabaseObject {

	protected static $tablename = 'oldresults';
	protected static $classname = 'OldResult';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `pos`;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($raceid, $paging, $searchname = '', $searchagecategory = '', $searchclubname = '', $searchracenumber = '', $searchsort = '') {

		$sql = "SELECT a.*, a.`name` AS `firstname`, '' AS `surname`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "agecategories` AS d ON a.`agecategorycode` = d.`code`
			WHERE a.`raceid` = ?";
		$params[] = $raceid;
		if (!empty($searchname)) {
			$sql .= " AND a.`name` LIKE ?";
			$params[] = "%". $searchname. "%";
		}
		if (!empty($searchagecategory)) {
			if ($searchagecategory == 'M') {
				$sql .= " AND NOT d.`femaleonly`";
			} elseif ($searchagecategory == 'F') {
				$sql .= " AND NOT d.`maleonly`";
			} else {
				$sql .= " AND a.`agecategorycode` LIKE ?";
				$params[] = $searchagecategory. "%";
			}
		}
		if (!empty($searchclubname)) {
			$sql .= " AND a.`clubname` = ?";
			$params[] = $searchclubname;
		}
		if (!empty($searchracenumber)) {
			$sql .= " AND a.`racenumber` = ?";
			$params[] = $searchracenumber;
		}
		if (!empty($searchsort)) {
			if ($searchsort == 'name') {
				$sql .= " ORDER BY a.`name`, a.`pos`";
			} elseif ($searchsort == 'racenumber') {
				$sql .= " ORDER BY a.`racenumber`, a.`pos`";
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

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findByPosition($raceid, $position) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `pos` = ? LIMIT 1;";
		$params[] = $raceid;
		$params[] = $position;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($raceid, $searchname = '', $searchagecategory = '', $searchclubname = '', $searchracenumber = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "agecategories` AS d ON a.`agecategorycode` = d.`code`
			WHERE a.`raceid` = ?";
		$params[] = $raceid;
		if (!empty($searchname)) {
			$sql .= " AND a.`name` LIKE ?";
			$params[] = "%". $searchname. "%";
		}
		if (!empty($searchagecategory)) {
			if ($searchagecategory == 'M') {
				$sql .= " AND NOT d.`femaleonly`";
			} elseif ($searchagecategory == 'F') {
				$sql .= " AND NOT d.`maleonly`";
			} else {
				$sql .= " AND a.`agecategorycode` LIKE ?";
				$params[] = $searchagecategory. "%";
			}
		}
		if (!empty($searchclubname)) {
			$sql .= " AND a.`clubname` = ?";
			$params[] = $searchclubname;
		}
		if (!empty($searchracenumber)) {
			$sql .= " AND a.`racenumber` = ?";
			$params[] = $searchracenumber;
		}
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	public static function findAllClubs($raceid) {

		global $pdo;
		$sql = "SELECT DISTINCT a.`clubname`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			WHERE a.`raceid` = ?
			AND a.`clubname` != ''
			ORDER BY a.`clubname`;";
		$params[] = $raceid;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}

	public static function findAllAgeCategories($raceid) {

		global $pdo;
		$sql = "SELECT DISTINCT a.`agecategorycode`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			WHERE a.`raceid` = ? AND a.`agecategorycode` != ''
			ORDER BY a.`agecategorycode`;";
		$params[] = $raceid;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
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