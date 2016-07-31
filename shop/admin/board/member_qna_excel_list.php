<?

include dirname(__FILE__)."/../../conf/config.php";
include dirname(__FILE__)."../../lib.php";
include "../../lib/page.class.php";
@include "../../conf/phone.php";

header('Content-type: application/vnd.ms-excel; charset=euc-kr');
header('Content-Disposition: attachment; filename=list_'.date('Y-m-d H:i:s',time()).'.xls');
list ($total) = $db->fetch("select count(*) from ".GD_MEMBER_QNA.""); # 총 레코드수

### 변수할당
$itemcds = codeitem( 'question' ); # 질문유형
$cfg['memberQnaFavoriteReplyUse'] = (!$cfg['memberQnaFavoriteReplyUse']) ? "n" : $cfg['memberQnaFavoriteReplyUse'];
$orderby = ($_GET[sort]) ? $_GET[sort] : "parent desc, sno asc"; # 정렬 쿼리

### 검색조건
if ($_GET[skey] && $_GET[sword])
{
	if ($_GET[skey]== 'all') $subwhere[] = "concat( subject, contents, ifnull(m_id, '') ) like '%$_GET[sword]%'";
	else $subwhere[] = "$_GET[skey] like '%$_GET[sword]%'";
}
if ($_GET[sitemcd] <> '' && $_GET[sitemcd] <> 'all') $subwhere[] = "itemcd='" . $_GET[sitemcd] . "'"; # 분류검색
if ($_GET[sregdt][0] && $_GET[sregdt][1]) $subwhere[] = "a.regdt between date_format({$_GET[sregdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[sregdt][1]},'%Y-%m-%d 23:59:59')";

if (count($subwhere))
{
	$parent = array();
	$res = $db->query( "select parent from ".GD_MEMBER_QNA." a left join ".GD_MEMBER." b on a.m_no=b.m_no ".$subtable." where " . implode(" and ", $subwhere) );
	while ( $row = $db->fetch( $res ) ) $parent[] = $row['parent'];
	$parent = array_unique ($parent);
	if ( count( $parent ) ) $where[] = "a.parent in ('" . implode( "','", $parent ) . "')";
	else $where[] = "a.parent in ('0')";
}

### 목록
$pg = new Page(1,$total);
$pg->field = "DISTINCT sno, parent, itemcd, ordno, subject, contents, regdt, m_no, notice, email, ip ";
$db_table = GD_MEMBER_QNA." AS a";
$pg->setQuery($db_table,$where,"notice desc, ".$orderby);
$pg->exec();

$res = $db->query($pg->query);
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
	<th>질문유형</th>
	<th>글제목</th>
	<th>글내용</th>
	<th>작성일</th>
</tr>
<?
while($data=$db->fetch($res)){
	$m_data = $db->fetch("SELECT name, m_id, mobile, email, dormant_regDate FROM ".GD_MEMBER." WHERE m_no = '".$data['m_no']."'");
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

	if($m_data['dormant_regDate'] != '0000-00-00 00:00:00' && $data['m_no'] && $data['m_no'] && $m_data['m_id']){
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
	<td><?=$data['email']?></td>
	<td><?=$data['ip']?></td>
	<td><?=$itemcds[$data['itemcd']]?></td>
	<td><?=$data['subject']?></td>
	<td><?=$data['contents']?></td>
	<td><?=$data['regdt']?></td>
</tr>
<? } ?>
