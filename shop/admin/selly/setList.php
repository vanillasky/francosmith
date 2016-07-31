<?
/*********************************************************
* 파일명     :  setList.php
* 프로그램명 :  세트리스트
* 작성자     :  이훈
* 생성일     :  2012.05.08
**********************************************************/
/*********************************************************
* 수정일     :  
* 수정내용   :  
**********************************************************/
$location = "셀리 > 세트관리";
include "../_header.php";
include "../../lib/sAPI.class.php";

list($cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
list($cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");

if(!$cust_seq || !$cust_seq) {
	msg("셀리를 신청하고 상점 인증 등록 후에 사용가능한 서비스입니다.");
	go("./setting.php");
}

$sAPI = new sAPI();

$grp_cd = Array('grp_cd'=>'MALL_CD');
$arr_mall_cd = $sAPI->getCode($grp_cd, 'hash');

$search_data = Array('search_data' => $_GET['set_data']);
$tmp_mall_set = $sAPI->getSetList($search_data);

$tmp_mall_info = $sAPI->getLoginId();
foreach($tmp_mall_info as $row_mall_info) {
	$arr_mall_info[$row_mall_info['mall_cd']][] = $row_mall_info['mall_login_id'];
}

$now_page = $_GET['page'];
if(!$now_page) $now_page = 1;

$arr_mall_set = array();
$set_total = 0;
if(is_array($tmp_mall_set)) {
	foreach($tmp_mall_set as $row_mall_set) {
		$set_total++;
		$arr_mall_set[$row_mall_set['mall_cd']][$row_mall_set['mall_login_in']][] = $row_mall_set;
	}
}
$total_page = ceil($set_total / 10);

for($i = 1; $i <= $total_page; $i++) {
	for($j = 0; $j < 10; $j++) {
		if(!$tmp_mall_set[$j]) break;
		$page_data[$i][] = $tmp_mall_set[$j];
		unset($tmp_mall_set[$j]);
	}
	$tmp_mall_set = array_values($tmp_mall_set);
}

$get_data = 'set_data='.$_GET['set_data'];
$page_navi = $sAPI->exec_page($set_total, $now_page, $get_data);

$selected['set_data'][$_GET['set_data']] = 'selected';

$cust_cd_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_cd');
$res_cust_cd = $db->_select($cust_cd_query);
$cust_cd = $res_cust_cd[0]['value'];

$cust_seq_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_seq');
$res_cust_seq = $db->_select($cust_seq_query);
$cust_seq = $res_cust_seq[0]['value'];

unset($cust_cd_query, $res_cust_cd, $cust_seq_query, $res_cust_seq);

$seq = $sAPI->xcryptare($cust_seq, $cust_cd, true);

?>

<script src="js/selly.js"></script>

<script>

function set_insert() {//세트등록 팝업
	if(!document.getElementsByName('set_data')[0].value) {
		alert('마켓 ID를 선택해 주세요.');
		return;
	}

	popup_return('_blank.php', 'set_pop', 915, 520, '', '', 0);
	var fm = document.frmList;
		fm.target = "set_pop";
		fm.action = "setInfoPop.php";
		fm.submit();
}

function search() {
	var fm = document.frmList;
		fm.target = "_self";
		fm.action = "setList.php";
		fm.submit();
}

function set_modi_copy(mall_cd, mall_login_id, set_cd, mode) {//세트 수정, 복사 팝업
	var ret_url = "/linkgoods/STSetInfoShop.gm?mall_cd="+mall_cd+"&mall_login_id="+mall_login_id+"&seq=<?=base64_encode($seq)?>&set_cd="+set_cd+"&mode="+mode;
	//popup2('setInfoPop.php?mode=modify&set_data=' + mall_cd +'|' + mall_login_id + '|' + set_cd +'&mode=' + mode, 915, 520, 'no');
	sellyPop(ret_url);
}

function set_del(set_cd) {//세트삭제
	alert('세트를 삭제할 경우 해당 세트로 링크된\n상품의 관리가 더이상 불가능 합니다.');
	if (!confirm("세트를 삭제하시겠습니까?")) return;

	sellyLink.setDelete(set_cd);
}

function successAjax(data) {
	var json_data = eval( '(' + data + ')' );

	if(json_data['code'] == '000') {//세트 삭제성공
		alert(json_data['msg']);
		location.reload();
		return;
	}
	else {
		alert(json_data['msg']);
		return;
	}
}

</script>

<form name="frmList" action="<?=$_SERVER['PHP_SELF']?>">
	<div class="title title_top">세트관리<span>마켓 ID별 상품등록 기본정보를 등록 및 관리하는 기능입니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=11')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

	<table class="tb">
		<col class="cellC"><col class="cellL" style="width:500px">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>마켓 ID선택</td>
			<td colspan="3">
				<select name="set_data">
					<option value="">== 아이디를 선택해 주세요. ==</option>
				<? foreach($arr_mall_cd as $mall_cd => $mall_nm) { ?>
					<? if($mall_cd == 'mall0005') continue; ?>
					<? foreach($arr_mall_info[$mall_cd] as $data) { ?>
						<option value="<?=$mall_cd?>|<?=$data?>" <?=$selected['set_data'][$mall_cd.'|'.$data]?>><?=$mall_nm?> - <?=$data?></option>
					<? } ?>
				<? }?>
				</select>
				<span class="noline"><input type="image" src="../img/btn_addset.gif" align="absbottom" alt="선택한 아이디로 세트등록" onclick="set_insert();return false;"></span>
			</td>
		</tr>
	</table>
	<div class="button_top"><input type="image" src="../img/btn_search2.gif" onclick="search();"></div>
	<div style="padding-top:15px"></div>
</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<col style="width:5%"><col style="width:10%"><col style="width:10%"><col style="width:61%"><col style="width:7%"><col style="width:7%">
	<tr><td class="rnd" colspan="6"></td></tr>
	<tr class="rndbg">
		<th>No.</th>
		<th>마켓</th>
		<th>마켓ID</th>
		<th>세트명</th>
		<th>복사</th>
		<th>삭제</th>
	</tr>
	<tr><td class="rnd" colspan="6"></td></tr>
	<?
	if(is_array($page_data[$now_page])) {
		foreach($page_data[$now_page] as $key => $data) {
		?>
		<tr><td height="4" colspan="6"></td></tr>
		<tr>
			<td align="center" class="noline"><!--No.-->
				<?=(($now_page-1)*10) + ($key+1)?>
			</td>
			<td align="center" class="noline"><!--마켓-->
				<?=$arr_mall_cd[$data['mall_cd']]?>
			</td>
			<td align="center" class="noline"><!--마켓ID-->
				<?=$data['mall_login_id']?>
			</td>
			<td><!--세트명-->
				<a href="javascript:set_modi_copy('<?=$data['mall_cd']?>', '<?=$data['mall_login_id']?>', '<?=$data['set_cd']?>', 'modify_shop')"><font color="303030"><?=$data['set_nm']?></font></a>
			</td>
			<td align="center"><!--복사-->
				<input type="image" src="../img/i_copy.gif" alt="복사" onclick="set_modi_copy('<?=$data['mall_cd']?>', '<?=$data['mall_login_id']?>', '<?=$data['set_cd']?>', 'copy_shop');">
			</td>
			<td align="center"><!--삭제-->
				<input type="image" src="../img/i_del.gif" alt="삭제" onclick="set_del('<?=$data['set_cd']?>');">
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=6 class=rndline></td></tr>
		<? } ?>
	<? } ?>
</table>
<div align="center" class="pageNavi"><font class="ver8"><?=$page_navi?></font></div>

<form name="delSet">
	<input type="hidden" name="set_cd" value="">
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
등록한 세트를 확인, 수정 하실 수 있습니다. 복사 기능을 이용하여 기존에 등록된 세트를 복사 등록 하실 수 있습니다.<br/><br/><br/>

세트 등록은 마켓 ID선택을 하신 후 선택한 아이디로 세트등록 버튼을 눌러주시면 됩니다.<br/>
세트 수정은 세트명을 클릭하여 띄워지는 팝업에서 세트를 수정하실 수 있습니다.<br/>
세트 복사는 복사버튼을 클릭하여 띄워지는 팝업에서 가능합니다.<br/>
세트 삭제는 삭제 버튼을 클릭하여 세트를 삭제하실 수 있으며<br/>
링크시 사용된적이 있는 세트를 삭제하실 경우 해당 링크상품을 더 이상 관리하실 수 없게 됩니다.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>