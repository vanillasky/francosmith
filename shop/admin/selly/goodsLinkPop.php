<?
/*********************************************************
* 파일명     :  goodsLinkPop.php
* 프로그램명 :  링크팝업
* 작성자     :  이훈
* 생성일     :  2012.05.08
**********************************************************/
/*********************************************************
* 수정일     :  
* 수정내용   :  
**********************************************************/
$location = "셀리 > 링크팝업";
include "../_header.popup.php";
include "../../lib/sAPI.class.php";

$post_data = $_POST;

if(empty($post_data['chk'])) {//선택한 상품이 없을 경우 팝업 닫기
	echo '<script>alert("선택한 상품이 없습니다.");self.close();</script>';
}

if($_POST['mode']) {//modify = 수정링크, status = 상태변경
	$mode = $_POST['mode'];
	unset($_POST['mode']);
}

$sAPI = new sAPI();

### 마켓정보 api START ###
$grp_cd = Array("grp_cd"=>"MALL_CD");
$arr_mall_cd = $sAPI->getcode($grp_cd, 'hash');
### 마켓정보 api END ###

### 세트정보 api START ###
$set_arr = array();
$set_arr['set_cd'] = $post_data['set_cd'];
$set_data = $sAPI->getSetList($set_arr);
$set_data = $set_data[0];
$mall_cd = $set_data['mall_cd'];
### 세트정보 api END ###

### 모드별 글자설정 START ###
if($mode == 'modify') {//수정링크
	$title_nm = '상품수정 링크 진행';
	$chk_nm = 'glink_idx';
}
else if($mode == 'status') {//상태변경
	$title_nm = '상품상태 변경';
	$chk_nm = 'glink_idx';
}
else if($mode == 'extend') {//기간연장
	$title_nm = '상품기간 연장';
	$chk_nm = 'glink_idx';
	$mall_cd = $_POST['mall_cd'];
}
else {//상품링크
	$title_nm = '상품 링크 진행';
	$chk_nm = 'goods_no';
}
### 모드별 글자설정 END ###

$arr_delivery_type = Array(
	"고정 배송비" => "선결제만 가능",
	"착불 배송비" => "착불만 가능",
	"선불" => "선결제만 가능",
	"착불" => "착불만 가능",
	"무료" => "무료",
	"수량별 배송비" => "",
);
?>

<script src="js/selly.js"></script>

<script>
var mode = document.getElementsByName('mode');//modify = 수정링크, status = 상태변경

var link_no = 0;
function successAjax(data) {//링크 리턴 후 처리
	var json_data = eval( '(' + data + ')' );

	if(json_data['code'] == '000') {//링크상태별 처리성공

		if(json_data['mode'] && ((json_data['mode'] == 'modify') || (json_data['mode'] == 'status') || (json_data['mode'] == 'extend'))) {//수정링크 or 상태변경
	
			var resMsg = '<span class="small" style="color:#0033FF;">' + json_data['msg'] + '</span>';
			var result_idx = json_data['glink_idx'];
		}
		else {//상품링크
					
			var resMsg = '<span class="small" style="color:#0033FF;"><div>링크성공</div><div>마켓 상품코드 : ' + json_data['mall_goods_cd'] + '</div></span>';
			var result_idx = json_data['goods_no'];
		}
	}
	else if(json_data['code'] && json_data['code'] != '000') {//상품링크실패시 메세지 출력
		if(json_data['msg'] == '' || json_data['msg'] == null || json_data['msg'] == 'null') {
			json_data['msg'] = 'return 값이 없습니다.';
		}

		if(json_data['mode'] && ((json_data['mode'] == 'modify') || (json_data['mode'] == 'status') || (json_data['mode'] == 'extend'))) {//수정링크 or 상태변경
			var result_idx = json_data['glink_idx'];
		}
		else {//상품링크
			var result_idx = json_data['goods_no'];
		}
		var resMsg = '<span class="small" style="color:#CD0000;"><div>링크실패</div><div>실패 메세지 : ' + json_data['msg'] + '</div></span>';
	}
	else {//마켓 카테고리 리턴값으로 옵션생성
		var obj = document.getElementsByName('mall_cate[]');
		createOption(obj, data);
		return;
	}

	document.getElementById('logBoard' + result_idx).style.color = '#0033FF';//진행상태
	document.getElementById('logBoard' + result_idx).innerHTML = '완료';//진행상태
	document.getElementById('resBoard' + result_idx).innerHTML = resMsg;//링크결과
	link_no++;

	if(json_data['mode'] && ((json_data['mode'] == 'modify') || (json_data['mode'] == 'status') || (json_data['mode'] == 'extend'))) {//수정링크 or 상태변경
		linkAjax(json_data['mode']);
	}
	else {//상품링크
		linkAjax();
	}
}

