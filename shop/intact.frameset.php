<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?=$meta_title?></title>
<meta name="description" content="<?=$meta_title?>" />
<meta name="keywords" content="<?=$meta_keywords?>" />
<?php echo $naverCommonInflowScript->getCommonInflowScript(); ?>
</head>
<frameset rows="0,*" border="0" frameborder="no" cols="*">
  <frame src="/index.php?behind=1" noresize scrolling="no">
  <frame src="shop/index.php<?php echo ($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''); ?>">
</frameset>
<noframes>
<body></body>
</noframes>
</html>