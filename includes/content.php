<?php
class Content extends DatabaseObject {

	protected static $tablename = 'contents';
	protected static $classname = 'Content';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `sequence`;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($pageid, $paging, $searchtext = '') {

		if (!is_numeric($pageid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `pageid` = ?";
		$params[] = $pageid;
		if (!empty($searchtext)) {
			$sql .= " AND `text` LIKE ? ";
			$params[] = "%". $searchtext. "%";
		}
		$sql .= " ORDER BY `sequence` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		return self::findBySQL($sql, $params);
	}

	public static function findByURL($url, $languageCode) {

		$sql = "SELECT a.`type`, CASE WHEN ISNULL(c.`text`) THEN a.`text` ELSE c.`text` END AS `text`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "pages` AS b ON a.`pageid` = b.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "contentlangs` AS c ON a.`id` = c.`contentid` AND c.`languagecode` = ?
			WHERE b.`url` = ?
			ORDER BY a.`sequence`;";
		$params[] = $languageCode;
		$params[] = $url;
		return self::findBySQL($sql, $params);
	}

	public static function findByPageId($pageid, $languageCode) {

		if (!is_numeric($pageid)) die();
		$sql = "SELECT a.`type`, CASE WHEN ISNULL(c.`text`) THEN a.`text` ELSE c.`text` END AS `text`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "contentlangs` AS c ON a.`id` = c.`contentid` AND c.`languagecode` = ? 
			WHERE a.`pageid` = ?
			ORDER BY a.`sequence`;";
		$params[] = $languageCode;
		$params[] = $pageid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllForPage($pageid) {

		if (!is_numeric($pageid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `pageid` = ?
			ORDER BY `sequence`;";
		$params[] = $pageid;
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findByType($type) {

		$sql = "SELECT b.`url`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "pages` AS b ON a.`pageid` = b.`id`
			WHERE a.`type` = ?;";
		$params[] = $type;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findById($id) {

		if (!is_numeric($id)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `id` = ? LIMIT 1;";
		$params[] = $id;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findSingleByPageId($pageid) {

		if (!is_numeric($pageid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `pageid` = ? LIMIT 1;";
		$params[] = $pageid;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($pageid, $searchtext = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `pageid` = ?";
		$params[] = $pageid;
		if (!empty($searchtext)) {
			$sql .= "AND `text` LIKE '%". $db->escapeValue($searchtext). "%' ";
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