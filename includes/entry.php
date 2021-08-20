<?php
class Entry extends DatabaseObject {

	protected static $tablename = 'entries';
	protected static $classname = 'Entry';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `title`;";
		return self::findBySQL($sql);
	}

	public static function findAllForSeries($seriesid) {

		if (!is_numeric($seriesid)) die();
		$sql = "SELECT a.`ordernumber`, CASE WHEN c.`seriesracenumbers` THEN a.`racenumber` ELSE '' END AS `racenumber`,
			c.`name` AS `seriesname`, a.`racenumber`, d.`affiliated` AS `clubaffiliated`, d.`frostbite`,
			a.`team`, d.`name` AS `clubname`, a.`dob`, a.`firstname`, a.`surname`, a.`address1`,
			a.`address2`, a.`town`, a.`county`, a.`postcode`, a.`regno`, a.`giftaid`, a.`gender`, a.`option1`, a.`option2`,
			b.`formid`, SUM(a.`total`) AS `total`, a.`salesitems`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT JOIN `". DB_PREFIX. "races` AS b ON a.`raceid` = b.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "series` AS c ON b.`seriesid` = c.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS d ON a.`clubid` = d.`id`
			WHERE a.`paid` AND b.`seriesid` = ? AND NOT a.`deleted`
			GROUP BY c.`name`, a.`racenumber`, d.`affiliated`, a.`team`, d.`name`, a.`dob`, a.`firstname`, a.`surname`,
				a.`address1`, a.`address2`, a.`town`, a.`county`,
				a.`postcode`, b.`formid` HAVING COUNT(*) = (SELECT COUNT(*) FROM `". DB_PREFIX. "races` WHERE `seriesid` = ?)
			ORDER BY a.`racenumber`;";
		$params[] = $seriesid;
		$params[] = $seriesid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllForRace($raceid, $type) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT a.*, b.formid, d.`name` AS `clubname`, d.`affiliated` AS `clubaffiliated`, d.`frostbite` AS `clubfrostbite`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT JOIN `". DB_PREFIX. "races` AS b ON a.`raceid` = b.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "series` AS c ON b.`seriesid` = c.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS d ON a.`clubid` = d.`id`
			WHERE ";
		if ($type != 'paid') {
			$sql .= "NOT";
		}
		$sql .= " a.`paid` AND a.`raceid` = ? AND NOT a.`deleted`
			ORDER BY a.`racenumber`;";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllForRaceForUpdate($raceid, $type) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND";
		if ($type != 'paid') {
			$sql .= " NOT";
		}
		$sql .= " `paid` AND NOT `deleted`
			ORDER BY `racenumber`;";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllByPage($raceid, $paging, $surname = '', $racenumber = '', $agecategorycode = '', $team = '', $clubid = '', $type, $sort) {

		$params = Array();
		if (!is_numeric($raceid)) die();
		$sql = "SELECT a.`id`, a.`ordernumber`, a.`racenumber`, a.`agecategorycode`, a.`firstname`,
			a.`surname`, CASE WHEN ISNULL(b.`name`) THEN 'Unattached' ELSE b.`name` END AS `clubname`, a.`team`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS b ON a.`clubid` = b.`id`
			WHERE NOT a.`deleted` AND a.`raceid` = {$raceid}";
		if (!empty($surname)) {
			$sql .= " AND a.`surname` LIKE ?";
			$params[] = "%". $surname. "%";
		}
		if (!empty($racenumber)) {
			$sql .= " AND a.`racenumber` >= ?";
			$params[] = $racenumber;
		}
		if (!empty($agecategorycode)) {
			$sql .= " AND a.`agecategorycode` = ?";
			$params[] = $agecategorycode;
		}
		if (!empty($team)) {
			$sql .= " AND a.`team` LIKE ?";
			$params[] = "%". $team. "%";
		}
		if ($clubid != '') {
			if ($clubid == '0') {
				$sql .= " AND b.`id` IS NULL ";
			} else {
				$sql .= " AND b.`id` = ?";
				$params[] = $clubid;
			}
		}
		// Incorporate pay status type
		if ($type == '0') {
			$sql .= " AND NOT a.`paid` AND NOT a.`waiting`";
		} elseif ($type == '1') {
			$sql .= " AND NOT a.`paid` AND a.`waiting`";
		} else {
			$sql .= " AND a.`paid`";
		}
		if ($sort == 'surname') {
			$sql .= " ORDER BY a.`surname`, a.`firstname`";
		} elseif ($sort == "racenumber") {
			$sql .= " ORDER BY a.`racenumber`, a.`surname`, a.`firstname`";
		} elseif ($sort == "agecategorycode") {
			$sql .= " ORDER BY a.`agecategorycode`, a.`surname`, a.`firstname`";
		} elseif ($sort == "teamname") {
			$sql .= " ORDER BY CASE WHEN ISNULL(a.`team`) OR a.`team` = '' THEN 'ZZZZZZZZ' ELSE a.`team` END, a.`surname`, a.`firstname`";
		} elseif ($sort == "clubname") {
			$sql .= " ORDER BY CASE WHEN ISNULL(b.`name`) OR b.`name` = '' THEN 'ZZZZZZZZ' ELSE b.`name` END, a.`surname`, a.`firstname`";
		} elseif ($sort == "date") {
			$sql .= " ORDER BY a.`waitdate`, a.`id`";
		} else {
			$sql .= " ORDER BY a.`surname`, a.`firstname`";
		}
		$sql .= " LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		return self::findBySQL($sql, $params);
	}

	public static function findAllBySurname($paging, $surname) {

		$sql = "SELECT DATE_FORMAT(b.`date`, '%e %b %Y') AS formatdate, b.`name`, CASE WHEN NOT a.`paid` THEN 'Unpaid' ELSE CASE WHEN a.`racenumber` = 0 OR ISNULL(a.`racenumber`) THEN 'n/a' ELSE a.`racenumber` END END AS `racenumber`, a.`firstname`, a.`surname`, DATE_FORMAT(a.`dob`, '%e %b %Y') AS `dob`, a.`postcode`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "races` AS b ON a.`raceid` = b.`id`
			WHERE b.`date` > NOW() AND a.`surname` LIKE ? AND NOT a.`deleted`
			ORDER BY b.`date`, b.`name`, a.`surname`, a.`firstname`, a.`dob`, a.`postcode` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		$params[] = $surname. "%";
		return self::findBySQL($sql, $params);
	}

	public static function findAllOnWaitingList($raceid) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `waiting` AND NOT `deleted` AND `raceid` = ?
			ORDER BY `waitdate`, `id`;";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	public static function findByOrderNumber($ordernumber) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `ordernumber` = ?;";
		$params[] = $ordernumber;
		return self::findBySQL($sql, $params);
	}

	public static function findByOrderNumberNotPaid($ordernumber) {

		$sql = "SELECT a.*, b.`seriesid`, c.`seriesracenumbers`, d.`name` AS `clubname`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "races` AS b ON a.`raceid` = b.`id`
			LEFT JOIN `". DB_PREFIX. "series` AS c ON b.`seriesid` = c.`id`
			LEFT JOIN `". DB_PREFIX. "clubs` AS d ON a.`clubid` = d.`id`
			WHERE a.`ordernumber` = ? AND
				NOT a.`paid` AND NOT a.`deleted` AND NOT a.`waiting`
			ORDER BY a.`id`, b.`seriesid`, a.`raceid`;";
		$params[] = $ordernumber;
		return self::findBySQL($sql, $params);
	}

	public static function findByOrderNumberNotPaidTPV($ordernumber) {

		$sql = "SELECT a.*, b.`seriesid`, c.`seriesracenumbers`, d.`name` AS `clubname`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "races` AS b ON a.`raceid` = b.`id`
			LEFT JOIN `". DB_PREFIX. "series` AS c ON b.`seriesid` = c.`id`
			LEFT JOIN `". DB_PREFIX. "clubs` AS d ON a.`clubid` = d.`id`
			WHERE RIGHT(a.`ordernumber`, 10) = ? AND
				NOT a.`paid` AND NOT a.`deleted` AND NOT a.`waiting`
			ORDER BY a.`id`, b.`seriesid`, a.`raceid`;";
		$params[] = $ordernumber;
		return self::findBySQL($sql, $params);
	}

	public static function findAllUsedAgeCategories($raceid, $type = '') {

		$sql = "SELECT DISTINCT `agecategorycode`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ?";
		if ($type == '') {
			$sql .= " AND `paid`";
		} elseif ($type == '0') {
			$sql .= " AND NOT `paid` AND NOT `deleted`";
		} elseif ($type == '1') {
			$sql .= " AND NOT `paid` AND `waiting` AND NOT `deleted`";
		}
		$sql .= " ORDER BY `agecategorycode`;";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllUsedClubs($raceid, $type = '') {

		$sql = "SELECT DISTINCT b.`id`, b.`name`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS b ON a.`clubid` = b.`id`
			WHERE a.`raceid` = ? AND b.`name` IS NOT NULL";
		if ($type == '') {
			$sql .= " AND a.`paid`";
		} elseif ($type == '0') {
			$sql .= " AND NOT a.`paid` AND NOT a.`deleted`";
		} elseif ($type == '1') {
			$sql .= " AND NOT a.`paid` AND a.`waiting` AND NOT a.`deleted`";
		}
		$sql .= " ORDER BY b.`name`;";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllClubsOnWaitList($raceid) {

		if (!is_numeric($raceid)) die();
		$sql = "SELECT DISTINCT b.`id`, b.`name`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS b ON a.`clubid` = b.`id`
			WHERE a.`raceid` = ? AND NOT a.`deleted` AND a.`waiting` AND b.`name` IS NOT NULL
			ORDER BY b.`name`;";
		$params[] = $raceid;
		return self::findBySQL($sql, $params);
	}

	public static function reconcileAgainstPayHistory($paging) {

		$sql = "SELECT * FROM
			(SELECT `ordernumber`, SUM(`total`) AS `entryamount`, SUM(`discount`) AS `entrydiscount`
				FROM `". DB_PREFIX. self::$tablename. "` AS a
				INNER JOIN `". DB_PREFIX. "races` AS b
				ON a.`raceid` = b.`id`
				WHERE a.`paid` AND
				b.`date` >= CURDATE()
				GROUP BY a.`ordernumber`) AS a
			LEFT OUTER JOIN
				(SELECT `ordernumber` AS `payordernumber`, SUM(`in` + `charge`) AS `payamount`
					FROM `". DB_PREFIX. "payhistory`
					GROUP BY `ordernumber`) AS b
			ON a.`ordernumber` = b.`payordernumber`
			WHERE b.`payordernumber` IS NULL OR
			b.`payordernumber` IS NOT NULL AND
			a.`entryamount` <> b.`payamount`
			ORDER BY a.`ordernumber`";
		if ($paging) {
			$sql .= " LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		}
		return self::findBySQL($sql);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findByDetails($raceid, $surname, $firstname, $postcode, $dob) {

		$sql = "SELECT a.*, b.`name` AS `clubname`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS b ON a.`clubid` = b.`id`
			WHERE a.`paid` AND
				a.`raceid` = ? AND
				a.`surname` = ? AND
				a.`postcode` = ? AND
				a.`dob` = ?";
		$params[] = $raceid;
		$params[] = $surname;
		$params[] = $postcode;
		$params[] = $dob;
		if (!empty($firstname)) {
			$sql .= " AND a.`firstname` = ? LIMIT 1;";
			$params[] = $firstname;
		}
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findWaitingListByDetails($raceid, $surname, $firstname, $postcode, $dob) {

		$sql = "SELECT a.*, b.`name` AS `clubname`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS b ON a.`clubid` = b.`id`
			WHERE a.`waiting` AND NOT a.`deleted` AND
				a.`raceid` = ? AND
				a.`firstname` = ? AND
				a.`surname` = ? AND
				a.`postcode` = ? AND
				a.`dob` = ? LIMIT 1";
		$params[] = $raceid;
		$params[] = $firstname;
		$params[] = $surname;
		$params[] = $postcode;
		$params[] = $dob;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findChequeTotal($raceid) {

		$sql = "SELECT SUM(`total` +`discount`) AS `total`, SUM(`discount` * -1) AS `discount`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `paid` AND
				`ordernumber` NOT IN (SELECT `ordernumber`
						FROM `". DB_PREFIX. "payhistory`
						WHERE `type` != 'Withdraw' AND `ordernumber` IS NOT NULL);";
		$params[] = $raceid;
		$resultArray = self::findBySQL($sql, $params);
		return array_shift($resultArray);
	}

	public static function findById($id) {

		if (!is_numeric($id)) die();
		$sql = "SELECT a.*, b.`name` AS `clubname`, b.`affiliated` AS `memberaffil`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS b ON a.`clubid` = b.`id`
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

	public static function findByIdForEntry($id) {

		if (!is_numeric($id)) die();
		$sql = "SELECT a.*, b.`seriesid`, c.`seriesracenumbers`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "races` AS b ON a.`raceid` = b.`id`
			LEFT JOIN `". DB_PREFIX. "series` AS c ON b.`seriesid` = c.`id`
			WHERE a.`id` = ? LIMIT 1;";
		$params[] = $id;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function countAll($raceid, $surname = '', $racenumber = '', $agecategorycode = '', $team = '', $clubid = '', $type) {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS b ON a.`clubid` = b.`id`
			WHERE NOT a.`deleted` AND a.`raceid` = ?";
		$params[] = $raceid;
		if (!empty($surname)) {
			$sql .= " AND a.`surname` LIKE ?";
			$params[] = "%". $surname. "%";
		}
		if (!empty($racenumber)) {
			$sql .= " AND a.`racenumber` >= ?";
			$params[] = $racenumber;
		}
		if (!empty($agecategorycode)) {
			$sql .= " AND a.`agecategorycode` = ?";
			$params[] = $agecategorycode;
		}
		if (!empty($team)) {
			$sql .= " AND a.`team` LIKE ?";
			$params[] = "%". $team. "%";
		}
		if ($clubid != '') {
			if ($clubid == '0') {
				$sql .= " AND b.`id` IS NULL";
			} else {
				$sql .= " AND b.`id` = ?";
			$params[] = $clubid;
			}
		}
		// Incorporate pay status type
		if ($type == '0') {
			$sql .= " AND NOT a.`paid` AND NOT a.`waiting`";
		} elseif ($type == '1') {
			$sql .= " AND NOT a.`paid` AND a.`waiting`";
		} else {
			$sql .= " AND a.`paid`";
		}
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	public static function countAllBySurname($surname = '') {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "races` AS b ON a.`raceid` = b.`id`
			WHERE b.`date` > NOW() AND NOT a.`deleted` AND `surname` LIKE ?;";
		$params[] = $surname. "%";
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	public static function countAllPaid($raceid) {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `paid`;";
		$params[] = $raceid;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	public static function countAllOnline($raceid) {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `paid` AND `online`;";
		$params[] = $raceid;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	public static function countAllNonaffil($raceid) {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "clubs` AS b ON a.`clubid` = b.`id`
			WHERE a.`raceid` = ? AND a.`paid` AND
			(b.`affiliated` IS NULL OR (NOT b.`affiliated` AND (a.`regno` = '' OR a.`regno` IS NULL)));";
		$params[] = $raceid;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	public static function getWaitingListCount($raceid) {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `raceid` = ? AND `waiting` AND NOT `deleted` LIMIT 1;";
		$params[] = $raceid;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}

	public static function findTotalsByOrderNumber($ordernumber) {

		global $pdo;
		$sql = "SELECT COUNT(*) AS `entrycount`, SUM(total) AS `totalvalue`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `ordernumber` = ? AND
				NOT `paid` AND NOT `deleted` AND NOT `waiting`;";
		$params[] = $ordernumber;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	public static function findOverallChequeTotal() {
		
		$sql = "SELECT SUM(`total`) AS `total`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `paid` AND
				`ordernumber` NOT IN (SELECT `ordernumber` FROM `". DB_PREFIX. "payhistory` WHERE `ordernumber` IS NOT NULL) AND
				`raceid` IN (SELECT `id` FROM `". DB_PREFIX. "races` WHERE `date` >= CURDATE());";
		$resultArray = self::findBySQL($sql);
		$row = array_shift($resultArray);
		return $row->total;
	}

	public static function findDiscountTotal() {

		$sql = "SELECT SUM(`discount` * -1) AS `total`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `paid`;";
		$resultArray = self::findBySQL($sql);
		$row = array_shift($resultArray);
		return $row->total;
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

		global $pdo;
		$stmt = $pdo->prepare("UPDATE `". DB_PREFIX. self::$tablename. "`
			SET `paid` = FALSE, `racenumber` = 0, `deleted` = TRUE
			WHERE `id` = ? LIMIT 1;");
		$params[] = $this->id;
		$stmt->execute($params);
		return true;
	}

	public function incrementOfferCount() {

		global $pdo;
		$stmt = $pdo->prepare("UPDATE `". DB_PREFIX. self::$tablename. "`
			SET `waiting` = 1,
			`offercount` = CASE WHEN ISNULL(`offercount`) THEN 1 ELSE `offercount` + 1 END,
			`waitdate` = NOW()
			WHERE `id` = ? LIMIT 1;");
		$params[] = $this->id;
		$stmt->execute($params);
		return true;
	}
}
?>