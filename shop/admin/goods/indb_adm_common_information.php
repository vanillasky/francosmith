<?php
include "../lib.php";

$param = array(
	'separate_supply_info' => Clib_Application::request()->get('separate_supply_info'),
	'separate_cancel_info' => Clib_Application::request()->get('separate_cancel_info'),
	'separate_claim_info' => Clib_Application::request()->get('separate_claim_info'),
	'separate_trouble_info' => Clib_Application::request()->get('separate_trouble_info'),
	'separate_service_info' => Clib_Application::request()->get('separate_service_info'),
);

Core::loader('config')->save('goods_common_information', $param);

msg('저장되었습니다.',-1);
