<?

include "../_header.popup.php";

if ($_GET['mode']=="modify"  || $_GET['mode']=="reply"){
	$data = $db->fetch("select *, if(sno = parent and m_no > 0 , 'Y' , 'N') as apply, if(sno = parent and m_no > 0 and emoney = 0 , 'Y' , 'N') as apply2 from ".GD_TODAYSHOP_GOODS_REVIEW." where sno='" . $_GET['sno'] . "'",1);
	$data['subject']	= htmlspecialchars( $data['subject'] );
	$data['contents']	= htmlspecialchars( $data['contents'] );

	if ( $_GET['mode']=="reply" ){
		$data['subject']	= '';
		$data['contents']	= '';
		$data['regdt']		= date( 'Y-m-d H:i:s' );
		$data['ip']			= $_SERVER['REMOTE_ADDR'];
	}

	if ( empty($data['m_no']) ){
		$data['m_id']	= $data['name']; // 비회원명
		$data['name']	= "";
	}else{
		list( $data['m_id'], $data['name'], $data['mobile'] ) = $db->fetch("select m_id, name, mobile from ".GD_MEMBER." where m_no='" . $data['m_no'] . "'");
	}

	$query = "select b.goodsnm,b.img_s,c.price
	from
		".GD_GOODS." b
		left join ".GD_GOODS_OPTION." c on b.goodsno=c.goodsno and link and go_is_deleted <> '1'
	where
		b.goodsno = '" . $data['goodsno'] . "'";
	list( $data['goodsnm'], $data['img_s'], $data['price'] ) = $db->fetch($query);
}
?>

<script language="JavaScript" type="text/JavaScript">
function chkLength(obj){
	str = obj.value;
	document.getElementById('vLength').innerHTML = chkByte(str);
	if (chkByte(str)>80){
		alert("80byte까지만 입력이 가능합니다");
		obj.value = strCut(str,80);
	}
}
</script>

<form name="form" method="post" action="goods_review_indb.php" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>" />
<input type="hidden" name="sno" value="<?=$_GET['sno']?>" />
<input type="hidden" name="writer_m_no" value="<?=$data['m_no']?>" />
<input type="hidden" name="goodsno" value="<?=$data['goodsno']?>" />
<div class="title title_top">상품후기 <?=( $_GET['mode'] == "modify" ? '수정' : '답변' )?></div>

<table class="tb">
<col class="cellC" /><col class="cellL" />
<tr height=26>
	<td>상품</td>
	<td>
	<div style="float:left"><?=goodsimg($data['img_s'],40,"style='border:1 solid #efefef;margin-right:10px;'",1)?></div>
	<div style="float:left;color:#0074BA;" class="small">[ <?=$data['goodsnm']?> ]</div>
	</td>
</tr>
<tr>
	<td><?=( $_GET['mode'] == "modify" ? '작성자' : '답변관리자' )?></td>
	<td><font class="ver8"><?
if ( $_GET['mode']=="reply" ){
	echo '<select name="m_no">';

	$res = $db->query( "select m_no, m_id, name from ".GD_MEMBER." where m_id!='godomall' and level = 100 order by m_id" );
	while( $row = $db->fetch( $res ) ){
		echo '<option value="' . $row['m_no'] . '">' . $row['m_id'] . ' [' . $row['name'] . ']</option>';
	}

	echo '</select>';
}
else {
	echo $data['name'] . "[".$data['m_id']."]";
}
?></td>
</tr>
<tr>
	<td>작성일</td>
	<td><font class="ver8"><?=$data[regdt]?> &nbsp;&nbsp;&nbsp; ( <?=$data[ip]?> )</td>
</tr>
<?if($_GET['mode'] != 'reply' && $_GET['mode'] != 'modify'){?>
<tr>
	<td>평점</td>
	<td><?=str_repeat( "★", $data['point'] )?></td>
</tr>
<?}?>
<?
	if($data['apply'] == "Y" && $data['m_id'] && $_GET['mode'] == "reply"){
		if($data['apply2'] == "Y"){
?>
<tr>
	<td>적립금지급이유</td>
	<td>
	<select name="memo" required label="지급이유" onchange="openLayer('direct', (this.value=='direct' ? 'block' : 'none') )" style="float:left;">
		<option value="">- 선택하세요 -</option>
<?
			foreach( codeitem('point') as $v ){
				$selected = "";
				if($v == "상품후기 작성 포인트 적립")$selected = "selected";
				echo '<option value="' . $v . '" '.$selected.'>' . $v . '</option>' . "\n";
			}
?>
		<option value="direct">☞ 직접입력</option>
	</select>
	<div id="direct" style="display:none;"><input type="text" name="direct_memo" size="30" /></div>
	</td>
</tr>
<tr>
	<td>적립금지급</td>
	<td>
	<input type="hidden" name="emoneyPut" value="Y" />
	<input type="text" name="emoney" value="<?=$data['emoney']?>" size="6" class="rline" onkeydown="onlynumber();" />원
	※ <? echo $data['name'] . "[".$data['m_id']."]";?> 회원에게 지급
	</td>
</tr>
<?
		}
	}

	if ( $data['m_id'] && $_GET['mode'] == "reply" ){

		if( getSmsPoint() < 1){
			$disabled = "disabled";
		}
?>
<tr>
	<td>SMS 전송<br /><font class="small1">[잔여건수 <span id="span_sms" style="font-weight:bold"><font class="ver9" color="0074ba"><b><?=number_format(getSmsPoint())?></b></font></span><font color="262626">건</font>]</font></td>
	<td>
	<div class="noline"><input type="checkbox" name="smsSendYN" value="Y" <?=$disabled?> /> 체크시 <? echo $data['name'] . "[".$data['m_id']."]";?> 회원에게 SMS 전송</div>
	<div>
	<input type="hidden" name="type" value="1" />
	<input type="hidden" name="name" value="<?=$data['name']?>" />
	<input type="hidden" name="phone" value="<?=str_replace("-","",$data['mobile'])?>" />
	<input type="hidden" name="callback" value="<?=str_replace("-","",$cfg['smsRecall'])?>" />
	<input type="text" name="msg" value="" style="width:80%;" class="line" onkeydown="chkLength(this);" onkeyup="chkLength(this);" onchange="chkLength(this);" <?=$disabled?> />
	<span id="vLength">0</span>/80 Bytes
	</div>
	</td>
</tr>
<?
	}
	if($_GET['mode'] != "reply"){
?>
<tr>
	<td>지급된 적립금</td>
	<td><font class="ver8" color="EF6D00"><span style="margin-right:10;"><?=number_format($data['emoney'])?> 원</span></td>
</tr>
<?
	}
?>
<tr>
	<td>제목</td>
	<td><input type="text" name="subject" value="<?=$data['subject']?>" style="width:90%;" required class=line /></td>
</tr>
<tr>
	<td><?=( $_GET['mode'] == "modify" ? '후기' : '답변' )?></td>
	<td><textarea name="contents" cols="60" rows="9" style="width:90%;" class=tline><?=$data['contents']?></textarea></td>
</tr>
</table>

<div class="button_popup">
<input type="image" src="../img/btn_confirm_s.gif" />
<a href="javascript:parent.closeLayer();"><img src="../img/btn_cancel_s.gif" /></a>
</div>

</form>

<script>table_design_load();</script>