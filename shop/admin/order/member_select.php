<?
	include "../_header.popup.php";
	include "../../conf/config.pay.php";
	include "../../lib/page.class.php";

	$page = ($_GET['page']) ? $_GET['page'] : 1;
	$page_num = 10;
	$orderby = "regdt DESC";
	$db_table = GD_MEMBER;

	list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_MEMBER . " WHERE " . MEMBER_DEFAULT_WHERE);

	$where[] = MEMBER_DEFAULT_WHERE;

	if($_GET['skey'] && $_GET['sval']) {
		switch($_GET['skey']) {
			case 'all' :
				$where[] = "(CONCAT( m_id, name, phone, mobile) LIKE '%".$_GET['sval']."%')";
				break;
			case 'phone' :
				$where[] = "(phone LIKE '%".$_GET['sval']."%' OR mobile LIKE '%".$_GET['sval']."%')";
				break;
			default :
				$where[] = $_GET['skey']." LIKE '%".$_GET['sval']."%'";
				break;
		}
	}

	$pg = new Page($page, $_GET['page_num']);
	$pg->field = "name, m_id, sex, phone, mobile";
	$pg->setQuery($db_table, $where, $orderby);
	$pg->exec();

	$res = $db->query($pg->query);
?>
<style type="text/css">
	body { padding:0px; margin:5px; }
</style>
<script language="JavaScript">
	function applyMember(memID, memName) {
		document.getElementById('m_id').value = memID;
		opener.document.getElementById('m_id').value = memID;
		opener.document.getElementById('nameOrder').value = memName;
		document.findForm.action = "../order/indb.self_order.php";
		document.findForm.method = "post";
		document.findForm.submit();
	}
</script>
<div style="margin-bottom:7px; font-weight:bold; font-size:14px; font-family:dotum;"><img src="../img/titledot.gif" align="absbottom" style="margin-right:5px;" />회원검색 <span class="extext">주문을 원하는 회원을 검색하여 등록합니다.</span></div>

<div style="margin:10px;">
<form name="findForm" style="margin:0px; padding:0px;">
<input type="hidden" name="mode" id="mode" value="selectMember" />
<input type="hidden" name="m_id" id="m_id" value="" />
<select name="skey" id="skey">
	<option value="all">통합검색</option>
	<option value="name" <?=($_GET['skey'] == "name") ? "selected" : ""?>>이름</option>
	<option value="m_id" <?=($_GET['skey'] == "m_id") ? "selected" : ""?>>아이디</option>
	<option value="phone" <?=($_GET['skey'] == "phone") ? "selected" : ""?>>연락처</option>
</select>
<input type="text" name="sval" id="sval" value="<?=$_GET['sval']?>" />
<input type="image" src="../img/btn_search2.gif" align="absmiddle" style="border:0px;" />
</form>
</div>

<div style="color:#FF0000; font-size:11px; font-family:dotum;">* 검색된 회원의 아이디를 클릭하시면 회원정보가 등록됩니다.</div>

<div style="margin-top:10px;">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="4"></td></tr>
<tr class="rndbg">
	<th width="60">번호</th>
	<th width="">이름</th>
	<th width="">아이디</th>
	<th width="60">CRM</th>
</tr>
<tr><td class="rnd" colspan="4"></td></tr>
<? while($data=$db->fetch($res)) { ?>
<tr height="30" align="center">
	<td><?=$pg->idx--?></td>
	<td><?=$data['name']?></td>
	<td><a href="javascript:;" onclick="applyMember('<?=$data['m_id']?>', '<?=$data['name']?>')" style="color:#3482CA;text-decoration:underline;"><?=$data['m_id']?></a></td>
	<td><a href="javascript:popup2('../member/Crm_view.php?m_id=<?=$data['m_id']?>',780,600,1);"><img src="../img/icon_crmlist<?=$data['sex']?>.gif" /></a><?getlinkPc080($data['phone'],'phone')?><?getlinkPc080($data['mobile'],'mobile')?></td>
</tr>
<tr><td colspan="4" style="height:1px; background:#DCD8D6;"></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td align="center" height="35" style="padding-left:13px"><font class="ver8"><?=$pg->page['navi']?></font></td>
</tr>
</table>
</div>

</body>
</html>