<?php
class NewsItem extends DatabaseObject {

	protected static $tablename = 'news';
	protected static $classname = 'NewsItem';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `date`;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($paging, $searchtext = '') {

		$params = Array();
		$sql = "SELECT *, DATE_FORMAT(`date`, '%e %b %Y %T') AS `formatdate`
			FROM `". DB_PREFIX. self::$tablename. "`";
		if (!empty($searchtext)) {
			$sql .= " WHERE `text` LIKE ?";
			$params[] = "%". $searchtext. "%";
		}
		$sql .= " ORDER BY `date` DESC LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		return self::findBySQL($sql, $params);
	}

	public static function findLatest($count, $languageCode) {

		$sql = "SELECT a.`id`, a.`date`, CASE WHEN ISNULL(b.`languagecode`) THEN a.`summary` ELSE b.`summary` END AS `summary`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`text` ELSE b.`text` END AS `text`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`headline` ELSE b.`headline` END AS `headline`, b.`languagecode`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "newslangs` AS b ON a.`id` = b.`newsid` AND b.`languagecode` = ?
			ORDER BY a.`date` DESC LIMIT ?;";
		$params[] = $languageCode;
		$params[] = $count;
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findForUpdate($id) {

		if (!is_numeric($id)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `id` = ? LIMIT 1;";
		$params[] = $id;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findById($id) {

		if (!is_numeric($id)) die();
		global $db;
		$sql = "SELECT *, DATE_FORMAT(`date`, '%e %b %Y') AS `formatdate`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `id` = ? LIMIT 1;";
		$params[] = $id;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($searchtext = '') {

		global $pdo;
		$params = Array();
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`";
		if (!empty($searchtext)) {
			$sql .= " WHERE `text` LIKE ?";
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

	public function datenumber() {

		return strtotime($this->date);
	}

	public function delete() {

		parent::deleteObject(self::$tablename);
	}

	public function day() {

		return date("d", $this->datenumber());
	}

	public function month() {

		return date("m", $this->datenumber());
	}

	public function year() {

		return date("Y", $this->datenumber());
	}
}
?>