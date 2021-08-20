<?php
$itemcount = Language::countAll();

// Only show if more than one language set up
if ($itemcount > 1) {
	
	$languages = Language::findAll(); ?>

	<div id="langsel">
		<form name="langform" id="langform" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="margin-top: -80px;">
			<select name="languagecode" id="languagecode" onchange="document.langform.submit();" dojoType="dijit.form.FilteringSelect">
				<?php
				// Write languages out with the site locale at the top (selected if
				// no matching locale found)
				$gotSelection = false;
				foreach ($languages as $language) {
					if ($language->code == $currentSite->languagecode) {
						$p_sitelanguagename = $language->name;
					} else {
						$p_otherlanguages .= '<option value="'. $language->code. '"';
						if ($cookieLanguageCode == $language->code) {
							$p_otherlanguages .= ' selected="selected"';
							$p_gotSelection = true;
						}
						$p_otherlanguages .= ">". $language->name. "</option>";
					}
				}
				if ($p_gotSelection) {
					echo '<option value="'. $currentSite->languagecode. '">'. $p_sitelanguagename. '</option>';
				} else {
					echo '<option value="'. $currentSite->languagecode. '" selected="selected">'. $p_sitelanguagename. '</option>';
				}
				echo $p_otherlanguages;
				?>
			</select>
			<input type="hidden" name="langsubmit" value="go" />
		</form>
	</div>
<?php
} ?>