function linkAjax(mode) {//링크ajax 호출
	var param = new Array();
	var pro_idx = link_no;

	if(!mode) var obj = document.getElementsByName('goods_no' + pro_idx);
	else var obj = document.getElementsByName('glink_idx' + pro_idx);

	if(obj.length == 0) {//링크종료
		document.getElementsByName('link_complete_check')[0].value = 'N';

		if(!mode) {//상품링크 완료
			//카테고리 disabled 해제
			var obj = document.getElementsByName('mall_cate[]');
			for(var i = 0; i < obj.length; i++) {
				document.getElementsByName('mall_cate[]')[i].disabled = false;
			}

			document.getElementById('link_page_btn').src = '../img/btn_linkbaro.gif';//상품링크 활성화 버튼 이미지
			document.getElementById('link_goods_btn').src = '../img/btn_linkpro.gif';//상품링크 활성화 버튼 이미지
			return;
		}
		else {//수정링크/상태변경 완료
			document.getElementById('link_pop_close').src = '../img/btn_delinum_close.gif';//닫기 활성화 버튼 이미지
		}
		return;
	}

	if(mode == 'modify') {//상품수정링크
		var glink_idx = obj[0].value;
		var price = document.getElementsByName('price' + glink_idx)[0].value;
		var delivery_price = document.getElementsByName('delivery_price' + glink_idx)[0].value;
		document.getElementById('logBoard' + glink_idx).innerHTML = '<span class="small" style="color:#228B22;">진행중.......</span>';
		sellyLink.linkModifyGoods(glink_idx, price, delivery_price);
	}
	else if(mode == 'status') {//상품상태변경
		var glink_idx = obj[0].value;
		document.getElementById('logBoard' + glink_idx).innerHTML = '<span class="small" style="color:#228B22;">진행중.......</span>';
		var sale_status = document.getElementsByName('sale_status')[0].value;
		sellyLink.linkGoodsStatus(glink_idx, sale_status);
	}
	else if(mode == 'extend') {//상품판매기간 연장
		var glink_idx = obj[0].value;
		document.getElementById('logBoard' + glink_idx).innerHTML = '<span class="small" style="color:#228B22;">진행중.......</span>';
		var extend_term = document.getElementsByName('extend_term')[0].value;
		var extend_set = document.getElementsByName('extend_set')[0].value;
		var sale_term_start = document.getElementsByName('sale_term_start')[0].value;
		var sale_term_end = document.getElementsByName('sale_term_end')[0].value;
		var mall_cd = document.getElementsByName('mall_cd')[0].value;

		sellyLink.linkGoodsExtend(glink_idx, extend_term, extend_set, sale_term_start, sale_term_end, mall_cd);

	}
	else {//상품링크
		var mall_cd = document.getElementsByName('mall_cd')[0].value;
		var set_cd = document.getElementsByName('set_cd')[0].value;
		var mall_login_id = document.getElementsByName('mall_login_id')[0].value;
		var mall_category_cd = document.getElementsByName('mall_category_cd')[0].value;
		var mall_category_nm = document.getElementsByName('mall_category_nm')[0].value;
		var goods_no = obj[0].value;
		var price = document.getElementsByName('price' + goods_no)[0].value;
		var delivery_price = document.getElementsByName('delivery_price' + goods_no)[0].value;
		document.getElementById('logBoard' + goods_no).innerHTML = '<span class="small" style="color:#228B22;">진행중.......</span>';

		sellyLink.linkGoods(mall_cd, set_cd, mall_login_id, mall_category_cd, mall_category_nm, goods_no, price, delivery_price);
	}
}

function goodsLink() {//상품링크
	var link_check = document.getElementsByName('link_check')[0].value;
	if(link_check == 'N') {//링크최초 시도시
		if(!cateSelectCheck()) {//카테고리 체크/hidden입력
			alert('카테고리를 선택해 주세요.');
			return;
		}
		if(!cateCheck()) {//카테고리체크
			alert('마지막 카테고리까지 선택해 주세요.');
			return;
		}

		//카테고리 disabled 설정
		var obj = document.getElementsByName('mall_cate[]');
		for(var i = 0; i < obj.length; i++) {
			document.getElementsByName('mall_cate[]')[i].disabled = true;
		}

		document.getElementById('link_btn').src = '../img/btn_link_out.gif';//상품링크 비활성화 버튼 이미지
		document.getElementById('link_page_btn').src = '../img/btn_linkbaro_out.gif';//상품링크바로가기 비활성화 버튼 이미지
		document.getElementById('link_goods_btn').src = '../img/btn_linkpro_out.gif';//링크상품관리바로가기 비활성화 버튼 이미지
		document.getElementsByName('link_check')[0].value = 'Y';//링크중/링크완료
		document.getElementsByName('link_complete_check')[0].value = 'P';//진행중
	}
	else return;//링크했을경우 return

	linkAjax();
}

