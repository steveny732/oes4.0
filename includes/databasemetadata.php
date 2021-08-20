<?php
class DatabaseMetadata {

	protected static $classname = 'DatabaseMetadata';

	public static function findMetadata($table) {

		return self::findBySQL("SHOW FIELDS FROM `". DB_PREFIX. $table. "`;");
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
	public function columnType() {

		$type = 'text';
		if (substr($this->Type, 0, 3) == 'int') {
			$type = 'int';
		} elseif (substr($this->Type, 0, 4) == 'date') {
			$type = 'date';
		} elseif (substr($this->Type, 0, 7) == 'tinyint') {
			$type = 'boolean';
		} elseif (substr($this->Type, 0, 7) == 'decimal') {
			$type = 'double';
		}
		return $type;
	}
}
?>