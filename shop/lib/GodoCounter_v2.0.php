<?
//==============================================================================//
//		PROGRAM Title : Godo-Shoppingmall Counter
//		Company Name  : (주) 플라이폭스 - 고도몰
//
//		Version       : 2.0 Version
//		Create Date   : 2006.06.01
//		Update Date   : ----,--,--
//		Programer     : 박선희(신동규)
//		Copyright (C)2004 flyfox.co.kr , All rights reserved.
//==============================================================================//





class Counter
{
	var $engine_srchname = array( 'yahoo' => 'p', 'naver' => 'query', 'msn' => 'q', 'empas' => 'q', 'daum' => 'q', 'google' => 'q', 'nate' => 'query', 'korea' => 'keyword', 'dreamwiz' => 'q', 'netian' => 'keyword' );

	function Counter()
	{
		$this->ConnectDate	= date("Y/m/d/D/H");											// 년/월/일/요일/시간
		$this->Get_Date();																	// 오늘 날짜 구하기
		$this->Date			= "{$this->DateYear}-{$this->DateMonth}-{$this->DateDay}";		// 날짜(YYYY:MM:DD)
		$this->Time			= "{$this->DateHour}:{$this->DateMinut}:{$this->DateSecond}";	// 시간(HH:II:SS}
		$this->LogIP		= getenv("REMOTE_ADDR");										// 접속 IP
		$this->ConnectLink	= getenv("HTTP_REFERER");										// 접속 경로
		if ( $this->ConnectLink == '' ) $this->ConnectLink = "직접입력 또는 즐겨찾기적용";	// 접속 경로 값이 없으면
		$this->Check_CountCookie();															// 쿠키
	}

	//==============================================================================//
	//	날짜 구하는 함수
	//==============================================================================//
	function Get_Date()
	{
		$DateStr = explode("/", $this->ConnectDate);

		$this->DateYear		= $DateStr[0];
		$this->DateMonth	= $DateStr[1];
		$this->DateDay		= $DateStr[2];
		$this->DateWeek		= $DateStr[3];
		$this->DateHour		= $DateStr[4];
		$this->DateMinut	= date(i);
		$this->DateSecond	= date(s);
	}

	//==============================================================================//
	//	쿠키가 없으면 생성
	//==============================================================================//
	function Check_CountCookie()
	{
		if ( !IsSet( $_COOKIE['CountCookie'] ) ){ // Cookie 없어 생성
			$RandCookie = md5(uniqid(rand()));
			SetCookie("CountCookie",$RandCookie,0,"/");
		}
		else $RandCookie = $_COOKIE['CountCookie'];

		$this->RandCookie = $RandCookie;
	}

	//==============================================================================//
	//	배열을 문장화
	//==============================================================================//
	function Concat_str( $str )
	{
		$tmp = array();
		foreach ( $str as $key => $value ) $tmp[] = $key  ."=" . $value;
		$str = implode( "&", $tmp );
		return $str;
	}

	//==============================================================================//
	//	문장 파싱
	//==============================================================================//
	function Parsing_str( $str )
	{
		$parse = array();
		$tmp = explode( "&", $str );
		foreach ( $tmp as $pro ){
			if ( $pro == '' ) continue;
			$tmp2 = explode( "=", $pro );
			$parse[ $tmp2[0] ] = $tmp2[1];
		}
		return $parse;
	}

