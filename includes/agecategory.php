<?php
require_once(LIB_PATH. "dateinterval.php");

class AgeCategory extends DatabaseObject {

	protected static $tablename = 'agecategories';
	protected static $classname = 'AgeCategory';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `code`;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($agecategorytypeid, $paging, $searchcode = '') {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `agecategorytypeid` = ?";
		$params[] = $agecategorytypeid;
		if (!empty($searchcode)) {
			$sql .= " AND `code` LIKE ? ";
			$params[] = "%". $searchcode. "%";
		}
		$sql .= " ORDER BY `code` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		return self::findBySQL($sql, $params);
	}

	public static function findAllForType($agecategorytypeid) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `agecategorytypeid` = ?
			ORDER BY `code`;";
		$params[] = $agecategorytypeid;
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
	public static function countAll($agecategorytypeid, $searchcode = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `agecategorytypeid` = ? ";		
		$params[] = $agecategorytypeid;
		if (!empty($searchcode)) {
			$sql .= "AND `code` LIKE ?";
			$params[] = "%". $searchcode. "%";
		}
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	// ********************************************************************************
	// Public static functions (other)
	public static function deleteAll($agecategorytypeid) {

		global $pdo;
		$stmt = $pdo->prepare("DELETE FROM `". DB_PREFIX. self::$tablename. "`
						WHERE `agecategorytypeid` = ?");
		$params[] = $agecategorytypeid;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		return true;
	}

	public static function calcAgeCategory($racedate, $basis, $agecategorytypeid, $dob, $gender) {
	
		$raceYear = (int)substr($racedate, 0, 4);
		$raceMonth = (int)substr($racedate, 5, 2);
		$raceDay = (int)substr($racedate, strlen($racedate) - 2, 2);
		$extraSQL = '';
		$dobYear = (int)substr($dob, 0, 4);
		$dobMonth = (int)substr($dob, 5, 2);
		$dobDay = (int)substr($dob, strlen($dob) - 2, 2);

		$checkValue = '';
		$extraSQL = '';

		$sql = "SELECT `code`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `agecategorytypeid` = ?";

		$params[] = $agecategorytypeid;
		// Age at race date
		if ($basis == 1) {
			$age = $raceYear - $dobYear;
			if ($raceMonth < $dobMonth || ($raceMonth == $dobMonth && $raceDay < $dobDay)) {
				$age--;
			}
			$sql .= ' AND ? BETWEEN lowervalue AND uppervalue';
			$params[] = $age;

		// Age at beginning of race year
		} elseif ($basis == 2) {
			$raceMonth = 1;
			$raceDay = 1;
			$age = $raceYear - $dobYear;
			if ($raceMonth < $dobMonth || ($raceMonth == $dobMonth && $raceDay < $dobDay)) {
				$age--;
			}
			$sql .= ' AND ? BETWEEN lowervalue AND uppervalue';
			$params[] = $age;

		// Year of birth
		} elseif ($basis == 3) {
			$sql .= ' AND ? BETWEEN ? + lowervalue AND ? + uppervalue';
			$params[] = $dobYear;
			$params[] = $raceYear;
			$params[] = $raceYear;

		// Manual selection
		} elseif ($basis == 99) {
			$ageCategoryCode = htmlspecialchars($_REQUEST['agecategorycode'], ENT_QUOTES);
		}
		if (empty($ageCategoryCode)) {
						
			if ($gender == 'male') {
				$sql .= ' AND NOT femaleonly';
			} else {
				$sql .= ' AND NOT maleonly';
			}

			$resultArray = self::findBySQL($sql, $params);;
			if (!empty($resultArray)) {
				$agecategory = array_shift($resultArray);
				$ageCategoryCode = $agecategory->code;
			}
		}
		return $ageCategoryCode;
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