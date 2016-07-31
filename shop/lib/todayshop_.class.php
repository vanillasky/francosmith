<?php
if (class_exists('todayshop_', false)) return;

class todayshop_ {

	function getPginfo($sub = null) {
		$pg = ($this->cfg['pg'] != '') ? unserialize($this->cfg['pg']) : null;
		return ($pg && $sub)? $pg[$sub] : $pg;
	}

	function getCheckTodayShopPage($design_skinToday) {
		if (is_array($design_skinToday) && empty($design_skinToday) === false) {
			foreach($design_skinToday as $key => $val) {
				if (preg_match('/'.str_replace(array('/','.htm'), array('\/','.php'), $key).'/', $_SERVER['PHP_SELF'])) {
					return true;
				}
			}
		}
		if (preg_match("/\/todayshop\/*/", $_SERVER['PHP_SELF'])) return true;
		return false;
	}


	function getGoodsSortSql($sortOrder) {

		switch($sortOrder) {
			case 'open' : { $orderby = ' ORDER BY startdt ASC, tgsno ASC'; break; }
			case 'close' : { $orderby = ' ORDER BY enddt DESC, tgsno DESC'; break; }
			case 'admin' : { $orderby = ' ORDER BY tg.sort, startdt ASC'; break; }
			case 'random' : { $orderby = ' ORDER BY rand()'; break; }
		}
		return $orderby;
	}

	function getLastDay($year, $month) {

		$month = (int)$month;
		$day31 = array('1','3','5','7','8','10','12');
		$day30 = array('4','6','9','11');
		if (in_array($month, $day31)) return 31;
		elseif (in_array($month, $day30)) return 30;
		elseif ($month == 2) {
			if ($year % 4 == 0) return 29;
			else return 28;
		}
		return 0;
	}

	function getGoods($tgsno) {

		$query = " SELECT tg.*, COALESCE(tc.level,0) AS level
				FROM
					".GD_TODAYSHOP_GOODS_MERGED." AS tg
					LEFT JOIN ".GD_TODAYSHOP_LINK." AS tl ON tl.tgsno=tg.tgsno
					LEFT JOIN ".GD_TODAYSHOP_CATEGORY." AS tc ON tc.category=tl.category
				WHERE
					tg.tgsno='".$tgsno."'
				LIMIT 1";
		$data = $this->db->fetch($query,1);

		return ((int)$data['level'] > (int)$this->level) ? false : $data;
	}

	function getGoodsList( $params = array() ) {


		$result = $arRow = $paging = array();

		// 변수 설정
			$paging['keyword'] = isset($params['keyword']) ? $params['keyword'] : '';
			$paging['category'] = isset($params['category']) ? $params['category'] : '';

			$paging['page'] = isset($params['page']) ? $params['page'] : 1;
			$paging['size'] = isset($params['size']) ? $params['size'] : 10;


		// 검색 절
			$_where = " 1=1 ";

			if ($paging['keyword'] != '')
				$_where .= ' AND TG.goodsnm like \'%'.$paging['keyword'].'%\'';

			if ($paging['category'] != '')
				$_where .= ' AND LNK.category = \''.$paging['category'].'\'';

		// 페이징
			// 총 갯수
			list($paging['total']) =  $this->db->fetch(" SELECT COUNT(tgsno) FROM ".GD_TODAYSHOP_GOODS_MERGED." WHERE ".$_where);

			// 페이지당 레코드수
			$paging['size'];

			// 총 페이지
			$paging['total_page'] = ceil($paging['total'] / $paging['size']);

			// 현재 페이지
			$paging['page'];

			// 시작 레코드
			$paging['start'] = ($paging['page'] - 1) * $paging['size'];

			$paging['total_page'] = ceil($paging['total'] / $paging['size']);
			$block = floor(($paging['page'] - 1) / 10) + 1;
			$spage = (($block - 1) * 10) + 1;
			$lpage = $spage + 10 - 1;
			$lpage = ($paging['total_page'] < $lpage)? $paging['total_page'] : $lpage;

			for($i = $spage; $i <= $lpage; $i++) {
				$tmp['pagenum'] = $i;
				if ($i == $paging['page']) $tmp['curpage'] = 'y';
				$pageinfo[] = $tmp;
				unset($tmp);
			}
			if ($spage > 1) $rtn['prevpage'] = $spage - 1;
			if ($lpage < $paging['total_page']) $rtn['nextpage'] = $lpage + 1;
			$rtn['page'] = $pageinfo;


		// 레코드 가져오기
			$query = "
			SELECT
				DISTINCT TG.tgsno, TG.startdt, TG.enddt,TG.goodsnm, TG.img_i, TG.img_s, TG.sms, TG.limit_ea, TG.showtimer,

				TG.runout, (TG.fakestock + TG.buyercnt) AS buyercnt,

				GO.stock, GO.price, GO.consumer,
				COALESCE(TC.level,0) AS level

			FROM ".GD_TODAYSHOP_GOODS_MERGED." AS TG

			LEFT JOIN ".GD_GOODS_OPTION." AS GO
			ON TG.goodsno = GO.goodsno AND GO.link = 1 and go_is_deleted <> '1' and go_is_display = '1'

			LEFT JOIN ".GD_TODAYSHOP_LINK." AS LNK
			ON TG.tgsno = LNK.tgsno

			LEFT JOIN ".GD_TODAYSHOP_CATEGORY." AS TC
			ON TC.category=LNK.category

			WHERE
				".$_where."

			LIMIT ".$paging['start'].", ".$paging['size']."
			";

			$rs = $this->db->query($query);

			while ($row = $this->db->fetch($rs,1)) {
				$arRow[] = $row;
			}

		// 결과..
			$result['paging'] = $rtn;
			$result['data'] = $arRow;

		return $result;
	}

