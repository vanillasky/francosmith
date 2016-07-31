<?foreach($objs as $obj){?>
	<li><a href="javascript:" onclick="template('<?=$obj->get('TP_id')?>');"><img src='../../data/tmplate_thumbnail/<?=$obj->get('TP_id')?>.gif' /></a></li>						
<?}?>