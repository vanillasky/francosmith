<?
### 필수옵션
$optnm = explode("|",$data['optnm']);
$query = "select * from ".GD_SHOPLE_GOODS_OPTION." where goodsno='$goodsno' ORDER BY `sort`";
$res = $db->query($query);
while ($tmp=$db->fetch($res)){
	$tmp = array_map("htmlspecialchars",$tmp);
	$opt1[] = $tmp['opt1'];
	$opt2[] = $tmp['opt2'];
	$opt[$tmp['opt1']][$tmp['opt2']] = $tmp;

	### 총재고량 계산
	$stock += $tmp['stock'];
}
if ($opt1) $opt1 = array_unique($opt1);
if ($opt2) $opt2 = array_unique($opt2);
if (!$opt){
	$opt1 = array('');
	$opt2 = array('');
}
?>


<div style="padding:10px;border:1px solid #CCCCCC;background-color:#F8F8F8;margin-bottom:10px;"><font class=small color=444444>이상품의 옵션이 여러개인경우 등록하세요 (색상, 사이즈 등)</font>
<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_infoprice.html',730,700)"><img src="../img/icon_sample.gif" border="0" align=absmiddle></a></div>

<div id="objOption">
	<div style="padding-bottom:10">
		<b>옵션명1</b> : <input type="text" name="optnm[]" value="<?=$optnm[0]?>">
			<a href="javascript:addopt1()" onfocus="blur()"><img src="../img/i_add.gif" align=absmiddle></a> <a href="javascript:delopt1()" onfocus=blur()><img src="../img/i_del.gif" align=absmiddle></a><span style="width:20"></span>

		<b>옵션명2</b> : <input type="text" name="optnm[]" value="<?=$optnm[1]?>">
			<a href="javascript:addopt2()" onfocus="blur()"><img src="../img/i_add.gif" align=absmiddle></a> <a href="javascript:delopt2()" onfocus=blur()><img src="../img/i_del.gif" align=absmiddle></a><span style="width:20"></span>
	</div>

	<div style="margin:10px 0"><span class="extext">등록한 옵션명1과 옵션명2를 더블클릭하시여 옵션을 삭제하실 수 있습니다.</span></div>

	<table id="tbOption" border="1" bordercolor="#cccccc" style="border-collapse:collapse">
	<tr align="center">
		<td>&nbsp;</td>
		<td><span style="color:#333333;font-weight:bold;">판매가</span></td>
		<td><span style="color:#333333;font-weight:bold;">정가</span></td>
		<?
			$j=4;
			foreach ($opt2 as $v){
			$j++;
		?>
		<td id='tdid_<?=$j?>'><input type="text" name="opt2[]" <?if($v != ''){?>class=fldtitle value="<?=$v?>"<?}else{?>class="opt gray" value='옵션명2'<?}?> <?if($j>5){?> ondblclick="delopt2part('tdid_<?=$j?>')"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"></td>
		<? } ?>
	</tr>
		<?
		$i=0;
		$op2=$opt2[0]; foreach ($opt1 as $op1){
		$i++;
		?>
	<tr id="trid_<?=$i?>">
		<td nowrap><input type="text" name="opt1[]" <?if($op1 != ''){?>class=fldtitle value="<?=$op1?>"<?}else{?>class="opt gray" value='옵션명1'<?}?> <?if($i != 1){?>ondblclick="delopt1part('trid_<?=$i?>')"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)" style="width:110px;"></td>
		<td><input type="text" name="option[price][]" class="opt gray" value="<?=$opt[$op1][$op2][price]?>" style="width:65px;"></td>
		<td><input type="text" name="option[consumer][]" class="opt gray" value="<?=$opt[$op1][$op2][consumer]?>" style="width:65px;"></td>
		<? foreach ($opt2 as $op2){ ?>
		<td><input type="text" name="option[stock][]" <?if($opt[$op1][$op2][stock]){?>class="opt" value="<?=$opt[$op1][$op2][stock]?>"<?}else{?>class="opt gray" value="0"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"><input type="hidden" name="option[optno][]" value="<?=$opt[$op1][$op2][optno]?>"></td>
		<? } ?>
	</tr>
	<? } ?>
	</table>

</div>

<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

















