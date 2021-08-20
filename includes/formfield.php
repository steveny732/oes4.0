<?php
class FormField extends DatabaseObject {

	protected static $tablename = 'formfields';
	protected static $classname = 'FormField';

	public static $types = array('sum' => 'Charge Summary Amount',
		'checkbox' => 'Check Box',
		'date' => 'Date',
		'datetime' => 'Date & Time',
		'memo' => 'Free-format Text',
		'races' => 'Race List',
		'radio' => 'Radio Button',
		'section' => 'Section Heading',
		'salesitems' => 'Sales Items',
		'text' => 'Text Box');
	public static $columns = array('0' => 'Full Column',
		'1' => 'Left Column',
		'2' => 'Right Column');
	public static $controls = array('' => 'None',
		'*NOCLEAR' => 'Keep on same line',
		'*ONOPTION1' => 'Show if option 1 enabled',
		'*ONOPTION2' => 'Show if option 2 enabled',
		'*ONTSHIRT' => 'Show if t-shirts enabled',
		'*ONCHIPFEE' => 'Show if chip charges enabled',
		'*ONOWNCHIP' => 'Show if own chip enabled',
		'*ONTEAM' => 'Show if teams enabled',
		'*ONCLUB' => 'Show if clubs enabled',
		'*ONCLUBNOTAFFIL' => 'Show if selected club is unaffiliated',
		'*ONTRANSFERAFFIL' => 'Show if transfering an affiliated entry',
		'*ONCHANGEAFFIL' => 'Show if changing an affiliated entry',
		'*ONTRANSFERNONAFFIL' => 'Show if transfering an unaffiliated entry',
		'*ONCHANGENONAFFIL' => 'Show if changing an unaffiliated entry',
		'*ONTRANSPORT' => 'Show if transport is enabled',
		'*ONTRANSPORTREQ' => 'Show if transport is required',
		'*ONTRANSPORTFEE' => 'Show if a transport is to be charged',
		'*ONGIFTAID' => 'Show if giftaid enabled',
		'*ONCHARITY' => 'Show if charity fee charged',
		'*ONDISCOUNT' => 'Show if discount given',
		'*ONADMIN' => 'Show on admin form only',
		'*ONRACECONDITION' => 'Show for specific races only');
	public static $formats = array('0' => 'None',
		'1' => 'Proper Name',
		'2'  => 'Uppercase',
		'3'  => 'Lowercase');
	public static $specialdbcolumns = array('' => 'n/a',
		'afee' => '*afee',
		'transfee' => '*transfee',
		'cfee' => '*cfee',
		'charfee' => '*charfee',
		'chipfee' => '*chipfee',
		'clubname' => '*clubname',
		'discfee' => '*discfee',
		'email2' => '*email2',
		'fee' => '*fee',
		'searchclub' => '*searchclub',
		'tfee' => '*tfee',
		'sifee' => '*sifee');

	public function __clone() {

		unset($this->id);
	}

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAllForForm($formid) {

		if (!is_numeric($formid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `formid` = {$formid}
			ORDER BY `sequence`;";
		return self::findBySQL($sql);
	}

	public static function findAllForFormDisplay($formid, $languagecode) {

		if (!is_numeric($formid)) die();
		$sql = "SELECT CASE WHEN ISNULL(b.`languagecode`) THEN a.`caption` ELSE b.`caption` END AS `caption`,
			CASE WHEN ISNULL(b.`languagecode`) THEN a.`memo` ELSE b.`memo` END AS `memo`, a.`column`,
			a.`type`, a.`show`, a.`control`, a.`values`, a.`dbname`, a.`width`, a.`mandatory`, a.`format`,
			a.`minval`, a.`maxval`, c.`length`, c.`type` AS `dbtype`
		FROM `". DB_PREFIX. self::$tablename. "` AS a
		LEFT OUTER JOIN `". DB_PREFIX. "formfieldlangs` AS b ON b.`languagecode` = ? AND a.`id` = b.`formfieldid`
		LEFT OUTER JOIN `". DB_PREFIX. "dbcolumns` AS c ON a.`dbname` = c.`name`
		WHERE a.`formid` = ?
		ORDER BY a.`sequence`;";
		$params[] = $languagecode;
		$params[] = $formid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllByPage($formid, $paging, $searchtext = '') {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `formid` = ?";
		$params[] = $formid;
		if (!empty($searchtext)) {
			$sql .= " AND (`memo` LIKE ? OR
				`caption` LIKE ? OR
				`dbname` LIKE ?)";
			$params[] = "%". $searchname. "%";
			$params[] = "%". $searchname. "%";
			$params[] = "%". $searchname. "%";
		}
		$sql .= " ORDER BY `sequence` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		return self::findBySQL($sql, $params);
	}

	public static function findExtraCols($formid) {

		if (!is_numeric($formid)) die();
		$sql = "SELECT a.*, b.`type`, b.`description`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "dbcolumns` AS b ON a.`dbname` = b.`name`
			WHERE a.`formid` = ? AND a.`show` AND b.dbgroupid != 1
			ORDER BY a.`sequence`;";
		$params[] = $formid;
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
	public static function countAll($formid, $searchtext = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `formid` = ?";
		$params[] = $formid;
		if (!empty($searchtext)) {
			$sql .= " AND (`memo` LIKE ? OR
				`caption` LIKE ? OR
				`dbname` LIKE ?)";
			$params[] = "%". $searchname. "%";
			$params[] = "%". $searchname. "%";
			$params[] = "%". $searchname. "%";
		}
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	// ********************************************************************************
	// Public static functions (other)
	public static function deleteAllForForm($formid) {
		
		if (!is_numeric($formid)) die();
		global $pdo;
		$sql = "DELETE FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `formid` = ?;";
		$params[] = $formid;
		$stmt = $pdo->prepare($sql);
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
}
?>