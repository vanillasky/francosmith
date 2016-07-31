<?php

class attendance {

	var $design_head_image = array(
		'1'=>'../data/attendance/header_image_1.gif',
		'2'=>'../data/attendance/header_image_2.gif',
		'3'=>'../data/attendance/header_image_3.gif',
		'4'=>'../data/attendance/header_image_4.gif',
	);

	var $design_body = array(
		'1'=>'member/attendance_caldr_1.htm',
		'2'=>'member/attendance_caldr_2.htm',
		'3'=>'member/attendance_comt_1.htm',
		'4'=>'member/attendance_comt_2.htm',
	);

    var $check_message_select = array(
        '1'=>'짝짝짝~ 출석체크 이벤트에 참여하셨어요~',
        '2'=>'추카추카~ 출석체크 이벤트에 참여하셨어요 ^^',
        '3'=>'콩크레츄에이션~ 출석체크에 성공하셨어요',
        '4'=>'쿠~욱 출석체크 도장을 찍으셨어요!',
    );

    var $check_message_none = '출석체크되었습니다';


    function get_check_message($attendance_no) {
		$db = Core::loader('db');
        $attendance_no = (int)$attendance_no;
        $query = "
            select
                check_message_type , check_message_select , check_message_custom
            from
                gd_attendance
            where
                attendance_no='{$attendance_no}'
        ";
        $result = $db->_select($query);
        $result = $result[0];

        if($result['check_message_type']=='select') {
			return $this->check_message_select[$result['check_message_select']];
        }
		elseif($result['check_message_type']=='custom') {
			return $result['check_message_custom'];
		}
		else {
			return $this->check_message_none;
		}
    }

    // 로그인시 출석체크
    function login_check($member_no, $is_mobile = false) {
		$db = Core::loader('db');
		if ($is_mobile === true) $condMobileUseYN = 'AND mobile_useyn="y"';
    	$query = "
    		select
    			attendance_no
    		from
    			gd_attendance
    		where
    			start_date <= curdate() and end_date >= curdate() and
    			check_method='login'
			{$condMobileUseYN}
    		order by
    			attendance_no desc";
    	$result = $db->_select($query);
    	foreach($result as $v) {
			$result = $this->check($v['attendance_no'],$member_no);
			if(!$result) {

				return $v['attendance_no'];
				break;
			}
    	}
    	return false;
    }

