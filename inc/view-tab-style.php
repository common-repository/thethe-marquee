<?php /** @version $Id$ */ ?>
<?php $config = $this->config('style');?>

<fieldset>
  <legend>Style Settings</legend>
  <ul class="thethe-settings-list">
    <li>
      <label for="data-customcss">Custom CSS:</label>
      <textarea style="width:200px;height:260px;" name="data[custom-css]" id="data-customcss"><?php print htmlspecialchars(stripslashes($config['custom-css']));?></textarea>
      <a class="tooltip" href="javascript:void(0);">?<span>Custom CSS.</a> </li>
  </ul>
</fieldset>
