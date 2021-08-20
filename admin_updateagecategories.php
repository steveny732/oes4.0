<?php
require_once("includes/initialise.php");
require_once(LIB_PATH. "content.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Age Categories</title>
</head>

<body>
<?php
if (empty($_REQUEST["raceid"])) {
	
	echo "<br /><br /><br /><br /><br /><br />No race number specified";
	return;
}
$race = Race::findById($_REQUEST["raceid"]);

$entries = Entry::findAllForRaceForUpdate($_REQUEST["raceid"], "paid");
foreach ($entries as $entry) {
	if (!empty($entry->overridecategory)) {
		$p_agecategorycode = $entry->overridecategory;
	} else {
		$agecategorytype = AgeCategoryType::findById($race->agecategorytypeid);
		$p_agecategorycode = AgeCategory::calcAgeCategory($race->date, $agecategorytype->basis, $agecategorytype->id, $entry->dob, $entry->gender);
	}
	if ($entry->agecategorycode != $p_agecategorycode) {
		echo $entry->firstname. " ". $entry->surname. " ". $entry->dob. " was: ". $entry->agecategorycode. " now: ". $p_agecategorycode. "<br />";
		$entry->agecategorycode = $p_agecategorycode;
		$entry->save();
	}
} ?>
</body>
</html>