<?if($total){?>
	<div id="searchGoods" style="margin-top:0px" class="textarea">
	<?	$i=1;
		foreach($objs as $obj){
			$img_i = explode('|',$obj['img_l']);
	?>
		<div  class="drag-images">
			<img id="draggable<?=$i?>" onmouseover='draggableScript(this.id)' src="/shop/data/goods/<?=$obj['img_s']?>" title="<?=str_replace('"','',strip_tags($obj['goodsnm']))?> <?=number_format($obj['price'])."원"?>" name="<?=$img_i[0]?>" alt="<?=$obj['goodsno']?>" style="width:80px;height:80px;">
			<div class="drag-images-title"><?=$dao->strCut($obj['goodsnm'],23,"..." )?></div>
			<div class="drag-images-price"><?=number_format($obj['price'])." 원"?></div>
		</div>		
	<?	
		$i++;
		}
	?>
	</div>
	<div style="margin-top:13px;text-align:center;font:12px arial;color:#000000;">
		<?=$paging?>
	</div>
<?}else{?>				
	<div id="searchGoods" style="margin-top:0px" class="textarea">
		<div style="width:217px;height:162px;margin:180px 0 0 60px">
			※ 검색결과가 없습니다.
		</div>
	</div>
<?}?>
					