    function getGoodsByDate($dt='', $category=''){

		$orderby = $this->getGoodsSortSql($this->cfg['sortOrder']);

		$sql = " SELECT distinct tg.tgsno, tg.startdt, tg.enddt, go.price, go.consumer, tg.goodsnm, tg.img_i, tg.img_s, tg.sms, tg.limit_ea, tg.showtimer, COALESCE(tc.level,0) AS level ";
//		$sql .= "	,IF ((tg.enddt IS NOT NULL AND tg.enddt < now()) OR g.runout=1, 'y', 'n') AS tgout ";
		$sql .= " FROM ".GD_TODAYSHOP_GOODS_MERGED." AS tg ";
		$sql .= " LEFT JOIN ".GD_GOODS_OPTION." AS go ON tg.goodsno=go.goodsno AND go.link=1 and go_is_deleted <> '1' and go_is_display = '1' ";
		if ($category) {
			$sql .= " INNER JOIN ".GD_TODAYSHOP_LINK." AS tl ON tl.tgsno=tg.tgsno AND tl.category='".$category."' ";
		}
		else {
			$sql .= " LEFT JOIN ".GD_TODAYSHOP_LINK." AS tl ON tl.tgsno=tg.tgsno";
		}
		$sql .= " LEFT JOIN ".GD_TODAYSHOP_CATEGORY." AS tc ON tc.category=tl.category";  // 2011.04.06 카테고리수정
		$sql .= " WHERE tg.visible=1 ";
		if (empty($dt)) {
			$sql .= "	AND (tg.startdt IS NULL OR now() >= tg.startdt)";
			$sql .= "	AND (tg.enddt IS NULL OR now() <= tg.enddt)";
			$sql .= "	AND tg.runout = 0";
		}
		else {
			$sql .= "	AND (tg.startdt IS NULL OR DATE_FORMAT('".$dt."','%Y-%m-%d') >= DATE_FORMAT(tg.startdt,'%Y-%m-%d'))";
			$sql .= "	AND (tg.enddt IS NULL OR DATE_FORMAT('".$dt."','%Y-%m-%d') <= DATE_FORMAT(tg.enddt,'%Y-%m-%d'))";
		}
		$sql .= " GROUP BY tg.tgsno ";
		$sql .= $orderby;
		$res = $this->db->query($sql);

		$rtn = array();
		while($data = $this->db->fetch($res, 1)) {
			if ((int)$data['level'] > (int)$this->level) continue;
			$rtn[] = $data;
		}

		unset($data, $res);
		return $rtn;
    }

