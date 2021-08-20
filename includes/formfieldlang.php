<?php
class FormFieldLang extends DatabaseObject {

	protected static $tablename = 'formfieldlangs';
	protected static $classname = 'FormFieldLang';

	public function __clone() {

		unset($this->id);
	}

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAllForFormField($formfieldid) {

		if (!is_numeric($formfieldid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `formfieldid` = ? ORDER BY `id`;";
		$params[] = $formfieldid;
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findById($formfieldid, $languagecode) {

		if (!is_numeric($formfieldid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `formfieldid` = ? AND `languagecode` = ? LIMIT 1;";
		$params[] = $formfieldid;
		$params[] = $languagecode;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (other)
	public static function deleteAllOrphaned() {
		
		global $pdo;
		$sql = "DELETE FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `formfieldid` NOT IN (SELECT `formfieldid` FROM `". DB_PREFIX. "formfields`);";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
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