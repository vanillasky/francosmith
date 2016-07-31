<?
/*********************************************************
* 파일명     :  mallList.php
* 프로그램명 :  마켓 리스트
* 작성자     :  이훈
* 생성일     :  2012.05.08
**********************************************************/
/*********************************************************
* 수정일     :  
* 수정내용   :  
**********************************************************/
$location = "셀리 > 마켓관리";
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
$ret_code = $sAPI->getCode($grp_cd, '');
$return_data = $ret_code['data'][0]['child']['return'][0]['child']['item'];

foreach($return_data as $data) {
	$tmp_data = $data['child'];
	$arr_mall_cd[$tmp_data['com_cd'][0]['data']]['mall_nm'] = $tmp_data['com_nm'][0]['data'];
	$arr_mall_cd[$tmp_data['com_cd'][0]['data']]['temp'] = $tmp_data['temp'][0]['data'];
}

$pagenum = $_GET['page'];

if(!$perpage) $perpage = '10';
if(!$pagenum) $pagenum = '1';

$mall_list_data['perpage'] = $perpage;
$mall_list_data['pagenum'] = $pagenum;
$arr_mall_list = $sAPI->getMallList($mall_list_data, 'hash');

if(!$totalcount) $totalcount = $arr_mall_list[0]['totalcount'];
if(!$nowpage) $nowpage = $arr_mall_list[0]['nowpage'];

$page_navi = $sAPI->exec_page($totalcount, $nowpage, '');

$use_yn = Array(
	'Y' => '사용',
	'N' => '미사용'
);

$domain_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'domain');
$res_domain = $db->_select($domain_query);
$domain = $res_domain[0]['value'];

$cust_seq_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_seq');
$cust_seq_res = $db->_select($cust_seq_query);
$cust_seq = $cust_seq_res[0]['value'];

$cust_cd_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_cd');
$cust_cd_seq = $db->_select($cust_cd_query);
$cust_cd = $cust_cd_seq[0]['value'];

$seq = base64_encode($sAPI->xcryptare($cust_seq, $cust_cd, true));

?>

<script src="js/selly.js"></script>

<script>

function mall_modify(minfo_idx) {//마켓 수정팝업
	popup('mallInfo.php?minfo_idx=' + minfo_idx, 600, 300);
}

function mall_del(minfo_idx) {
	alert('등록된 마켓을 삭제하시면 링크작업을 하실 수 없습니다.');
	if (!confirm("해당 마켓정보를 삭제하시겠습니까?")) return;
	sellyLink.insMall('', '', '', '', '', 'delete', minfo_idx);
}

function successAjax(data) {
	var json_data = eval( '(' + data + ')' );

	if(json_data['code'] == '000') {//마켓 삭제성공
		alert(json_data['msg']);
		location.reload();
		return;
	}
	else {
		alert(json_data['msg']);
		return;
	}
}

function mall_register() {//마켓 등록페이지 이동
	location.replace("mallInfo.php");
}

function scm_login(minfo_idx) {
	if(minfo_idx == 'none') {
		alert('SCM로그인 기능이 지원되지 않는 마켓 입니다.');
		return;
	}

	document.getElementsByName('minfo_idx')[0].value = minfo_idx;

	var fm = document.mallInfo;
	fm.target = "_blank";
	fm.method = "POST";
	fm.action = "http://<?=$domain?>/basic/STMallLoginTestShop.gm";
	fm.submit();
}

</script>

<div class="title title_top">마켓리스트<span>SELLY에 등록된 마켓을 관리하실 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=6')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<div style="padding-top:15px"></div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<col style="width:5%"><col style="width:12%"><col style="width:16%"><col style="width:9%"><col style="width:19%"><col style="width:19%"><col style="width:10%"><col style="width:10%">
	<tr><td class="rnd" colspan="10"></td></tr>
	<tr class="rndbg">
		<th>No.</th>
		<th>마켓</th>
		<th>마켓 로그인ID</th>
		<th>사용여부</th>
		<th>최종주문수집일</th>
		<th>최종상품링크일</th>
		<th>수정/삭제</th>
		<th>SCM로그인</th>
	</tr>
	<tr><td class="rnd" colspan="10"></td></tr>
	<?
	if($arr_mall_list['code'] != '990' && is_array($arr_mall_list)) {
		$minus = 0;
		foreach($arr_mall_list as $key => $data) {
			if($data['mall_cd'] == 'mall0005') {
				$minus++;
				continue;
			}
		?>
		<tr><td height="4" colspan="10"></td></tr>
		<tr>
			<td align="center" class="noline"><!--No.-->
				<?=(($data['nowpage']-1)*10) + ($key+1-$minus)?>
			</td>
			<td align="center" class="noline"><!--마켓-->
				<?=$arr_mall_cd[$data['mall_cd']]['mall_nm']?>
			</td>
			<td align="center" class="noline"><!--마켓 로그인ID-->
				<?=$data['mall_login_id']?>
			</td>
			<td align="center" class="noline"><!--사용여부-->
				<?=$use_yn[$data['status']]?>
			</td>
			<td align="center"><!--최종주문수집일-->
				<?=$data['last_order_date']?>
			</td>
			<td align="center"><!--최종상품링크일-->
				<?=$data['last_link_date']?>
			</td>
			<td align="center"><!--수정/삭제-->
				<input type="image" src="../img/i_edit.gif" alt="수정" onclick="mall_modify('<?=$data['minfo_idx']?>');">
				<input type="image" src="../img/i_del.gif" alt="삭제" onclick="mall_del('<?=$data['minfo_idx']?>');">
			</td>
			<td align="center"><!--SCM로그인-->
				<? if($arr_mall_cd[$data['mall_cd']]['temp'] == 'Y') { ?>
				<input type="image" src="../img/btn_scmlogin.gif" align="absbottom" alt="SCM로그인" onclick="scm_login('<?=$data['minfo_idx']?>');">
				<? } else { ?>
				<input type="image" src="../img/btn_no_addmall.gif" align="absbottom" alt="지원불가" onclick="scm_login('none');">
				<? } ?>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=10 class=rndline></td></tr>
		<? } ?>
	<? } ?>
</table>
<div align="center" class="pageNavi"><font class="ver8"><?=$page_navi?></font></div>

<div align="right" class="pageNavi"><input type="image" src="../img/btn_addmarket.gif" align="absbottom" alt="마켓 등록" onclick="mall_register();"></div>

<form name="mallInfo">
	<input type="hidden" name="seq" value="<?=$seq?>">
	<input type="hidden" name="minfo_idx" value="">
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
등록하신 마켓 정보를 확인 하실 수 있습니다.<br/>
마켓정보별 최종적으로 작업한 시간이 표시됩니다.<br/>
SCM로그인 직접 하지 마시고 리스트 우측 <img src="../img/btn_scmlogin.gif" align="absbottom"> 버튼을 이용하여 one click 으로 이용하세요~! <br/><br/><br/>

등록된 마켓정보를 삭제하신 경우 삭제한 마켓에 주문수집과 상품링크/수정링크를 사용하실 수 없습니다.<br/>
마켓의 정보가 변경된 경우 마켓리스트에서 변경된 정보로 수정해주셔야 합니다.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>