    function getGoodsByMonth($year, $month, $category=''){

		$month = ($month < 10)? '0'.(int)$month : (int)$month;
		$lastDay = $this->getLastDay($year, $month);

		/*$starttime = mktime(23, 59, 59, (int)$month, (int)$lastDay, $year);
		if (time() > $starttime) $startsql = "now()";
		else*/
		$startsql = "'".$year."-".$month."-".$lastDay."'";

		$orderby = $this->getGoodsSortSql($this->cfg['sortOrder']);
		$intDt = (int)($year.$month);

		$sql = " SELECT distinct tg.tgsno, tg.startdt, tg.enddt, SUBSTR(tg.startdt,1,10) AS sdt, SUBSTR(tg.enddt,1,10) AS edt, tg.sort, tg.goodsnm, tg.runout, tg.img_s, COALESCE(tc.level,0) AS level ";
		$sql .= " , IF ((tg.enddt IS NOT NULL AND tg.enddt < now()) OR tg.runout=1, 'y', 'n') AS tgout ";
		$sql .= " , IF ((tg.startdt IS NOT NULL AND tg.startdt > now()), 'y', 'n') AS scheduled ";
		$sql .= " FROM ".GD_TODAYSHOP_GOODS_MERGED." AS tg ";
		if ($category) {
			$sql .= " INNER JOIN ".GD_TODAYSHOP_LINK." AS tl ON tl.tgsno=tg.tgsno AND tl.category='".$category."' ";
		}
		else {
			$sql .= " LEFT JOIN ".GD_TODAYSHOP_LINK." AS tl ON tl.tgsno=tg.tgsno ";
		}
		$sql .= " LEFT JOIN ".GD_TODAYSHOP_CATEGORY." AS tc ON tc.category=tl.category ";  // 2011.04.06 카테고리수정
		$sql .= " WHERE tg.visible=1 ";
		$sql .= "	AND (";
		$sql .= "		(tg.startdt IS NOT NULL AND DATE_FORMAT(tg.startdt,'%Y-%m-%d') <= ".$startsql.")";
		$sql .= "		OR tg.startdt IS NULL";
		$sql .= "	)";
		$sql .= "	AND (";
		$sql .= "		(tg.enddt IS NOT NULL AND DATE_FORMAT(tg.enddt,'%Y-%m-%d') >= '".$year."-".$month."-01')";
		$sql .= "		OR tg.enddt IS NULL";
		$sql .= "	)";

		$sql .= $orderby;
		$res = $this->db->query($sql);

		$rtn = array();

		$startDay = 1;

		while($data = $this->db->fetch($res, 1)) {
			if ((int)$data['level'] > (int)$this->level) continue;

			$arrSdt = explode('-', $data['sdt']);
			$arrEdt = explode('-', $data['edt']);

			$g_endDay = $lastDay;
			$g_startDay = $startDay;

			if (empty($arrSdt) === false) {
				$intSdt = (int)($arrSdt[0].$arrSdt[1]);
				if ($intSdt < $intDt) $g_startDay = 1;
				elseif ($intSdt == $intDt) $g_startDay = (int)$arrSdt[2];
			}
			else $intSdt = (int)($year.$month);

			if (empty($arrEdt) === false) {
				$intEdt = (int)($arrEdt[0].$arrEdt[1]);
				if ($intEdt > $intDt) $g_endDay = $lastDay;
				elseif ($intEdt == $intDt) {
					$g_endDay = (int)$arrEdt[2];
				}
			}
			else $intEdt = (int)($year.$month);
			unset($arrSdt, $arrEdt);

			$data['closed'] = $data['tgout'];
			for($i = $g_startDay; $i <= $g_endDay; $i++) {
				$day = ($i < 10)? '0'.$i : $i;
				$rtn[$month.$day][] = $data;
			}
		}

		unset($data, $res);
		return $rtn;
    }

	function getTalk(&$member, $tgsno, $page=1, $perPage=10) {

		if (!is_numeric($page) || $page < 1) $page = 1;
		if (!is_numeric($perPage) || $perPage < 1) $perPage = 10;
		$start = ($page - 1) * $perPage;
		$sql = "SELECT * FROM (";
		$sql .= "(SELECT * FROM ".GD_TODAYSHOP_TALK." WHERE notice>0 AND tgsno IN (0, ".$tgsno."))";
		$sql .= "UNION ALL ";
		$sql .= "(SELECT * FROM ".GD_TODAYSHOP_TALK." WHERE notice=0 AND tgsno=".$tgsno." ORDER BY gid DESC, HEX(thread) LIMIT ".$start.", ".$perPage.")";
		$sql .= ") AS t ORDER BY notice DESC, tgsno ASC, gid DESC, HEX(thread)";
		$res = $this->db->query($sql);
		while($data = $this->db->fetch($res, 1)) {
			foreach($data as $key => $val) {
				$tmp[$key] = $val;
			}
			$tmp['step'] = strlen($tmp['thread']) / 2;
			$tmp['auth'] = ($member['level'] >= 80 || $tmp['m_no'] == $member['m_no'])? 'y' : 'n';
			$rtn[] = $tmp;
		}
		return $rtn;
	}

	function getTalkPager($tgsno, $page=1, $perPage=10, $perBlock=10) {

		if (!is_numeric($page) || $page < 1) $page = 1;
		if (!is_numeric($perPage) || $perPage < 1) $perPage = 10;
		$sql = "SELECT COUNT(*) AS cnt FROM ".GD_TODAYSHOP_TALK." WHERE notice=0 AND tgsno=".$tgsno;
		$res = $this->db->fetch($sql, 1);

		$totalpage = ceil($res['cnt'] / $perPage);
		$block = floor(($page - 1) / $perBlock) + 1;
		$spage = (($block - 1) * $perBlock) + 1;
		$lpage = $spage + $perBlock - 1;
		$lpage = ($totalpage < $lpage)? $totalpage : $lpage;

		for($i = $spage; $i <= $lpage; $i++) {
			$tmp['pagenum'] = $i;
			if ($i == $page) $tmp['curpage'] = 'y';
			$pageinfo[] = $tmp;
			unset($tmp);
		}
		if ($spage > 1) $rtn['prevpage'] = $spage - 1;
		if ($lpage < $totalpage) $rtn['nextpage'] = $lpage + 1;
		$rtn['page'] = $pageinfo;

		return $rtn;
	}

