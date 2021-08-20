<?php
class Club extends DatabaseObject {

	protected static $tablename = 'clubs';
	protected static $classname = 'Club';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `name`;";
		return self::findBySQL($sql);
	}
	
	public static function findAllForClubType($clubtypeid) {

		if (!is_numeric($clubtypeid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `clubtypeid` = ?
			ORDER BY `name`;";
		$params[] = $clubtypeid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllByPage($clubtypeid, $paging, $searchname = '') {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `clubtypeid` = ?";
		$params[] = $clubtypeid;
		if (!empty($searchname)) {
			$sql .= " AND `name` LIKE ?";
			$params[] = "%". $searchname. "%";
		}
		$sql .= " ORDER BY `name` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		return self::findBySQL($sql, $params);
	}

	public static function findAllForRace($raceid) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `clubtypeid` IN (SELECT b.`clubtypeid`
				FROM `". DB_PREFIX. "races` AS a
				INNER JOIN `". DB_PREFIX. "forms` AS b ON a.`formid` = b.`id`
				WHERE a.`id` = ?)
			ORDER BY `name`;";
		$params[] = $raceid;
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

	public static function findByName($formid, $name) {

		if (!is_numeric($formid)) die();
		$sql = "SELECT a.*
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "forms` AS b ON a.`clubtypeid` = b.`clubtypeid` AND b.`id` = ?
			WHERE a.`name` = ? LIMIT 1;";
		$params[] = $formid;
		$params[] = $name;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($clubtypeid, $searchname = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `clubtypeid` = ?";
		$params[] = $clubtypeid;
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
	// Public static functions (other)
	public static function deleteAll($clubtypeid) {

		if (!is_numeric($clubtypeid)) die();
		global $pdo;
		$stmt = $pdo->prepare("DELETE FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `clubtypeid` = ". $db->escapeValue($clubtypeid). ";");
		$params[] = $clubtypeid;
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