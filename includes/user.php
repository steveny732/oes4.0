<?php
class User extends DatabaseObject {

	protected static $tablename = 'users';
	protected static $classname = 'User';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `name`;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($paging, $searchname = '') {

		$params = Array();
		$sql = "SELECT a.*, b.`name` AS `levelname`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "levels` AS b ON a.`levelid` = b.`id`";
		if (!empty($searchname)) {
			$sql .= "WHERE a.`name` LIKE ?";
			$params[] = "%". $searchname. "%";
		}
		$sql .= " ORDER BY a.`name` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
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
	public static function authenticate($username, $password) {

		$sql = "SELECT a.*, a.`password`, b.`number` AS `accesslevel`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "levels` AS b ON a.`levelid` = b.`id`
			WHERE a.`name` = ? LIMIT 1;";
		$params[] = $username;
		$resultArray = self::findBySQL($sql, $params);
		if (!empty($resultArray)) {
			$user = array_shift($resultArray);
			// We still verify unhashed passwords for the first login on db versions before 311 (to allow the dbupdate script to run)
			$config = Config::findByKey("dbversion");
			if ($config->value < 311) {
				if ($password == $user->password) {
					return $user;
				}
			} else {
				if (password_verify($password, $user->password)) {
					return $user;
				}
			}
		}

		return false;
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