	function writeTalk($mode, $tgsno, $ttsno, &$member, $comment, $notice='0', $allgoods='n') {

		if (!isset($member['m_no']) || !isset($member['name'])) return '권한이 없습니다.';

		if (!class_exists('todayshop_cache', false)) {
			require_once( dirname(__FILE__).'/todayshop_cache.class.php' );
		}
		todayshop_cache::remove($tgsno, 'todaytalk');

		switch($mode) {
			case 'regist': {
				// 전체상품에 대한 공지사항.
				if ($notice && $allgoods == 'y') $tgsno=0;

				$sql = "SELECT MAX(gid) AS gid FROM ".GD_TODAYSHOP_TALK;
				$data = $this->db->fetch($sql);
				$gid = $data['gid'] + 1;
				unset($data);

				$arInsert['tgsno'] = $tgsno;
				$arInsert['gid'] = $gid;
				$arInsert['thread'] = '';
				$arInsert['m_no'] = $member['m_no'];
				$arInsert['writer'] = $member['name'];
				$arInsert['comment'] = $comment;
				if ($notice) $arInsert['notice'] = $notice;
				$arInsert['ip'] = $_SERVER['REMOTE_ADDR'];
				$query = $this->db->_query_print('INSERT INTO '.GD_TODAYSHOP_TALK.' SET [cv], regdt=now()', $arInsert);
				break;
			}
			case 'reply': {
				$sql = "SELECT tgsno, gid, thread FROM ".GD_TODAYSHOP_TALK." WHERE ttsno=".$ttsno;
				$data = $this->db->fetch($sql, 1);
				$tgsno = $data['tgsno'];
				$gid = $data['gid'];
				$pThread = $data['thread'];
				unset($data);

				$sql = "SELECT SUBSTR(thread, -2) AS thread FROM ".GD_TODAYSHOP_TALK." WHERE gid=".$gid." AND LENGTH(thread)=".(strlen($pThread)+2)." AND SUBSTR(thread,1,".strlen($pThread).")='".$pThread."' ORDER BY HEX(thread) LIMIT 1";
				$data = $this->db->fetch($sql, 1);
				$thread = $data['thread'];
				unset($data);

				if (empty($thread)) $thread = $pThread.'zz';
				else {
					// Thread 순서 : 소문자역순,대문자역순,숫자역순
					$oldTh[0] = substr($thread,0,1);
					$oldTh[1] = substr($thread,-1);
					$newTh = $oldTh;
					switch($oldTh[1]) {
						case 'a' : { $newTh[1] = 'Z'; break; }
						case 'A' : { $newTh[1] = '9'; break; }
						case '0' : {
							$newTh[1] = 'z';
							switch($oldTh[0]) {
								case 'a' : { $newTh[0] = 'Z'; break; }
								case 'A' : { $newTh[0] = '9'; break; }
								case '0' : { return '댓글 가능 수량을 초과하였습니다.'; }
								default : { $newTh[0] = chr(ord($oldTh[0]) - 1); break; }
							}
							break;
						}
						default : {
							$newTh[1] = chr(ord($oldTh[1]) - 1);
							break;
						}
					}
					$thread = $pThread.implode('', $newTh);
				}

				$arInsert['tgsno'] = $tgsno;
				$arInsert['gid'] = $gid;
				$arInsert['thread'] = $thread;
				$arInsert['m_no'] = $member['m_no'];
				$arInsert['writer'] = $member['name'];
				$arInsert['comment'] = $comment;
				$arInsert['ip'] = $_SERVER['REMOTE_ADDR'];
				$query = $this->db->_query_print('INSERT INTO '.GD_TODAYSHOP_TALK.' SET [cv], regdt=now()', $arInsert);
				break;
			}
			case 'edit': {
				$editable = false;
				if ($member['level'] >= 80) $editable = true;
				else {
					$sql = "SELECT m_no FROM ".GD_TODAYSHOP_TALK." WHERE ttsno=".$ttsno;
					$data = $this->db->fetch($sql);
					if ($data['m_no'] == $member['m_no']) $editable = true;
				}

				if (!$editable) return '권한이 없습니다.';

				$arInsert['comment'] = $comment;
				$query = $this->db->_query_print('UPDATE '.GD_TODAYSHOP_TALK.' SET [cv] WHERE ttsno=[i]', $arInsert, $ttsno);
				break;
			}
		}
		$this->db->query($query);

	}

	function removeTalk($ttsno, &$member) {

		$editable = false;
		$sql = "SELECT tt1.tgsno, tt1. m_no, COUNT(*) AS cnt FROM ".GD_TODAYSHOP_TALK." AS tt1 JOIN ".GD_TODAYSHOP_TALK." AS tt2 ON tt1.gid=tt2.gid AND tt1.ttsno<>tt2.ttsno AND tt1.thread=SUBSTR(tt2.thread,1,LENGTH(tt1.thread)) WHERE tt1.ttsno=".$ttsno;
		$data = $this->db->fetch($sql, 1);

		if ($member['level'] >= 80 || $data['m_no'] == $member['m_no']) $editable = true;
		if (!$editable) return '권한이 없습니다.';

		if ($data['cnt'] == 0) $sql = "DELETE FROM ".GD_TODAYSHOP_TALK." WHERE ttsno=".$ttsno;
		else $sql = "UPDATE ".GD_TODAYSHOP_TALK." SET remove='y' WHERE ttsno=".$ttsno;
		$this->db->fetch($sql);

		if (!class_exists('todayshop_cache', false)) {
			require_once( dirname(__FILE__).'/todayshop_cache.class.php' );
		}
		todayshop_cache::remove($data['tgsno'], 'todaytalk');

	}

