<?php if ( 'list.php' == basename( $_SERVER['SCRIPT_FILENAME'])) {die ('Forbidden');}?>
<div class='wrap'>
<h2><?php echo $page_title;?></h2>
<?php echo $groups_table->display();?>
</div>
<div class="clear"></div>