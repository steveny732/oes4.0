<?php
require_once('page.php'); ?>

<div id="footer">
	<div class="footerleft">
		<?php echo $currentSite->copyrightMessage(); ?>
	</div>
	<div class="footerright">
		<a href="http://www.openentrysystem.org" style="color: #006677;">Powered by openEntrySystem</a>
	</div><br />
	
	<div class="footercentre">
		<?php
		$p_separator = "";
		$footers = Page::findAllFooters($currentSite->id);
		foreach($footers as $footer) {
			if (substr($footer->url, 0, 9) == "index.php") {
				echo $p_separator. '<a href="'. $footer->url. '">'. $footer->name. '</a>';
			} else {
				echo $p_separator. '<a href="#" onclick="openWindow(\''. $footer->url. '\',\'\',\'scrollbars=yes,resizable=yes,width=790,height=800\')">'. $footer->name. '</a>';
			}
			if ($p_separator == "") {
				$p_separator = " | ";
			}
		} ?>
	</div>
	<?php
	if ($session->isLoggedIn()) { ?>
		<div class="footercentre"><a href="admin_loggedin.php">Administration Home</a></div>
	<?php } ?>
	
</div>
<p class="accessibility">[<a href="#topofpage" accesskey="T">Go back to the top of the page</a>]</p>
<?php
if ($currentSite->googleanalyticscode != "*NONE") { ?>
	<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '<?php echo $currentSite->googleanalyticscode; ?>']);
	_gaq.push(['_trackPageview']);
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
	</script>
<?php
}
if (isset($db)) {
	$db->closeConnection();
} ?>