	// 달력모양에 맞게 배열생성
	function getCalendar($year, $month, $mode) {

		switch($mode) {
			case 'cal' : {
				$month = ($month < 10)? '0'.(int)$month : (int)$month;

				$fday = getdate(mktime(0, 0, 0, $month, 1, $year));
				$lastDay = $this->getLastDay($year, $month);
				$lday = getdate(mktime(0, 0, 0, $month, $lastDay, $year));
				$lastWday = $lday['wday'];

				for($i = 0; $i < $fday['wday']; $i++) $rtn[] = '';
				for($i = 1; $i <= $lastDay; $i++) {
					$day = ($i < 10)? '0'.$i : $i;
					$rtn[] = array('day'=>(int)$day, 'date'=>$month.$day, 'wday'=>($fday['wday']+$i-1)%7);
				}
				for($i = $lastWday; $i < 6; $i++) $rtn[] = '';
				break;
			}
			case 'list' : {
				$month = ($month < 10)? '0'.(int)$month : (int)$month;

				$fday = getdate(mktime(0, 0, 0, $month, 1, $year));
				$lastDay = $this->getLastDay($year, $month);

				for($i = $lastDay; $i >= 1; $i--) {
					$day = ($i < 10)? '0'.$i : $i;
					$rtn[] = array('day'=>(int)$day, 'date'=>$month.$day, 'wday'=>($fday['wday']+$i-1)%7);
				}

				break;
			}
		}

		return $rtn;
	}

	// 달력 네비(전달, 다음달)
	function getMonthNavi($year, $month) {

		$month = (int)$month;
		$rtn['beforeyear'] = $rtn['nextyear'] = $rtn['year'] = $year;
		$rtn['month'] = ($month < 10)? '0'.$month : $month;
		$rtn['beforemonth'] = $month - 1;
		$rtn['nextmonth'] = $month + 1;

		switch((int)$month) {
			case 1 : {
				$rtn['beforeyear'] = $year - 1;
				$rtn['beforemonth'] = '12';
				break;
			}
			case 12 : {
				$rtn['nextyear'] = $year + 1;
				$rtn['nextmonth'] = '01';
				break;
			}
			default : {
				$rtn['beforemonth'] = ($rtn['beforemonth'] < 10)? '0'.$rtn['beforemonth'] : $rtn['beforemonth'];
				$rtn['nextmonth'] = ($rtn['nextmonth'] < 10)? '0'.$rtn['nextmonth'] : $rtn['nextmonth'];
				break;
			}
		}

		foreach($rtn as $key => $val) {
			$rtn[$key] = (string)$val;
		}
		return $rtn;
	}

	// 날짜 네비(전날, 다음날)
	function getDateNavi($year, $month, $day) {

		$year = (int)$year;
		$month = (int)$month;
		$day = (int)$day;
		$tm = mktime(0, 0, 0, $month, $day, $year);
		$beforeDay = $tm - (60 * 60 * 24);
		$nextDay = $tm + (60 * 60 * 24);

		$bd = getdate($beforeDay);
		$nd = getdate($nextDay);

		$rtn['year'] = (string)$year;
		$rtn['month'] = ($month < 10)? '0'.$month : (string)$month;
		$rtn['day'] = ($day < 10)? '0'.$day : (string)$day;

		$rtn['beforeyear'] = (string)$bd['year'];
		$rtn['beforemonth'] = ($bd['mon'] < 10)? '0'.$bd['mon'] : (string)$bd['mon'];
		$rtn['beforeday'] = ($bd['mday'] < 10)? '0'.$bd['mday'] : (string)$bd['mday'];

		$rtn['nextyear'] = (string)$nd['year'];
		$rtn['nextmonth'] = ($nd['mon'] < 10)? '0'.$nd['mon'] : (string)$nd['mon'];
		$rtn['nextday'] = ($nd['mday'] < 10)? '0'.$nd['mday'] : (string)$nd['mday'];

		return $rtn;
	}

	function getCurrentDate() {

		$now = getdate();
		$rtn['year'] = $now['year'];
		$rtn['month'] = $now['mon'];
		$rtn['day'] = $now['mday'];
		unset($now);
		return $rtn;
	}

