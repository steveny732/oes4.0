<?php
class Comment extends DatabaseObject {

	protected static $tablename = 'comments';
	protected static $classname = 'Comment';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `date`;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($raceid, $paging, $searchtext = '') {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT *, DATE_FORMAT(`date`, '%e %b %Y %T') AS `formatdate`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ?";
		$params[] = $raceid;
		if (!empty($searchtext)) {
			$sql .= " AND `text` LIKE ?";
			$params[] = "%". $searchtext. "%";
		}
		$sql .= " ORDER BY `date` DESC LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		return self::findBySQL($sql, $params);
	}

	public static function findAllByPageModerated($raceid, $paging, $searchtext = '') {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT *, DATE_FORMAT(`date`, '%e %b %Y %T') AS `formatdate`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `moderated`";
		$params[] = $raceid;
		if (!empty($searchtext)) {
			$sql .= " AND `text` LIKE ?";
			$params[] = "%". $searchtext. "%";
		}
		$sql .= " ORDER BY `date` DESC LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
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

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($raceid, $searchtext = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ?";
		$params[] = $raceid;
		if (!empty($searchtext)) {
			$sql .= " AND `text` LIKE ?";
			$params[] = "%". $searchtext. "%";
		}
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	public static function countAllModerated($raceid, $searchtext = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `moderated`";
		$params[] = $raceid;
		if (!empty($searchtext)) {
			$sql .= " AND `text` LIKE ?";
			$params[] = "%". $searchtext. "%";
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
}
?>