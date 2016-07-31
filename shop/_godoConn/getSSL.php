<?
include "../conf/config.php";

if ($cfg['ssl_type']) {
	echo 'SSL=YES|SSL_DOMAIN='.$cfg['ssl_domain'].'|SSL_PORT='.$cfg['ssl_port'];
}
else {
	echo 'SSL=NO';
}
?>