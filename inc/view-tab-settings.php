<?php /** @version $Id: view-tab-settings.php 913 2011-08-10 08:00:57Z xagero $ */ ?>
<?php $config = $this->config();?>
<fieldset>
  <legend>Main Settings</legend>
  <ul class="thethe-settings-list">
    <li>
      <label for="data-limit">Number  of  Posts:</label>
      <input name="data[limit]" id="data-limit" class="str-field" value="<?php print $config['limit'];?>" type="text">
      <a class="tooltip" href="javascript:void(0);">?<span>Enter the number of latest posts to use for the News Line output.</a> </li>
    <li>
      <label for="data-categories">Categories:</label>
      <select id="data-categories" name="data[categories][]" multiple="true" size="10"  class="text-field">
        <?php
	foreach (get_categories() as $e) {
		$selected = '';
		if (is_array($config['categories']) && in_array($e->term_id, $config['categories'])) {
			$selected = 'selected';
		}
		echo "<option {$selected} value='{$e->term_id}'>{$e->name}</option>";
	}
?>
      </select>
      <a class="tooltip" href="javascript:void(0);">?<span>Select the categories to get the post from.</a> </li>
<?php if (false) :?>
    <li>
      <label for="data-limit">Tags:</label>
      <select id="data-categories" name="data[categories][]" multiple="true" size="10" class="text-field">
        <?php
	foreach (get_tags() as $e) {
		$selected = '';
		if (is_array($config['tags']) && in_array($e->term_id, $config['tags'])) {
			$selected = 'selected';
		}
		echo "<option {$selected} value='{$e->term_id}'>{$e->name}</option>";
	}
?>
      </select>
      <a class="tooltip" href="javascript:void(0);">?<span>Select the tags that posts must contain to be taken into the Marquee output.</a> </li>
<?php endif;?>
    <li>
      <label for="data-width">Width:</label>
      <input name="data[width]" id="data-width" class="str-field" value="<?php print $config['width'];?>" type="text">
      <a class="tooltip" href="javascript:void(0);">?<span>Enter the width of the Marquee.</a> </li>
    <li>
      <label for="data-height">Height:</label>
      <input name="data[height]" id="data-height" class="str-field" value="<?php print $config['height'];?>" type="text">
      <a class="tooltip" href="javascript:void(0);">?<span>Enter the height of the Marquee.</a> </li>
    <li>
      <label for="data-showtime">Show Time:</label>
      <input name="data[showtime]" id="data-showtime" class="str-field" value="<?php print $config['showtime'];?>" type="text">
      <a class="tooltip" href="javascript:void(0);">?<span>Showtime.</a>
    </li>
    <li>
      <label for="data-stoptime">Stop Time:</label>
      <input name="data[stoptime]" id="data-stoptime" class="str-field" value="<?php print $config['stoptime'];?>" type="text">
      <a class="tooltip" href="javascript:void(0);">?<span>Stoptime.</a> </li>
    <li>
      <label for="data-date-format">Date\time format:</label>
      <input name="data[date-format]" id="data-date-format" class="str-field" value="<?php print $config['date-format'];?>" type="text">
      <a class="tooltip" href="javascript:void(0);">?<span>
      	Date\time format:<br>
		F j, Y g:i a<br>
		F j, Y<br>
		F, Y<br>
		g:i a
      	</span>
      </a>
    </li>
    <li>
      <label for="data-effect">Replacement Effect:</label>
      <select id="data-effect" name="data[effect]">
        <?php 
	foreach (array(
		0 => 'Marquee easy',
		1 => 'Scrolling to the Left',
		2 => 'Scrolling to the Right',
		3 => 'Scrolling to the Up',
		4 => 'Scrolling to the Down'
		
	) as $num => $effect) {
		$selected = '';
		if ($num == $config['effect']) {
			$selected = 'selected';
		}
		echo "<option {$selected} value='{$num}'>{$effect}</option>";
	}
?>
      </select>
      <a class="tooltip" href="javascript:void(0);">?<span>Replacement Effect.</a> </li>
  </ul>
</fieldset>