	function getSnsPostBtn($args){

		mb_internal_encoding('EUC-KR'); // 인코딩 EUC-KR 설정.

		// 트위터
		$msg = '[{shopnm}] {goodsnm}'.chr(13).'{goodsurl}';
		$msg = preg_replace('/{shopnm}/i', $args['shopnm'], $msg);
		$msg = preg_replace('/{goodsurl}/i', $args['goodsurl'], $msg);
		$msg_length = mb_strlen(preg_replace('/{goodsnm}/i', '', $msg));
		$tw_goodsnm = $args['goodsnm'];
		if ($msg_length <= 140) $tw_goodsnm = mb_substr($args['goodsnm'], 0, 140 - $msg_length);
		$msg = preg_replace('/{goodsnm}/i', $tw_goodsnm, $msg);
		$encodedMsg = urlencode(@iconv('EUC-KR', 'UTF-8//IGNORE', $msg));
		$twitterurl = 'http://twitter.com/home?status='.$encodedMsg;
		// 트위터

		// 페이스북
		$msg = '[{shopnm}] {goodsnm}';
		$msg = preg_replace('/{shopnm}/i', $args['shopnm'], $msg);
		$msg = preg_replace('/{goodsnm}/i', $args['goodsnm'], $msg);
		$msg = preg_replace('/{goodsurl}/i', $args['goodsurl'], $msg);
		$encodedMsg = urlencode(@iconv('EUC-KR', 'UTF-8//IGNORE', $msg));
		$facebookurl = 'http://www.facebook.com/sharer.php?u='.urlencode($args['goodsurl'].'&time='.time());
		// 페이스북

		// 미투데이
		$tmpmsg = '[{shopnm}] "{goodsnm}":{goodsurl}';
		$tmpmsg = preg_replace('/{shopnm}/i', $args['shopnm'], $tmpmsg);
		$tmpmsg = preg_replace('/{goodsnm}/i', $args['goodsnm'], $tmpmsg);
		$tmpmsg = preg_replace('/{goodsurl}/i', $args['goodsurl'], $tmpmsg);
		$tmpmsg = preg_replace('/\"([^\"]*)\":http:\/\/[^\s]*/i', '$1', $tmpmsg);
		$msg_length = mb_strlen($tmpmsg);

		$msg = '[{shopnm}] "{goodsnm}":{goodsurl}';
		$msg = preg_replace('/{shopnm}/i', $args['shopnm'], $msg);
		$me_goodsnm = $args['goodsnm'];
		if ($msg_length > 150) $me_goodsnm = mb_substr($args['goodsnm'], 0, 150 - $msg_length);
		$msg = preg_replace('/{goodsnm}/i', $args['goodsnm'], $msg);
		$msg = preg_replace('/{goodsurl}/i', $args['goodsurl'], $msg);

		$encodedMsg = urlencode(@iconv('EUC-KR', 'UTF-8//IGNORE', $msg));
		$tag = ($args['tag'])? $args['tag'] : $args['shopnm'];
		$encodedTag = urlencode(@iconv('EUC-KR', 'UTF-8//IGNORE', $tag));
		$me2dayurl = 'http://me2day.net/posts/new?new_post[body]='.$encodedMsg.'&new_post[tags]='.$encodedTag;
		// 미투데이

		// 페이스북 메타 TAG
		$title_fb ='[{shopnm}] {goodsnm}';
		$title_fb = preg_replace('/{shopnm}/i', $args['shopnm'], $title_fb);
		$title_fb = preg_replace('/{goodsnm}/i', $args['goodsnm'], $title_fb);
		$title_fb = preg_replace('/{goodsurl}/i', $args['goodsurl'], $title_fb);
		$title_fb = htmlspecialchars($title_fb);
		$rtn['meta'] = '<meta property="og:title" content="'.$title_fb.'" />';
		$rtn['meta'] .= '<meta property="og:description" content="'.$title_fb.'" />';
		if (preg_match('/^http:\/\//',$args['img'])) $rtn['meta'] .= '<meta property="og:image" content="'.$args['img'].'" />';
		else $rtn['meta'] .= '<meta property="og:image" content="http://'.$_SERVER['HTTP_HOST'].'/shop/data/goods/'.$args['img'].'" />';

		// 버튼 TAG
		$rtn['twitter'] = $twitterurl;
		$rtn['facebook'] = $facebookurl;
		$rtn['me2day'] = $me2dayurl;
		return $rtn;
	}

	function encor($tgsno, $m_no) {

		if (!$m_no || !is_numeric($m_no)) return '로그인한 회원만 사용이 가능합니다.';

		$query = $this->db->_query_print('SELECT COUNT(*) AS cnt FROM '.GD_TODAYSHOP_ENCOR.' WHERE m_no=[s] AND tgsno=[i]', $m_no, $tgsno);
		$res = $this->db->_select($query);

		if ($res[0]['cnt'] == 0) {
			$query = $this->db->_query_print('INSERT INTO '.GD_TODAYSHOP_ENCOR.' SET m_no=[s], tgsno=[i], regdt=now()', $m_no, $tgsno);
			$this->db->query($query);

			$query = $this->db->_query_print('UPDATE '.GD_TODAYSHOP_GOODS_MERGED.' SET encor=(SELECT COUNT(*) FROM '.GD_TODAYSHOP_ENCOR.' WHERE tgsno=[i]) WHERE tgsno=[i]', $tgsno, $tgsno);
			$this->db->query($query);

			$query = $this->db->_query_print('UPDATE '.GD_TODAYSHOP_GOODS.' SET encor=(SELECT COUNT(*) FROM '.GD_TODAYSHOP_ENCOR.' WHERE tgsno=[i]) WHERE tgsno=[i]', $tgsno, $tgsno);
			$this->db->query($query);
		}
		else return '이미 앵콜요청을 하셨습니다.';
	}

