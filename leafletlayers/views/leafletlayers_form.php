<p class="leafletlayers-form-title"><?php echo __('Submit a marker',$domain);?></p>
<p class="leafletlayers-form-desc"><?php echo __('You can submit us a marker by clicking the "add" button, click on the map to add a marker and fill the details form.<br>All fields are required.',$domain);?></p>
<button class="btn waves-effect waves-light" type="button" id="leafletlayers-show-form"><?php echo __('Add a marker', $domain);?>
    <i class="material-icons right">location_on</i>
</button>
<div id="leafletlayers-form-message"></div>
<div id="leafletlayers-form-div">
<form action="" id="leafletlayers-form" method="post">
<p><?php echo __('Title', $domain);?>:<br><input name="title" id="ititle" type="text" value="" size="40"></p>
<p><?php echo __('Description', $domain);?>:<br><input name="desc" id="idesc" type="text" value="" size="40"></p>
<p><?php echo __('Address', $domain);?>:<br><input name="addr" id="iaddr" type="text" value="" size="40"></p>
<p  class="input-field"><?php echo __('Group', $domain);?>:<br>
<select name="group_id" id="igroup" autocomplete="off">
	<?php
	foreach($groups as $gr)
    {
		echo '<option value="'.$gr['id'].'">'.$gr['title'].'</option>';
    }?>
</select></p>
<input type="hidden" name="lat" value="" id="mlat">
<input type="hidden" name="lng" value="" id="mlng">
<input type="hidden" value="<?php echo $pepito;?>" name="pepito" id="pepita">
<input type="hidden" name="action" value="marker_submission">
<br>
<button class="btn waves-effect waves-light" type="submit" value="" id="leafletlayers-form-submit" name="leafletlayers-formsubmit"><?php echo __('Save', $domain);?>
    <i class="material-icons right">send</i>
  </button>
  <span id="leafletlayers-sending"><?php echo __('Sending...',$domain);?></span>
</form>
</div>