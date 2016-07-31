<?php
$location = '회원관리 > 휴면 회원 리스트';
include '../_header.php';
include '../../lib/page.class.php';

$dormant = Core::loader('dormant');

//개인정보 유효기간제 설정 체크
if($dormant->checkDormantAgree() === false){
	msg("개인정보 유효기간제 설정 후 이용가능합니다.", "../basic/adm_basic_dormantConfig.php");
	exit;
}

//회원그룹
$group = array();
$res = $db->query("SELECT * FROM ".GD_MEMBER_GRP);
while ($data=$db->fetch($res)) {
	$group[$data['level']] = $data['grpnm'];
}

//총 휴면회원 수
$total = $dormant->getDormantMemberCount('dormantCount');

if (!$_GET['page_num']) $_GET['page_num'] = 10;
if (!$_GET['skey']) $_GET['skey'] = 'm_id';
$selected['page_num'][$_GET['page_num']]	= "selected";
$selected['skey'][$_GET['skey']]			= "selected";

$where[] = "m_id != 'godomall'";
if ($_GET['skey'] && trim($_GET['sword'])){
	$where[] = $dormant->getListWhere($_GET['skey'], trim($_GET['sword']));
}
if ($_GET['sregdt'][0] && $_GET['sregdt'][1]) $where[] = "dormant_regDate between date_format(".$_GET['sregdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['sregdt'][1].",'%Y-%m-%d 23:59:59')";

$orderby = 'dormant_regDate desc, regdt desc';

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = $dormant->getSecretField();
$pg->setQuery($dormant->getDormantTableName(), $where, $orderby);
$pg->exec();
$res = $db->query($pg->query);
?>
<div class="title title_top">휴면 회원 리스트<span>휴면 회원을 확인하실 수 있습니다.<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=27');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<form>
<table class="tb">
<colgroup>
	<col class="cellC" style="width: 150px;"/>
	<col class="cellL" />
</colgroup>
<tr>
	<td>휴면 대상 회원 정보</td>
	<td>
		<select name="skey">
			<option value="m_id" <?=$selected['skey']['m_id']?>> 아이디 </option>
			<option value="name" <?=$selected['skey']['name']?>> 이름 </option>
			<option value="email" <?=$selected['skey']['email']?>> 이메일 </option>
			<option value="mobile" <?=$selected['skey']['mobile']?>> 휴대폰번호 </option>
			<option value="phone" <?=$selected['skey']['phone']?>> 전화번호 </option>
		</select> <input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" />
		<div class="ver81" style="color: #627dce; margin-top: 5px;"> * 아이디, 이름 등 회원정보 검색 시 정확한 정보를 입력해야 검색가능합니다.<br /> 예) 검색하려는 회원 ID가 godomall 인 경우 : go(X), godo(X), godomall(O)</div>
	</td>
</tr>
<tr>
	<td>휴면회원 전환일</td>
	<td>
		<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][0]?>" size="10" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" /> ~
		<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][1]?>" size="10" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" />
		<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
</table>

<div class="button_top" style="margin-bottom:30px;"><input type="image" src="../img/btn_search2.gif" /></div>

<table width="100%">
<tr>
	<td class="pageInfo ver8">
	<strong> ■ 휴면회원 리스트 (검색결과 <?php echo number_format($pg->recode['total']);?>명 / 전체 <?php echo number_format($total); ?>명)</strong>
	<br />
	마지막 접속일로부터 1년이 지난 회원은 휴면회원으로 분리 저장됩니다
	</td>
	<td align="right">
		<select name="page_num" onchange="this.form.submit();">
		<?php
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?php echo $v; ?>" <?php echo $selected['page_num'][$v]; ?>><?php echo $v; ?>개 출력</option>
		<? } ?>
		</select>
	</td>
</tr>
</table>
</form>

<form name="dormantForm" id="dormantForm" action="./adm_dormant_indb.php" method="post" target="ifrmHidden" />
<input type="hidden" name="mode" id="mode" value="" />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<colgroup>
	<col width="5%" align="center" />
	<col width="10%" align="center" />
	<col width="10%" align="center" />
	<col width="10%" align="center" />
	<col width="10%" align="center" />
	<col width="10%" align="right" />
	<col width="12%" align="right" />
	<col width="13%" align="center" />
	<col width="10%" align="center" />
	<col width="10%" align="center" />