function cateCheck() {//마지막 카테고리 선택 체크

	var mall_cd = document.getElementsByName('mall_cd')[0].value;

	var form = document.linkInfo;
	var last_cate = form.last_cate.value;
	if(last_cate == 'N') return false;
	else return true;
}

function cateSelectCheck() {//상품링크시 카테고리 선택체크/hidden입력
	var cate_obj = document.getElementsByName('mall_cate[]');
	var category_cd = '';
	var category_nm = '';
	var mall_cd = document.getElementsByName('mall_cd')[0].value;

	for(var i = 0; i < cate_obj.length; i++) {
		if(cate_obj[i].value) {
			category_cd += cate_obj[i].value + '>';
			category_nm += cate_obj[i].options[cate_obj[i].selectedIndex].text + ' > ';
		}
	}

	if(category_cd && category_nm) {
		document.getElementsByName('mall_category_cd')[0].value = category_cd;
		document.getElementsByName('mall_category_nm')[0].value = category_nm;
		return true;
	}
	else {
		return false;
	}
}

function cateSelect(obj, category_type) {//카테고리 선택시 다음 카테고리 불러오기
	var elements = document.getElementsByName(obj.name);
	for(var i = 0; i < elements.length; i++) {
		if(elements[i] == obj) {
			var idx = i+1;
		}
	}

	var tmp_obj = document.getElementsByName('mall_cate[]');
	var mall_cd = document.getElementsByName('mall_cd')[0].value;
	var mall_login_id = document.getElementsByName('mall_login_id')[0].value;
	var category_cd = obj.value;
	var last_cate = document.getElementsByName('last_cate')[0];
	sellyLink.ajaxMallCategory(tmp_obj, mall_cd, mall_login_id, category_type, category_cd);
}

function page_move(mode) {//link_page = 상품링크바로가기, link_goods = 링크상품관리바로가기
	var check = document.getElementsByName('link_complete_check')[0].value;
	if(check == 'P') return;//링크중일때 페이지 이동 막기(P = 링크진행중, N = 링크진행전/완료후)

	if(mode == 'link_page') {//상품링크 바로가기
		opener.parent.location.replace("goodsLink.php");
	}
	else if( mode == 'link_goods') {//링크상품관리 바로가기
		opener.parent.location.replace("linkGoodsList.php");
	}
	else {
		opener.parent.location.reload();
	}
	self.close();
}

function goodsModifyLink() {
	document.getElementById('link_pop_close').src = '../img/btn_delinum_close.gif';//닫기 비활성화 버튼 이미지
	document.getElementsByName('link_complete_check')[0].value = 'P';//진행중

	linkAjax(mode[0].value);
}

window.onload = function(){
	if(mode[0].value) {//상품수정링크, 상태변경, 기간연장
		goodsModifyLink();

	}
	else {//상품링크
		//카테고리 로딩 START
		var obj = document.getElementsByName('mall_cate[]');
		var mall_cd = document.getElementsByName('mall_cd')[0].value;
		var mall_login_id = document.getElementsByName('mall_login_id')[0].value;
		var category_type = 'L';
		var last_cate = document.getElementsByName('last_cate')[0];
		sellyLink.ajaxMallCategory(obj, mall_cd, mall_login_id, category_type, '');
		//카테고리 로딩 END

	}
	table_design_load();
}
</script>

