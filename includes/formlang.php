<?php
class FormLang extends DatabaseObject {

	protected static $tablename = 'formlangs';
	protected static $classname = 'FormLang';
	
	public function __clone() {

		unset($this->id);
	}

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAllForForm($formid) {

		if (!is_numeric($formid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `formid` = {$formid} ORDER BY `id`;";
		return self::findBySQL($sql);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findById($formid, $languagecode) {

		if (!is_numeric($formid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `formid` = ? AND `languagecode` = ? LIMIT 1;";
		$params[] = $formid;
		$params[] = $languagecode;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (other)
	public static function deleteAllForForm($formid) {

		if (!is_numeric($formid)) die();
		global $pdo;
		$sql = "DELETE FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `formid` = ?;";
		$params[] = $formid;
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