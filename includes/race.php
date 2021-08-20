<?php
class Race extends DatabaseObject {

	protected static $tablename = 'races';
	protected static $classname = 'Race';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function findAll() {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			ORDER BY `date` DESC, `sequence`;";
		return self::findBySQL($sql);
	}

	public static function findAllForSite($siteid) {

		if (!is_numeric($siteid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `siteid` = ?
			ORDER BY `date` DESC, `sequence`;";
		$params[] = $siteid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllExtended() {

		$sql = "SELECT a.*, DATE_FORMAT(a.`date`, '%e %b %Y') AS `formatdate`, b.`basis`, c.`clubtypeid`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "agecategorytypes` AS b ON a.`agecategorytypeid` = b.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "forms` AS c ON a.`formid` = c.`id`
			ORDER BY a.`date` DESC, a.`sequence`;";
		return self::findBySQL($sql);
	}

	public static function findAllFuture($siteid, $languagecode) {

		$sql = "SELECT a.`id`, CASE WHEN ISNULL(b.`languagecode`) THEN a.`name` ELSE b.`name` END AS `name`, 
				a.`limit`, a.`minimumage`, a.`entriesopen`, a.`entriesclose`, a.`transfersallowed`,
				a.`allocatenumbers`, a.`waitinglistenabled`, a.`date`, a.`transferclosedays`, a.`clubsenabled`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`entryformurl` ELSE b.`entryformurl` END AS `entryformurl`,
				a.`waitinglistadditionsopen`, a.`waitinglistoffersopen`, a.`feedbackopen`, a.`fee`, a.`onlinefee`, a.`affilfee`, a.`charityfee`,
				a.`formid`, a.`teamsenabled`, a.`clubsenabled`, c.`clubtypeid`, a.`showentrylist`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "racelangs` AS b ON a.`id` = b.`raceid` AND b.`languagecode` = ?
			INNER JOIN `". DB_PREFIX. "forms` AS c ON a.`formid` = c.`id`
			WHERE a.`siteid` = ? AND (a.`date` >= CURDATE() OR a.`feedbackopen`)
			ORDER BY a.`date`, a.`sequence`;";
		$params[] = $languagecode;
		$params[] = $siteid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllFutureNoFeedback($siteid, $languagecode) {

		$sql = "SELECT a.`id`, CASE WHEN ISNULL(b.`languagecode`) THEN a.`name` ELSE b.`name` END AS `name`, 
				a.`limit`, a.`minimumage`, a.`entriesopen`, a.`entriesclose`, a.`transfersallowed`,
				a.`allocatenumbers`, a.`waitinglistenabled`, a.`date`, a.`transferclosedays`, a.`clubsenabled`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`entryformurl` ELSE b.`entryformurl` END AS `entryformurl`,
				a.`waitinglistadditionsopen`, a.`waitinglistoffersopen`, a.`feedbackopen`, a.`fee`, a.`onlinefee`, a.`affilfee`, a.`charityfee`,
				a.`formid`, a.`teamsenabled`, a.`clubsenabled`, c.`clubtypeid`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "racelangs` AS b ON a.`id` = b.`raceid` AND b.`languagecode` = ?
			INNER JOIN `". DB_PREFIX. "forms` AS c ON a.`formid` = c.`id`
			WHERE a.`siteid` = ? AND a.`date` >= CURDATE()
			ORDER BY a.`date`, a.`sequence`;";
		$params[] = $languagecode;
		$params[] = $siteid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllFutureWithWaitList($siteid, $languagecode) {

		$sql = "SELECT a.`id`, CASE WHEN ISNULL(b.`languagecode`) THEN a.`name` ELSE b.`name` END AS `name`, 
				a.`limit`, a.`minimumage`, a.`entriesopen`, a.`entriesclose`, a.`transfersallowed`,
				a.`allocatenumbers`, a.`waitinglistenabled`, a.`date`, a.`transferclosedays`, a.`clubsenabled`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`entryformurl` ELSE b.`entryformurl` END AS `entryformurl`,
				a.`waitinglistadditionsopen`, a.`waitinglistoffersopen`, a.`feedbackopen`, a.`emailfrom`, a.`emailbcc`,
				a.`teamsenabled`, a.`clubsenabled`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "racelangs` AS b ON a.`id` = b.`raceid` AND b.`languagecode` = ?
			WHERE a.`siteid` = ? AND a.`date` >= CURDATE() AND
				(a.`entriesopen` <= CURDATE() OR a.`entriesopen` = 0) AND (a.`entriesclose` >= CURDATE() OR a.`entriesclose` = 0) AND
				a.`waitinglistenabled`
			ORDER BY a.`date` DESC, a.`sequence`;";
		$params[] = $languagecode;
		$params[] = $siteid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllForForm($siteid, $formid, $languagecode) {

		$sql = "SELECT a.`id`, CASE WHEN ISNULL(b.`languagecode`) THEN a.`name` ELSE b.`name` END AS `name`, 
				a.`limit`, a.`minimumage`, a.`entriesopen`, a.`entriesclose`, a.`transfersallowed`,
				a.`allocatenumbers`, a.`waitinglistenabled`, a.`date`, a.`transferclosedays`, a.`clubsenabled`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`entryformurl` ELSE b.`entryformurl` END AS `entryformurl`,
				a.`waitinglistadditionsopen`, a.`waitinglistoffersopen`, a.`feedbackopen`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "racelangs` AS b ON a.`id` = b.`raceid` AND b.`languagecode` = ?
			WHERE a.`siteid` = ? AND a.`formid` = ? AND
				(a.`entriesopen` <= CURDATE() OR a.`entriesopen` = 0) AND
				(a.`entriesclose` >= CURDATE() OR a.`entriesclose` = 0)
			ORDER BY a.`date` DESC, a.`sequence`;";
		$params[] = $languagecode;
		$params[] = $siteid;
		$params[] = $formid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllFutureByForm($siteid, $languagecode) {

		$sql = "SELECT a.`id`, a.`formid`, a.`limit`,
			CASE WHEN ISNULL(c.`languagecode`) THEN b.`name` ELSE c.`name` END AS `formname`, b.`clubtypeid`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			INNER JOIN `". DB_PREFIX. "forms` AS b ON a.`formid` = b.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "formlangs` AS c ON b.`id` = c.`formid` AND c.`languagecode` = ?
			WHERE a.`siteid` = ? AND a.`date` >= CURDATE() AND
				(a.`entriesopen` <= CURDATE() OR a.`entriesopen` = 0) AND
				(a.`entriesclose` >= CURDATE() OR a.`entriesclose` = 0)
			ORDER BY CASE WHEN ISNULL(c.`languagecode`) THEN b.`name` ELSE c.`name` END;";
		$params[] = $languagecode;
		$params[] = $siteid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllAllowingChanges($siteid, $languagecode) {

		$sql = "SELECT a.`id`, CASE WHEN ISNULL(b.`languagecode`) THEN a.`name` ELSE b.`name` END AS `name`,
			a.`limit`, a.`minimumage`, a.`formid`, a.`date`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "racelangs` AS b ON a.`id` = b.`raceid` AND b.`languagecode` = ?
			WHERE a.`siteid` = ? AND a.`date` >= CURDATE() AND
				(a.`entriesopen` <= CURDATE() OR a.`entriesopen` = 0)
				AND a.`transfersallowed`
			ORDER BY a.`date`, a.`sequence`;";
		$params[] = $languagecode;
		$params[] = $siteid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllWithWaitingList($siteid, $languagecode) {

		$sql = "SELECT a.`id`, CASE WHEN ISNULL(b.`languagecode`) THEN a.`name` ELSE b.`name` END AS `name`,
			a.`limit`, a.`minimumage`, a.`formid`, a.`date`, c.`clubtypeid`, a.`transfersallowed`, a.`transferclosedays`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "racelangs` AS b ON a.`id` = b.`raceid` AND b.`languagecode` = ?
			LEFT OUTER JOIN `". DB_PREFIX. "forms` AS c ON a.`formid` = c.`id`
			WHERE a.`siteid` = ? AND a.`date` >= CURDATE() AND
				(a.`entriesopen` <= CURDATE() OR a.`entriesopen` = 0) AND
				(a.`entriesclose` >= CURDATE() OR a.`entriesclose` = 0) AND
				a.`waitinglistenabled`
			ORDER BY a.`date`, a.`sequence`;";
		$params[] = $languagecode;
		$params[] = $siteid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllByPage($paging, $searchname = '') {

		$params = Array();
		$sql = "SELECT *, DATE_FORMAT(`date`, '%Y') AS `year`, DATE_FORMAT(`date`, '%m') AS `month`,
			DATE_FORMAT(`date`, '%d') AS `day`
			FROM `". DB_PREFIX. self::$tablename. "`";
		if (!empty($searchname)) {
			$sql .= " WHERE `name` LIKE ?";
			$params[] = "%". $languagecode. "%";
		}
		$sql .= " ORDER BY `date` DESC, `sequence` LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";

		return self::findBySQL($sql, $params);
	}

	public static function countForms($siteid) {

		$sql = "SELECT DISTINCT a.`formid`, b.`clubtypeid`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "forms` AS b ON a.`formid` = b.`id`
			WHERE a.`siteid` = ? AND a.`date` >= CURDATE() AND
				(a.`entriesopen` <= CURDATE() OR a.`entriesopen` = 0) AND
				(a.`entriesclose` >= CURDATE() OR a.`entriesclose` = 0);";
		$params[] = $siteid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllWithComments() {

		$sql = "SELECT *, DATE_FORMAT(`date`, '%e %b %Y') AS `formatdate`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `feedbackopen` OR `id` IN (SELECT DISTINCT `raceid` FROM `". DB_PREFIX. "comments`)
			ORDER BY `date` DESC, `sequence`;";
		return self::findBySQL($sql);
	}

	public static function findAllWithResults($siteid, $languagecode) {

		$sql = "SELECT a.`id`, a.`teamsenabled`, a.`allocatenumbers`, a.`date`, a.`chiptimes`,
				CASE WHEN ISNULL(d.`raceid`) THEN CONCAT('N', c.`raceid`) ELSE CONCAT('O', d.`raceid`) END AS `xraceid`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`name` ELSE b.`name` END AS `name`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`timetext` ELSE b.`timetext` END AS `timetext`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`chiptimetext` ELSE b.`chiptimetext` END AS `chiptimetext`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`chiptime2text` ELSE b.`chiptime2text` END AS `chiptime2text`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`chiptime3text` ELSE b.`chiptime3text` END AS `chiptime3text`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`chiptime4text` ELSE b.`chiptime4text` END AS `chiptime4text`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`chiptime5text` ELSE b.`chiptime5text` END AS `chiptime5text`,
				CASE WHEN ISNULL(b.`languagecode`) THEN a.`chiptime6text` ELSE b.`chiptime6text` END AS `chiptime6text`,
				a.`teamsenabled`, a.`clubsenabled`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "racelangs` AS b ON a.id = b.raceid AND b.languagecode = ?
			LEFT OUTER JOIN (SELECT DISTINCT `raceid` FROM `". DB_PREFIX. "results`) AS c ON a.`id` = c.`raceid`
			LEFT OUTER JOIN (SELECT DISTINCT `raceid` FROM `". DB_PREFIX. "oldresults`) AS d ON a.`id` = d.`raceid`
			WHERE a.`siteid` = ? AND
				(c.`raceid` IS NOT NULL OR d.`raceid` IS NOT NULL)
			ORDER BY a.`date` DESC, a.`sequence`, a.`name`;";
		$params[] = $languagecode;
		$params[] = $siteid;
		return self::findBySQL($sql, $params);
	}

	public static function findAllBySeries($raceid, $formid, $processurl) {

		$sql = "SELECT a.*, DATE_FORMAT(a.`date`, '%e %b %Y') AS `formatdate`, b.`id` AS `seriesid`,
			b.`discnoraces`, b.`discount`, CASE WHEN ISNULL(c.`fee`) THEN a.`fee` ELSE c.`fee` END AS `racefee`,
			CASE WHEN ISNULL(c.`onlinefee`) THEN a.`onlinefee` ELSE c.`onlinefee` END AS `raceonlinefee`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "series` AS b ON a.`seriesid` = b.`id`
			LEFT OUTER JOIN `". DB_PREFIX. "raceprices` AS c ON a.`id` = c.`raceid` AND c.`startdate` <= CURDATE() AND c.`enddate` >= CURDATE() ";
		if ($processurl == 'admin_entryupdate2.php') {
			$sql .= "WHERE a.`id` = ?";
			$params[] = $raceid;
		} else {
			$sql .= "WHERE a.`formid` = ? AND
					a.`date` >= CURDATE() AND
					(a.`entriesopen` <= CURDATE() OR a.`entriesopen` = 0) AND
					(a.`entriesclose` >= CURDATE() OR a.`entriesclose` = 0)
				ORDER BY a.`seriesid`, a.`date`, a.`sequence`;";
			$params[] = $formid;
		}
		return self::findBySQL($sql, $params);
	}

	public static function findForPublicEntryForm($raceid, $formid, $processurl, $languagecode, $siteid) {

		$sql = "SELECT a.`id`, CASE WHEN ISNULL(c.`languagecode`) THEN a.`name` ELSE c.`name` END AS `name`,
							a.`limit`, a.`minimumage`, a.`date`, a.`allocatenumbers`, CASE WHEN ISNULL(d.`fee`) THEN a.`fee` ELSE d.`fee` END AS `fee`,
							a.`affilfee`, a.`charityfee`, CASE WHEN ISNULL(d.`onlinefee`) THEN a.`onlinefee` ELSE d.`onlinefee` END AS `onlinefee`,
							a.`teamsenabled`, a.`clubsenabled`, a.`tshirtsenabled`,
							CASE WHEN ISNULL(c.`languagecode`) THEN a.`charityname` ELSE c.`charityname` END AS `charityname`,
							a.`seriesid`, b.`discnoraces`, b.`discount`, a.`option1enabled`, a.`option2enabled`,
							a.`ownchipallowed`, a.`chiprentalfee`, a.`transportenabled`, a.`transportfee`
						FROM `". DB_PREFIX. self::$tablename. "` AS a
						LEFT OUTER JOIN `". DB_PREFIX. "series` AS b ON a.`seriesid` = b.`id`
						LEFT OUTER JOIN `". DB_PREFIX. "racelangs` AS c ON a.`id` = c.`raceid` AND c.`languagecode` = ?
						LEFT OUTER JOIN `". DB_PREFIX. "raceprices` AS d ON a.`id` = d.`raceid` AND d.`startdate` <= CURDATE() AND d.`enddate` >= CURDATE()
						WHERE a.`siteid` = ?";
		$params[] = $languagecode;
		$params[] = $siteid;
		if ($processurl != 'enter3.php') {
			$sql .= ' AND a.`id` = ?';
			$params[] = $raceid;
		} else {
			$sql .= ' AND a.`formid` = ? AND
					a.`date` >= CURDATE() AND
					(a.`entriesopen` <= CURDATE() OR a.`entriesopen` = 0) AND
					(a.`entriesclose` >= CURDATE() OR a.`entriesclose` = 0)
				ORDER BY a.`seriesid`, a.`date`, a.`sequence`;';
			$params[] = $formid;
		}
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
	
	public static function findWithPricingById($id) {

		if (!is_numeric($id)) die();
		$sql = "SELECT a.`date`, a.`agecategorytypeid`, a.`allocatenumbers`, CASE WHEN ISNULL(d.`fee`) THEN a.`fee` ELSE d.`fee` END AS `fee`,
				a.`option1enabled`, a.`option2enabled`, a.`affilfee`, a.`charityfee`, CASE WHEN ISNULL(d.`onlinefee`) THEN a.`onlinefee` ELSE d.`onlinefee` END AS `onlinefee`, a.`emailfrom`, a.`emailbcc`,
				a.`ownchipallowed`, a.`chiprentalfee`, a.`teamsenabled`, a.`clubsenabled`, a.`tshirtsenabled`, a.`transportenabled`, a.`transportfee`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "raceprices` AS d ON a.`id` = d.`raceid` AND d.`startdate` <= CURDATE() AND d.`enddate` >= CURDATE()
			WHERE a.`id` = ? LIMIT 1;";
		$params[] = $id;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findByIdWithFormData($id) {

		if (!is_numeric($id)) die();
		$sql = "SELECT a.*, b.`clubtypeid`
			FROM `". DB_PREFIX. self::$tablename. "` AS a
			LEFT OUTER JOIN `". DB_PREFIX. "forms` AS b ON a.`formid` = b.`id`
			WHERE a.`id` = ? LIMIT 1;";
		$params[] = $id;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findFirstForSeries($seriesid) {

		if (!is_numeric($seriesid)) die();
		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `seriesid` = ?
			ORDER BY `sequence` LIMIT 1;";
		$params[] = $seriesid;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findByName($name, $date) {

		$sql = "SELECT * FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `name` = ? AND `date` = ? LIMIT 1;";
		$params[] = $name;
		$params[] = $date;
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
			$sql .= " WHERE `name` LIKE ?;";
			$params[] = "%". $searchnumber. "%";
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

	public function transferclosedate() {

		return date("Ymd", mktime(0, 0, 0, $this->month(), $this->day() - $this->transferclosedays, $this->year()));
	}

	public function displaytransferclosedate() {

		return date("d M Y", mktime(0, 0, 0, $this->month(), $this->day() - $this->transferclosedays, $this->year()));
	}

	public function transfersclosed() {

		$transfersclosed = false;
		if ($this->transfersallowed) {
			if (mktime(0, 0, 0, $this->month(), $this->day() - $this->transferclosedays, $this->year()) <= time()) {
				$transfersclosed = true;
			}
		}
		return $transfersclosed;
	}
	
	public function transfersenabled() {

		$transfersenabled = false;
		if ($this->transfersallowed) {
			if (strtotime($this->transferclosedate()) > time() && strtotime($this->entriesopen) <= time()) {
				$transfersenabled = true;
			}
		}
		return $transfersenabled;
	}

	public function areentriesopen() {

		$entriesopen = false;
		if (strtotime($this->entriesopen) > time()) {
			$entriesopen = true;
		}
		return $entriesopen;
	}

	public function areentriesclosed() {

		$entriesclosed = false;
		if (strtotime($this->entriesclose) < time()) {
			$entriesclosed = true;
		}
		return $entriesclosed;
	}

	public function isinfuture() {

		$future = false;
		if ($this->datenumber() >= time()) {
			$future = true;
		}
		return $future;
	}

	public function datenumber() {

		return strtotime($this->date);
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

	public function opendate() {

		return strtotime($this->entriesopen);
	}

	public function openday() {

		return date("d", $this->opendate());
	}

	public function openmonth() {

		return date("m", $this->opendate());
	}

	public function openyear() {

		return date("Y", $this->opendate());
	}
}
?>