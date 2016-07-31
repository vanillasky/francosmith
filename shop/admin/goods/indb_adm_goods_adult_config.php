<?php
include "../lib.php";

$param = array(
	'allow_guest_auth' => Clib_Application::request()->get('allow_guest_auth'),
);

Core::loader('config')->save('goods_adult_auth', $param);

msg('저장되었습니다.',-1);
