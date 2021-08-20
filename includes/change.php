<?php
class Change extends DatabaseObject {

	protected static $tablename = 'changes';
	protected static $classname = 'Change';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `time` DESC;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($entryid, $paging, $source = '') {

		global $db;
		$sql = "SELECT a.*, DATE_FORMAT(a.`time`, '%e %b %Y %T') AS `formatdate`,
				IFNULL(b.`name`, '*entrant') AS `username`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "users` AS b
			ON a.`userid` = b.`id`
			WHERE `entryid` = ?";
		$params[] = $entryid;
		if (!empty($source)) {
			$sql .= " AND (`source` LIKE ? OR
				`column` LIKE ?)";
			$params[] = "%". $source. "%";
			$params[] = "%". $source. "%";
		}
		$sql .= " ORDER BY `time` DESC LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($entryid, $source = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `entryid` = ? ";
		$params[] = $entryid;
		if (!empty($source)) {
			$sql .= "AND (`source` LIKE ? OR
				`column` LIKE ?)";
			$params[] = "%". $source. "%";
			$params[] = "%". $source. "%";
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