	// 출석체크하기
	function check($attendance_no,$member_no) {
		$db = Core::loader('db');
		$attendance_no = (int)$attendance_no;
		$member_no = (int)$member_no;

        $curdate = date('Y-m-d');
		$int_curdate = (int)str_replace('-','',$curdate);

		// 유효한 출석종류인지 확인한다
		$query = "
			select attendance_no,start_date,end_date,condition_type from gd_attendance
			where
				attendance_no = '{$attendance_no}' and
				manual_stop = 'n'
		";

		$attd_info = $db->_select($query);
		$attd_info = $attd_info[0];
		if(!$attd_info['attendance_no']) {
			return 'NOT_VALID_ATTENDANCE';
		}
		$attd_info['int_start_date']=(int)str_replace('-','',$attd_info['start_date']);
		$attd_info['int_end_date']=(int)str_replace('-','',$attd_info['end_date']);
		if(!($int_curdate >= $attd_info['int_start_date'] && $int_curdate <= $attd_info['int_end_date'])) {
			return 'NOT_VALID_DATE';
		}


		// 오늘체크했는지 확인한다
		$query = "
			select * from gd_attendance_check
			where
				attendance_no = '{$attendance_no}' and
				member_no = '{$member_no}'
		";

		$result = $db->_select($query);
        $result=$result[0];
		$ar_check_date = explode(',',$result['check_date_all']);

        if($result['check_no'] && $ar_check_date[count($ar_check_date)-1]==$curdate) {
            return 'ALREADY_CHECKED';
        }

        if($result['check_no']) {
			$ar_check_date[] = $curdate;

			if($attd_info['condition_type']=='straight') { // 매일출석형의 경우에는 연속된 날짜기간을 구해야한다.
				$max_period = 0;
				$tmp_period = 0;
				$prev_date = null;
				foreach($ar_check_date as $v) {
					$int_date = strtotime($v);
					if($prev_date == strtotime('-1 day',$int_date)) {
						$tmp_period++;
					}
					else {
						if($max_period < $tmp_period) {
							$max_period = $tmp_period;
						}
						$tmp_period=1;
					}
					$prev_date = $int_date;
				}
				if($max_period < $tmp_period) {
					$max_period = $tmp_period;
				}
				$result_period = $max_period;
			}
			else { // 횟수출석형은 그냥 +1만
				$result_period = $result['check_period']+1;
			}

            $query = $db->_query_print("
                update gd_attendance_check set
                    check_period=[s],
                    check_date_all=[s]
                where check_no=[s]
            ",$result_period,implode(',',$ar_check_date),$result['check_no']);
            $db->query($query);
        }
        else {
            $ar_insert = array(
    			'attendance_no'=>$attendance_no,
    			'member_no'=>$member_no,
    			'check_period'=>1,
                'check_date_all'=>$curdate,
                'provide_method'=>'',
                'reserve'=>0
    		);
    		$query = $db->_query_print("insert into gd_attendance_check set [cv]",$ar_insert);
    		$db->query($query);
        }
		return;
	}

	function add_attendance($ar_data) {
		$db = Core::loader('db');

		$validation_check = array(
			'name'=>array('require'=>true,'max_byte'=>100),
			'start_date'=>array('require'=>true,'pattern'=>'/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/'),
			'end_date'=>array('require'=>true,'pattern'=>'/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/'),
			'mobile_useyn'=>array('require'=>true,'array'=>array('n','y')),
			'condition_type'=>array('array'=>array('straight','sum')),
			'condition_period'=>array('type'=>'int'),
			'provide_method'=>array('require'=>true,'array'=>array('auto','manual')),
			'auto_reserve'=>array('type'=>'int'),
			'check_method'=>array('require'=>true,'array'=>array('stamp','comment','login')),
			'check_message_type'=>array('require'=>true,'array'=>array('select','custom','none')),
			'check_message_select'=>array('type'=>'int'),
			'check_message_custom'=>array(),
			'design_head_type'=>array('require'=>true,'array'=>array('html','image','upload')),
			'design_head_html'=>array(),
			'design_head_image'=>array(),
			'design_body'=>array(),
			'design_stamp'=>array('require'=>true,'array'=>array('default','upload')),
		);
		array_diff_unset($ar_data,array_keys($validation_check));
		$chk_result = array_value_cheking($validation_check,$ar_data);
		if(count($chk_result)!=0) {
			debug($chk_result);
			return 'NOT_VALID_DATA';
		}

		if(!($ar_data['condition_period']>0)) {
			return 'NOT_VALID_DATA';
		}

		if(
			(
				((int)str_replace('-','',$ar_data['end_date']))
				-
				((int)str_replace('-','',$ar_data['start_date']))
				+ 1
			) < $ar_data['condition_period']
		) {
			return 'NOT_VALID_START_END_DATE';
		}

		if(strtotime($ar_data['start_date']) > strtotime($ar_data['end_date'])) {
			return 'NOT_VALID_START_END_DATE';
		}

		$ar_data['reg_date']=G_CONST_NOW;
		$ar_data['manual_stop']='n';

		$query = "
			select
				attendance_no
			from
				gd_attendance
			where
				( start_date between '{$ar_data['start_date']}' and '{$ar_data['end_date']}' )
				or
				( end_date between '{$ar_data['start_date']}' and '{$ar_data['end_date']}' )
			limit 1
		";

		$result = $db->_select($query);
		if($result[0]['attendance_no']) {
			return 'DATE_OVERLAP';
		}



		$query = $db->_query_print("insert into gd_attendance set [cv]",$ar_data);
		//echo $query;
		$db->query($query);
		return $db->_last_insert_id();
	}

	function modify_attendance($attendance_no,$ar_data) {
		$db = Core::loader('db');

		$attendance_no = (int)$attendance_no;
		$validation_check = array(
			'name'=>array('require'=>true,'max_byte'=>100),
			'design_head_type'=>array('require'=>true,'array'=>array('html','image','upload')),
			'design_head_html'=>array(),
			'design_head_image'=>array(),
			'design_body'=>array(),
			'design_stamp'=>array('require'=>true,'array'=>array('default','upload')),
		);

		array_diff_unset($ar_data,array_keys($validation_check));
		$chk_result = array_value_cheking($validation_check,$ar_data);
		if(count($chk_result)!=0) {
			return 'NOT_VALID_DATA';
		}
		$ar_data['update_date']=time();

		$query = $db->_query_print("update gd_attendance set [cv] where attendance_no=[s]",$ar_data,$attendance_no);
		echo $query;
		$db->query($query);
		return ;
	}

	function delete_attendance($attendance_no) {
		$db = Core::loader('db');
		$attendance_no = (int)$attendance_no;
		$query = "delete from gd_attendance where attendance_no='{$attendance_no}'";
		$db->query($query);
		$query = "delete from gd_attendance_check where attendance_no='{$attendance_no}'";
		$db->query($query);
		$query = "delete from gd_attendance_comment where attendance_no='{$attendance_no}'";
		$db->query($query);
	}

	function stop_attendance($attendance_no) {
		$db = Core::loader('db');
		$attendance_no = (int)$attendance_no;
		$query = $db->_query_print("update gd_attendance set manual_stop='y' where attendance_no=[s]",$attendance_no);
		$db->query($query);
		return;
	}

	function add_comment($attendance_no,$member_no,$comment) {
		$db = Core::loader('db');
		$ar_insert = array(
			'attendance_no'=>$attendance_no,
			'member_no'=>$member_no,
			'comment'=>$comment,
			'reg_date'=>G_CONST_NOW
		);

		$query = $db->_query_print("insert into gd_attendance_comment set [cv]",$ar_insert);
		$db->query($query);
		return;
	}





}



?>
