<?php
class PageLang extends DatabaseObject {

	protected static $tablename = 'pagelangs';
	protected static $classname = 'PageLang';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAllForPage($pageid) {

		if (!is_numeric($pageid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `pageid` = ?
			ORDER BY `id`;";
		$params[] = $pageid;
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findById($pageid, $languagecode) {

		if (!is_numeric($pageid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `pageid` = ? AND `languagecode` = ? LIMIT 1;";
		$params[] = $pageid;
		$params[] = $languagecode;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (other)
	public static function deleteAllForPage($pageid) {

		if (!is_numeric($pageid)) die();
		global $pdo;
		$sql = "DELETE FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `pageid` = ?;";
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