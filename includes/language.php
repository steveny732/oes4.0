<?php
class Language extends DatabaseObject {

	protected static $tablename = 'languages';
	protected static $classname = 'Language';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `code`;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($paging, $searchname = '') {

		$params = Array();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`";
		if (!empty($searchname)) {
			$sql .= " WHERE `name` LIKE ?";
			$params[] = "%". $searchname. "%";
		}
		$sql .= " ORDER BY `code` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
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

	public static function findByCode($code) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `code` = ? LIMIT 1;";
		$params[] = $code;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($searchname = '') {

		global $pdo;
		$params = Array();
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`";
		if (!empty($searchname)) {
			$sql .= " WHERE `name` LIKE ?";
			$params[] = "%". $searchname. "%";
		}
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	// ********************************************************************************
	// Public static functions (other)
	public static function findCurrent($p_languagecode) {
	
		global $cookieLanguageCode;
		if (isset($_POST['langsubmit']) && $_POST['langsubmit'] == 'go') {
			$cookieLanguageCode = htmlspecialchars($_REQUEST['languagecode'], ENT_QUOTES);
			setcookie('languageCode', $cookieLanguageCode);
		} else {
			if (empty($cookieLanguageCode)) {
				$cookieLanguageCode = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
				$pos = strpos($cookieLanguageCode, ",");
				if ($pos > 0) {
					$cookieLanguageCode = substr($cookieLanguageCode, 0, $pos);
				}

				// if the browser language is not set up, default to the site language
				if (!self::findByCode($cookieLanguageCode)) {
					$cookieLanguageCode = $p_languagecode;
				}
				setcookie('languageCode', $cookieLanguageCode);
			}
		}
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