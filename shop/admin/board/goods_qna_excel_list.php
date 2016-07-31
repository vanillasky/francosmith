<?
include dirname(__FILE__)."/../../conf/config.php";
include dirname(__FILE__)."../../lib.php";
include "../../lib/page.class.php";
@include "../../conf/phone.php";

header('Content-type: application/vnd.ms-excel; charset=euc-kr');
header('Content-Disposition: attachment; filename=list_'.date('Y-m-d H:i:s',time()).'.xls');

list ($total) = $db->fetch("select count(*) from ".GD_GOODS_QNA); # 총 레코드수

### 변수할당
$orderby = ($_GET['sort']) ? $_GET['sort'] : "notice desc, parent desc, ( case when parent=sno then 0 else 1 end ) asc, regdt desc"; # 정렬 쿼리

### 검색조건
if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];

	if ($category){
		$subtable = " left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";

		// 상품분류 연결방식 전환 여부에 따른 처리
		$subwhere[]	= getCategoryLinkQuery('c.category', $category, 'where');
	}
}

if ($_GET['skey'] && $_GET[sword]){
	if ($_GET['skey']== 'goodnm' ||  $_GET['skey']== 'all'){
		$tmp = array();
		$res = $db->query("select goodsno from ".GD_GOODS." where goodsnm like '%$_GET[sword]%'");
		while ($data=$db->fetch($res))$tmp[] = $data[goodsno];
		if ( is_array( $tmp ) && count($tmp) ) $goodnm_where = "a.goodsno in(" . implode( ",", $tmp ) . ")";
		else $goodnm_where = "0";
	}

	if ($_GET['skey']== 'all') $subwhere[] = "( concat( subject, contents, ifnull(m_id, ''), ifnull(a.name, '') ) like '%$_GET[sword]%' or $goodnm_where )";
	else if ($_GET['skey']== 'goodnm') $subwhere[] = $goodnm_where;
	else if ($_GET['skey']== 'm_id') $subwhere[] = "concat( ifnull(m_id, ''), ifnull(a.name, '') ) like '%$_GET[sword]%'";
	else $subwhere[] = $_GET['skey']." like '%$_GET[sword]%'";
}

if ($_GET[sregdt][0] && $_GET[sregdt][1]) $subwhere[] = "a.regdt between date_format({$_GET[sregdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[sregdt][1]},'%Y-%m-%d 23:59:59')";

if (count($subwhere))
{
	$parent = array();
	$res = $db->query( "select parent from ".GD_GOODS_QNA." a left join ".GD_MEMBER." b on a.m_no=b.m_no {$subtable} where " . implode(" and ", $subwhere) );
	while ( $row = $db->fetch( $res ) ) $parent[] = $row['parent'];
	$parent = array_unique ($parent);
	if ( count( $parent ) ) $t_where[] = "parent in ('" . implode( "','", $parent ) . "')";
	else $t_where[] = "parent in ('0')";
}

### 목록
$pg = new Page(1,$total);
$pg->field = "DISTINCT a.sno, a.parent, a.email, a.ip, a.subject, a.contents, a.regdt, a.name, a.m_no, a.goodsno, a.secret, a.notice";
$db_table = GD_GOODS_QNA." AS a LEFT JOIN ".GD_MEMBER." AS m ON (a.m_no = m.m_no) ";
$pg->setQuery($db_table,$t_where,"notice desc, ".$orderby);
$pg->exec();
echo $pg->query;
$res = $db->query($pg->query);

$replyQuery = "SELECT sno, subject FROM ".GD_GOODS_QNA_REPLY." ORDER BY regdt DESC";
$replyRes = $db->query($replyQuery);
$replyTotal = $db->count_($replyRes);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<style>
table.excel td {mso-number-format:'@';}
</style>
</head>
<body>
<table border='1' class="excel">
<tr>
	<th>번호</th>
	<th>형식</th>
	<th>회원이름</th>
	<th>회원ID</th>
	<th>휴대폰</th>
	<th>이메일(회원)</th>
	<th>이메일(게시물)</th>
	<th>IP</th>
	<th>상품명</th>
	<th>글제목</th>
	<th>글내용</th>
	<th>작성일</th>
	<th>상품적립금</th>
</tr>
<?
while($data=$db->fetch($res)){
	$m_data = $db->fetch("SELECT m_id, mobile, email, name, dormant_regDate FROM ".GD_MEMBER." WHERE m_no = '".$data['m_no']."'");
	$g_data = $db->fetch("SELECT G.goodsnm, O.reserve FROM ".GD_GOODS." AS G LEFT JOIN ".GD_GOODS_OPTION." AS O ON G.goodsno = O.goodsno and go_is_deleted <> '1' and go_is_display = '1' WHERE G.goodsno = '".$data['goodsno']."'");
	if($data['notice']) {
		$data['type'] = "<b>[ 공지 ]</b>";
	}
	elseif($data['sno'] == $data['parent']) {
		$data['type'] = "<b style='color:#ff6666'>[ 문의 ]</b>";
	}
	else {
		$data['type'] = "<b style='color:#448eff'>└ 답변</b>";
	}

	if($m_data['dormant_regDate'] != '0000-00-00 00:00:00' && $data['m_no'] && $m_data['m_id']){
		$m_data["m_id"] = '휴면회원';
		$m_data["name"] = $m_data["mobile"] = $m_data["email"] = '';
	}
?>
<tr>
	<td><?=$pg->idx--?></td>
	<td><?=$data["type"]?></td>
	<td><?=($m_data["name"]) ? $m_data["name"] : $data["name"]?></td>
	<td><?=$m_data["m_id"]?></td>
	<td><?=$m_data["mobile"]?></td>
	<td><?=$m_data["email"]?></td>
	<td><?=$data["email"]?></td>
	<td><?=$data["ip"]?></td>
	<td><?=$g_data["goodsnm"]?></td>
	<td><?=$data["subject"]?></td>
	<td><?=$data["contents"]?></td>
	<td><?=$data["regdt"]?></td>
	<td><?=$g_data["reserve"]?></td>
</tr>
<?
}
?>
</table>
