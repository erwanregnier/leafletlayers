<?php
if ( 'edit.php' == basename( $_SERVER['SCRIPT_FILENAME'])) {die ('Forbidden');}
?>
<div class='wrap'>
<h2><?php echo $page_title;?></h2>
<p><?php echo __('Please zoom and click on the map to add marker.', $domain);?></p>
<div id="leafmap" style="height:500px;"></div>
<h3><?php echo __('Marker details', $domain);?></h3>
<p class="description"><?php echo __('all fields are required', $domain);?></p>
<form class="form-valid" action="admin-post.php" id="leafletlayers_medit" method="post" name="leafletlayers_medit">
	    <table class="form-table">
		<tbody>
        	<tr>
			<th scope="row">
			    <label for="mtitle"><?php echo __('Title', $domain);?></label>
			</th>
			<td>
			    <input type="text" name="title" value="<?php echo $marker_datas['title'];?>" id="mtitle" size="40">
			</td>
		    </tr>
		    <tr>
			<th scope="row">
			    <label for="mdesc"><?php echo __('Description', $domain);?></label>
			</th>
			<td>
			    <input type="text" name="desc" value="<?php echo $marker_datas['desc'];?>" id="mdesc" size="40">
			</td>
		    </tr>
            <tr>
			<th scope="row">
			    <label for="mlat"><?php echo __('Address', $domain);?></label>
			</th>
			<td>
			    <input type="text" name="addr" value="<?php echo $marker_datas['addr'];?>" id="maddr" size="40">
			</td>
		    </tr>
            <tr>
			<th scope="row">
			    <label for="mgroup"><?php echo __('Group', $domain);?></label>
			</th>
			<td>
			    <select name="group_id" id="mgroup" autocomplete="off">
                <?php foreach($markers_groups as $gr)
				{
					if($marker_datas['group_id'] == $gr['group_id'])
						echo '<option value="'.$gr['group_id'].'" selected="selected">'.$gr['group_title'].'</option>';
					else
						echo '<option value="'.$gr['group_id'].'">'.$gr['group_title'].'</option>';
				}?>
                </select>
			</td>
		    </tr>
			<tr class="submit_row">
			<td colspan="2">
            	<input type="hidden" name="lat" value="<?php echo $marker_datas['lat'];?>" id="mlat">
                <input type="hidden" name="lng" value="<?php echo $marker_datas['lng'];?>" id="mlng">
				<input type="hidden" value="<?php echo $pepito;?>" name="pepito" id="pepita">
                <input type="hidden" name="mid" value="<?php echo $_GET['mid'];?>">
			    <input type="hidden" name="action" value="leafletlayers_edit_marker">
			    <input type="submit" class="button-primary" value="<?php echo __('Save', $domain);?>">
			</td>
		    </tr>
		</tbody>
	    </table>
	</form>
</div>
<div class="clear"></div>