	//==============================================================================//
	//	IP,Cookie 체크
	//==============================================================================//
	function Count_init()
	{
		GLOBAL $db;

		$query = "
		create temporary table gd_godocount_ip(
			sno	int unsigned not null auto_increment primary key,
			thatDay datetime default NULL,
			IPAddress varchar(15) default NULL,
			CookieCHK varchar(32) default NULL
		)
		";
		$db->query($query); // 임시 IP TABLE 생성

		$path = dirname(__FILE__) . "/../log/godocount_ip.ini";

		if ( is_file ( $path ) ){ // TABLE에 IP DATA 저장
			foreach ( parse_ini_file( $path, true) as $arr ) $db->query( "INSERT INTO gd_godocount_ip VALUES ('', '{$arr['thatDay']}', '{$arr['IPAddress']}', '{$arr['CookieCHK']}')" );
		}

		$db->query( "DELETE FROM gd_godocount_ip WHERE thatDay < '{$this->Date} 00:00:00'" ); // 지난일 자료 삭제

		list( $cnt ) = $db->fetch( "SELECT count(sno) FROM gd_godocount_ip WHERE thatDay LIKE '{$this->Date}%' AND IPAddress = '{$this->LogIP}' AND CookieCHK = '{$this->RandCookie}'" );

		if ( $cnt < 1 ){ // 지정 시간안에 같은 IP 가 없으면 IP 저장 및 카운터 증가
			$db->query( "INSERT INTO gd_godocount_ip (thatDay,IPAddress,CookieCHK) VALUES ('{$this->Date} {$this->Time}','{$this->LogIP}','{$this->RandCookie}')" );
			$this->Count_Increase(); // 카운터 증가
		}
		else $db->query( "UPDATE gd_godocount_ip SET thatDay = '{$this->Date} {$this->Time}' WHERE IPAddress = '{$this->LogIP}' AND CookieCHK='{$this->RandCookie}'" );
		//if ( $cnt >= 1 ) $this->Count_Increase(); // 카운터 증가(오픈시삭제)

		{ // 저장
			$fp = @fopen( $path, "w" );
			$res = $db->query( "SELECT * FROM gd_godocount_ip ORDER BY sno" );
			while ( $row = $db->fetch( $res ) ){
				@fwrite( $fp, "[" . $row['sno'] . "]" . "\n" );
				@fwrite( $fp, "thatDay = \"" . $row['thatDay'] . "\"" . "\n" );
				@fwrite( $fp, "IPAddress = \"" . $row['IPAddress'] . "\"" . "\n" );
				@fwrite( $fp, "CookieCHK = \"" . $row['CookieCHK'] . "\"" . "\n" );
			}
			@fclose( $fp );
			@chMod( $path, 0757 );
		}

		$db->query( "drop temporary table gd_godocount_ip" );

		$this->Count_Pageview(); // 페이지뷰 증가
	}

