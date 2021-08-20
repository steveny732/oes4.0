<?php
class Config extends DatabaseObject {

	protected static $tablename = 'config';
	protected static $classname = 'Config';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `key`;";
		return self::findBySQL($sql);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findByKey($key) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `key` = ? LIMIT 1;";
		$params[] = $key;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Protected static functions
	protected static function findBySQL($sql, $params = Array()) {

		global $pdo;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll(PDO::FETCH_CLASS, self::$classname);
	}
}
?>