</colgroup>
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev');" class="white">선택</a></th>
	<th>휴면회원 전환일</th>
	<th>아이디</th>
	<th>이름</th>
	<th>그룹</th>
	<th>적립금</th>
	<th>최종로그인</th>
	<th>이메일</th>
	<th>휴대폰번호</th>
	<th>전화번호</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>
<?php
while ($data = $db->fetch($res)){
?>
<tr height=40 align="center">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?php echo $data['m_no']; ?>"></td>
	<td><?php echo $data['dormant_regDate']; ?></td>
	<td><?php echo $data['m_id']; ?></td>
	<td><?php echo $data['name']; ?></td>
	<td><?php echo $group[$data['level']]; ?></td>
	<td><?php echo number_format($data['emoney']); ?> 원</td>
	<td><?php echo $data['last_login']; ?></td>
	<td><?php echo $data['email']; ?></td>
	<td><?php echo $data['mobile']; ?></td>
	<td><?php echo $data['phone']; ?></td>
</tr>
<tr><td colspan="40" class="rndline"></td></tr>
<?php } ?>
</table>
</form>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td width="20%" height="35" style="padding-left:13px">
		<div style="width: 225px;">선택한 <img src="../img/btn_dormantClear.gif" border="0" id="dormantRestoreAdmin" class="hand" style="vertical-align: bottom;" />&nbsp;<img src="../img/btn_dormantDelete.gif" border="0" id="dormantMemberDelete" class="hand" style="vertical-align: bottom;" /></div>
	</td>
	<td width="60%" align="center"><font class="ver8"><?php echo $pg->page['navi']; ?></font></td>
	<td width="20%" align="right"><img src="../img/btn_dormantAllDelete.gif" border="0" id="dormantMemberDeleteAll" class="hand" style="vertical-align: bottom;" /></td>
</tr>
</table>

<?php include "../_footer.php"; ?>
<script type="text/javascript">
jQuery(document).ready(function($){
	var form = $("#dormantForm");

	//휴면회원 전환
	$("#dormantRestoreAdmin").click(function(){
		if($("input[name='chk[]']:checkbox:checked").length < 1){
			alert("회원을 선택하여 주세요.");
			return;
		}
		if(confirm("선택한 회원의 휴면 상태를 해제 하시겠습니까?")){
			$("#mode").val("dormantRestoreAdmin");
			form.submit();
		}
		return;
	});
	//회원 삭제
	$("#dormantMemberDelete").click(function(){
		if($("input[name='chk[]']:checkbox:checked").length < 1){
			alert("회원을 선택하여 주세요.");
			return;
		}
		if(confirm("선택한 휴면회원정보를 삭제하시겠습니까?\n선택한 회원은 탈퇴처리됩니다.")){
			$("#mode").val("dormantMemberDelete");
			form.submit();
		}
		return;
	});
	//휴면회원정보 전체삭제
	$("#dormantMemberDeleteAll").click(function(){
		var total = '<?php echo $total; ?>';
		if(confirm("전체 " + total + " 건의 휴면회원정보를 삭제하시겠습니까?\n삭제 진행 시 해당 회원은 탈퇴처리됩니다.")){
			$("#mode").val("dormantMemberDeleteAll");
			form.submit();
		}
		return;
	});
	$("iframe[name='ifrmHidden']").load(function(){
		hiddenDormantProgressBar();
	});
	form.submit(function(){
		showDormantProgressBar();
	});

	function showDormantProgressBar(msg){
		var progressImgMarginTop = Math.round((jQuery(window).height() - 116) / 2);

		jQuery("body").append('<div id="dormantProgressBar" style="position:absolute;top:0;left:0;background:#44515b;filter:alpha(opacity=80);opacity:0.8;width:100%;height:'+jQuery('body').height()+'px;cursor:progress;z-index:100000;margin:0 auto;text-align: center;"><img src="../img/admin_progress.gif" border="0" style="margin-top:'+progressImgMarginTop+'px;" /></div>');
	}

	function hiddenDormantProgressBar(){
		jQuery("#dormantProgressBar").remove();
	}
});
</script>