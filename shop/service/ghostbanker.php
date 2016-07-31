<?
include "../_header.php";

$ghost_cfg = $config->load('ghostbanker');
if ($ghost_cfg['use'] != 1) {
	msg('사용할 수 없습니다.','close');
	exit;
}

$_GET['page'] = (isset($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;

// 데이터 가져오기
	$loop = array();
	$paging = '';


	if ($ghost_cfg['bankda_use'] == 1) {

		$file	= "../conf/godomall.cfg.php";
		$file	= file($file);
		$godo	= decode($file[1],1);


		$query = '';
		$query .= '&belowprice='.$ghost_cfg['bankda_limit']; // 이하가격
		$query .= '&term='.$ghost_cfg['expire']; // 노출기간(3,7,14,30,60)
		$query .= '&page='.$_GET['page']; // 페이지번호

		if ($_GET['date'] != '')
			$query .= '&date='.$_GET['date']; // 입금일자

		if ($_GET['name'] != '')
			$query .= '&name='.urlencode($_GET['name']); // 입금자


		/***************************************************************************************************
		*  hashdata 생성
		*    - 데이터 무결성을 검증하는 데이터로 요청시 필수 항목.
		*    - MID 를 조합한후 md5 방식으로 생성한 해쉬값.
		***************************************************************************************************/
		$MID		= sprintf("GODO%05d",$godo[sno]);	# 상점아이디
		$hashdata	= md5($MID);						# hashdata 생성

		$json = readurl("http://bankmatch.godo.co.kr/sock_listing_unconfirm.php?MID={$MID}&{$query}&hashdata={$hashdata}");
		unset($godo, $query, $hashdata, $MID);

		if ( !preg_match("/^false[ |]*/i",$json) ) { // 성공

		 // inline json_decode... 훗;
			$eval_json = array();

			$_quoted = false;
			$_out = '$eval_json=';

			for ($i=0,$max=strlen($json); $i<$max; $i++) {

				if (! $_quoted) {
					if ($json[$i] == '{')		$_out .= ' array(';
					else if ($json[$i] == '}')	$_out .= ')';
					else if ($json[$i] == ':')	$_out .= '=>';
					else if ($json[$i] == '[')	$_out .= ' array(';
					else if ($json[$i] == ']')	$_out .= ')';
					else $_out .= $json[$i];
				}
				else $_out .= $json[$i];

				if ($json[$i] == '"') $_quoted = ! $_quoted;
			}

			@eval($_out.';');


			if (!empty($eval_json)) {

				$_page = & $eval_json['page'];

				/*
				recode	검색 레코드 수
				total	총 페이지 수
				now	현 페이지 번호
				*/

				$_page['current'] = $_GET['page'];
				if ($_page['total'] && $_page['current']>$_page['total']) $_page['current'] = $_page['total'];
				$_page['start']		= (ceil($_page['current']/10)-1)*10;

				$param = '?date='.$_GET['date'].'&name='.urlencode($_GET['name']);

				if($_page['current']>10){
					$paging .= '
					<a href="'.$_SERVER['PHP_SELF'].$param.'&page=1" class=navi>[1]</a>
					<a href="'.$_SERVER['PHP_SELF'].$param.'&page='.$_page['start'].'" class=navi>◀</a>
					';
				}

				$i=0;
				while($i+$_page['start']<$_page['total']&&$i<10){
					$i++;
					$page[move] = $i+$_page['start'];
					$paging .= ($_page['current']==$page[move]) ? " <b>$page[move]</b> " : " <a href=\"{$_SERVER['PHP_SELF']}{$param}&page=$page[move]\" class=navi>[$page[move]]</a> ";
				}

				if($_page['total']>$page[move]){
					$page[next] = $page[move]+1;
					$paging .= "
					<a href=\"{$_SERVER['PHP_SELF']}{$param}&page=$page[next]\" class=navi>▶</a>
					<a href=\"{$_SERVER['PHP_SELF']}{$param}&page={$_page['total']}\" class=navi>[{$_page['total']}]</a>
					";
				}

				// 리스트
				$_row = array();
				$no = sizeof($eval_json['lists']);
				foreach( $eval_json['lists'] as $row ) {

					$_row['money'] = $row['price'];
					$_row['bank'] = $row['bkname'];
					$_row['name'] = $row['name'];
					$_row['date'] = $row['date'];
					$_row['no'] = $row['no'];

					$loop[] = $_row;

				}
			}


		}
		else { // 실패
			$out = preg_replace("/^false[ |]*-[ |]*/i", "", $json);
			msg('사용할 수 없습니다.','close');
			exit;
		}

	}
	else {
		$pg = Core::loader('page', $_GET['page'], 10);

		$where = array();
		$where[] = "`date` >= '".date('Ymd',strtotime('-'.$ghost_cfg['expire'].' day'))."'";

		if ($_GET['date'] != '')
			$where[] = "`date` = '".$_GET['date']."'";

		if ($_GET['name'] != '')
			$where[] = "`name` like '%".$_GET['name']."%'";


		$pg->field = " * ";

		$pg->setQuery( GD_GHOSTBANKER ,$where,'date ASC, name');
		$pg->exec();

		$res = $db->query($pg->query);

		//$no = mysql_num_rows($res);
		while ($row = $db->fetch($res,1)) {

			$row['no'] = $pg->idx--;
			$loop[] = $row;

		}
		$paging = $pg->page['navi'];

	}


// 데이터 쓱싹
	foreach( $loop as $k=> $row ) {

		if ($ghost_cfg['hide_bank']) {
			if	   (($_pos = strpos($row['bank'],'은행')) !== false) $row['bank'] = substr_replace($row['bank'], sprintf("%'*".$_pos."s", '*'), 0, $_pos);
			elseif (($_pos = strpos($row['bank'],'협')) !== false)	 $row['bank'] = substr_replace($row['bank'], sprintf("%'*".$_pos."s", '*'), 0, $_pos);
			elseif (($_pos = strpos($row['bank'],'금고')) !== false) $row['bank'] = substr_replace($row['bank'], sprintf("%'*".$_pos."s", '*'), 0, $_pos);
			elseif (($_pos = strpos($row['bank'],'뱅크')) !== false) $row['bank'] = substr_replace($row['bank'], sprintf("%'*".$_pos."s", '*'), 0, $_pos);
			else													 $row['bank'] = sprintf("%'*".(strlen($row['bank']))."s", '*');
		}

		$row['money'] = number_format(str_replace(",","",$row['money']));
		if ($ghost_cfg['hide_money']) {
			$row['money'] = preg_replace('/(^[1-9]{1})([0-9,]+)/e','\'\\1\'.(preg_replace(\'/[0-9]/\',\'*\',\'\\2\'))',$row['money']);
		}


		$loop[$k] = $row;
	}

// 출력
	$tpl->template_dir= SHOPROOT.'/data/ghostbanker/tpl/src';
	$tpl->compile_dir = SHOPROOT.'/data/ghostbanker/tpl/cpl';

	if ($ghost_cfg['design_skin_type'] == 'select') {
		$_template = $ghost_cfg['design_skin'].'.htm';
	}
	else {

		if (is_file( SHOPROOT.'/data/ghostbanker/tpl/src/custom.htm' ))
			$_template = 'custom.htm';
		else
			$_template = '1.htm';
	}

	$tpl->define('tpl',$_template);

// 변수 지정 & 프린트
	$tpl->assign('loop', $loop);
	$tpl->assign('paging', $paging);

	$tpl->print_('tpl');
?>
