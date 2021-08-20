<?php
class Transfer extends DatabaseObject {

	protected static $tablename = 'transfers';
	protected static $classname = 'Transfer';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `oldsurname`, `oldfirstname`;";
		return self::findBySQL($sql);
	}

	public static function findAllByPage($raceid, $paging, $include = '') {

		$sql = "SELECT a.*, b.`racenumber`, c.`name` AS `oldclubname`, d.`name` AS `newclubname`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "entries` AS b ON a.`entryid` = b.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS c ON a.`oldclubid` = c.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS d ON a.`newclubid` = d.`id`
			WHERE NOT a.`offerrevoked` AND ";
		if ($include == 'open' || empty($include)) {
			$sql .= "NOT a.`complete` AND NOT a.`superceded` AND ";
		} elseif ($include == 'superceded') {
			$sql .= "a.`superceded` AND ";
		} elseif ($include == 'closed') {
			$sql .= "a.`complete` AND ";
		} elseif ($include == 'offered') {
			$sql .= "a.`offereddate` IS NOT NULL AND ";
		}
		$sql .= "a.`raceid` = ?
			ORDER BY a.`id` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllOffers($raceid) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `offerready` AND
				(`offertoentryid` IS NULL OR `offertoentryid` = 0) AND
				NOT `superceded` AND NOT `offerrevoked` AND NOT `complete` AND
				`raceid` = ?
				ORDER BY `id`;";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findById($id) {

		if (!is_numeric($id)) die();
		$sql = "SELECT a.*, b.`racenumber`, c.`name` AS `oldclubname`,
			d.`name` AS `newclubname`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "entries` AS b ON a.`entryid` = b.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS c ON a.`oldclubid` = c.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS d ON a.`newclubid` = d.`id`
			WHERE a.`id` = ? LIMIT 1;";
		$params[] = $id;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findByIdForUpdate($id) {

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

	public static function findByOfferToForUpdate($raceid, $entryid) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE NOT `complete` AND `raceid` = ? AND `offertoentryid` = ? LIMIT 1;";
		$params[] = $raceid;
		$params[] = $entryid;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findByOfferTo($raceid, $entryid) {

		$sql = "SELECT a.`entryid`, c.`racenumber`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "entries` AS b ON a.`offertoentryid` = b.`id`
			INNER JOIN `". DB_PREFIX. "entries` AS c ON a.`entryid` = c.`id`
			WHERE NOT a.`complete` AND a.`raceid` = ? AND a.`offertoentryid` = ? LIMIT 1;";
		$params[] = $raceid;
		$params[] = $entryid;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findByEntry($raceid, $entryid) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND
				`entryid` = ? AND
				NOT `offerrevoked` AND NOT `superceded` AND NOT `complete` AND `offertoentryid` != 0 LIMIT 1;";
		$params[] = $raceid;
		$params[] = $entryid;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findForWaitingList($transferid, $entryid) {

		$sql = "SELECT a.`id`, b.`paid`, a.`raceid`, a.`offertoentryid`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "entries` AS b ON a.`offertoentryid` = b.`id`
			WHERE NOT a.`complete` AND
				a.`id` = ? AND
				a.`offertoentryid` = ? LIMIT 1;";
		$params[] = $transferid;
		$params[] = $entryid;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findWaitingListEntry($ordernumber) {

		$sql = "SELECT a.`id`, a.`entryid`, c.`racenumber`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "entries` AS b ON a.`offertoentryid` = b.`id`
			INNER JOIN `". DB_PREFIX. "entries` AS c ON a.`entryid` = c.`id`
			WHERE a.`offertoentryid` != 0 AND NOT a.`complete` AND b.`ordernumber` = ?;";
		$params[] = $ordernumber;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($raceid, $include = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "entries` AS b ON a.`entryid` = b.`id`
			WHERE NOT a.`offerrevoked` AND ";
		if ($include == 'open' || empty($include)) {
			$sql .= "NOT a.`complete` AND NOT a.`superceded` AND ";
		} elseif ($include == 'superceded') {
			$sql .= "a.`superceded` AND ";
		} elseif ($include == 'closed') {
			$sql .= "a.`complete` AND ";
		} elseif ($include == 'offered') {
			$sql .= "a.`offereddate` IS NOT NULL AND ";
		}
		$sql .= "a.`raceid` = ?";
		$params[] = $raceid;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	public static function supercedeEntry($raceid, $entryid) {

		global $pdo;
		$stmt = $pdo->prepare("UPDATE `". DB_PREFIX. self::$tablename. "`
			SET `superceded` = TRUE
			WHERE `raceid` = ? AND `entryid` = ? AND NOT `superceded`;");
		$params[] = $raceid;
		$params[] = $entryid;
		$stmt->execute($params);
		return true;
	}

	public static function countAvailWaitListPlaces($raceid) {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `availablecount`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE NOT `complete` AND `offerready` AND `offertoentryid` = 0 AND `raceid` = ?;";
		$params[] = $raceid;
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

	public function newdob() {

		return strtotime($this->newdob);
	}

	public function newdobday() {

		return date("d", $this->newdob());
	}

	public function newdobmonth() {

		return date("m", $this->newdob());
	}

	public function newdobyear() {

		return date("Y", $this->newdob());
	}
}
?>