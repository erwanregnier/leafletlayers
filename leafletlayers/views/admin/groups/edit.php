<?php
if ( 'edit.php' == basename( $_SERVER['SCRIPT_FILENAME'])) {die ('Forbidden');}
?>
<div class='wrap'>
<h2><?php echo $page_title;?></h2>
<h3><?php echo __('Group details', $domain);?></h3>
<form class="form-valid" action="admin-post.php" id="leafletlayers_gedit" method="post" name="leafletlayers_gedit">
	    <table class="form-table">
		<tbody>
        	<tr>
			<th scope="row">
			    <label for="mtitle"><?php echo __('Title', $domain);?></label>
			</th>
			<td>
			    <input type="text" name="title" value="<?php echo $group_datas['group_title'];?>" id="mtitle" size="40">
			</td>
		    </tr>
			<tr class="submit_row">
			<td colspan="2">
				<input type="hidden" value="<?php echo $pepito;?>" name="pepito" id="pepita">
                <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
			    <input type="hidden" name="action" value="leafletlayers_edit_group">
			    <input type="submit" class="button-primary" value="<?php echo __('Save', $domain);?>">
			</td>
		    </tr>
		</tbody>
	    </table>
	</form>
</div>
<div class="clear"></div>