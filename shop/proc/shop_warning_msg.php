<?php
	

		### 고도몰 환경코드
		$file = dirname(__FILE__)."/../conf/godomall.cfg.php";
		if (!is_file($file)) msg("고도몰 설정파일을 등록하세요",1);
		$file = file($file);
		$godo = decode($file[1],1);

		### 운영 기준값
		if ( preg_match( "/^rental_mxfree/i", $godo['ecCode'] ) ){ // 무료형
			$g_warn_day			= 5;		# 종료 경고 기간
			$g_shopstop_day		= 30;		# 사용자화면 제한 개시일
			$g_drop_day			= 60;		# 솔루션 삭제 개시일
		}
		else if ( preg_match( "/^rental_mx/i", $godo['ecCode'] ) ){ // 임대형
			if ( $godo['freeType'] == "y" ){
				$g_warn_day			= 5;		# 만료 경고 기간
				$g_shopstop_day		= 1;		# 사용자화면 제한 개시일
				$g_drop_day			= 1;		# 솔루션 삭제 개시일
			}else{
				$g_warn_day			= 5;		# 만료 경고 기간
				$g_shopstop_day		= 30;		# 사용자화면 제한 개시일
				$g_drop_day			= 60;		# 솔루션 삭제 개시일
			}
		}


		### 운영 체크
		if ( preg_match( "/^rental_mxfree/i", $godo['ecCode'] ) ){ // 무료형
			$nowDay = betweenDate(date('Ymd'),$godo['edate']);

			if ( $godo['edate'] != 0 && $nowDay <= $g_warn_day && $nowDay > 0 ){ # 종료예정회원
				$err_msg = "무료 쇼핑몰을 이용하는 고객은 적어도 " . $g_drop_day . "일에 한 번은 관리자 화면에 접속해야 하며 \\n마지막 접속시간 후 " . $g_shopstop_day . "일이 경과하면 회사는 사전 통보 없이 해당 쇼핑몰의 사용자화면 접속을 제한할 수 있습니다";
			}
			else if ( $godo['edate'] != 0 && $nowDay <= 0 ){ # 종료회원

				if ( $nowDay <= ( 0 - ( $g_drop_day - $g_shopstop_day ) ) ){ # 솔루션 삭제 개시
					msg("마지막 접속시간 후 " . $g_drop_day . "일 이상 경과하여 쇼핑몰 이용이 불가능합니다.",-1);
				}
				else { # 사용자화면 제한 개시
					$err_msg = "마지막 접속시간 후 " . ( abs( $nowDay ) + $g_shopstop_day ) . "일째 접속하셨습니다. \\n사용자화면 제한이 해제되었습니다.";
				}
			}

			$ret = @readurl("http://www.godo.co.kr/userinterface/_godoConn/host_eFree_connect.php?sno=".$godo['sno']);
		}else if(preg_match( "/^rental_mx/i", $godo['ecCode'])){ // 임대형
			$nowDay = betweenDate(date('Ymd'),$godo['edate']);

			if ( $nowDay <= $g_warn_day && $nowDay > 0 ){ # 만료예정회원
				$err_msg = "서비스 사용기간이 {$nowDay}일 남았습니다.";
			}
			else if ( $nowDay <= 0 ){ // 만료후 상태

				if ( $nowDay <= ( 0 - $g_drop_day ) ){ # 솔루션 삭제 개시
					msg("서비스 만료 후 {$g_drop_day}일 이상 경과하여 쇼핑몰 이용이 불가능합니다.",-1);
				}
				else if ( $nowDay <= ( 0 - $g_shopstop_day ) ){ # 사용자화면 제한 개시
					$err_msg = "서비스 만료 후 " . abs( $nowDay ) . "일 지났습니다.\\n만료일 후 " . $g_shopstop_day . "일이 경과하여 사용자화면 접속이 제한되었습니다.\\n마이고도에 접속하셔서 서비스기간을 연장해 주시기 바랍니다.";
				}
				else {
					$err_msg = "서비스 만료 후 " . abs( $nowDay ) . "일 지났습니다.\\n만료일 후 " . $g_shopstop_day . "일이 지나면 사용자화면 접속이 제한됩니다.\\n마이고도에 접속하셔서 서비스기간을 연장해 주시기 바랍니다.";
				}
			}
		}
	
?>