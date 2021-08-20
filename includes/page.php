<?php
class Page extends DatabaseObject {

	protected static $tablename = 'pages';
	protected static $classname = 'Page';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `sequence`;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($paging, $levelid = '', $searchname = '') {

		$params = Array();
		$joiner = " WHERE";
		$sql = "SELECT a.*
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "levels` AS b ON a.`levelid` = b.`id`";
		if (!empty($searchname)) {
			$sql .= $joiner. " a.`name` LIKE ?";
			$params[] = "%". $searchname. "%";
			$joiner = " AND";
		}
		if (!empty($levelid)) {
    	$sql .= $joiner. " a.`levelid` = ?";
			$params[] = $levelid;
			$joiner = " AND";
		} else {
			$sql .= $joiner. " b.`number` = 99 ";
		}
		$sql .= " ORDER BY a.`sequence`, a.`url` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		return self::findBySQL($sql, $params);
	}

	public static function findAllPublic() {

		$sql = "SELECT *
			FROM ". DB_PREFIX. self::$tablename. " 
			WHERE `levelid` IN (SELECT `id` FROM `". DB_PREFIX. "levels` WHERE `number` = 99)
			ORDER BY `sequence`, `name`;";
		return self::findBySQL($sql);
	}

	public static function findAllFooters($siteid) {

		$sql = "SELECT `url`, `name`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `showonfooter` AND `subpageof` = 0 AND (`siteid` = 0 OR `siteid` = ?) ORDER BY `sequence`;";
		$params[] = $siteid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllMenuItems($siteid, $languageCode) {

		$sql = "SELECT a.`id`, a.`sequence`, CASE WHEN ISNULL(b.`languagecode`) THEN a.`name` ELSE b.`name` END AS `name`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`alttext` ELSE b.`alttext` END AS `alttext`, a.`url`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "pagelangs` AS b ON a.`id` = b.`pageid` AND `languagecode` = ?
			WHERE a.`showonmenu` AND a.`subpageof` = 0 AND (a.`siteid` = 0 OR a.`siteid` = ?)
			ORDER BY a.`sequence`;";
		$params[] = $languageCode;
		$params[] = $siteid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllSubMenuItems($pageid, $languageCode) {

		$sql = "SELECT a.`id`, a.`sequence`, CASE WHEN ISNULL(b.`languagecode`) THEN a.`name` ELSE b.`name` END AS `name`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`alttext` ELSE b.`alttext` END AS `alttext`, a.`url`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "pagelangs` AS b ON a.`id` = b.`pageid` AND `languagecode` = ?
			WHERE a.`showonmenu` AND a.`subpageof` = ?
			ORDER BY a.`sequence`";
		$params[] = $languageCode;
		$params[] = $pageid;
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

	public static function findByURL($url) {

		$sql = "SELECT a.*, b.`number` AS `levelnumber`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "levels` AS b ON a.`levelid` = b.`id`
			WHERE a.url = ?
			ORDER BY a.`sequence`;";
		$params[] = $url;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($levelid = '', $searchname = '') {

		global $pdo;
		$params = Array();
		$joiner = " WHERE";
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "levels` AS b ON a.`levelid` = b.`id`";
		if (!empty($searchname)) {
			$sql .= $joiner. " a.`name` LIKE ?";
			$params[] = "%". $searchname. "%";
			$joiner = " AND";
		}
		if (!empty($levelid)) {
    	$sql .= $joiner. " a.`levelid` = ?";
			$params[] = $levelid;
			$joiner = " AND";
		} else {
			$sql .= $joiner. " b.`number` = 99";
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