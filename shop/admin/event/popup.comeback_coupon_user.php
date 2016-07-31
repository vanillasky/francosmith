<?
include "../_header.popup.php";
include "../../lib/page.class.php";

### 그룹명 가져오기
$query = "select * from ".GD_MEMBER_GRP;
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$r_grp[$data['level']] = $data['grpnm'];
	$r_grp_sno[$data['level']] = $data['sno'];
}

$query = $db->_query_print("SELECT type, step, goodsno, date, price, couponyn, couponcd, smsyn FROM ".GD_COMEBACK_COUPON." WHERE sno = '[i]'",$_GET['sno']);
$coupon_info = $db->fetch($query,1);

if ($coupon_info['type'] == '1') {
	$db_table = GD_ORDER." o LEFT JOIN ".GD_MEMBER." m ON o.m_no = m.m_no";
	if ($coupon_info['price'] && $coupon_info['price'] != ',') {
		$price = explode(',',$coupon_info['price']);
		if ($price[0]) {
			$where[] = "o.settleprice >= ".$price[0];
		}
		if ($price[1]) {
			$where[] = "o.settleprice <= ".$price[1];
		}
	}
} else if ($coupon_info['type'] == '2') {
	$db_table = GD_ORDER_ITEM." ot LEFT JOIN ".GD_ORDER." o ON ot.ordno = o.ordno LEFT JOIN ".GD_MEMBER." m ON o.m_no = m.m_no";
	$where[] = "ot.goodsno IN (".$coupon_info['goodsno'].")";
}

$where[] = "o.m_no > 0";
$where[] = "m.m_no IS NOT NULL";
$where[] = "DATE_FORMAT(o.".$coupon_info['step'].",'%Y%m%d') <= DATE_FORMAT(CURDATE()-INTERVAL ".$coupon_info['date']." DAY,'%Y%m%d')";
if ($coupon_info['couponyn'] == 'n' && $coupon_info['smsyn'] == 'y') {
	$where[] = "m.sms = 'y'";
}

$pg = new Page($_GET['page']);
$pg -> field = "DISTINCT m.m_no, m.name, m.m_id, m.level";
$pg->setQuery($db_table,$where,'m.m_no');

$pg->exec();
$res = $db->query($pg->query);
?>
<div class="title title_top">컴백쿠폰/SMS 발송대상<br /><p class="extext" style="padding:0; margin:0;">현재 시간 기준 발송 대상 회원 리스트입니다.<br />동일한 쿠폰을 보유하고 있는 회원에게는 쿠폰을 재발급하지 않습니다. SMS만 발송됩니다.</p></div>
<div style="padding:15px 0 10px;">발송 대상 회원 총 <?=$pg->recode['total']?>명</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th>번호</th>
	<th>이름</th>
	<th>아이디</th>
	<th>그룹</th>
	<th>쿠폰보유</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<col width="50" align="center">
<col align="center">
<col width="150" align="center">
<col width="100" align="center">
<col width="100" align="center">
<?
while ($data=$db->fetch($res)){

$coupon_num_query = "
	SELECT
		SUM(cnt)
	FROM (
		(SELECT COUNT(*) as cnt FROM ".GD_COUPON_APPLY." WHERE couponcd = '".$coupon_info['couponcd']."' AND membertype = '0')
		UNION ALL
		(SELECT COUNT(*) as cnt FROM ".GD_COUPON_APPLY." WHERE couponcd = '".$coupon_info['couponcd']."' AND membertype = '1' AND member_grp_sno = '".$r_grp_sno[$data['level']]."')
		UNION ALL
		(SELECT COUNT(*) as cnt FROM ".GD_COUPON_APPLY." ca LEFT JOIN ".GD_COUPON_APPLYMEMBER." cam ON ca.sno = cam.applysno WHERE ca.couponcd = '".$coupon_info['couponcd']."' AND m_no = '".$data['m_no']."')
	) gd_coupon_cnt
";
list($coupon_num) = $db->fetch($coupon_num_query);
?>
<tr height="30" align="center">
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td><font color="#0074ba"><b><?=$data['name']?></b></font></td>
	<td><font class="ver81" color="#0074ba"><b><?=$data['m_id']?></b></font></td>
	<td><?=$r_grp[$data['level']]?></td>
	<td><?=$coupon_num > 0 ? "Y" : "N"?></td>
</tr>
<tr><td colspan="12" class="rndline"></td></tr>
<?}?>
</table>

<div class="pageNavi" align=center><font class=ver8><?=$pg->page[navi]?></div>