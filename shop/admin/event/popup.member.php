<?
include "../_header.popup.php";
include "../../lib/page.class.php";

### 그룹명 가져오기
$query = "select * from ".GD_MEMBER_GRP;
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[$data['level']] = $data['grpnm'];

list ($total) = $db->fetch("select count(*) from ".GD_MEMBER." WHERE " . MEMBER_DEFAULT_WHERE); # 총 레코드수

if (!$_GET['page_num']) $_GET['page_num'] = 10;
$orderby = ($_GET['sort']) ? $_GET['sort'] : "regdt desc"; # 정렬 쿼리

### 변수할당
$selected['page_num'][$_GET['page_num']]	= "selected";
$selected['sort'][$orderby]					= "selected";
$selected['skey'][$_GET['skey']]			= "selected";
$selected['sstatus'][$_GET['sstatus']]		= "selected";
$selected['slevel'][$_GET['slevel']]		= "selected";
$selected['sage'][$_GET['sage']]			= "selected";
$selected['birthtype'][$_GET['birthtype']]	= "selected";
$selected['marriyn'][$_GET['marriyn']]		= "selected";
$checked['sex'][$_GET['sex']]				= "checked";
$checked['mailing'][$_GET['mailing']]		= "checked";
$checked['smsyn'][$_GET['smsyn']]			= "checked";
$checked['popupDetail'][$_GET['popupDetail']]	= "checked";

### 목록
$db_table = GD_MEMBER;

if ($_GET['skey'] && $_GET['sword']){
	if ( $_GET['skey']== 'all' ){
		$where[] = "( concat( m_id, name ) like '%".$_GET['sword']."%' or nickname like '%".$_GET['sword']."%' )";
	}
	else $where[] = $_GET['skey'] ." like '%".$_GET['sword']."%'";
}

if ($_GET['sstatus']!='') $where[] = "status='".$_GET['sstatus']."'";
if($_GET['slevel'] == '__null__'){
	$where[] = 'level not in ('.implode(',',array_keys($r_grp)).')';
}
else{
	if ($_GET['slevel']!='') $where[] = "level='".$_GET['slevel']."'";
}

if ($_GET['ssum_sale'][0] != '' && $_GET['ssum_sale'][1] != '') $where[] = "sum_sale between ".$_GET['ssum_sale'][0]." and ".$_GET['ssum_sale'][1];
else if ($_GET['ssum_sale'][0] != '' && $_GET['ssum_sale'][1] == '') $where[] = "sum_sale >= ".$_GET['ssum_sale'][0];
else if ($_GET['ssum_sale'][0] == '' && $_GET['ssum_sale'][1] != '') $where[] = "sum_sale <= ".$_GET['ssum_sale'][1];

if ($_GET['semoney'][0] != '' && $_GET['semoney'][1] != '') $where[] = "emoney between ".$_GET['semoney'][0]." and ".$_GET['semoney'][1];
else if ($_GET['semoney'][0] != '' && $_GET['semoney'][1] == '') $where[] = "emoney >= ".$_GET['semoney'][0];
else if ($_GET['semoney'][0] == '' && $_GET['semoney'][1] != '') $where[] = "emoney <= ".$_GET['semoney'][1];

