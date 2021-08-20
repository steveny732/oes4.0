<?php
/* ************************************************************************
' admin_getcoupon.php -- Return a Matching Coupon via Ajax
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
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'paging.php');
require_once(LIB_PATH. 'entrant.php');

$p_raceid = str_replace("'", "''", htmlspecialchars($_REQUEST['raceid'], ENT_QUOTES));
$p_couponcode = str_replace("'", "''", htmlspecialchars($_REQUEST['couponcode'], ENT_QUOTES));

header('Content-type: text/xml');
echo "<?xml version='1.0' encoding='UTF-8'?>";
echo '<coupon>';

if (empty($p_couponcode)) {
	echo '	<name>No coupon found</name>';
	echo '	<discount> </discount>';
} else {

	if (!empty($p_raceid)) {
		$coupon = Coupon::findByRaceAndCouponCode($p_raceid, $p_couponcode);
	} else {
		$coupon = Coupon::findByCouponCode($p_couponcode);
	}

	if (empty($coupon)) {
		echo '	<name>No coupon found</name>';
		echo '	<discount> </discount>';
	} else {
		echo '	<name>'. XMLEncode($coupon->name). '</name>';
		echo '	<discount>'. $coupon->discount. '</discount>';
	}
}
echo '</coupon>'; ?>