	//==============================================================================//
	//	카운터 올리기
	//==============================================================================//
	function Count_Increase()
	{
		{ // 카운터 올리기 - 전체(godocount_counter)
			$counter = array();
			$path = dirname(__FILE__) . "/../log/godocount_counter.ini";
			if ( is_file ( $path ) ) $counter = parse_ini_file( $path );

			if ( $counter['thatDay'] != "{$this->Date}" ){ // 날짜 상이한 경우 today, yesday 초기화 및 저장
				$fp = @fopen( $path, "w" );
				@fwrite( $fp, "thatDay = \"" . "{$this->Date}" . "\"" . "\n" );
				@fwrite( $fp, "today = \"" . 1 . "\"" . "\n" );
				@fwrite( $fp, "yesday = \"" . $counter['today'] . "\"" . "\n" );
				@fwrite( $fp, "count = \"" . ( $counter['count'] + 1 ) . "\"" . "\n" );
				@fclose( $fp );
				@chMod( $path, 0757 );
			}
			else { // 날짜 동일한 경우 증가 및 저장
				$fp = @fopen( $path, "w" );
				@fwrite( $fp, "thatDay = \"" . "{$this->Date}" . "\"" . "\n" );
				@fwrite( $fp, "today = \"" . ( $counter['today'] + 1 ) . "\"" . "\n" );
				@fwrite( $fp, "yesday = \"" . $counter['yesday'] . "\"" . "\n" );
				@fwrite( $fp, "count = \"" . ( $counter['count'] + 1 ) . "\"" . "\n" );
				@fclose( $fp );
				@chMod( $path, 0757 );
			}
		}

		{ // 카운터 올리기 - 방문수(godocount_visit)
			$counter = array();
			$path = dirname(__FILE__) . "/../log/godocount_visit_" . date('Y') . ".ini";
			if ( is_file ( $path ) ) $counter = parse_ini_file( $path );

			parse_str( $counter[ "{$this->Date}" ], $counter[ "{$this->Date}" ] ); // 당일건 데이타 파싱
			$counter[ "{$this->Date}" ][  "time" . sprintf( "%02d", $this->DateHour ) ] += 1; // 카운터 증가
			$counter[ "{$this->Date}" ] = $this->Concat_str( $counter[ "{$this->Date}" ] ); // 당일건 데이타 문장화

			{ // 저장
				$fp = @fopen( $path, "w" );
				foreach ( $counter as $day => $value ) @fwrite( $fp, $day  . " = \"" . $value . "\"" . "\n" );
				@fclose( $fp );
				@chMod( $path, 0757 );
			}
		}

		{ // 카운터 올리기 - 검색엔진(godocount_engine)
			$link = parse_url( $this->ConnectLink );	// 주소 데이타 파싱
			parse_str( $link[query], $link[query] );	// 질의 문자열 파싱
			if ( $_SERVER['HTTP_HOST'] == 'dev2.godo.co.kr' ) $link[host] = $link[query][site]; // 임시

			if ( preg_match("/yahoo\.com|naver\.com|msn\.co\.kr|empas\.com|daum\.net|google\.co\.kr|nate\.com|korea\.com|dreamwiz\.com|netian\.com$/is", $link[host], $matches) ){

				$engine = substr( $matches[0], 0, strpos($matches[0], '.') );	// 검색엔진
				$search = $link[query][  $this->engine_srchname[ $engine ] ];			// 검색어
				$search = str_replace( array( '"', '&', '=', '[', ']' ), array( '%22', '%26', '%3d', '%5b', '%5d' ), stripslashes( $search ) );

				if ( $search != '' ){
					$counter = array();
					$path = dirname(__FILE__) . "/../log/godocount_engine_" . date('Y') . ".ini";
					if ( is_file ( $path ) ) $counter = parse_ini_file( $path, true );

					$engine_searchs = $this->Parsing_str( $counter[ "{$this->Date}" ][ $engine ] ); // 검색엔진 데이타 파싱
					$engine_searchs[ "$search" ] += 1; // 카운터 증가
					$counter[ "{$this->Date}" ][ $engine ] = $this->Concat_str( $engine_searchs ); // 검색엔진 데이타 문장화

					{ // 저장
						$fp = @fopen( $path, "w" );
						foreach ( $counter as $day => $arr ){
							@fwrite( $fp, "[$day]" . "\n" );
							foreach ( $arr as $engine => $search ) @fwrite( $fp, $engine  . " = \"" . $search . "\"" . "\n" );
						}
						@fclose( $fp );
						@chMod( $path, 0757 );
					}
				}
			}
		}
	}

	//==============================================================================//
	//	페이지뷰 올리기
	//==============================================================================//
	function Count_Pageview()
	{
		// 카운터 올리기 - 페이지뷰(godocount_pageview)
		$counter = array();
		$path = dirname(__FILE__) . "/../log/godocount_pageview_" . date('Y') . ".ini";
		if ( is_file ( $path ) ) $counter = parse_ini_file( $path );

		parse_str( $counter[ "{$this->Date}" ], $counter[ "{$this->Date}" ] ); // 당일건 데이타 파싱
		$counter[ "{$this->Date}" ][  "time" . sprintf( "%02d", $this->DateHour ) ] += 1; // 카운터 증가
		$counter[ "{$this->Date}" ] = $this->Concat_str( $counter[ "{$this->Date}" ] ); // 당일건 데이타 문장화

		{ // 저장
			$fp = @fopen( $path, "w" );
			foreach ( $counter as $day => $value ) @fwrite( $fp, $day  . " = \"" . $value . "\"" . "\n" );
			@fclose( $fp );
			@chMod( $path, 0757 );
		}
	}

}

$counter = new Counter();
$counter->Count_init();

/*echo <<<ENDH
<pre>
{$counter->ConnectDate}
{$counter->LogIP}
{$counter->ConnectLink}
{$counter->DateYear}
{$counter->DateMonth}
{$counter->DateDay}
{$counter->DateWeek}
{$counter->DateHour}
{$counter->DateMinut}
{$counter->DateSecond}
{$counter->RandCookie}

<br>
ENDH;*/
?>