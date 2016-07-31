<?
	include dirname(__FILE__)."/../../conf/config.php";
	include dirname(__FILE__)."../../lib.php";
	include "../../lib/page.class.php";
	@include "../../conf/bd_".$_GET['board'].".php";

	header('Content-type: application/vnd.ms-excel; charset=euc-kr');
	header('Content-Disposition: attachment; filename='.(($bdName) ? $bdName : "전체목록").'_'.date('Y_m_d_H_i_s',time()).'.xls');

	if(!$_GET['skey']) $_GET['skey'] = "all";
	if(!$_GET['board']) $_GET['board'] = "all";
	if(!$_GET['sort']) $_GET['sort'] = "regdt DESC";

	$res = $db->_select("SELECT id FROM gd_board");
	for($i = 0; $i < count($res); $i++) {
		include "../../conf/bd_".$res[$i]['id'].".php";
		$board[$res[$i]['id']] = $bdName;
		$tmp = $db->fetch("SELECT COUNT(*) AS cnt FROM gd_bd_".$res[$i]['id']." WHERE main <> 0 ");
		$total += $tmp['cnt'];
	}
	$tables = array();
	$where = array();
	$tmp = array();

	$where[] = " main <> 0 ";

	if ($_GET['skey'] && $_GET['sword']){
		switch ($_GET['skey']){
			case "all": $key = "CONCAT( b.subject, b.contents, b.name, b.m_no )"; break;
			default: $key = $_GET['skey'];
		}

		$r_word = array_notnull(array_unique(explode(" ",$_GET['sword'])));
		for ($i=0;$i<count($r_word);$i++){
			$tmp[] = "$key LIKE '%$r_word[$i]%'";
			if (strlen($r_word[$i])>2) $log_word[] = $r_word[$i];
		}
		if (is_array($tmp)) $where[] = "(".implode(" AND ",$tmp).")";
	}

	if( $_GET['sregdt'][0] && $_GET['sregdt'][1] ) $where[] = " DATE_FORMAT(b.regdt, '%Y%m%d') BETWEEN '".$_GET['sregdt'][0]."' AND '".$_GET['sregdt'][1]."'";

	if( $_GET['board'] != 'all'){
		$tables[] = "( SELECT '".$board[$_GET['board']]."' AS boardnm, '".$_GET['board']."' AS board, b.no, b.titleStyle, b.subject, b.name, b.m_no, b.main, b.comment, b.regdt, b.hit, b.idx, HEX(b.sub) AS sub, m.m_id, m.mobile, m.email, m.dormant_regDate, b.ip, b.contents, b.old_file FROM gd_bd_".$_GET['board']." AS b LEFT JOIN ".GD_MEMBER." AS m ON b.m_no = m.m_no";
		if($where) $tables[] .= " WHERE ".implode(' AND ', $where);
		$tables[] = ") AS a ";

	}
	else {
		if($_GET['sort'] == "idx,main,sub") $_GET['sort'] = "regdt desc";
		$t_cnt = 0;
		$tables[] = " ( ";
		foreach($board as $key => $val) {
			$t_cnt++;
			$tables[] = " SELECT '".$val."' AS boardnm, '".$key."' AS board, b.no, b.titleStyle, b.subject, b.name, b.m_no, b.main, b.comment, b.regdt, b.hit, b.idx, HEX(b.sub) AS sub, m.m_id, m.mobile, m.email, m.dormant_regDate, b.ip, b.contents, b.old_file ";
			$tables[] .= " FROM gd_bd_".$key." AS b LEFT JOIN ".GD_MEMBER." AS m ON b.m_no = m.m_no";
			if($where) $tables[] .= " WHERE ".implode(' AND ', $where);
			if($tmpWhere) $tables[] .= " AND ".implode(' AND ', $tmpWhere);
			if($t_cnt < count($board)) $tables[] = " UNION ALL ";
			unset($tmpWhere);
		}
		$tables[] = ") AS a ";
	}
	unset($t_cnt);

	$db_table = implode(" ", $tables);
	$db_where[] = "1=1";

	$pg = new Page(1, $total);
	$pg->vars['page'] = getVars('page,log,x,y');
	$pg->field = " * ";
	$pg->setQuery($db_table, $db_where, $_GET['sort']);
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
	<th>게시판이름</th>
	<th>번호</th>
	<th>형식</th>
	<th>회원이름</th>
	<th>회원ID</th>
	<th>휴대폰</th>
	<th>이메일</th>
	<th>IP</th>
	<th>글제목</th>
	<th>글내용</th>
	<th>작성일</th>
	<th>첨부파일명</th>
	<th>조회수</th>
	<th>댓글수</th>
</tr>
<?
	while($data = $db->fetch($res)) {
		$data["bType"] = ($data["sub"]) ? "답글" : "원글";

		if($data['m_id'] && $data['dormant_regDate'] != '0000-00-00 00:00:00'){
			$data["m_id"] = '휴면회원';
			$data["mobile"] = $data["email"] = '';
		}
?>
<tr>
	<td><?=$data["boardnm"]?></td>
	<td><?=$data["no"]?></td>
	<td><?=$data["bType"]?></td>
	<td><?=$data["name"]?></td>
	<td><?=$data["m_id"]?></td>
	<td><?=$data["mobile"]?></td>
	<td><?=$data["email"]?></td>
	<td><?=$data["ip"]?></td>
	<td><?=$data["subject"]?></td>
	<td><?=nl2br(htmlspecialchars($data["contents"]))?></td>
	<td><?=$data["regdt"]?></td>
	<td><?=$data["old_file"]?></td>
	<td><?=$data["hit"]?></td>
	<td><?=$data["comment"]?></td>
</tr>
<?
	}
?>
</table>