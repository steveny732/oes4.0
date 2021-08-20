<?php
require_once(LIB_PATH. "entry.php"); ?>

<ul id="myMenuBar" class="MenuBarVertical">
	<?php
	$menuitems = Page::findAllMenuItems($currentSite->id, $cookieLanguageCode);

	foreach ($menuitems as $menuitem) {

		$submenuitems = Page::findAllSubMenuItems($menuitem->id, $cookieLanguageCode);

		echo "<li><a ";
		if (!empty($submenuitems)) {
			echo 'class="MenuBarItemSubmenu" ';
		}
		echo 'href="'. $menuitem->url. '" ';
		echo 'alttext="'. $menuitem->alttext. '">';
		echo $menuitem->name. "</a>";

		if (!empty($submenuitems)) {
			echo "<ul>";
			foreach ($submenuitems as $submenuitem) {
				echo '<li><a href="'. $submenuitem->url. '" ';
				echo 'alttext="'. $submenuitem->alttext. '">';
				echo $submenuitem->name. '</a></li>';
			}
			echo "</ul>";
		}
		echo "</li>";
	} ?>
</ul><br />
<?php
$JS1 = '<script type="text/javascript">function loadBasketValues(){';
if (!empty($_SESSION['ordernumber'])) {
	$entrytotals = Entry::findTotalsByOrderNumber($_SESSION['ordernumber']);

	if ($entrytotals['entrycount'] > 0) { ?>
		<div class="basket">
			<div class="basketheader">Shopping Basket</div>
			<div class="basketinner">
				There are <?php echo $entrytotals["entrycount"]; ?> entries awaiting payment:<br />
				<div class="floatleft">Total:</div>
				<div class="floatright"><strong><span id="displaytotal"></span></strong></div>
				<div class="clear"></div>
				<form name="basketform" id="backetform" action="javascript:openWindow('enter4.php','','scrollbars=yes,resizable=yes,width=790,height=800');">
					<input type="hidden" name="totalvalue" id="totalvalue" value="<?php echo $entrytotals['totalvalue']; ?>" />
					<input type="hidden" name="currency" id="currency" value="<?php echo $currentSite->currencycode; ?>" />
					<input type="submit" name="submit" value="View Basket" />
				</form>
			</div>
		</div>
		<?php
		$JS1 .= "MM_setTextOfLayer('displaytotal', '', dojo.currency.format(document.basketform.totalvalue.value, {currency: '". $currentSite->currencycode. "'}));";
	} else {
		$_SESSION['ordernumber'] = '';
	}
}
$JS1 .= "}</script>";
echo $JS1;
?>
<br />