<?php
class Site extends DatabaseObject {

	protected static $tablename = 'sites';
	protected static $classname = 'Site';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `title`;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($paging, $searchtitle = '') {

		$params = Array();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`";
		if (!empty($searchtitle)) {
			$sql .= " WHERE `title` LIKE ?";
			$params[] = "%". $searchtitle. "%";
		}
		$sql .= " ORDER BY `title` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";

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
	
	public static function findTheSite() {
		
		$url = 'http://'. $_SERVER['SERVER_NAME']. $_SERVER['PHP_SELF'];
		$url = substr($url, 0, strrpos($url, '/'));
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `url` = ? LIMIT 1;";
		$params[] = $url;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : self::findById(1);
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($searchtitle = '') {

		global $pdo;
		$params = Array();
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`";
		if (!empty($searchtitle)) {
			$sql .= " WHERE `title` LIKE ?";
			$params[] = "%". $searchtitle. "%";
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

	public function copyrightMessage() {

		return 'Copyright 2006 - '. date('Y'). ' '. $this->copyright. '.';
	}
}
?>