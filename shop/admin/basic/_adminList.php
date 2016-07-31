<?

### 그룹명 가져오기
unset($r_grp,$where);
$query = "select * from ".GD_MEMBER_GRP;
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[$data['level']] = $data['grpnm'];

if (!$_GET['page_num']) $_GET['page_num'] = 10;
$orderby = ($_GET['sort']) ? $_GET['sort'] : "regdt desc"; # 정렬 쿼리

### 변수할당
if(!$_GET['grpType'])$_GET['grpType']=0;
$selected['page_num'][$_GET['page_num']]	= "selected";
$selected['sort'][$orderby]					= "selected";
$selected['skey'][$_GET['skey']]			= "selected";
$checked['grpType'][$_GET['grpType']]		=" checked";

### 목록
$db_table = GD_MEMBER;

if ($_GET['skey'] && $_GET['sword']){
	if ( $_GET['skey']== 'all' ){
		$where[] = "( concat( m_id, name ) like '%".$_GET['sword']."%' or nickname like '%".$_GET['sword']."%' )";
	}
	else $where[] = $_GET['skey'] ." like '%".$_GET['sword']."%'";
}

if(!$_GET['grpType']){
	$where[] = "level >= 80";
}else{
	$where[] = "level < 80";
}

$where[] = "m_id != 'godomall'";


$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);

list ($total) = $db->fetch("select count(*) from ".GD_MEMBER." where ".implode(' and ',$where)); # 총 레코드수
?>

<script>
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}
function delMember(fm)
{
	if (!isChked(document.getElementsByName('chk[]'))) return;
	if (!confirm('정말로 삭제 하시겠습니까?')) return;
	fm.target = "_self";
	fm.mode.value = "delete";
	fm.action = "../member/indb.php";
	fm.submit();
}
function modiMember(fm)
{

	if (!confirm('정말로 수정 하시겠습니까?')) return;
	fm.target = "_self";
	fm.mode.value = "adminModify";
	fm.action = "../member/indb.php";
	fm.submit();
}
</script>

<form>

<div style="padding:10 0 5 5;color:#fe5400;"><font color="000000"><b>2. 관리자로 설정할 회원을 검색하고 관리자그룹으로 변경합니다. </b></font><font class="extext" color="#fe5400">(관리자로 설정될 사람은 반드시 미리 회원으로 가입되어 있어야 합니다)</font></div>

<table class="tb">
<col class="cellC" /><col class="cellL" style="width:330" />
<col class="cellC" /><col class="cellL" />
<tr height="30">
	<td>선택</td>
	<td>
	<input type="radio" name="grpType" value="0" class="null" <?=$checked['grpType'][0]?> />관리자그룹에서 검색
	&nbsp;&nbsp;&nbsp;<input type="radio" name="grpType" value="1" class="null" <?=$checked['grpType'][1]?> />일반회원그룹에서 검색
	</td>
	<td>키워드</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected['skey']['all']?>> 통합검색 </option>
	<option value="name" <?=$selected['skey']['name']?>> 회원명 </option>
	<option value="m_id" <?=$selected['skey']['m_id']?>> 아이디 </option>
	<option value="email" <?=$selected['skey']['email']?>> 이메일 </option>
	<option value="phone" <?=$selected['skey']['phone']?>> 전화번호 </option>
	<option value="mobile" <?=$selected['skey']['mobile']?>> 핸폰번호 </option>
	</select> <input type="text" name="sword" value="<?=$_GET['sword']?>" style="width:200px" class="line" />
	</td>
</tr>

</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>

<table width="100%">
<tr>
	<td class="pageInfo"><font class=small color=777777>
	총 <b><?=number_format($total)?></b>명, 검색 <b><?=number_format($pg->recode[total])?></b>명, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page[total])?> Pages
	</td>
	<td align=right>
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
	<select name=page_num onchange="this.form.submit()">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>개 출력
	<? } ?>
	</select>
	</td>
</tr>
</table>
</form>

<form name="fmList" method="post">
<input type="hidden" name="mode" />
<input type="hidden" name="query" value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class=rnd colspan=14></td></tr>
<tr class=rndbg style="padding-top:2">
	<th width=60><font class="small1"><b>번호</th>
	<th width=100><font class="small1"><b>이름</th>
	<th><font class="small1"><b>아이디</th>
	<th width=60><font class="small1"><b>CRM</th>
	<th><font class="small1"><b>그룹</th>
	<th><font class="small1"><b>방문수</th>
	<th><font class="small1"><b>가입일</th>
	<th><font class="small1"><b>최근로그인</th>
	<th><font class="small1"><b>승인</th>
	<th><font class="small1"><b>수정</th>
</tr>
<tr><td class=rnd colspan=14></td></tr>

<?
while ($data=$db->fetch($res)){
	$last_login = (substr($data[last_login],0,10)!=date("Y-m-d")) ? substr($data[last_login],0,10) : "<font color=#f54500>".substr($data[last_login],11)."</font>";
	$status = ( $data[status] == '1' ? '승인' : '미승인' );
?>
<tr height="30" align="center">
	<td><font class=ver71 color=616161><?=$pg->idx--?></font></td>
	<td><span id="navig" name="navig" m_id="<?=$data[m_id]?>" m_no="<?=$data[m_no]?>"><font class="small1" color=0074BA><b><?=$data[name]?></b></font></span></td>
	<td><span id="navig" name="navig" m_id="<?=$data[m_id]?>" m_no="<?=$data[m_no]?>"><font class=ver811 color=0074BA><b><?=$data[m_id]?></b></font></span></td>
	<td><a href="javascript:popupLayer('../member/Crm_view.php?m_id=<?=$data['m_id']?>',780,600)"><img src="../img/icon_crmlist<?=$data['sex']?>.gif"></a></td>
	<td><font class=def><select name='level[<?=$data[m_no]?>]' ><?foreach($r_grp as $k => $v){?><option value='<?=$k?>'<?=($k==$data[level])?" selected":""?>><?=$v?></option><?}?></select></font></td>
	<td><font class=ver81 color=616161><?=$data[cnt_login]?></font></td>
	<td><font class=ver81 color=616161><?=substr($data[regdt],0,10)?></font></td>
	<td><font class=ver81 color=616161><?=$last_login?></font></td>
	<td><font class=small color=616161><?=$status?></font></td>
	<td><a href="../member/info.php?m_id=<?=$data[m_id]?>"><img src="../img/i_edit.gif"></a></td>
</tr>
<tr><td colspan=14 class=rndline></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td align=center style="padding-top:10"><font class=ver8><?=$pg->page[navi]?></font></td></tr>
<tr><td align=center style="padding:25 0 20 0"><a href="javascript:modiMember(document.fmList)"><img src="../img/btn_save.gif"></a></td>
</tr></table>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">이름과 아이디를 클릭하면 회원정보를 볼 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>관리자는 기본적으로 쇼핑몰의 회원이 되며, 관리자를 추가하려면 회원가입후 해당회원을 관리자 그룹으로 지정하시면 됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form>

<script>window.onload = function(){ UNM.inner();};</script>