	function makeSmsMsg($msg) {

		$len = strlen($msg);
		$buf = '';
		$str = '';
		for($i = 0; $i < strlen($msg); $i++) {
			$buf = substr($msg, $i, 1);
			if (ord($buf) > 127) {
				if (strlen($str) == 79) {
					$rtn[] = $str;
					$str = '';
				}

				$buf = substr($msg, $i, 2);
				$i++;
			}
			$str .= $buf;
			if (strlen($str) == 80 || $i == strlen($msg) - 1) {
				$rtn[] = $str;
				$str = '';
			}
		}
		return $rtn;
	}

	function sendSms($tgsno, $rcvphone, $callback) {

		GLOBAL $cfg;
		$query = $this->db->_query_print('SELECT sms FROM '.GD_TODAYSHOP_GOODS_MERGED.' WHERE tgsno=[i]', $tgsno);
		$res = $this->db->_select($query);
		$msg = $this->makeSmsMsg($res[0]['sms']);
		unset($res);

		$sms = Core::loader('sms');
		$smsPt = preg_replace('/[^0-9-]*/', '', $sms->smsPt);
		if ($sms->smsPt >= count($msg)) {
			for($i = 0; $i < count($msg); $i++) {
				$rtn = $sms->send($msg[$i],$rcvphone,$callback);
				if (!$rtn) return 'SMS 서비스를 사용할 수 없습니다.';
				$sms->update();
			}
			return 'SMS가 발송되었습니다.';
		}
		else return 'SMS 서비스를 사용할 수 없습니다.';
	}

	function getCategory($all = false) {

		if (array_shift(explode('.',phpversion())) > 4) {
			static $tmpcategory = null;
			static $sort;
		}
		else {
			$tmpcategory = null;
			$sort;
		}

		if ($tmpcategory == null) {

			$query = "
			SELECT
				tc.category, tc.catnm, tc.hidden, tc.sort, COALESCE(tc.level,0) AS level, COALESCE(sub.cnt,0) AS cnt
			FROM ".GD_TODAYSHOP_CATEGORY." AS tc
			LEFT JOIN (
						SELECT
								tl.category, count(tl.category) AS cnt
						FROM ".GD_TODAYSHOP_LINK." AS tl
						INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS tg
						ON
							tl.tgsno=tg.tgsno
						WHERE tl.hidden=0 AND tg.visible=1
							AND COALESCE(tg.startdt, NOW()) <= NOW()
							AND COALESCE(tg.enddt,	 NOW()) >= NOW()
						GROUP BY tl.category
						) AS sub
			ON tc.category = sub.category
			WHERE tc.hidden=0 AND COALESCE(tc.level,0) <='".$this->level."' ORDER BY LENGTH(tc.category);
			";

			$res = $this->db->query($query);
			$tmpcategory = array();
			$sort = array();

			$glue = '>';

			while ($row = $this->db->fetch($res,1)){

				$depth = strlen($row['category']) / 3;
				$parent = ($depth > 1) ? substr($row['category'],0,($depth-1)*3 ) : null;

				$tmpcatnm = array();
				if ($tmpcategory[$parent]) $tmpcatnm[] = $tmpcategory[$parent]['catnm'];

				if (empty($tmpcatnm)===false) $tmpcatnm = implode($tmpcatnm, $glue).$glue;
				else $tmpcatnm = '';

				$tmpcategory[$row['category']] = array(	'catnm'=>$tmpcatnm.$row['catnm'],
														'category'=>$row['category'],
														'sort'=> (is_null($parent) ? $row['sort'] : $tmpcategory[$parent]['sort'] + $row['sort'] / pow(1000,$depth-1)),
														'cnt'=>$row['cnt']);

				// multi sort 에서 사용할 값
				$sort['sort'][]		= $tmpcategory[$row['category']]['sort'];
				$sort['category'][] = $tmpcategory[$row['category']]['category'];
			}
		}

		$category = array();
		if (empty($tmpcategory)===false) {

			array_multisort(
								$sort['sort'], SORT_ASC, SORT_NUMERIC,
								$sort['category'], SORT_ASC, SORT_STRING,
								$tmpcategory);

			foreach($tmpcategory as $key => $val) {
				if ($all === false && $val['cnt'] == 0) continue;
				$_val = array(
					'catnm'=>$val['catnm'],
					'category'=>$val['category'],
					'cnt'=>$val['cnt'],
					'sort'=>$val['sort']
					);
				$category[] = $_val;
			}
		}

		return $category;
	}

