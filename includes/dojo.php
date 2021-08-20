<?php
if (!isset($dojoParse)) {
	$dojoParse = '';
} ?>
<script type="text/javascript">
	var djConfig = {
		isDebug: false, parseOnLoad: <?php echo $dojoParse != '' ? $dojoParse: 'true'; ?>, locale: '<?php echo $cookieLanguageCode; ?>'
	};
</script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/dojo/1.5.3/dojo/dojo.xd.js"></script>
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/dojo/1.5.3/dijit/themes/tundra/tundra.css" />