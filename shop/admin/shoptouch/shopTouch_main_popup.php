<?php
$location = "쇼핑몰 App관리 > 메인 팝업 관리";
include "../_header.php";
@include_once "../../lib/pAPI.class.php";
@include_once "../../lib/json.class.php";
$pAPI = new pAPI();
$json = new Services_JSON(16);

$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('서비스 신청후에 사용가능한 메뉴입니다.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('서비스 사용기간 만료후 30일이 지나 서비스가 삭제 되었습니다.\n서비스를 다시 신청해 주시기 바랍니다.', -1);
}

$query = $db->_query_print('SELECT * FROM '.GD_SHOPTOUCH_DISPLAY.' WHERE mode=[s] ORDER BY sort ASC, no ASC', 'popup');

$rows_display = $db->_select($query);

$arr_link_type = array(1 => '분류');
$arr_link_type[] = '상품';
$arr_link_type[] = 'URL';

?>
<script type="text/javascript">
function addDisplay() {
	popupLayer('popup.display_popup.php', 600, 630);
}

function editDisplay(no) {
	popupLayer('popup.display_popup.php?no=' + no, 600, 630);
}

function delDisplay(no) {
	if(confirm("팝업진열을 삭제 하시겠습니까?")) {
		var frm = del_form;
		frm.no.value = no;

		frm.submit();
	}
}
function checkImage(goodsno, img_nm, img_type, mode) {
	var params = '';
	var frm = document.form;

	params = '?mode='+mode;

	if(img_type == 1) {
		params += '&img_nm='+img_nm;
	}
	else{
		if(goodsno == '') {
			goodsno = frm.link_path.value;
			if(goodsno == '') {
				alert("팝업을 선택해 주세요");
				return;
			}
		}
		params += '&goodsno='+goodsno;
	}

	window.open('popup.main_image.php' + params, '이미지확인', 'width=300,height=400, menubar=no, status=no');
}

function chk_display(obj) {

	var chk_ele = document.getElementsByName('use_display[]');

	var chk_cnt = 0;
	for(var i=0; i<chk_ele.length; i++) {
		if(chk_ele[i].checked) {
			chk_cnt++;
		}
	}

	if(chk_cnt > 1) {
		alert('메인팝업 설정은 하나만 가능 합니다.');
		obj.checked = false;
	}
}
</script>
<?
if($expire_dt < $now_date) {
	@include('shopTouch_expire_msg.php');
}
?>
<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="main_popup_use">

<div class="title title_top">메인 팝업 관리 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=16')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=8></td></tr>
<tr class="rndbg">
	<th width="50" align="center">선택</th>
	<th width="70" align="center">유형</th>
	<th width="200" align="center">이동경로</th>
	<th width="150" align="center">팝업명</th>
	<th width="100" align="center">메인이미지</th>
	<th width="50" align="center">수정</th>
	<th width="50" align="center">삭제</th>
</tr>
<tr><td class="rnd" colspan="8"></td></tr>
<tr><td height=4 colspan=8></td></tr>
<tr height=25>
	<td width="50" align="center" class="noline">
		<input type="hidden" name="no[]" value="0" />
		<input type="radio" name="use_display" value="0" checked />
	</td>
	<td align="left">사용안함</td>
	<td align="center">--</td>
	<td align="center">--</td>
	<td align="center">--</td>
	<td align="center">--</td>
	<td align="center">--</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=7 class=rndline></td></tr>
<?
if(!empty($rows_display)) {
	$no = 1;
	foreach($rows_display as $row) {
		$no ++;

		if($row['link_type'] == '1') {
			$tmp_data = $pAPI->getMainMenuItem($godo['sno'], $row['category']);
			$cate_data = $json->decode($tmp_data);

			if(!$cate_data['name']) {
				$row['link_path'] = '삭제된 카테고리';
			}
			else {
				$row['link_path'] = $cate_data['name'].'('.$row['category'].')';
			}
		}
		else if($row['link_type'] == '2') {
			$field = 'g.goodsnm, sg.img_shoptouch';
			$table = GD_GOODS.' g LEFT JOIN '.GD_SHOPTOUCH_GOODS.' sg ON g.goodsno=sg.goodsno';
			$where = $db->_query_print('g.goodsno = [i]', $row['goodsno']);

			$row_query = $db->_query_print('SELECT '.$field.' FROM '.$table.' WHERE '.$where);
			$row_result = $db->_select($row_query);
			$row_result = $row_result[0];

			if(!$row_result['goodsnm']) {
				$row['link_path'] = '삭제된 상품';
			}
			else {
				$row['link_path'] = $row_result['goodsnm'].'('.$row['goodsno'].')';
			}

			//$tmp_img = explode('|', $row['img_shoptouch']);
			//$row['main_img'] = $tmp_img[0];
		}
		else {
			$row['link_path'] = $row['link_url'];
		}

		$checked = '';
		if($row['use_display'] == '1') $checked = 'checked';
?>
<tr><td height=4 colspan=7></td></tr>
<tr height=25>
	<td width="50" align="center" class="noline">
		<input type="hidden" name="no[]" value="<?=$row['no']?>" />
		<input type="radio" name="use_display" value="<?=$row['no']?>" <?=$checked?> />
	</td>
	<td width="70" align="left"><?=$arr_link_type[$row['link_type']]?></td>
	<td width="200" align="left"><?=$row['link_path']?></td>
	<td width="150" align="center"><?=$row['popup_nm']?></td>
	<td width="100" align="center">
	<? if($row['image_up'] == '1') { ?>
		<a href="javascript:popupImg('<?='../data/shoptouch/popup/'.$row['main_img']?>', '../');"><?=$row['main_img']?></a>
	<? } else { ?>
		<a href="javascript:popupImg('<?=$row['main_img']?>', '../');">상품원본이미지</a>
	<? } ?>
	</td>
	<td width="50" align="center"><a href="javascript:editDisplay('<?=$row['no']?>');"><img src="../img/i_edit.gif"></a></td>
	<td width="50" align="center"><a href="javascript:delDisplay('<?=$row['no']?>');"><img src="../img/i_del.gif"></a></td>

</tr>
<tr><td height=4></td></tr>
<tr><td colspan=7 class=rndline></td></tr>
<?
	}
}
?>
</table>
<div style="height:10px;"></div>
<div style="width:100%;">
	<div style="width:100%;position:absolute;text-align:left;">
		<a href="javascript:addDisplay();"><img src="../img/btn_popup_ad.gif" alt="팝업 이미지추가"></a>
	</div>
</div>
<div style="height:10px;"></div>
<div class="button">
<input type=image src="../img/btn_modify.gif">
</div>
</form>
<form name="del_form" method="post" action="indb.php" enctype="multipart/form-data">
<input type="hidden" name="mode" value="del_main_popup" />
<input type="hidden" name="no" value="" />
</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">쇼핑몰 App의 메인화면에서 처음에 보여질 팝업을 설정하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">지정된 카테고리 이동 / 지정된 상품 이동 / 지정된 URL 이동 등 세가지를 설정 하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">팝업은 이미지로만 구성되며, 이미지 권장 사이즈는 400px * 450px 입니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>