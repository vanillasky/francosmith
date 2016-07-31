<?
include "../lib.php";

$info_cfg = $config->load('member_info');

switch ($_POST['mode']) {
	case 'finder_pwd':
		$info_cfg['finder_use_email'] = (int)$_POST['finder_use_email'];
		$info_cfg['finder_use_mobile'] = (int)$_POST['finder_use_mobile'];
		$info_cfg['finder_mobile_auth_message'] = $_POST['finder_mobile_auth_message'];
		break;

	case 'campaign':
		$info_cfg['campaign_use'] = (int)$_POST['campaign_use'];
		$info_cfg['campaign_period_type'] = $_POST['campaign_period_type'];
		$info_cfg['campaign_period_value'] = $info_cfg['campaign_period_type'] == 'd' ? (int)$_POST['campaign_period_value_d'] : (int)$_POST['campaign_period_value_m'];
		$info_cfg['campaign_next_term'] = (int)$_POST['campaign_next_term'];
		break;

	case 'event':
		$info_cfg['event_use'] = (int)$_POST['event_use'];

		// 이벤트를 사용한다면, 기간 필수.
		if ($info_cfg['event_use']) {
			$info_cfg['event_start_date'] = date('Y-m-d H:i:s', strtotime($_POST[event_period_date_s].$_POST[event_period_time_s].'0000') );
			$info_cfg['event_end_date'] = date('Y-m-d H:i:s', strtotime($_POST[event_period_date_e].$_POST[event_period_time_e].'0000') );
		}
		else {
			//$info_cfg['event_start_date'] = '';
			//$info_cfg['event_end_date'] = '';
		}

		$info_cfg['event_emoney'] = (int) preg_replace('/[^0-9]/','',$_POST['event_emoney']);
		break;

}

$config->save('member_info',$info_cfg);

go($_SERVER[HTTP_REFERER]);
?>