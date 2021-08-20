<?php
class SalesItemLang extends DatabaseObject {

	protected static $tablename = 'salesitemlangs';
	protected static $classname = 'SalesItemLang';

	public function __clone() {

		unset($this->id);
	}

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAllForSalesItem($salesitemid) {

		if (!is_numeric($salesitemid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `salesitemid` = ? ORDER BY `id`;";
		$params[] = $salesitemid;
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findById($salesitemid, $languagecode) {

		if (!is_numeric($salesitemid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `salesitemid` = ? AND `languagecode` = ? LIMIT 1;";
		$params[] = $salesitemid;
		$params[] = $languagecode;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (other)
	public static function deleteAllForSalesItem($salesitemid) {

		if (!is_numeric($salesitemid)) die();
		global $pdo;
		$sql = "DELETE FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `salesitemid` = ?;";
		$params[] = $salesitemid;
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