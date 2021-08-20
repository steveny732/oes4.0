<?php
class PayHistory extends DatabaseObject {

	protected static $tablename = 'payhistory';
	protected static $classname = 'PayHistory';

	// ********************************************************************************
	// Public static functions (returning array of objects)
	public static function reconcileAgainstEntries($paging) {

		$sql = "SELECT * FROM
			(SELECT `ordernumber`, SUM(`in` + `charge`) AS `payamount`
					FROM `". DB_PREFIX. self::$tablename. "`
					GROUP BY `ordernumber`) AS a
			LEFT OUTER JOIN
				(SELECT `ordernumber` AS `entryordernumber`, SUM(`total`) AS `entryamount`, SUM(`discount`) AS `entrydiscount`
					FROM `". DB_PREFIX. "entries` AS a
					INNER JOIN `". DB_PREFIX. "races` AS b ON a.`raceid` = b.`id`
					WHERE a.`paid` AND
					b.`date` >= CURDATE()
					GROUP BY a.`ordernumber`) AS b ON a.`ordernumber` = b.`entryordernumber`
			WHERE b.`entryordernumber` IS NULL OR
				b.`entryordernumber` IS NOT NULL AND
				a.`payamount` <> b.`entryamount`
			ORDER BY a.`ordernumber`";
		if ($paging) {
			$sql .= " LIMIT ". $paging->pagesize. " OFFSET ". $paging->offset(). ";";
		}
		return self::findBySQL($sql);
	}

	// ********************************************************************************
	// Public static functions (returning single object)
	public static function findByOrderNumber($ordernumber) {

		$sql = "SELECT *
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `ordernumber` = ?;";
		$params[] = $ordernumber;
		$resultArray = self::findBySQL($sql, $params);
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findTotals($type) {

		$sql = "SELECT SUM(`out` - `in` - `charge`) AS `total`, SUM(`charge` * -1) AS `charge`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `type` != 'Withdraw' AND ";
			if ($type == 'entry') {
				$sql .= "`ordernumber` IN (SELECT `ordernumber` FROM `". DB_PREFIX. "entries` WHERE `paid`);";
			} else {
				$sql .= "`ordernumber` NOT IN (SELECT `ordernumber` FROM `". DB_PREFIX. "entries` WHERE `paid`);";
			}
		$resultArray = self::findBySQL($sql);
		return array_shift($resultArray);
	}

	public static function findRaceTotals($raceid) {
		
		$sql = "SELECT SUM((`out` - `in` - `charge`) * -1) AS `total`, SUM(`charge`) AS `charge`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `type` != 'Withdraw' AND `ordernumber` IN (SELECT `ordernumber` FROM `". DB_PREFIX. "entries` WHERE `raceid` = ? AND `paid`);";
		$params[] = $raceid;
		$resultArray = self::findBySQL($sql, $params);
		return array_shift($resultArray);
	}

	// ********************************************************************************
	// Public static functions (returning value)
	public static function findBanked() {

		$sql = "SELECT SUM(`out`) AS `total`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `type` = 'Withdraw'";
		$resultArray = self::findBySQL($sql);
		$row = array_shift($resultArray);
		return $row->total;
	}

	public static function findBalance() {
		
		$sql = "SELECT `bal` AS `total`
			FROM `". DB_PREFIX. self::$tablename. "`
			WHERE `date` = (SELECT MAX(`date`) FROM `". DB_PREFIX. "payhistory`);";
		$resultArray = self::findBySQL($sql);
		$row = array_shift($resultArray);
		return $row->total;
	}

	public static function countAll() {

		global $pdo;
		$joiner = " WHERE";
		$sql = "SELECT COUNT(*) AS `count`
			FROM `". DB_PREFIX. self::$tablename. "`";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
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