<form name="linkInfo">
	<input type="hidden" id="mall_cd" name="mall_cd" value="<?=$mall_cd?>" />
	<input type="hidden" name="mall_login_id" value="<?=$set_data['mall_login_id']?>" />
	<input type="hidden" name="set_cd" value="<?=$set_data['set_cd']?>" />
	<input type="hidden" name="mall_category_cd" value=""><!--마켓 카테고리-->
	<input type="hidden" name="mall_category_nm" value=""><!--마켓 카테고리-->
	<input type="hidden" name="last_cate" value="N"><!--마지막 카테고리여부 -->
	<input type="hidden" name="link_check" value="N"><!-- 링크버튼 활성화여부(N = 활성화, Y = 비활성화 -->
	<input type="hidden" name="link_complete_check" value="N"><!-- 링크완료 여부(N = 진행전/진행완료, P = 진행중 -->
	<input type="hidden" name="mode" value="<?=$mode?>"><!-- '' = 상품링크, modify = 수정, status = 상태변경 -->
	<input type="hidden" name="sale_status" value="<?=$_POST['sale_status']?>"><!-- 상품상태변경시 변경할 상태값 -->
	<input type="hidden" name="extend_term" value="<?=$_POST['extend_term']?>"><!-- 기간연장(기간설정값 - 코드값) -->
	<input type="hidden" name="extend_set" value="<?=$_POST['extend_set']?>"><!-- 기간연장(기간설정여부) -->
	<input type="hidden" name="sale_term_start" value="<?=$_POST['sale_term_start']?>"><!-- 기간연장(기간설정값 - 판매시작일) -->
	<input type="hidden" name="sale_term_end" value="<?=$_POST['sale_term_end']?>"><!-- 기간연장(기간설정값 - 판매종료일) -->

	<div class="title title_top"><?=$title_nm?><span>링크 진행중입니다. 완료 전에 창을 닫거나 esc버튼을 누르시면 링크가 중단됩니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=2')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
	<? if(!$mode) { //상품링크 ?>
	<table class="tb">
		<col class="cellC" style="wiidth:100px"><col class="cellC"  style="width:100px"><col class="cellL">
		<tr>
			<td rowspan="3">세트정보</td>
			<td bgcolor="F8F8F8">마켓</td>
			<td><?=$arr_mall_cd[$mall_cd]?></td>
		</tr>
		<tr>
			<td>마켓 ID</td>
			<td><?=$set_data['mall_login_id']?></td>
		</tr>
		<tr>
			<td>세트명</td>
			<td><?=$set_data['set_nm']?></td>
		</tr>
		<tr>
			<td>카테고리정보</td>
			<td>카테고리선택</td>
			<td>
				<select name="mall_cate[]" onchange="cateSelect(this, 'M');">
				</select>
				<select name="mall_cate[]" onchange="cateSelect(this, 'S');">
				</select>
				<select name="mall_cate[]" onchange="cateSelect(this, 'D');">
				</select>
				<select name="mall_cate[]" onchange="cateSelect(this, '');">
				</select>
			</td>
		</tr>
	</table>
	<? } ?>
</form>

<div style="padding-top:15px"></div>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<col width="60%"><col width="20%" align="center"><col width="20%" align="center">
	<tr class="rndbg">
		<th>상품명</th>
		<th>진행상태</th>
		<th>링크 결과</th>
	</tr>
	<? for($i = 0; $i < count($post_data['chk']); $i++) {?>
	<tr height="40">
		<td style="padding:6px;">
			<?=$post_data['goodsnm'][$post_data['chk'][$i]]?>
			<input type="hidden" name="price<?=$post_data['chk'][$i]?>" value="<?=$post_data['price'][$post_data['chk'][$i]]?>" /><!--상품명-->
			<input type="hidden" id="delivery_price<?=$post_data['chk'][$i]?>" name="delivery_price<?=$post_data['chk'][$i]?>" value="<?=$post_data['goods_delivery'][$post_data['chk'][$i]]?>" /><!--배송비-->
			<input type="hidden" name="<?=$chk_nm?><?=$i?>" value="<?=$post_data['chk'][$i]?>" /><!--고유번호(상품코드)-->
		</td>
		<td id="process_<?=$post_data['chk'][$i]?>">
			<font id="logBoard<?=$post_data['chk'][$i]?>" class="small1" color="#AAAAAA">대기</font>
		</td>
		<td id="link_<?=$post_data['chk'][$i]?>">
			<span id="resBoard<?=$post_data['chk'][$i]?>"></span>
		</td>
	</tr>
	<tr><td colspan="3" class="rndline"></td></tr>
	<? } ?>
</table>

<div align="right" style="margin:30px 20px 0px 0px;">
	<? if(!$mode) { //상품링크 ?>
	<span style="margin-right:10px;"><input id="link_btn" type="image" src="../img/btn_link_on.gif" align="absbottom" alt="링크하기" onclick="goodsLink();"></span><!--링크하기-->
	<span style="margin-right:10px;"><input id="link_page_btn" type="image" src="../img/btn_linkbaro.gif" align="absbottom" alt="상품링크 바로가기" onclick="page_move('link_page');"></span><!--상품링크 바로가기-->
	<input id="link_goods_btn" type="image" src="../img/btn_linkpro.gif" align="absbottom" alt="링크상품관리 바로가기" onclick="page_move('link_goods');"><!--링크상품관리 바로가기-->
	<? } else { //수정링크/상태변경 ?>
	<input id="link_pop_close" type="image" src="../img/btn_delinum_close.gif" align="absbottom" alt="닫기" onclick="page_move();"><!--닫기-->
	<? } ?>
</div>