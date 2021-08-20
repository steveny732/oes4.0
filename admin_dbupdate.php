<?php
/* ************************************************************************
' admin_dbupdate.php -- Administration - Upgrade the Database
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
'	Version: 3.0
'	Last Modified By:
'		betap 2010-11-29
'		Update for MySQL database version 3.0.
'
' ************************************************************************ */
require_once("includes/initialise.php");

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}
$newdbversion = 312;
$updated = false;

$config = Config::findByKey("dbversion");

// Versions prior to 207 cannot be upgraded here (must be upgraded in ASP version first)
if ($config->value < 207) {
	redirect_to("admin_dbupdateresult.php?status=FAIL&dbversion=". $newdbversion. "&currentdbversion=". $config->value. "&requireddbversion=207");
}

// Version 300 upgrade to 301
if ($config->value < 301) {
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

	$sql = "ALTER TABLE `". DB_PREFIX. "racelangs`
		ADD `timetext` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptimetext` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime2text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime3text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime4text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime5text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime6text` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "ALTER TABLE `". DB_PREFIX. "results`
		CHANGE `chiptime` `chiptime` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime2` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime3` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime4` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime5` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime6` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "ALTER TABLE `". DB_PREFIX. "oldresults`
		CHANGE `chiptime` `chiptime` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime2` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime3` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime4` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime5` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
		ADD `chiptime6` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "ALTER TABLE `". DB_PREFIX. "dbcolumns`
		ADD `transferpersist` TINYINT(1) NOT NULL DEFAULT '0',
		ADD `changepersist` TINYINT(1) NOT NULL DEFAULT '0';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `transferpersist` = 1, `changepersist` = 1;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$row = pdo_fetch_assoc($result);

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

	$updated = true;
}

// Version 301 upgrade to 302
if ($config->value < 302) {
	$sql = "CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "changes` (
	`id` int(11) NOT NULL auto_increment,
	`entryid` int(11) NOT NULL,
	`source` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL,
	`time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	`column` varchar(20) character set utf8 collate utf8_unicode_ci NOT NULL,
	`oldvalue` text character set utf8 collate utf8_unicode_ci NOT NULL,
	`newvalue` text character set utf8 collate utf8_unicode_ci NOT NULL,
	`userid` int(11) NOT NULL,
	PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Offline Admin Tool - Get Age Categories', 'admin_getagecats.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Offline Admin Tool - Upload Changes', 'admin_uploadchanges.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Offline Admin Tool - Get Form Fields', 'admin_getformfields.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('List entry changes', 'admin_entrychangelist.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Race ID' WHERE `name` = 'raceid';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Entry No.' WHERE `name` = 'racenumber';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Address 1' WHERE `name` = 'address1';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Address 2' WHERE `name` = 'address2';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Tel.(Day)' WHERE `name` = 'telday';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Tel.(Eve)' WHERE `name` = 'televe';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'DOB' WHERE `name` = 'dob';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Email' WHERE `name` = 'email';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Tshirt' WHERE `name` = 'tshirt';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Affiliated' WHERE `name` = 'affil';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Reg.No.' WHERE `name` = 'regno';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Giftaid' WHERE `name` = 'giftaid';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Club ID' WHERE `name` = 'clubid';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Option 1' WHERE `name` = 'option1';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Option 2' WHERE `name` = 'option2';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Team' WHERE `name` = 'team';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Age Category' WHERE `name` = 'agecategorycode';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Entrant ID' WHERE `name` = 'entrantid';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Paid' WHERE `name` = 'paid';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "dbcolumns` SET `description` = 'Value' WHERE `name` = 'total';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `type` = 'races' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `dbname` = 'clubid' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `dbname` = 'entrantid' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `dbname` = 'languagecode' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `dbname` = 'total' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `dbname` = 'paid' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `dbname` = 'online' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `dbname` = 'discount' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `dbname` = 'waiting' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `dbname` = 'offercount' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `dbname` = 'waitdate' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `dbname` = 'overridecategory' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "formfields` SET `control` = '' WHERE `dbname` = 'sendemail' AND `control` = '*NOCLEAR';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "SELECT * FROM `". DB_PREFIX. "formfields` WHERE `dbname` = 'overridecategory';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	while ($row = pdo_fetch_assoc($result)) {
		$seq = $row['sequence'] + 1;
		$sql = "INSERT INTO `". DB_PREFIX. "formfields` (`formid`, `sequence`, `column`, `type`, `show`, `caption`, `memo`, `control`, `values`, `dbname`, `width`, `mandatory`, `format`, `minval`, `maxval`)
			VALUES(". $row['formid']. ", ". $seq. ", 2, 'checkbox', 0, 'New Person?', 'Tick if changing entry to a new person', '', '', 'createentrant', 0, 0, 0, '', '');";
		$result1 = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	}

	$sql = "INSERT INTO `". DB_PREFIX. "dbcolumns` (`dbgroupid`, `name`, `description`, `type`, `length`, `dbupdated`, `entryrel`, `entrantrel`, `transferrel`, `transferpersist`, `changepersist`) VALUES(1, 'createentrant', 'Create New Person', 11, NULL, 1, 0, 0, 0, 1, 1);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "ALTER TABLE `". DB_PREFIX. "payhistory` CHANGE `ordernumber` `ordernumber` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$updated = true;
}

// Version upgrade to 305
if ($config->value < 305) {
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Offline Admin Tool - Get Clubs', 'admin_getclubs.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
// TODO: Need to check here for missing fields on results table
	$updated = true;
}

// Version upgrade to 306
if ($config->value < 306) {
	$sql = "CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "coupons` (
		`id` int(11) NOT NULL auto_increment,
		`raceid` int(11) NOT NULL,
		`code` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL,
		`name` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL,
		`startdate` date NOT NULL,
		`enddate` date NOT NULL,
		`discount` decimal(11,2) NOT NULL,
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "raceprices` (
		`id` int(11) NOT NULL auto_increment,
		`raceid` int(11) NOT NULL,
		`name` varchar(50) collate utf8_unicode_ci NOT NULL,
		`startdate` date NOT NULL,
		`enddate` date NOT NULL,
		`fee` decimal(11,2) NOT NULL,
		`onlinefee` decimal(11, 2) NOT NULL,
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('List coupons for maintenance', 'admin_couponlist.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Add a coupon', 'admin_couponadd.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Update a coupon', 'admin_couponupdate.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Delete a coupon', 'admin_coupondelete.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('List race prices for maintenance', 'admin_racepricelist.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Add a race price', 'admin_racepriceadd.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Update a race price', 'admin_racepriceupdate.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Delete a race price', 'admin_racepricedelete.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "INSERT INTO `". DB_PREFIX. "dbgroups` (`name`, `enabled`, `system`) VALUES('Discount Coupon', 0, 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "SELECT * FROM `". DB_PREFIX. "dbgroups` WHERE `name` = 'Discount Coupon';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$row = pdo_fetch_assoc($result);
	$id = $row['id'];
	$sql = "INSERT INTO `". DB_PREFIX. "dbcolumns` (`dbgroupid`, `name`, `description`, `type`, `length`, `dbupdated`, `entryrel`, `entrantrel`, `transferrel`, `transferpersist`, `changepersist`) VALUES(". $id. ", 'couponcode', 'Coupon Code for Discount', 202, 50, 0, 1, 0, 0, 1, 1);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$updated = true;
}

// Version upgrade to 307
if ($config->value < 307) {

	$sql = "CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "paymethods` (
		`id` int(11) NOT NULL auto_increment,
		`paytype` int(11) NOT NULL,
		`name` varchar(50) collate utf8_unicode_ci NOT NULL,
		`testurl` varchar(100) collate utf8_unicode_ci NOT NULL,
		`liveurl` varchar(100) collate utf8_unicode_ci NOT NULL,
		`field1name` varchar(50) collate utf8_unicode_ci NOT NULL,
		`field2name` varchar(50) collate utf8_unicode_ci NOT NULL,
		`field3name` varchar(50) collate utf8_unicode_ci NOT NULL,
		`field4name` varchar(50) collate utf8_unicode_ci NOT NULL,
		`field5name` varchar(50) collate utf8_unicode_ci NOT NULL,
		`field6name` varchar(50) collate utf8_unicode_ci NOT NULL,
		PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "INSERT INTO `". DB_PREFIX. "paymethods` (`paytype`, `name`, `testurl`, `liveurl`, `field1name`) VALUES(1, 'NOCHEX', 'test_apc.php', 'https://secure.nochex.com/', 'Merchant ID');";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "INSERT INTO `". DB_PREFIX. "paymethods` (`paytype`, `name`, `testurl`, `liveurl`, `field1name`) VALUES(2, 'PAYPAL', 'https://www.sandbox.paypal.com/cgi-bin/webscr', 'https://www.paypal.com/cgi-bin/webscr', 'Business');";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "INSERT INTO `". DB_PREFIX. "paymethods` (`paytype`, `name`, `testurl`, `liveurl`, `field1name`, `field2name`, `field3name`, `field4name`, `field5name`, `field6name`) VALUES(3, 'Virtual TPV', '', 'https://sis.sermepa.es/sis/realizarPago', 'Merchant Name', 'Key', 'Titular', 'Commercial Code', 'Terminal', 'Currency');";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "ALTER TABLE `". DB_PREFIX. "races`
		ADD `tcpageid` int(11) NOT NULL DEFAULT '0',
		ADD `showentrylist` tinyint(1) NOT NULL DEFAULT '1';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Copy Page', 'admin_pagecopy.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$updated = true;
}

// Version upgrade to 310
if ($config->value < 310) {

	// The last update statement for version 307 may not have run. Run it now if the auth is missing.
	$sql = "SELECT COUNT(*) `count` FROM `". DB_PREFIX. "pages` WHERE `url` = 'admin_pagecopy.php';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$row = pdo_fetch_assoc($result);
	$count = $row['id'];
	if ($count == 0) {
		$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Copy Page', 'admin_pagecopy.php', 1, 0, 9999, 0, 0, 0, '', 0);";
		$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	}

	// Increase the size of the form field values column up to 400 characters
	$sql = "ALTER TABLE `". DB_PREFIX. "formfields`
		CHANGE `values` `values` VARCHAR(400) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	// Create sales item tables
	$sql = "CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "racesalesitems` (
		`raceid` int(11) NOT NULL,
		`salesitemid` int(11) NOT NULL,
		PRIMARY KEY  (`raceid`, `salesitemid`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
$sql = "CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "salesitems` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
	`price` decimal(11,2) default '0.00',
	`allowquantity` tinyint(1) default '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";
$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
$sql = "CREATE TABLE IF NOT EXISTS `". DB_PREFIX. "salesitemlangs` (
	`id` int(11) NOT NULL auto_increment,
	`salesitemid` int(11) default NULL,
	`languagecode` varchar(5) character set utf8 collate utf8_unicode_ci default NULL,
	`name` varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
	PRIMARY KEY (`id`),
	KEY `salesitemid` (`salesitemid`,`languagecode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";
$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	// Add security for the sales items maintenance functions
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('List sales items for maintanence', 'admin_salesitemlist.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Add a sales item', 'admin_salesitemadd.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Update a sales item', 'admin_salesitemupdate.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Delete a sales item', 'admin_salesitemdelete.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Translate a sales item', 'admin_salesitemtrans.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "pages` (`name`, `url`, `levelid`, `levelonly`, `sequence`, `showonmenu`, `showonfooter`, `subpageof`, `alttext`, `siteid`) VALUES('Maintain Race Sales Items', 'admin_racesalesitem.php', 1, 0, 9999, 0, 0, 0, '', 0);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	// Add the entries salesitems database column entry
	$sql = "INSERT INTO `". DB_PREFIX. "dbcolumns` (`dbgroupid`, `name`, `description`, `type`, `length`, `dbupdated`, `entryrel`, `entrantrel`, `transferrel`, `transferpersist`, `changepersist`) VALUES(1, 'salesitems', 'Sales Items', 202, 100, 1, 1, 0, 0, 1, 1);";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "ALTER TABLE `". DB_PREFIX. "entries`
		ADD `salesitems` varchar(100) character set utf8 collate utf8_unicode_ci default NULL;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	// Allow the get races Ajax call to run public
	$sql = "UPDATE `". DB_PREFIX. "pages` SET `levelid` = 4 WHERE `url` = 'admin_getraces.php';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	// Add sales item layout additions to the standard entry email
	$sql = "INSERT INTO `". DB_PREFIX. "contents` (`pageid`, `sequence`, `text`, `type`) VALUES(156, 150, '<p>&nbsp;</p><p><span style=\"text-decoration: underline;\">Extras Purchased</span></p>', 'text');";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "contents` (`pageid`, `sequence`, `text`, `type`) VALUES(156, 160, '<p>Item: <strong>*SINAME</strong></p><p>Quantity: <strong>*SIQTY</strong></p><p>Amount: <strong>*SIAMOUNT</strong></p>', 'text');";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	$sql = "SELECT `id`, `text` FROM `". DB_PREFIX. "contents` WHERE `text` LIKE '%*TSHIRTDETS*OPTION1DETS*OPTION2DETS%'";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$row = pdo_fetch_assoc($result);
	if (count($row) == 1) {
		$id = $row['id'];
		$text = $row['text'];
		$sql = "UPDATE `". DB_PREFIX. "contents` SET `text` = '". str_replace('*TSHIRTDETS*OPTION1DETS*OPTION2DETS', '*TSHIRTDETS*OPTION1DETS*OPTION2DETS*SI', $text). "' WHERE `id` = {$id}";
		$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	}

	// Add sales item stuff to every recognizable form (default to hidden)
	$sql = "SELECT * FROM `". DB_PREFIX. "formfields` WHERE `dbname` = 'transporttype';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	while ($row = pdo_fetch_assoc($result)) {
		$seq = $row['sequence'] + 1;
		$sql = "INSERT INTO `". DB_PREFIX. "formfields` (`formid`, `sequence`, `column`, `type`, `show`, `caption`, `memo`, `control`, `values`, `dbname`, `width`, `mandatory`, `format`, `minval`, `maxval`) VALUES(". $row['formid']. ", ". $seq. ", 0, 'salesitems', 0, '*RACENAME Chargeable Options', '', '', '', '', 0, 0, 0, '', '');";
		$result1 = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
		$seq = $row['sequence'] + 1;
		$sql = "INSERT INTO `". DB_PREFIX. "formfields` (`formid`, `sequence`, `column`, `type`, `show`, `caption`, `memo`, `control`, `values`, `dbname`, `width`, `mandatory`, `format`, `minval`, `maxval`) VALUES(". $row['formid']. ", ". $seq. ", 0, 'memo', 0, '', '<p><span style=\"text-decoration: underline;\">Other Options</span></p>', '', '', '', 0, 0, 0, '', '');";
		$result1 = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	}
	$sql = "SELECT * FROM `". DB_PREFIX. "formfields` WHERE `dbname` = 'discfee';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	while ($row = pdo_fetch_assoc($result)) {
		$seq = $row['sequence'] + 1;
		$sql = "INSERT INTO `". DB_PREFIX. "formfields` (`formid`, `sequence`, `column`, `type`, `show`, `caption`, `memo`, `control`, `values`, `dbname`, `width`, `mandatory`, `format`, `minval`, `maxval`) VALUES(". $row['formid']. ", ". $seq. ", 0, 'sum', 0, 'Total Sales Items:', '', '', '', 'sifee', 0, 0, 0, '', '');";
		$result1 = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	}

	// Add the email sender column to sites
	$sql = "ALTER TABLE `". DB_PREFIX. "sites`
		ADD `emailfrom` text collate utf8_unicode_ci;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "UPDATE `". DB_PREFIX. "sites` SET `emailfrom` = `entrybcc`;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	// Add stuff for captcha
	$sql = "SELECT `id` FROM `". DB_PREFIX. "pages` WHERE `url` = 'contact'";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$row = pdo_fetch_assoc($result);
	if (count($row) == 1) {
		$id = $row['id'];
		$sql = "INSERT INTO `". DB_PREFIX. "contents` (`pageid`, `sequence`, `text`, `type`)
			VALUES({$id}, 2, 'You must enter the security code as shown', 'plaintext'),
				({$id}, 80, 'Security Code', 'plaintext'),
				({$id}, 90, 'Please type the security code above to confirm your identity', 'plaintext');";
		$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	}

	$updated = true;
}

// Version upgrade to 311
if ($config->value < 311) {
	
	// Add strings for FOC payment page
	$sql = "SELECT * FROM `". DB_PREFIX. "pages` WHERE `url` = 'enter4.php';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$row = pdo_fetch_assoc($result);
	$pageid = $row['id'];
	$sql = "INSERT INTO `". DB_PREFIX. "contents` (`pageid`, `sequence`, `text`, `type`) VALUES(". $pageid. ", 90, 'Accept Entry', 'plaintext');";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "INSERT INTO `". DB_PREFIX. "contents` (`pageid`, `sequence`, `text`, `type`) VALUES(". $pageid. ", 100, '<p>&nbsp;</p><p>No payment is necessary. You can just click the ''Accept Entry'' button above and you''ll be registered on the event.</p>', 'text');";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	// Increase the size of the password for hashing
	$sql = "ALTER TABLE `". DB_PREFIX. "users`
		CHANGE `password` `password` varchar(255) character set utf8 collate utf8_unicode_ci default NULL;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	$sql = "SELECT * FROM `". DB_PREFIX. "users`;";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	while ($row = pdo_fetch_assoc($result)) {
		$sql = "UPDATE `". DB_PREFIX. "users` SET `password` = '". password_hash($row['password'], PASSWORD_DEFAULT). "'
			WHERE id = ". $row['id']. ";";
		$result1 = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");
	}

	$updated = true;
}

// Version upgrade to 312
if ($config->value < 312) {
	// No database changes
	
	$updated = true;
}

// Further db version changes will be posted here

// If the database has been upgraded, set the current dbversion accordingly
if ($updated) {

// These bits always run if we've upgraded
// $result = unlink($_SERVER["PATH_TRANSLATED"]. DS. "changelog.txt");

	$sql = "UPDATE `". DB_PREFIX. "config` SET `value` = ". $newdbversion. " WHERE `key` = 'dbversion';";
	$result = pdo_query($sql) or die ("Error executing SQL: ". $sql. "<br />");

	redirect_to("admin_dbupdateresult.php?status=OK&dbversion=". $newdbversion);
}

redirect_to("admin_loggedin.php")
?>