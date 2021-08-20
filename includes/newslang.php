<?php
class NewsLang extends DatabaseObject {

	protected static $tablename = 'newslangs';
	protected static $classname = 'NewsLang';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAllForNews($newsid) {

		if (!is_numeric($newsid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `newsid` = ?
			ORDER BY `id`;";
		$params[] = $newsid;
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findById($newsid, $languagecode) {

		if (!is_numeric($newsid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `newsid` = ? AND `languagecode` = ? LIMIT 1;";
		$params[] = $newsid;
		$params[] = $languagecode;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (other)
	public static function deleteAllForNews($newsid) {

		if (!is_numeric($newsid)) die();
		global $pdo;
		$sql = "DELETE FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `newsid` = ?;";
		$params[] = $newsid;
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