if ($_GET['sregdt'][0] && $_GET['sregdt'][1]) $where[] = "regdt between date_format(".$_GET['sregdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['sregdt'][1].",'%Y-%m-%d 23:59:59')";
if ($_GET['slastdt'][0] && $_GET['slastdt'][1]) $where[] = "last_login between date_format(".$_GET['slastdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['slastdt'][1].",'%Y-%m-%d 23:59:59')";

if ($_GET['sex']) $where[] = "sex = '".$_GET['sex']."'";
if ($_GET['sage']!=''){
	$age[] = date('Y') + 1 - $_GET['sage'];
	$age[] = $age[0] - 9;
	foreach ($age as $k => $v) $age[$k] = substr($v,2,2);
	if ($_GET['sage'] == '60') $where[] = "right(birth_year,2) <= ".$age[1];
	else $where[] = "right(birth_year,2) between ".$age[1]." and ".$age[0];
}

if ($_GET['scnt_login'][0] != '' && $_GET['scnt_login'][1] != '') $where[] = "cnt_login between ".$_GET['scnt_login'][0]." and ".$_GET['scnt_login'][1];
else if ($_GET['scnt_login'][0] != '' && $_GET['scnt_login'][1] == '') $where[] = "cnt_login >= ".$_GET['scnt_login'][0];
else if ($_GET['scnt_login'][0] == '' && $_GET['scnt_login'][1] != '') $where[] = "cnt_login <= ".$_GET['scnt_login'][1];

if ($_GET['dormancy']){
	$dormancyDate	= date("Ymd",strtotime("-{$_GET['dormancy']} day"));
	$where[] = " date_format(last_login,'%Y%m%d') <= '".$dormancyDate."'";
}

if ($_GET['mailing']) $where[] = "mailling = '".$_GET['mailing']."'";
if ($_GET['smsyn']) $where[] = "sms = '".$_GET['smsyn']."'";

if( $_GET['birthtype'] ) $where[] = "calendar = '".$_GET['birthtype']."'";
if( $_GET['birthdate'][0] ){
	if( $_GET['birthdate'][1] ){
		if(strlen($_GET['birthdate'][0]) > 4 && strlen($_GET['birthdate'][1]) > 4) $where[] = "concat(birth_year, birth) between '".$_GET['birthdate'][0]."' and '".$_GET['birthdate'][1]."'";
		else $where[] = "birth between '".$_GET['birthdate'][0]."' and '".$_GET['birthdate'][1]."'";
	}else{
		$where[] = "birth = '".$_GET['birthdate'][0]."'";
	}
}

if( $_GET['marriyn'] ) $where[] = "marriyn = '".$_GET['marriyn']."'";
if( $_GET['marridate'][0] ){
	if( $_GET['marridate'][1] ){
		if(strlen($_GET['marridate'][0]) > 4 && strlen($_GET['marridate'][1]) > 4) $where[] = "marridate between '".$_GET['marridate'][0]."' and '".$_GET['marridate'][1]."'";
		else $where[] = "substring(marridate,5,4) between '".$_GET['marridate'][0]."' and '".$_GET['marridate'][1]."'";
	}else{
		$where[] = "substring(marridate,5,4) = '".$_GET['marridate'][0]."'";
	}
}

if(is_array($where)){
	$where[] = MEMBER_DEFAULT_WHERE;

	$pg = new Page($_GET['page'],$_GET['page_num']);
	$pg->setQuery($db_table,$where,$orderby);
	$pg->exec();

	$res = $db->query($pg->query);
}
?>
<div class="title title_top">회원검색</div>
<form method=get>
<?
### 회원 검색폼
$popupSearch = "Y";
include "../member/_listForm.php";
?>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>
<table width="100%">
<tr>
	<td class="pageInfo">
	총 <font class="ver8"><b><?=number_format($total-1)?></b>명, 검색 <b><?=number_format($pg->recode['total'])?></b>명, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</td>
	<td align="right">
	<select name="sort" onchange="this.form.submit();">
	<option value="regdt desc" <?=$selected['sort']['regdt desc']?>>- 가입일 정렬↑</option>
	<option value="regdt asc" <?=$selected['sort']['regdt asc']?>>- 가입일 정렬↓</option>
	<option value="last_login desc" <?=$selected['sort']['last_login desc']?>>- 최종로그인 정렬↑</option>
	<option value="last_login asc" <?=$selected['sort']['last_login asc']?>>- 최종로그인 정렬↓</option>
	<option value="cnt_login desc" <?=$selected['sort']['cnt_login desc']?>>- 방문수 정렬↑</option>
	<option value="cnt_login asc" <?=$selected['sort']['cnt_login asc']?>>- 방문수 정렬↓</option>
    <optgroup label="------------"></optgroup>
	<option value="name desc" <?=$selected['sort']['name desc']?>>- 이름 정렬↑</option>
	<option value="name asc" <?=$selected['sort']['name asc']?>>- 이름 정렬↓</option>
	<option value="m_id desc" <?=$selected['sort']['m_id desc']?>>- 아이디 정렬↑</option>
	<option value="m_id asc" <?=$selected['sort']['m_id asc']?>>- 아이디 정렬↓</option>
    <optgroup label="------------"></optgroup>
	<option value="emoney desc" <?=$selected['sort']['emoney desc']?>>- 적립금 정렬↑</option>
	<option value="emoney asc" <?=$selected['sort']['emoney asc']?>>- 적립금 정렬↓</option>
	<option value="sum_sale desc" <?=$selected['sort']['sum_sale desc']?>>- 구매금액 정렬↑</option>
	<option value="sum_sale asc" <?=$selected['sort']['sum_sale asc']?>>- 구매금액 정렬↓</option>
	</select>&nbsp;
	<select name="page_num" onchange="this.form.submit();">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>개 출력</option>
	<? } ?>
	</select>
	</td>
</tr>
</table>
</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev');" class="white">선택</a></th>
	<th>번호</th>
	<th>이름</th>
	<th>아이디</th>
	<th>CRM</th>
	<th>그룹</th>
	<th>가입일</th>
	<th>최종로그인</th>
	<th>메일</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<col width="30" align="center">
<col width="30" align="center">
<col width="80" align="center" span="2">
<col width="30" align="center">
<col width="80" align="center">
<col width="80" align="center" span="2">
<col width="50" align="center">
<?
if(is_resource($res)){
while ($data=$db->fetch($res)){
	$last_login = (substr($data['last_login'],0,10)!=date("Y-m-d")) ? substr($data['last_login'],0,10) : "<font color=#7070B8>".substr($data['last_login'],11)."</font>";
	$msg_mailing = ( $data['mailling'] == 'y') ? '허용' : '거부';
?>
<tr height="30" align="center">
<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data[m_no]?>:<?=$data[name]?>:<?=$data[m_id]?>"></td>
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td>
	<font color="#0074ba"><b><?=$data['name']?></b></font>
	<?if($data['nickname']){?><br />
	<div style="padding-top:2"><img src="../img/icon_nic.gif" align="absmiddle" /><font class="small1" color="#488d00"><?=$data['nickname']?></font></div>
	<?}?>
	</td>
	<td><font class="ver81" color="#0074ba"><b><?=$data['m_id']?></b></font></td>
	<td><a href="javascript:popup('../member/Crm_view.php?m_id=<?=$data['m_id']?>',780,600);"><img src="../img/icon_crmlist<?=$data['sex']?>.gif" /></a></td>
	<td><font class="def"><?=$r_grp[$data['level']]?></font></td>
	<td><font class="ver81" color="#616161"><?=substr($data['regdt'],0,10)?></font></td>
	<td><font class="ver81" color="#616161"><?=$last_login?></font></td>
	<td><font class="small" color="#616161"><?=$data['email']?></font></td>
</tr>
<tr><td colspan="12" class="rndline"></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td width="20%"></td>
	<td width="60%" align="center"><font class="ver8"><?=$pg->page['navi']?></font></td>
	<td width="20%"></td>
</tr>
</table>
<? } ?>
<div align=center>
<a href="javascript:exec_add();"><img src="../img/btn_confirm_s.gif"></a>
</div>
<script>

function exec_add()
{
	var ret;
	var str = new Array();
	var obj = document.getElementsByName('chk[]');
	var tbl = opener.document.getElementById('m_ids');
	ret = false;

	for (i=0;i<obj.length;i++){
		if (obj[i].checked){
			str = obj[i].value.split(':');
			oTr = tbl.insertRow();
			oTd = oTr.insertCell();
			oTd.id = "currPosition";
			oTd.innerHTML = str[1] + '(' + str[2] + ')';
			oTd = oTr.insertCell();
			oTd.innerHTML = "\<input type=text name=m_ids[] value='" + str[0] + "' style='display:none'>";
			oTd = oTr.insertCell();
			oTd.innerHTML = "<a href='javascript:void(0)' onClick='del_options(this.parentNode.parentNode)'><img src='../img/i_del.gif' align=absmiddle></a>";
			ret = true;
		}

	}
	if (!ret){
		alert('회원을 선택해주세요');
		return;
	}
	opener.calSmsCnt();
	self.close();
}
table_design_load();
</script>