<?php
/* ************************************************************************
' install.php -- Runs when the dbconfig.php file is empty
'
' Copyright (c) 1999-2008 The openEntrySystem Project Team
'
'		This library is free software; you can redistribute it and/or
'		modify it under the terms of the GNU Lesser General Public
'		License as published by the Free Software Foundation; either
'		version 2.1 of the License, or (at your option) any later version.
'
'		This library is distributed in the hope that it will be useful,
'		but WITHOUT ANY WARRANTY; without even the implied warranty of
'		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
'		Lesser General Public License for more details.
'
'		You should have received a copy of the GNU Lesser General Public
'		License along with this library; if not, write to the Free Software
'		Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
'
'		For full terms see the file licence.txt.
'
'	Version: 1.0
'	Last Modified By:
'		betap 2010-12-12 17:46:20
'		New script.
'
' ************************************************************************ */
include_once("includes/pdo_mysql.php");
$dirname = "includes";
$filename = "dbconfig.php";
$version = 300;

$installerror = '';
if (!is_writable($dirname. "/". $filename)) {
	$installerror = "Test 1 failed: {$dirname} is not writable. Please change the authorities on this directory/folder during installation.";
}

if (isset($_POST['formid']) && $_POST['formid'] == 'form1') {

	if (isset($_POST['root']) && $_POST['root']) {
		if (empty($_POST['server'])) {
			$installerror = 'The MySQL server name must be entered.';
		} elseif (empty($_POST['rootusername'])) {
			$installerror = 'The MySQL root user name must be entered.';
		} elseif (empty($_POST['rootpassword'])) {
			$installerror = 'The MySQL root password must be entered.';
		}
	} elseif (empty($_POST['database'])) {
		$installerror = 'The OES MySQL database name must be entered.';
	} elseif (empty($_POST['username'])) {
		$installerror = 'The OES MySQL User name must be entered.';
	} elseif (empty($_POST['password'])) {
		$installerror = 'The OES MySQL Password must be entered.';
	}

	// Check server exists (root access only)
	if (isset($_POST['root']) && $_POST['root']) {
		if (empty($installerror)) {
			if (!$connection = @pdo_connect($_POST['server'], $_POST['rootusername'], $_POST['rootpassword'])) {
				$installerror = "Could not connect to server <strong>{$_POST['server']}</strong> with the root user name <strong>{$_POST['rootusername']}</strong> and the root password given.";
			}
		}

		// Check if the OES MySQL database already exists (attempt to create it if it doesn't)
		if (empty($installerror)) {
			$sql = "SELECT `SCHEMA_NAME`
				FROM `INFORMATION_SCHEMA`.`SCHEMATA`
				WHERE `SCHEMA_NAME` = '{$_POST['database']}'";
			if (!$resultset = @pdo_query($sql)) {
				$installerror = "The root user given does not have the necessary authorities within MySQL to perform the installation (needs read privileges on INFORMATION_SCHEMA.SCHEMATA).";
			} else {
				if (!$row = @pdo_fetch_assoc($resultset)) {
					$sql = "CREATE DATABASE `{$_POST['database']}` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
					if (!$resultset = @pdo_query($sql)) {
						$installerror = "Database {$_POST['database']} could not be created (root user given has insufficient privileges?).";
					}
				}
			}
		}

		// Check if the OES MySQL user already exists (attempt to create it if it doesn't)
		if (empty($installerror)) {
			$sql = "SELECT `user` FROM `mysql`.`user` WHERE `user` = '{$username}';";
			if (!$resultset = @pdo_query($sql)) {
				$sql = "CREATE USER '{$_POST['username']}'@'{$_POST['server']}' IDENTIFIED BY '{$_POST['password']}';";
				if (!$resultset = @pdo_query($sql)) {
					$installerror = "User {$_POST['username']} could not be created (root user given has insufficient privileges?).";
				}
			}
		}

		// Check if the OES MySQL user has the necessary permissions on the OES MySQL database (attempt to grant them if it doesn't)
		if (empty($installerror)) {
			$selectOK = false;
			$insertOK = false;
			$updateOK = false;
			$deleteOK = false;
			$createOK = false;
			$dropOK = false;
			$indexOK = false;
			$alterOK = false;
			$sql = "SELECT `PRIVILEGE_TYPE`
				FROM `INFORMATION_SCHEMA`.`SCHEMA_PRIVILEGES`
				WHERE `TABLE_SCHEMA` = '{$_POST['database']}' AND
					`GRANTEE` IN (\"'{$_POST['username']}'@'%'\", \"'{$_POST['username']}'@'{$_POST['server']}'\");";
			if (!$resultset = @pdo_query($sql)) {
				$installerror = "User does not have the necessary authorities within MySQL to perform the installation (needs read privileges on INFORMATION_SCHEMA.SCHEMA_PRIVILEGES).";
			} else {
				while ($row = @pdo_fetch_assoc($resultset)) {
					if ($row['PRIVILEGE_TYPE'] == 'SELECT') {
						$selectOK = true;
					} elseif ($row['PRIVILEGE_TYPE'] == 'INSERT') {
						$insertOK = true;
					} elseif ($row['PRIVILEGE_TYPE'] == 'UPDATE') {
						$updateOK = true;
					} elseif ($row['PRIVILEGE_TYPE'] == 'DELETE') {
						$deleteOK = true;
					} elseif ($row['PRIVILEGE_TYPE'] == 'CREATE') {
						$createOK = true;
					} elseif ($row['PRIVILEGE_TYPE'] == 'DROP') {
						$dropOK = true;
					} elseif ($row['PRIVILEGE_TYPE'] == 'INDEX') {
						$indexOK = true;
					} elseif ($row['PRIVILEGE_TYPE'] == 'ALTER') {
						$alterOK = true;
					}
				}
				if (!$selectOK) {
					$sql = "GRANT SELECT ON `{$_POST['database']}`.* TO '{$_POST['username']}'@'{$_POST['server']}';";
					if (!$resultset = @pdo_query($sql)) {
						$installerror = "Could not grant SELECT privileges to the OES database for user.";
					}
				} else if (!$insertOK) {
					$sql = "GRANT INSERT ON `{$_POST['database']}`.* TO '{$_POST['username']}'@'{$_POST['server']}';";
					if (!$resultset = @pdo_query($sql)) {
						$installerror = "Could not grant INSERT privileges to the OES database for user.";
					}
				} else if (!$updateOK) {
					$sql = "GRANT UPDATE ON `{$_POST['database']}`.* TO '{$_POST['username']}'@'{$_POST['server']}';";
					if (!$resultset = @pdo_query($sql)) {
						$installerror = "Could not grant UPDATE privileges to the OES database for user.";
					}
				} else if (!$deleteOK) {
					$sql = "GRANT DELETE ON `{$_POST['database']}`.* TO '{$_POST['username']}'@'{$_POST['server']}';";
					if (!$resultset = @pdo_query($sql)) {
						$installerror = "Could not grant DELETE privileges to the OES database for user.";
					}
				} else if (!$createOK) {
					$sql = "GRANT CREATE ON `{$_POST['database']}`.* TO '{$_POST['username']}'@'{$_POST['server']}';";
					if (!$resultset = @pdo_query($sql)) {
						$installerror = "Could not grant CREATE privileges to the OES database for user.";
					}
				} else if (!$dropOK) {
					$sql = "GRANT DROP ON `{$_POST['database']}`.* TO '{$_POST['username']}'@'{$_POST['server']}';";
					if (!$resultset = @pdo_query($sql)) {
						$installerror = "Could not grant DROP privileges to the OES database for user.";
					}
				} else if (!$indexOK) {
					$sql = "GRANT INDEX ON `{$_POST['database']}`.* TO '{$_POST['username']}'@'{$_POST['server']}';";
					if (!$resultset = @pdo_query($sql)) {
						$installerror = "Could not grant INDEX privileges to the OES database for user.";
					}
				} else if (!$alterOK) {
					$sql = "GRANT ALTER ON `{$_POST['database']}` . * TO '{$_POST['username']}'@'{$_POST['server']}';";
					if (!$resultset = @pdo_query($sql)) {
						$installerror = "Could not grant ALTER privileges to the OES database for user.";
					}
				}
			}
		}
	}

	// Validate that the OES password is correct (all the following stuff is done using the OES user)
	if (empty($installerror)) {
		if (!$connection = @pdo_connect($_POST['server'], $_POST['username'], $_POST['password'])) {
			$installerror = "Incorrect OES user password given.";
		}
	}

	if (empty($installerror)) {
		if (!$db_select = @pdo_select_db($_POST['database'], $connection)) {
			$installerror = 'Unable to select database.';
		}
	}

	if (empty($installerror)) {

		// Set up the environment as if the dbconfig file was already populated
		$prefix = '';
		if (!empty($_POST['prefix'])) {
			$prefix = $_POST['prefix']. '_';
		}
		defined('DB_SERVER') ? null : define('DB_SERVER', $_POST['server']);
		defined('DB_USER') ? null : define('DB_USER', $_POST['username']);
		defined('DB_PASS') ? null : define('DB_PASS', $_POST['password']);
		defined('DB_NAME') ? null : define('DB_NAME', $_POST['database']);
		defined('DB_PREFIX') ? null : define('DB_PREFIX', $prefix);
		$sql = "SELECT * FROM `". DB_PREFIX. "config` WHERE `config_key` = 'dbversion';";
		
		pdo_query('SET NAMES "utf8"');
		pdo_query('SET sql_mode = "";');

		// If the config table doesn't already exist, we're doing a full database install...
		if (!$resultSet = pdo_query($sql)) {
			require_once('databaseinstall.php');
		} else {
		// ...otherwise we're probably upgrading from the Access/ASP version, in which case we need to update the
		// database before we can do anything more. If we're already at version 3, writing the dbconfig.php file
		// was enough and we can just drop through the the admin page as if nothing happened.
			$row = pdo_fetch_assoc($resultSet);
			if (!empty($row['config_value']) && $row['config_value'] < $version) {

				$sql = "ALTER TABLE `". DB_PREFIX. "agecategories` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `agecategory_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `agecategory_agecategorytype_id` `agecategorytypeid` DOUBLE NULL DEFAULT NULL,
					CHANGE `agecategory_code` `code` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `agecategory_maleonly` `maleonly` TINYINT(1) NULL DEFAULT '0',
					CHANGE `agecategory_femaleonly` `femaleonly` TINYINT(1) NULL DEFAULT '0',
					CHANGE `agecategory_uppervalue` `uppervalue` DOUBLE NULL DEFAULT NULL,
					CHANGE `agecategory_lowervalue` `lowervalue` DOUBLE NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "agecategories` ADD INDEX `agecategorytypeid` (`agecategorytypeid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "agecategorytypes` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `agecategorytype_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `agecategorytype_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `agecategorytype_basis` `basis` INT(11) NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "clubs` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `club_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `club_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `club_frostbite` `frostbite` TINYINT(1) NULL DEFAULT '0',
					CHANGE `club_affiliated` `affiliated` TINYINT(1) NULL DEFAULT '0',
					CHANGE `club_clubtype_id` `clubtypeid` INT(11) NULL DEFAULT NULL,
					DROP INDEX `club_id`;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "clubs`
					ADD INDEX `clubtypeid` (`clubtypeid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "clubtypes` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `clubtype_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `clubtype_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "comments` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `comment_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `comment_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `comment_email` `email` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `comment_text` `text` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `comment_moderated` `moderated` TINYINT(1) NULL DEFAULT '0',
					CHANGE `comment_date` `date` DATETIME NULL DEFAULT NULL,
					CHANGE `comment_race_id` `raceid` DOUBLE NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "config` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `config_key` `key` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
					CHANGE `config_value` `value` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "contacts` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `contact_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `contact_race_id` `raceid` INT(11) NULL DEFAULT NULL,
					CHANGE `contact_role` `role` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `contact_email` `email` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "contacts`
					ADD INDEX `raceid` (`raceid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "contentlangs` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `contentlang_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `contentlang_content_id` `contentid` INT(11) NULL DEFAULT NULL,
					CHANGE `contentlang_language_code` `languagecode` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `contentlang_text` `text` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "contentlangs`
					ADD UNIQUE `contentid` (`contentid` , `languagecode`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "contents` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `content_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `content_page_id` `pageid` INT(11) NULL DEFAULT NULL,
					CHANGE `content_sequence` `sequence` INT(11) NULL DEFAULT NULL,
					CHANGE `content_text` `text` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `content_type` `type` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "contents`
					ADD INDEX `pageid` (`pageid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "currencies` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					DROP PRIMARY KEY,
					ADD `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
					CHANGE `currency_code` `code` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
					CHANGE `currency_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "dbcolumns` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `dbcolumn_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `dbcolumn_dbgroup_id` `dbgroupid` INT(11) NULL DEFAULT NULL,
					CHANGE `dbcolumn_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `dbcolumn_description` `description` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `dbcolumn_type` `type` INT(11) NULL DEFAULT NULL,
					CHANGE `dbcolumn_length` `length` INT(11) NULL DEFAULT NULL,
					CHANGE `dbcolumn_dbupdated` `dbupdated` TINYINT(1) NULL DEFAULT '0';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "dbcolumns`
					ADD INDEX `dbgroupid` (`dbgroupid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "dbcolumns`
					ADD `entryrel` TINYINT(1) NULL DEFAULT '0',
					ADD `entrantrel` TINYINT(1) NULL DEFAULT '0',
					ADD `transferrel` TINYINT(1) NULL DEFAULT '0',
					ADD `transferpersist` TINYINT(1) DEFAULT '0',
					ADD `changepersist` TINYINT(1) DEFAULT '0';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "dbgroups` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `dbgroup_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `dbgroup_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `dbgroup_enabled` `enabled` TINYINT(1) NULL DEFAULT '0',
					CHANGE `dbgroup_system` `system` TINYINT(1) NULL DEFAULT '0';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "entrants` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `entrant_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `entrant_username` `username` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_password` `password` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_passwordref` `passwordref` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_surname` `surname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_firstname` `firstname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_address1` `address1` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_address2` `address2` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_town` `town` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_county` `county` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_postcode` `postcode` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_telday` `telday` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_televe` `televe` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_gender` `gender` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_dob` `dob` DATE NULL DEFAULT NULL,
					CHANGE `entrant_email` `email` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_tshirt` `tshirt` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_affil` `affil` TINYINT(1) NULL DEFAULT '0', CHANGE `entrant_club_id` `clubid` INT(11) NULL DEFAULT '0',
					CHANGE `entrant_regno` `regno` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_language_code` `languagecode` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entrant_main_entrant_id` `mainentrantid` INT(11) NULL DEFAULT '0',
					CHANGE `entrant_ownchipno` `ownchipno` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "entrants`
					ADD INDEX `clubid` (`clubid`),
					ADD INDEX `mainentrantid` (`mainentrantid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "entries` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `entry_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `entry_ordernumber` `ordernumber` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_paid` `paid` TINYINT(1) NULL DEFAULT '0',
					CHANGE `entry_racenumber` `racenumber` INT(11) NULL DEFAULT '0',
					CHANGE `entry_race_id` `raceid` INT(11) NULL DEFAULT '0',
					CHANGE `entry_surname` `surname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_firstname` `firstname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_address1` `address1` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_address2` `address2` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_town` `town` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_county` `county` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_postcode` `postcode` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_telday` `telday` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_televe` `televe` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_gender` `gender` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_dob` `dob` DATE NULL DEFAULT NULL,
					CHANGE `entry_email` `email` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_tshirt` `tshirt` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_affil` `affil` TINYINT(1) NULL DEFAULT '0',
					CHANGE `entry_club_id` `clubid` INT(11) NULL DEFAULT '0',
					CHANGE `entry_regno` `regno` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_giftaid` `giftaid` TINYINT(1) NULL DEFAULT '0',
					CHANGE `entry_total` `total` DECIMAL(11,2) NULL DEFAULT NULL,
					CHANGE `entry_online` `online` TINYINT(1) NULL DEFAULT '0',
					CHANGE `entry_waiting` `waiting` TINYINT(1) NULL DEFAULT '0',
					CHANGE `entry_offercount` `offercount` INT(11) NULL DEFAULT NULL,
					CHANGE `entry_deleted` `deleted` TINYINT(1) NULL DEFAULT '0',
					CHANGE `entry_discount` `discount` DECIMAL(11,2) NULL DEFAULT NULL,
					CHANGE `entry_option1` `option1` TINYINT(1) NULL DEFAULT '0',
					CHANGE `entry_option2` `option2` TINYINT(1) NULL DEFAULT '0',
					CHANGE `entry_waitdate` `waitdate` DATETIME NULL DEFAULT NULL,
					CHANGE `entry_entrant_id` `entrantid` INT(11) NULL DEFAULT '0',
					CHANGE `entry_language_code` `languagecode` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_team` `team` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_agecategory_code` `agecategorycode` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_ownchipno` `ownchipno` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `entry_transportreq` `transportreq` TINYINT(1) NULL DEFAULT '0',
					CHANGE `entry_transporttype` `transporttype` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `charval` decimal(11,2) default NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "entries`
					ADD INDEX `raceid` (`raceid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "entries`
					ADD INDEX `raceidnumber` (`raceid` , `racenumber`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "formfieldlangs` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `formfieldlang_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `formfieldlang_formfield_id` `formfieldid` INT(11) NULL DEFAULT NULL,
					CHANGE `formfieldlang_language_code` `languagecode` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `formfieldlang_caption` `caption` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `formfieldlang_memo` `memo` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "formfieldlangs`
					ADD UNIQUE `formfieldid` (`formfieldid` , `languagecode`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "formfields` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `formfield_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `formfield_form_id` `formid` INT(11) NULL DEFAULT NULL,
					CHANGE `formfield_sequence` `sequence` INT(11) NULL DEFAULT NULL,
					CHANGE `formfield_column` `column` INT(11) NULL DEFAULT NULL,
					CHANGE `formfield_type` `type` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `formfield_show` `show` TINYINT(1) NULL DEFAULT '0',
					CHANGE `formfield_caption` `caption` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `formfield_memo` `memo` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `formfield_control` `control` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `formfield_values` `values` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `formfield_dbname` `dbname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `formfield_width` `width` INT(11) NULL DEFAULT NULL,
					CHANGE `formfield_mandatory` `mandatory` TINYINT(1) NULL DEFAULT '0',
					CHANGE `formfield_format` `format` INT(11) NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "formfields`
					ADD INDEX `formid` (`formid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "formfields`
					ADD `minval` VARCHAR(10) collate utf8_unicode_ci NOT NULL,
					ADD `maxval` VARCHAR(10) collate utf8_unicode_ci NOT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "formlangs` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `formlang_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `formlang_form_id` `formid` INT(11) NULL DEFAULT NULL,
					CHANGE `formlang_language_code` `languagecode` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `formlang_name` `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "formlangs`
					ADD INDEX `formid` (`formid` , `languagecode`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "forms` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `form_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `form_name` `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `form_clubtype_id` `clubtypeid` INT(11) NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "forms`
					ADD INDEX `clubtypeid` (`clubtypeid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "languages` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					DROP PRIMARY KEY;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "languages`
					ADD `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
					CHANGE `language_code` `code` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
					CHANGE `language_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "levels` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `level_id` `id` INT(11) NOT NULL AUTO_INCREMENT ,
					CHANGE `level_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `level_number` `number` INT(11) NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "news` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `news_id` `id` INT(11) NOT NULL AUTO_INCREMENT ,
					CHANGE `news_date` `date` DATETIME NULL DEFAULT NULL ,
					CHANGE `news_summary` `summary` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `news_text` `text` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ,
					CHANGE `news_headline` `headline` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "newslangs` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `newslang_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `newslang_news_id` `newsid` INT(11) NULL DEFAULT NULL,
					CHANGE `newslang_language_code` `languagecode` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `newslang_summary` `summary` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `newslang_text` `text` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `newslang_headline` `headline` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE .`". DB_PREFIX. "newslangs`
					ADD UNIQUE `newsid` (`newsid` , `languagecode`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "oldresults` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `result_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `result_race_id` `raceid` INT(11) NULL DEFAULT NULL,
					CHANGE `result_pos` `pos` INT(11) NULL DEFAULT NULL,
					CHANGE `result_time` `time` VARCHAR(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `result_chiptime` `chiptime` VARCHAR(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `result_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `result_club_name` `clubname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `result_racenumber` `racenumber` INT(11) NULL DEFAULT NULL,
					CHANGE `result_agegrade` `agegrade` DECIMAL(11,2) NULL DEFAULT NULL ,
					CHANGE `result_team` `team` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `result_agecategory_code` `agecategorycode` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "oldresults`
					ADD INDEX `raceid` (`raceid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "oldresults`
					CHANGE `chiptime` `chiptime` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime2` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime3` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime4` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime5` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime6` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "pagelangs` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `pagelang_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `pagelang_page_id` `pageid` INT(11) NULL DEFAULT NULL,
					CHANGE `pagelang_language_code` `languagecode` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `pagelang_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `pagelang_alttext` `alttext` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "pagelangs`
					ADD UNIQUE `pageid` (`pageid` , `languagecode`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "pages` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `page_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `page_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `page_url` `url` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `page_level_id` `levelid` INT(11) NULL DEFAULT NULL,
					CHANGE `page_levelonly` `levelonly` TINYINT(1) NULL DEFAULT '0',
					CHANGE `page_sequence` `sequence` INT(11) NULL DEFAULT NULL,
					CHANGE `page_showonmenu` `showonmenu` TINYINT(1) NULL DEFAULT '0',
					CHANGE `page_showonfooter` `showonfooter` TINYINT(1) NULL DEFAULT '0',
					CHANGE `page_subpageof` `subpageof` INT(11) NULL DEFAULT NULL,
					CHANGE `page_alttext` `alttext` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `page_site_id` `siteid` INT(11) NULL DEFAULT NULL,
					DROP INDEX `page_level_id`;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "pages`
					ADD INDEX `levelid` (`levelid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "payhistory` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `payhistory_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `payhistory_provider` `provider` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `payhistory_reference` `reference` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `payhistory_date` `date` DATETIME NULL DEFAULT NULL,
					CHANGE `payhistory_type` `type` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `payhistory_email` `email` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `payhistory_desc` `desc` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `payhistory_out` `out` DECIMAL(11,2) NULL DEFAULT NULL,
					CHANGE `payhistory_in` `in` DECIMAL(11,2) NULL DEFAULT NULL,
					CHANGE `payhistory_charge` `charge` DECIMAL(11,2) NULL DEFAULT NULL,
					CHANGE `payhistory_bal` `bal` DECIMAL(11,2) NULL DEFAULT NULL,
					CHANGE `payhistory_ordernumber` `ordernumber` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "paylog` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `paylog_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `paylog_provider` `provider` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `paylog_ordernumber` `ordernumber` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `paylog_posts` `posts` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "preallnos` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					DROP INDEX `preallno_racenumber`,
					DROP INDEX `preallno_race_id`,
					DROP INDEX `preallno_club_id`;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "preallnos`
					ADD `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
					CHANGE `preallno_race_id` `raceid` INT(11) NULL DEFAULT '0',
					CHANGE `preallno_club_id` `clubid` INT(11) NULL DEFAULT '0',
					CHANGE `preallno_racenumber` `racenumber` INT(11) NULL DEFAULT '0';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "preallnos`
					ADD INDEX `raceid` (`raceid`),
					ADD INDEX `clubid` (`clubid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "racelangs` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `racelang_id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT,
					CHANGE `racelang_race_id` `raceid` INT(11) NULL DEFAULT NULL,
					CHANGE `racelang_language_code` `languagecode` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `racelang_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `racelang_charityname` `charityname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `racelang_postaladdress` `postaladdress` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `racelang_handlingfeetext` `handlingfeetext` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `racelang_entryformurl` `entryformurl` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "racelangs`
					ADD UNIQUE `raceid` (`raceid` , `languagecode`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "racelangs`
					ADD `timetext` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptimetext` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime2text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime3text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime4text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime5text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime6text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "racenos` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					DROP PRIMARY KEY,
					DROP INDEX `raceno_race_id`;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "racenos`
					ADD `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
					CHANGE `raceno_race_id` `raceid` INT(11) NOT NULL DEFAULT '0',
					CHANGE `raceno_racenumber` `racenumber` INT(11) NOT NULL DEFAULT '0';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "racenos`
					ADD INDEX `raceid` (`raceid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "racenos`
					ADD INDEX `raceidnumber` (`raceid`, `racenumber`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "races` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `race_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `race_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `race_date` `date` DATE NULL DEFAULT NULL,
					CHANGE `race_transfersallowed` `transfersallowed` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_transferclosedays` `transferclosedays` INT(11) NULL DEFAULT '0',
					CHANGE `race_waitinglistenabled` `waitinglistenabled` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_fee` `fee` DECIMAL(11,2) NULL DEFAULT '0.00',
					CHANGE `race_affilfee` `affilfee` DECIMAL(11,2) NULL DEFAULT '0.00',
					CHANGE `race_charityfee` `charityfee` DECIMAL(11,2) NULL DEFAULT '0.00',
					CHANGE `race_onlinefee` `onlinefee` DECIMAL(11,2) NULL DEFAULT '0.00',
					CHANGE `race_charityname` `charityname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `race_postaladdress` `postaladdress` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `race_emailbcc` `emailbcc` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `race_emailfrom` `emailfrom` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `race_handlingfeetext` `handlingfeetext` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `race_testmode` `testmode` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_teamsenabled` `teamsenabled` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_allocatenumbers` `allocatenumbers` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_sequence` `sequence` INT(11) NULL DEFAULT NULL,
					CHANGE `race_minimumage` `minimumage` INT(11) NULL DEFAULT NULL,
					CHANGE `race_tshirtsenabled` `tshirtsenabled` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_series_id` `seriesid` INT(11) NULL DEFAULT NULL,
					CHANGE `race_entriesclose` `entriesclose` DATE NULL DEFAULT NULL,
					CHANGE `race_distance` `distance` DECIMAL(10,4) NULL DEFAULT NULL,
					CHANGE `race_limit` `limit` INT(11) NULL DEFAULT NULL,
					CHANGE `race_startnumber` `startnumber` INT(11) NULL DEFAULT NULL,
					CHANGE `race_option1enabled` `option1enabled` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_option2enabled` `option2enabled` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_entriesopen` `entriesopen` DATE NULL DEFAULT NULL,
					CHANGE `race_ownchipallowed` `ownchipallowed` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_feedbackopen` `feedbackopen` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_clubsenabled` `clubsenabled` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_entryformurl` `entryformurl` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `race_payee` `payee` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `race_chiprentalfee` `chiprentalfee` DECIMAL(11,2) NULL DEFAULT '0.00',
					CHANGE `race_agecategorytype_id` `agecategorytypeid` INT(11) NULL DEFAULT NULL,
					CHANGE `race_form_id` `formid` INT(11) NULL DEFAULT NULL,
					CHANGE `race_waitinglistadditionsopen` `waitinglistadditionsopen` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_waitinglistoffersopen` `waitinglistoffersopen` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_site_id` `siteid` INT(11) NULL DEFAULT NULL,
					CHANGE `race_transportenabled` `transportenabled` TINYINT(1) NULL DEFAULT '0',
					CHANGE `race_transportfee` `transportfee` DECIMAL(11,2) NULL DEFAULT '0.00',
					DROP INDEX `race_id`;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "races`
					ADD INDEX `seriesid` (`seriesid`),
					ADD INDEX `formid` (`formid`),
					ADD INDEX `siteid` (`siteid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "races`
					ADD `cumulsplits` TINYINT(1) DEFAULT '0',
					ADD `timetext` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptimes` INT(11) NULL DEFAULT '0',
					ADD `chiptimetext` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime2text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime3text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime4text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime5text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime6text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "results` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					DROP PRIMARY KEY,
					DROP INDEX `result_race_id`,
					CHANGE `result_id` `result_id` INT(11) NOT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "results`
					DROP INDEX `result_id`,
					CHANGE `result_race_id` `raceid` INT(11) NULL DEFAULT '0',
					CHANGE `result_pos` `pos` INT(11) NULL DEFAULT '0',
					CHANGE `result_time` `time` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `result_chiptime` `chiptime` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `result_agegrade` `agegrade` DECIMAL(11,2) NULL DEFAULT NULL,
					CHANGE `result_entry_id` `entryid` INT(11) NULL DEFAULT NULL,
					ADD `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "results`
					DROP `result_id`,
					ADD INDEX `raceid` (`raceid`),
					ADD INDEX `entryid` (`entryid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "results`
					CHANGE `chiptime` `chiptime` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime2` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime3` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime4` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime5` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					ADD `chiptime6` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "series` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `series_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `series_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `series_discnoraces` `discnoraces` INT(11) NULL DEFAULT NULL,
					CHANGE `series_discount` `discount` DECIMAL(11,2) NULL DEFAULT '0.00',
					CHANGE `series_seriesracenumbers` `seriesracenumbers` TINYINT(1) NULL DEFAULT '0';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "sitepayments` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `sitepayment_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `sitepayment_site_id` `siteid` INT(11) NULL DEFAULT NULL ,
					CHANGE `sitepayment_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `sitepayment_type` `type` INT(11) NULL DEFAULT NULL,
					CHANGE `sitepayment_enabled` `enabled` TINYINT(1) NULL DEFAULT '0',
					CHANGE `sitepayment_merchantid` `merchantid` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "sitepayments`
					ADD INDEX `siteid` (`siteid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "sites` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `site_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `site_title` `title` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_url` `url` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_smtpserver` `smtpserver` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_smtpusername` `smtpusername` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_smtppassword` `smtppassword` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_downformaint` `downformaint` TINYINT(1) NULL DEFAULT '0',
					CHANGE `site_copyright` `copyright` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_bannerimgurl` `bannerimgurl` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_clubimgurl` `clubimgurl` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_newsimgurl` `newsimgurl` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_menucolour` `menucolour` VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_menurocolour` `menurocolour` VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_menutextcolour` `menutextcolour` VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_menutextrocolour` `menutextrocolour` VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_headingcolour` `headingcolour` VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_buttoncolour` `buttoncolour` VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_buttonrocolour` `buttonrocolour` VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_buttontextcolour` `buttontextcolour` VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_buttontextrocolour` `buttontextrocolour` VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_securesiteurl` `securesiteurl` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_paylogactive` `paylogactive` TINYINT(1) NULL DEFAULT '0',
					CHANGE `site_language_code` `languagecode` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_currency_code` `currencycode` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_googleanal` `googleanalyticscode` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_entrybcc` `entrybcc` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `site_home_page_id` `homepageid` INT(11) NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "transfers` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `transfer_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `transfer_code` `code` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_race_id` `raceid` INT(11) NULL DEFAULT '0',
					CHANGE `transfer_entry_id` `entryid` INT(11) NULL DEFAULT '0',
					CHANGE `transfer_old_surname` `oldsurname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_firstname` `oldfirstname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_address1` `oldaddress1` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_address2` `oldaddress2` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_town` `oldtown` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_county` `oldcounty` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_postcode` `oldpostcode` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_telday` `oldtelday` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_televe` `oldteleve` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_gender` `oldgender` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_dob` `olddob` DATE NULL DEFAULT NULL,
					CHANGE `transfer_old_email` `oldemail` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_tshirt` `oldtshirt` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_affil` `oldaffil` TINYINT(1) NULL DEFAULT '0',
					CHANGE `transfer_old_club_id` `oldclubid` INT(11) NULL DEFAULT '0',
					CHANGE `transfer_old_regno` `oldregno` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_surname` `newsurname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_firstname` `newfirstname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_address1` `newaddress1` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_address2` `newaddress2` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_town` `newtown` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_county` `newcounty` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_postcode` `newpostcode` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_telday` `newtelday` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_televe` `newteleve` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_gender` `newgender` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_dob` `newdob` DATE NULL DEFAULT NULL,
					CHANGE `transfer_new_email` `newemail` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_tshirt` `newtshirt` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_affil` `newaffil` TINYINT(1) NULL DEFAULT '0',
					CHANGE `transfer_new_club_id` `newclubid` INT(11) NULL DEFAULT '0',
					CHANGE `transfer_new_regno` `newregno` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_complete` `complete` TINYINT(1) NULL DEFAULT '0',
					CHANGE `transfer_offerready` `offerready` TINYINT(1) NULL DEFAULT '0',
					CHANGE `transfer_offerto_entry_id` `offertoentryid` INT(11) NULL DEFAULT '0',
					CHANGE `transfer_offered_date` `offereddate` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_offer_revoked` `offerrevoked` TINYINT(1) NULL DEFAULT '0',
					CHANGE `transfer_superceded` `superceded` TINYINT(1) NULL DEFAULT '0',
					CHANGE `transfer_old_option1` `oldoption1` TINYINT(1) NULL DEFAULT '0',
					CHANGE `transfer_old_option2` `oldoption2` TINYINT(1) NULL DEFAULT '0',
					CHANGE `transfer_new_option1` `newoption1` TINYINT(1) NULL DEFAULT '0',
					CHANGE `transfer_new_option2` `newoption2` TINYINT(1) NULL DEFAULT '0',
					CHANGE `transfer_old_language_code` `oldlanguagecode` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_language_code` `newlanguagecode` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_ownchipno` `oldownchipno` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_ownchipno` `newownchipno` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_team` `oldteam` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_agecategory_code` `oldagecategorycode` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_team` `newteam` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_agecategory_code` `newagecategorycode` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_old_transportreq` `oldtransportreq` TINYINT(1) NULL DEFAULT '0',
					CHANGE `transfer_old_transporttype` `oldtransporttype` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `transfer_new_transportreq` `newtransportreq` TINYINT(1) NULL DEFAULT '0',
					CHANGE `transfer_new_transporttype` `newtransporttype` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					DROP INDEX `transfer_new_club_id`,
					DROP INDEX `transfer_offerto_entry_id`,
					DROP INDEX `transfer_old_club_id`,
					DROP INDEX `transfer_race_id`;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "ALTER TABLE `". DB_PREFIX. "transfers`
					ADD INDEX `newclubid` (`newclubid`),
					ADD INDEX `offertoentryid` (`offertoentryid`),
					ADD INDEX `oldclubid` (`oldclubid`),
					ADD INDEX `raceid` (`raceid`);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "ALTER TABLE `". DB_PREFIX. "users` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
					CHANGE `user_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
					CHANGE `user_name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `user_password` `password` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
					CHANGE `user_level_id` `levelid` INT(11) NULL DEFAULT NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "INSERT INTO `". DB_PREFIX. "dbcolumns` (`dbgroupid`, `name`, `description`, `type`, `length`, `dbupdated`, `entryrel`, `entrantrel`, `transferrel`, `transferpersist`, `changepersist`) VALUES(1, 'charval', 'Charitable Donation', 6, 0, 1, 1, 0, 0, 1, 1);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "INSERT INTO `". DB_PREFIX. "formfields` (`formid`, `sequence`, `column`, `type`, `show`, `caption`, `memo`, `control`, `values`, `dbname`, `width`, `mandatory`, `format`) VALUES(1, 485, 0, 'text', 0, 'Voluntary Donation', '<p>Please enter a donation if you feel able to.</p>', '', '', 'charval', 50, 0, 0);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "UPDATE `". DB_PREFIX. "dbcolumns`
					SET `name` = 'raceid'
					WHERE `name` = 'race_id';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "dbcolumns`
					SET `name` = 'clubid'
					WHERE `name` = 'club_id';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "dbcolumns`
					SET `name` = 'agecategorycode'
					WHERE `name` = 'agecategory_code';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "dbcolumns`
					SET `name` = 'entrantid'
					WHERE `name` = 'entrant_id';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "UPDATE `". DB_PREFIX. "dbcolumns`
					SET `entryrel` = 1, `entrantrel` = 0, `transferrel` = 0, `transferpersist` = 1, `changepersist` = 1
					WHERE `name` IN ('ordernumber', 'paid', 'racenumber', 'giftaid', 'total', 'online', 'discount', 'entrantid', 'waiting', 'deleted', 'offercount', 'waitdate');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "UPDATE `". DB_PREFIX. "dbcolumns`
					SET `entryrel` = 1, `entrantrel` = 0, `transferrel` = 1, `transferpersist` = 1, `changepersist` = 1
					WHERE `name` IN ('raceid', 'tshirt', 'option1', 'option2', 'team', 'agecategorycode', 'transportreq', 'transporttype');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "UPDATE `". DB_PREFIX. "dbcolumns`
					SET `entryrel` = 1, `entrantrel` = 1, `transferrel` = 1, `transferpersist` = 1, `changepersist` = 1
					WHERE `name` IN ('surname', 'firstname', 'address1', 'address2', 'town', 'county', 'postcode', 'telday', 'televe', 'gender', 'dob', 'email', 'affil', 'clubid', 'regno', 'languagecode', 'ownchipno');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "UPDATE `". DB_PREFIX. "pages` SET `url` = REPLACE(`url`, '.asp', '.php');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "pages` SET `url` = REPLACE(`url`, 'default.php', 'index.php');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "pages` SET `url` = REPLACE(`url`, 'oldresults.php?race_id=', 'results.php?clearfilter=1&raceid=O');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "pages` SET `url` = REPLACE(`url`, 'results.php?race_id=', 'results.php?clearfilter=1&raceid=N');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "pages` SET `url` = REPLACE(`url`, 'entrylist.php?race_id', 'entrylist.php?clearfilter=1&raceid');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "pages` SET `url` = REPLACE(`url`, 'admin_sitepay', 'admin_sitepayment');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "pages` SET `levelid` = '2' WHERE `url` = 'admin_dbupdate.php';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "pages` SET `name` = 'Database Upgrade Result Page', `url` = 'admin_dbupdateresult.php', `levelid` = '2' WHERE `url` = 'admin_dbupdatesuccess.php';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Delete a User', 'admin_userdelete.php', 1, 0, 9999, 0, 0, 0, '', 0);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Delete a Currency', 'admin_currencydelete.php', 1, 0, 9999, 0, 0, 0, '', 0);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Offline Admin Tool - Get Races', 'admin_getraces.php', 1, 0, 9999, 0, 0, 0, '', 0);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Offline Admin Tool - Get Pages', 'admin_getpages.php', 1, 0, 9999, 0, 0, 0, '', 0);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES( 'Offline Admin Tool - Test Connection', 'admin_testconnection.php', 1, 0, 9999, 0, 0, 0, '', 0);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Offline Admin Tool - Check if a Page Exists', 'admin_checkpage.php', 1, 0, 9999, 0, 0, 0, '', 0);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Offline Admin Tool - Add a Page', 'admin_addpage.php', 1, 0, 9999, 0, 0, 0, '', 0);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Offline Admin Tool - Get Series', 'admin_getseries.php', 1, 0, 9999, 0, 0, 0, '', 0);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Offline Admin Tool - Save Content', 'admin_savecontent.php', 1, 0, 9999, 0, 0, 0, '', 0);";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "pages` SET `siteid` = 0 WHERE `siteid` IS NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "UPDATE `". DB_PREFIX. "contents` SET `text` = REPLACE(`text`, '.asp', '.php');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "contents` SET `text` = REPLACE(`text`, 'default.php', 'index.php');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "UPDATE `". DB_PREFIX. "formfields` SET `memo` = REPLACE(`memo`, '.asp', '.php');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "formfields` SET `dbname` = 'clubname' WHERE dbname = 'club_name';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "formfields` SET `dbname` = 'clubid' WHERE dbname = 'club_id';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "formfields` SET `dbname` = 'entrantid' WHERE dbname = 'entrant_id';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "formfields` SET `dbname` = 'languagecode' WHERE dbname = 'language_code';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "SELECT * FROM `". DB_PREFIX. "pages` WHERE `url` = 'enter4.php';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$row = pdo_fetch_assoc($result);
				$pageid = $row['id'];
				$sql = "INSERT INTO `". DB_PREFIX. "contents` (`pageid`, `sequence`, `text`, `type`) VALUES(". $pageid. ", 15, 'The shopping basket is empty', 'plaintext');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "INSERT INTO `". DB_PREFIX. "contents` (`pageid`, `sequence`, `text`, `type`) VALUES(". $pageid. ", 16, 'delete', 'plaintext');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "SELECT * FROM `". DB_PREFIX. "pages` WHERE `url` = 'entrylist.php' AND `sequence` = 10;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$row = pdo_fetch_assoc($result);
				$pageid = $row['id'];
				$sql = "INSERT INTO `". DB_PREFIX. "contents` (`pageid`, `sequence`, `text`, `type`) VALUES(". $pageid. ", 65, 'Team:', 'plaintext');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "INSERT INTO `". DB_PREFIX. "contents` (`pageid`, `sequence`, `text`, `type`) VALUES(". $pageid. ", 125, 'Team', 'plaintext');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "SELECT * FROM `". DB_PREFIX. "pages` WHERE `url` = 'waitlist.php';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$row = pdo_fetch_assoc($result);
				$pageid = $row['id'];
				$sql = "INSERT INTO `". DB_PREFIX. "contents` (`pageid`, `sequence`, `text`, `type`) VALUES(". $pageid. ", 65, 'Team:', 'plaintext');";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$sql = "SELECT * FROM `". DB_PREFIX. "pages` WHERE `url` = 'transfer1.php';";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$row = pdo_fetch_assoc($result);
				$pageid = $row['id'];
				$sql = "INSERT INTO `". DB_PREFIX. "contents` (`pageid`, `sequence`, `text`, `type`) VALUES(". $pageid. ", 5, 'You must select whether you want to change your entry or transfer it to a new runner', 'plaintext');";

				$sql = "UPDATE `". DB_PREFIX. "entrants` SET `dob` = '0' WHERE `dob` IS NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "entries` SET `dob` = '0' WHERE `dob` IS NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "entries` SET `waitdate` = '0' WHERE `waitdate` IS NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "races` SET `date` = '0' WHERE `date` IS NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "races` SET `entriesopen` = '0' WHERE `entriesopen` IS NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "races` SET `entriesclose` = '0' WHERE `entriesclose` IS NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "transfers` SET `olddob` = '0' WHERE `olddob` IS NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
				$sql = "UPDATE `". DB_PREFIX. "transfers` SET `newdob` = '0' WHERE `newdob` IS NULL;";
				$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

				$updated = true;
			}
		}

		// Attempt to write the configuration to the dbconfig.php file
		$writestring = "<?php\n
			defined('DB_SERVER') ? null : define('DB_SERVER', '{$_POST['server']}');\n
			defined('DB_USER') ? null : define('DB_USER', '{$_POST['username']}');\n
			defined('DB_PASS') ? null : define('DB_PASS', '{$_POST['password']}');\n
			defined('DB_NAME') ? null : define('DB_NAME', '{$_POST['database']}');\n
			defined('DB_PREFIX') ? null : define('DB_PREFIX', '{$prefix}');\n
			?>\n";
		if (!$size = @file_put_contents($dirname. "/". $filename, $writestring)) {
			header('Location: admin_dbupdateresult.php?status=fail&dbversion='. $version);
			exit;
		}

		// Further db version changes will be posted here

		// If the database has been upgraded, set the current dbversion accordingly
		if ($updated) {

		// These bits always run if we've upgraded
		// $result = unlink($_SERVER["PATH_TRANSLATED"]. DS. "changelog.txt");

			$sql = "UPDATE `". DB_PREFIX. "config` SET `value` = ". $version. " WHERE `key` = 'dbversion';";
			$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

			header('Location: admin_dbupdateresult.php?status=OK&dbversion='. $version);
			exit;
		} else {
			header('Location: admin.php');
			exit;
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>openEntrySystem Installation Routine</title>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<script type="text/javascript">
			function toggleroot() {
				if (document.form1.root.checked) {
					document.getElementById("hideroot").style.display = 'block';
				} else {
					document.getElementById("hideroot").style.display = 'none';
				}
			}
		</script>
	</head>

	<body onload="self.focus();document.form1.server.focus();toggleroot();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>openEntrySystem for PHP Installation Routine</h2>
					</div>
					<div class="clear"></div><br />
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form1" id="form1">
						<div class="formcaption">MySQL Server:</div>
						<div class="formfield"><input type="text" name="server" size="50" value="<?php if(isset($_POST['server'])) { echo htmlspecialchars($_POST['server'], ENT_QUOTES); } ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Create MySQL database and user?:</div>
						<div class="formfield"><input type="checkbox" name="root" onclick="toggleroot();"<?php if (isset($_POST['root']) && $_POST['root']) { echo 'checked="checked"'; } ?> /></div>
						<div class="clear"></div><br />
						<div id="hideroot">
							<div class="formcaption">MySQL Root User Name:</div>
							<div class="formfield"><input type="text" name="rootusername" size="50" value="<?php if(isset($_POST['rootusername'])) { echo htmlspecialchars($_POST['rootusername'], ENT_QUOTES); } ?>" /></div>
							<div class="clear"></div>
							<div class="formcaption">MySQL Root Password:</div>
							<div class="formfield"><input type="password" name="rootpassword" size="50" value="<?php if(isset($_POST['rootpassword'])) { echo htmlspecialchars($_POST['rootpassword'], ENT_QUOTES); } ?>" /></div>
							<div class="clear"></div><br /><br />
						</div>
						<div class="formcaption">OES MySQL Database:</div>
						<div class="formfield"><input type="text" name="database" value="<?php if(isset($_POST['database'])) { echo htmlspecialchars($_POST['database'], ENT_QUOTES); } ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">OES Table Prefix:</div>
						<div class="formfield"><input type="text" name="prefix" value="<?php if(isset($_POST['prefix'])) { echo htmlspecialchars($_POST['prefix'], ENT_QUOTES); } ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">OES MySQL User Name:</div>
						<div class="formfield"><input type="text" name="username" value="<?php if(isset($_POST['username'])) { echo htmlspecialchars($_POST['username'], ENT_QUOTES); } ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">OES MySQL Password:</div>
						<div class="formfield"><input type="password" name="password" value="<?php if(isset($_POST['password'])) { echo htmlspecialchars($_POST['password'], ENT_QUOTES); } ?>" /></div>
						<div class="clear"></div><br /><br />
						<div class="stylered"><?php echo $installerror; ?></div>
						<div class="clear"></div><br />
						Notes:<br />
							<ul>
								<li>If you have root access to your MySQL database and would like the OES database and user to be created as part of the installation, tick the "Create MySQL database and user" checkbox. Where you don't have root access to your MySQL installation and you have been given a MySQL database name and user by your hosting company, leave the checkbox unchecked. When checked, you'll be prompted for the MySQL root user and password. These are only used during installation and are not stored.</li>
								<li>If your MySQL database is located on the same server as your webserver, you can use 'localhost' as the server name. If you don't have any tables in your database you can leave the prefix blank. An underscore will automatically be appended to the prefix so tables will be named [prefix]_[tablename].</li>
								<li>The 'table prefix' is used to ensure that the OES tables created in your MySQL database do not use the same names as tables you already have in your database. You can enter up to five characters (the first character must be an alphabetic character a-z or A-Z.</li>
							</ul><br /><br />
						<input type="submit" name="submit" value="Install" />
						<input type="hidden" name="formid" value="form1" />
					</form>
				</div>
			</div>
		</div>
	</body>
</html>