<?php
class DbColumn extends DatabaseObject {

	protected static $tablename = 'dbcolumns';
	protected static $classname = 'DbColumn';

	public static $types = array('3' => 'integer',
		'6' => 'double',
		'7' => 'date',
		'11' => 'boolean',
		'202' => 'text',
		'300' => 'memo');

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `name`;";
		return self::findBySQL($sql);
	}

	public static function findAllByGroup($dbgroupid) {

		if (!is_numeric($dbgroupid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `dbgroupid` = ?
			AND NOT `dbupdated`
			ORDER BY `name`;";
		$params[] = $dbgroupid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllByPage($dbgroupid, $paging, $searchdescription = '') {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `dbgroupid` = ?";
		$params[] = $dbgroupid;
		if (!empty($searchdescription)) {
			$sql .= " AND `description` LIKE ?";
			$params[] = "%". $searchdescription. "%";
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

	public static function findByName($name) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `name` = ? LIMIT 1;";
		$params[] = $name;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($dbgroupid, $searchdescription = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `dbgroupid` = ?";
		$params[] = $dbgroupid;
		if (!empty($searchdescription)) {
			$sql .= " AND `description` LIKE ?";
			$params[] = "%". $searchdescription. "%";
		}
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	// ********************************************************************************
	// Public static functions (other)
	public static function deleteAll($dbgroupid) {

		global $pdo;
		$stmt = $pdo->prepare("DELETE FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `dbgroupid` = ?;");
		$params[] = $dbgroupid;
		$stmt->execute($params);
		return true;
	}

	public static function setGroupColumnsUpdated($dbgroupid) {

		global $pdo;
		$stmt = $pdo->prepare("UPDATE `". DB_PREFIX. self::$tablename. "`
			SET `dbupdated` = 1
			WHERE `dbgroupid` = ? AND NOT `dbupdated`;");
		$params[] = $dbgroupid;
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

	public function columnType() {

		if ($this->type == 3) {
			$p_type = "integer";
		} elseif ($this->type == 6) {
			$p_type = "double";
		} elseif ($this->type == 7) {
			$p_type = "date";
		} elseif ($this->type == 11) {
			$p_type = "boolean";
		} elseif ($this->type == 202) {
			$p_type = "text";
		} elseif ($this->type == 300) {
			$p_type = "memo";
		}
		return $p_type;
	}
}
?>