<?
if(!$cfg) @include dirname(__FILE__)."/../conf/config.php";
if (!$_COOKIE['godoLog'] && $cfg['counterYN'] != '2'){

	$db->silent(true);	// DB 오류 메시지 출력 안함

	# 정보 획득
	function get_agent($str)
	{
		$str = strtolower($str);

		$out['raw'] = $str;
		$out['os'] = $out['browser'] = '';


		// 크롤러가 아닐때만.
		if ((preg_match("/(bot|http|slurp)/",$str) || preg_match("/^Microsoft/",$str)) && strpos($str,'bsalsa.com') === false) {
			$out['os'] = "Search Robot";

			// 크롤러인 경우에는 도메인을 뽑아서 입력.
			preg_match("/[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})/",$str,$match);
			$out['browser'] = $match[0];

		}
		else {

			// 사용 OS

				if (strpos($str,'windows nt 5.1') !== false)		$out['os'] = 'Windows XP';
				elseif (strpos($str,'windows xp') !== false)		$out['os'] = 'Windows XP';
				elseif (strpos($str,'windows nt 6.0') !== false)	$out['os'] = 'Windows Vista';
				elseif (strpos($str,'windows nt 6.1') !== false)	$out['os'] = 'Windows 7';
				elseif (strpos($str,'windows nt 5.2') !== false)	$out['os'] = 'Windows 2003';
				elseif (strpos($str,'windows 95') !== false)		$out['os'] = 'Windows 95';
				elseif (strpos($str,'windows 98') !== false)		$out['os'] = 'Windows 98';
				elseif (strpos($str,'windows 9x') !== false)		$out['os'] = 'Windows ME';
				elseif (strpos($str,'windows ce') !== false)		$out['os'] = 'Windows CE';
				elseif (strpos($str,'windows nt 4.') !== false)		$out['os'] = 'Windows NT';
				elseif (strpos($str,'windows nt 5.0') !== false)	$out['os'] = 'Windows 2000';
				elseif (strpos($str,'windows nt') !== false)		$out['os'] = 'Windows NT';
				elseif (strpos($str,'winnt') !== false)				$out['os'] = 'Windows NT';
				elseif (strpos($str,'win95') !== false)				$out['os'] = 'Windows 95';

				elseif (strpos($str,'mac') !== false)				$out['os'] = 'Mac OS';
				elseif (strpos($str,'darwin') !== false)			$out['os'] = 'Darwin';
				elseif (strpos($str,'android') !== false)			$out['os'] = 'Android';

				elseif (strpos($str,'linux') !== false)				$out['os'] = 'Linux';
				elseif (strpos($str,'freebsd') !== false)			$out['os'] = 'FreeBSD';
				elseif (strpos($str,'openbsd') !== false)			$out['os'] = 'OpenBSD';
				elseif (strpos($str,'netbsd') !== false)			$out['os'] = 'NetBSD';
				elseif (strpos($str,'sunos') !== false)				$out['os'] = 'SunOS';

				elseif (strpos($str,'cros') !== false)				$out['os'] = 'Chrome OS';
				elseif (strpos($str,'beos') !== false)				$out['os'] = 'BeOS';
				elseif (strpos($str,'amigaos') !== false)			$out['os'] = 'AmigaOS';
				elseif (strpos($str,'inferno') !== false)			$out['os'] = 'Inferno';
				elseif (strpos($str,'blackberry') !== false)		$out['os'] = 'BlackBerry OS';
				elseif (strpos($str,'j2me') !== false)				$out['os'] = 'Java Platform 2 Micro Edition';

				else $out['os'] = 'unknown';


			// 사용 브라우저
				// IE 류 (compatible 포함)
				if (strpos($str,'msie') !== false) {
					if (preg_match('/msie [1-9]+([0-9]+)?\.[0-9a-z]+/',$str,$match)) {
						$out['browser'] = str_replace('msie','Internet Explorer',$match[0]);
					}
				}
				elseif (preg_match('/chrome\/[0-9a-z\.]+/',$str,$match)) $out['browser'] = str_replace('chrome','Chrome',$match[0]);
				elseif (preg_match('/firefox\/[0-9a-z\.]+/',$str,$match)) $out['browser'] = str_replace('firefox','FireFox',$match[0]);
				elseif (preg_match('/opera\/[0-9a-z\.]+/',$str,$match)) $out['browser'] = str_replace('opera','Opera',$match[0]);
				elseif (preg_match('/blackberry[0-9]{4}/',$str,$match)) $out['browser'] = str_replace('blackberry','BlackBerry',$match[0]);
				elseif (preg_match('/lynx\/[0-9]{4}/',$str,$match)) $out['browser'] = str_replace('lynx','Lynx',$match[0]);
				elseif (strpos($str,'amigavoyager') !== false) $out['browser'] = 'AmigaVoyager';
				elseif (strpos($str,'arora') !== false) $out['browser'] = 'Arora';
				elseif (strpos($str,'beonex') !== false) $out['browser'] = 'Beonex';
				elseif (strpos($str,'bonecho') !== false) $out['browser'] = 'BonEcho';
				elseif (strpos($str,'camino') !== false) $out['browser'] = 'Camino';
				elseif (strpos($str,'cheshire') !== false) $out['browser'] = 'Cheshire';
				elseif (strpos($str,'chimera') !== false) $out['browser'] = 'Chimera';
				elseif (strpos($str,'charon') !== false) $out['browser'] = 'Charon';
				elseif (strpos($str,'cometbird') !== false) $out['browser'] = 'CometBird';
				elseif (strpos($str,'conkeror') !== false) $out['browser'] = 'Conkeror';
				elseif (strpos($str,'element browser') !== false) $out['browser'] = 'Element Browser';
				elseif (strpos($str,'elinks') !== false) $out['browser'] = 'ELinks';
				elseif (strpos($str,'gecko') !== false) $out['browser'] = 'Gecko';
				else $out['browser'] = 'unknown';

		}

		return $out;

	}

	# 카운터 기본 정보 (오늘 날짜, 삭제기준일, 오늘 시간, referer)
	$godoLogInfo = array(
		today	=> date("Ymd"),
		del		=> date("Ymd",time()-(24*60*60*100)),
		hour	=> "h".date("H"),
		referer	=> $_SERVER['HTTP_REFERER'],
		);

	# referer 없는 경우
	if(!$godoLogInfo['referer']) $godoLogInfo['referer'] = "Direct Contact (Typing or Bookmark)";

	# 오늘 날짜의 카운터가 있는지를 체크
	list($_isDay) = $db->fetch("select count(*) from ".MINI_COUNTER." where day=".$godoLogInfo['today']);

	# 오늘 날짜의 카운터가 없는경우
	if (!$_isDay){
		$db->query("insert into ".MINI_COUNTER." set day=".$godoLogInfo['today']);
		$db->query("delete from ".MINI_REFERER." where day<".$godoLogInfo['del']);
		$db->query("delete from ".MINI_IP." where idx_date<".$godoLogInfo['del']);
	}

	# 오늘 날짜의 IP카운터가 있는지를 체크
	list($_isIp) = $db->fetch("select count(*) from ".MINI_IP." where idx_date=".$godoLogInfo['today']." and ip='".$_SERVER['REMOTE_ADDR']."'");

	# 오늘 날짜의 IP카운터가 없는경우
	if (!$_isIp){

		# OS 와 Browser 정보
		$godoLogInfo['client'] = get_agent($_SERVER['HTTP_USER_AGENT']);

		# 카운터 증가
		$db->query("update ".MINI_COUNTER." set uniques=uniques+1,".$godoLogInfo['hour']."=".$godoLogInfo['hour']."+1 where day=".$godoLogInfo['today']." or day=0");

		# IP와 정보 추가
		$db->query("insert into ".MINI_IP." set
					ip			= '".$_SERVER['REMOTE_ADDR']."',
					referer		= '".$godoLogInfo['referer']."',
					os			= '".$godoLogInfo['client']['os']."',
					browser		= '".$godoLogInfo['client']['browser']."',
					idx_date	= left(now()+0,8),
					reg_date	= UNIX_TIMESTAMP()
					");

		# referer 체크
		list($_isReferer) = $db->fetch("select count(*) from ".MINI_REFERER." where day=".$godoLogInfo['today']." and referer='".$godoLogInfo['referer']."'");

		# referer 가 없는 경우 추가 or 업데이트
		if (!$_isReferer) $db->query("insert into ".MINI_REFERER." (day,referer) values ('".$godoLogInfo['today']."','".$godoLogInfo['referer']."')");
		else $db->query("update ".MINI_REFERER." set hit=hit+1 where day='".$godoLogInfo['today']."' and referer='".$godoLogInfo['referer']."'");

		# 카운터 쿠키 생성
		setCookie("godoLog",date("Ymd"),0,"/");
	}

	$db->silent();	// DB 오류 출력 여부 복원
}

# 지정된 페이지 이외에는 페이지 뷰 증가
if (!$indexLog && $cfg['counterYN'] != '2') $db->query("update ".MINI_COUNTER." set pageviews=pageviews+1 where day=curdate()+0 or day=0");

?>
