<style type="text/css">
<?php
if (!empty($currentSite->headingcolour)) {
	echo 'h1 {color: '. $currentSite->headingcolour. ';}';
	echo 'h2 {color: '. $currentSite->headingcolour. ';}';
	echo 'h3 {color: '. $currentSite->headingcolour. ';}';
	echo 'a  {color: '. $currentSite->headingcolour. ';}';
}
if (!empty($currentSite->menucolour) || !empty($currentSite->menutextcolour)) {
	if (!empty($currentSite->menucolour)) {
		echo 'ul.MenuBarVertical ul {';
		echo 'background-color: '. $currentSite->menucolour. ';';
		echo '}';
	}
	echo 'ul.MenuBarVertical a {'; 
	if (!empty($currentSite->menucolour)) {
		echo 'background-color: '. $currentSite->menucolour. ';';
	}
	if (!empty($currentSite->menutextcolour)) {
		echo 'color: '. $currentSite->menutextcolour. ';';
	}
	echo '}';
}
if (!empty($currentSite->menurocolour) || !empty($currentSite->menutextrocolour)) {
	echo 'ul.MenuBarVertical a:hover, ul.MenuBarVertical a:focus {';
	if (!empty($currentSite->menurocolour)) {
		echo 'background-color: '. $currentSite->menurocolour. ';';
	}
	if (!empty($currentSite->menutextrocolour)) {
		echo 'color: '. $currentSite->menutextrocolour. ';';
	}
	echo '}';
	echo 'ul.MenuBarVertical a.MenuBarItemHover, ul.MenuBarVertical a.MenuBarItemSubmenuHover, ul.MenuBarVertical a.MenuBarSubmenuVisible {';
	if (!empty($currentSite->menurocolour)) {
		echo 'background-color: '. $currentSite->menurocolour. ';';
	}
	if (!empty($currentSite->menutextrocolour)) {
		echo 'color: '. $currentSite->menutextrocolour. ';';
	}
	echo '}';
}
if (!empty($currentSite->buttoncolour) || !empty($currentSite->buttontextcolour)) {
	echo '.button a:link, .button a:visited, .smallbutton a:link, .smallbutton a:visited {';
	if (!empty($currentSite->buttoncolour)) {
		echo 'background-color: '. $currentSite->buttoncolour. ';';
	}
	if (!empty($currentSite->buttontextcolour)) {
		echo 'color: '. $currentSite->buttontextcolour. ';';
	}
	echo '}';
}
if (!empty($currentSite->buttonrocolour) || !empty($currentSite->buttontextrocolour)) {
	echo '.button a:hover, .smallbutton a:hover {';
	if (!empty($currentSite->buttonrocolour)) {
		echo 'background-color: '. $currentSite->buttonrocolour. ';';
	}
	if (!empty($currentSite->buttontextrocolour)) {
		echo 'color: '. $currentSite->buttontextrocolour. ';';
	}
	echo '}';
}
?>
</style>