	/**
		쿠폰 발행
	 */
	function publishCoupon($ordno) {

		$formatter = Core::loader('stringFormatter');
		$couponGenerator = Core::loader('couponGenerator');
		$sms = Core::loader('sms');

		// 쿠폰인 투데이샵 상품을 가져
		$query = "
			SELECT

				A.ordno,A.mobileReceiver,

				B.ea

			FROM ".GD_ORDER." AS A

			INNER JOIN ".GD_ORDER_ITEM." AS B
			ON A.ordno = B.ordno

			INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS D
			ON B.goodsno = D.goodsno

			WHERE A.ordno = $ordno AND D.goodstype = 'coupon' AND D.processtype = 'i'	/* 즉시 발급인 투데이샵 쿠폰 상품을 가져옴 */
		";

		if ($data = $this->db->fetch($query)) {

			// 쿠폰 번호 생성
			$couponGenerator->max = 1;
			$couponGenerator->length = 12;			// 자리수 (prefix 포함)
			//$couponGenerator->prefix = 'GD';		// prefix

			$_unique = false;

			do {

				$couponGenerator->make();
				$_coupon = array_pop($couponGenerator->coupon);

				list($cnt) = $this->db->fetch("SELECT COUNT(cp_sno) FROM gd_todayshop_order_coupon WHERE cp_num = '$_coupon'");

				if ($cnt < 1) {

					// 저어장
					$query = "
					INSERT INTO ".GD_TODAYSHOP_ORDER_COUPON." SET
						ordno = '$data[ordno]',
						cp_num = '$_coupon',
						cp_ea = '$data[ea]',
						cp_publish = 'y',
						regdt = NOW()
					";

					if ($this->db->query($query)) {
						return $this->db->lastID();
						$_unique = true;
					}
				}

			} while (!$_unique);

			return $_coupon;

		}
		return false;



	}	// eof publishCoupon

	// 해당 상품의 카테고리 가져오기 (맨처음 한개만)
	function getCurCategory($tgsno) {

		$query = " SELECT tc.category, COALESCE(tc.level,0) AS level
				FROM
					".GD_TODAYSHOP_GOODS_MERGED." AS tg
					INNER JOIN ".GD_TODAYSHOP_LINK." AS tl ON tg.tgsno=tl.tgsno AND tl.hidden = 0
					INNER JOIN ".GD_TODAYSHOP_CATEGORY." AS tc ON tl.category=tc.category AND tc.hidden = 0
				WHERE
					tg.tgsno='".$tgsno."'
				ORDER BY tl.sno
				";

		$rs = $this->db->query($query);

		while ($data = $this->db->fetch($rs,1)) {
			if ((int)$data['level'] <= (int)$this->level) break;
		}

		$arCate = $this->getCategory();

		foreach($arCate as $key => $val) {
			if ($val['category'] == $data['category']) {
				$rtn = $val;
				break;
			}
		}
		unset($arCate, $key, $val);
		return $rtn;
	}

	// 상품페이지의 상품요약정보 (옵션, 구매량)
	function getGoodsSummary($tgsno) {

		$query = " SELECT tg.fakestock, tg.buyercnt, go.optno, go.opt1, go.opt2, go.price, go.stock, go.consumer, tg.runout
				FROM
					".GD_TODAYSHOP_GOODS_MERGED." AS tg
					LEFT JOIN ".GD_GOODS_OPTION." AS go ON tg.goodsno=go.goodsno and go_is_deleted <> '1' and go_is_display = '1'
				WHERE
					tg.tgsno='".$tgsno."'
				ORDER BY link DESC, sno ASC ";
		$res = $this->db->query($query);
		while($data = $this->db->fetch($res, 1)) {
			$rtn[] = $data;
		}
		return $rtn;
	}

	// 상품리스트의 상품요약정보 (옵션, 구매량)
	function getListSummary($dt, $category='') {

		$orderby = $this->getGoodsSortSql($this->cfg['sortOrder']);

		$sql = " SELECT distinct tg.tgsno, tg.fakestock, tg.buyercnt, tg.usestock, tg.totstock, tg.runout, COALESCE(tc.level,0) AS level ";
		$sql .= " FROM ".GD_TODAYSHOP_GOODS_MERGED." AS tg ";
		if ($category) {
			$sql .= " INNER JOIN ".GD_TODAYSHOP_LINK." AS tl ON tl.tgsno=tg.tgsno AND tl.category='".$category."' ";
		}
		else {  // 2011.04.06 카테고리수정
			$sql .= " LEFT JOIN ".GD_TODAYSHOP_LINK." AS tl ON tl.tgsno=tg.tgsno ";
		}
		$sql .= " LEFT JOIN ".GD_TODAYSHOP_CATEGORY." AS tc ON tc.category=tl.category ";  // 2011.04.06 카테고리수정
		$sql .= " WHERE tg.visible=1 ";
		$sql .= "	AND (tg.startdt IS NULL OR DATE_FORMAT('".$dt."','%Y-%m-%d') >= DATE_FORMAT(tg.startdt,'%Y-%m-%d'))";
		$sql .= "	AND (tg.enddt IS NULL OR DATE_FORMAT('".$dt."','%Y-%m-%d') <= DATE_FORMAT(tg.enddt,'%Y-%m-%d'))";
		$sql .= $orderby;
		$res = $this->db->query($sql);

		$rtn = array();
		while($data = $this->db->fetch($res, 1)) {
			if ((int)$data['level'] > (int)$this->level) continue;
			$rtn[] = $data;
		}
		unset($data, $res);
		return $rtn;
	}

	function interest() {

		static $cfg = null;

		if ($cfg == null)
			$cfg = unserialize(stripslashes($this->cfg['interest']));

		return $cfg;

	}

}
?>
