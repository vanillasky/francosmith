<?php
include dirname(__FILE__).'/../lib.php';

$dbCache = Core::loader('dbcache');
$qfile = Core::loader('qfile');

switch ($_REQUEST['mode']) {
	case 'clearCache':
		$dbCache->clearCache();
		msg('ĳ�ð� ���ŵǾ����ϴ�.');
		break;
	case 'save':
		$configPath = dirname(__FILE__).'/../../conf/cache.db.cfg.php';

		$qfile->open($configPath);
		$qfile->write('<?php'.PHP_EOL);
		$qfile->write('$cacheConfig = array();'.PHP_EOL);
		$qfile->write('$cacheConfig["db"] = array();'.PHP_EOL);
		$qfile->write('$cacheConfig["db"]["cacheUseType"] = "'.$_POST['cacheUseType'].'";'.PHP_EOL);
		$qfile->write('?>');
		$qfile->close();
		chmod ($configPath ,0707);

		$dbCache->clearCache();

		msg('���������� ����Ǿ����ϴ�.');
		break;
}