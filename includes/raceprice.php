<?php
class RacePrice extends DatabaseObject {

	protected static $tablename = 'raceprices';
	protected static $classname = 'RacePrice';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAllForRace($raceid) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? ORDER BY `startdate`;";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllByPage($raceid, $paging, $searchname = '') {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ?";
		$params[] = $raceid;
		if (!empty($searchname)) {
			$sql .= " AND `name` LIKE ?";
			$params[] = "%". $searchname. "%";
		}
		$sql .= " ORDER BY `startdate` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
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

	public static function checkForOverlappingPrices($id, $raceid, $startdate, $enddate) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `id` <> ? AND
				`raceid` = ? AND
				`startdate` <= ? AND `enddate` >= ? LIMIT 1;";
		$params[] = $id;
		$params[] = $raceid;
		$params[] = $enddate;
		$params[] = $startdate;
		$resultArray = self::findBySQL($sql, $params);
		return empty($resultArray) ? false : true;
	}
	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($raceid, $searchname = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ?";
		$params[] = $raceid;
		if (!empty($searchname)) {
			$sql .= " AND `name` LIKE ?";
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
	public function save() {

		return isset($this->id) ? self::update(self::$tablename) : self::create(self::$tablename);
	}

	public function delete() {

		parent::deleteObject(self::$tablename);
	}

	public function startdate() {

		return strtotime($this->startdate);
	}

	public function startday() {

		return date("d", $this->startdate());
	}

	public function startmonth() {

		return date("m", $this->startdate());
	}

	public function startyear() {

		return date("Y", $this->startdate());
	}
	public function enddate() {

		return strtotime($this->enddate);
	}

	public function endday() {

		return date("d", $this->enddate());
	}

	public function endmonth() {

		return date("m", $this->enddate());
	}

	public function endyear() {

		return date("Y", $this->enddate());
	}
}
?>