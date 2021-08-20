<?php
class SitePayment extends DatabaseObject {

	protected static $tablename = 'sitepayments';
	protected static $classname = 'SitePayment';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `name`;";
		return self::findBySQL($sql);
	}

	public static function findAllForSite($siteid) {

		if (!is_numeric($siteid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `siteid` = ? AND `enabled`
			ORDER BY `name`;";
		$params[] = $siteid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllByPage($siteid, $paging, $searchname = '') {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `siteid` = ?";
		$params[] = $siteid;
		if (!empty($searchname)) {
			$sql .= "AND `name` LIKE ?";
			$params[] = "%". $searchname. "%";
		}
		$sql .= " ORDER BY `name` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
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

	public static function findByType($siteid, $type) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `siteid` = ? AND `type` = ? LIMIT 1;";
		$params[] = $siteid;
		$params[] = $type;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($siteid, $searchname = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `siteid` = ?";
		$params[] = $siteid;
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
}
?>