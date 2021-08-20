<?php
class ContentLang extends DatabaseObject {

	protected static $tablename = 'contentlangs';
	protected static $classname = 'ContentLang';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `languagecode`;";
		return self::findBySQL($sql);
	}

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAllForContent($contentid) {

		if (!is_numeric($contentid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `contentid` = ?;";
		$params[] = $contentid;
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findById($contentid, $languagecode) {

		if (!is_numeric($contentid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `contentid` = ? AND `languagecode` = ? LIMIT 1;";
		$params[] = $contentid;
		$params[] = $languagecode;
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