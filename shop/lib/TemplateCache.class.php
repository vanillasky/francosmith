<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class TemplateCache
{

	private $_cachingPage = array(
		'{:PC:}/main/index.php',
		'{:PC:}/goods/goods_list.php',
		'{:PC:}/board/list.php',
		'{:PC:}/goods/goods_qna_list.php',
		'{:PC:}/goods/goods_review_list.php',
		'{:MOBILE:}/index.php',
		'{:MOBILE:}/board/list.php',
	);
	private $_shopRoot;
	private $_mobileRoot;
	private $_pathCode;
	private $_cached = false;
	private $_cacheConfig = array(
		'cacheUseType' => 'none',
		'expireInterval' => 60,
		'pageExpireInterval' => array(
			'{:PC:}/main/index.php' => 60,
			'{:PC:}/goods/goods_list.php' => 60,
			'{:PC:}/board/list.php' => 60,
			'{:PC:}/goods/goods_qna_list.php' => 60,
			'{:PC:}/goods/goods_review_list.php' => 60,
			'{:MOBILE:}/index.php' => 60,
			'{:MOBILE:}/board/list.php' => 60,
		),
	);

	public function __construct($path)
	{
		// 설정파일 할당
		include dirname(__FILE__).'/../conf/config.php';
		$this->_shopRoot = $cfg['rootDir'];
		$this->_mobileRoot = '/m2';   // 모바일샵이 v2인지 체크할 방법이 루트가 /m2인지를 확인하는 방법밖에 없음
		if (file_exists(dirname(__FILE__).'/../conf/cache.page.cfg.php')) {
			include dirname(__FILE__).'/../conf/cache.page.cfg.php';
			$this->_cacheConfig = $cacheConfig['page'];
		}

		// 현재 스크립트 파일코드 생성
		$this->_pathCode = $this->getPathCode($path);
	}

	public function getPathCode($path)
	{
		return preg_replace(array(
			'/^('.str_replace('/', '\/', $this->_shopRoot).')/',
			'/^('.str_replace('/', '\/', $this->_mobileRoot).')/',
		), array(
			'{:PC:}',
			'{:MOBILE:}',
		), $path);
	}

	public function loadConfig()
	{
		return $this->_cacheConfig;
	}

	public function isEnabled()
	{
		return ($this->_cacheConfig['cacheUseType'] === 'default' || $this->_cacheConfig['cacheUseType'] === 'advanced');
	}

	public function checkCacingPage()
	{
		return in_array($this->_pathCode, $this->_cachingPage) && ($this->_cacheConfig['pageExpireInterval'][$this->_pathCode] > 0);
	}

	public function checkCondition()
	{
		if ($this->_pathCode === '{:PC:}/goods/goods_list.php') {
			// 페이지에 영향을 주는 파라미터 체크
			foreach ($_GET as $name => $value) {
				if (strlen($name) < 1) continue;
				else if ($name === 'category' || $name === 'page') continue;
				else return false;
			}
			return (!$_GET['page'] || ($_GET['page'] === '1'));
		}
		else if ($this->_pathCode === '{:PC:}/goods/goods_qna_list.php') {
			return !$_GET['page'] || ($_GET['page'] === '1');
		}
		else if ($this->_pathCode === '{:PC:}/goods/goods_review_list.php') {
			return !$_GET['page'] || ($_GET['page'] === '1');
		}
		else if ($this->_pathCode === '{:PC:}/board/list.php') {
			// 페이지에 영향을 주는 파라미터 체크
			foreach ($_GET as $name => $value) {
				if (strlen($name) < 1 || strlen($value) < 1) continue;
				else if ($name === 'id' || $name === 'page') continue;
				else return false;
			}
			return !$_GET['page'] || ($_GET['page'] === '1');
		}
		else {
			return true;
		}
	}

	public function checkSkinPatch()
	{
		include dirname(__FILE__).'/../conf/config.php';
		include dirname(__FILE__).'/../conf/config.mobileShop.php';
		$pcSkinPath = dirname(__FILE__).'/../data/skin/'.$cfg['tplSkin'];
		$mobileSkinPath = dirname(__FILE__).'/../data/skin_mobileV2/'.$cfgMobileShop['tplSkinMobile'];
		if (strpos(file_get_contents($pcSkinPath.'/main/index.htm'), '#PAGECACHE_PATCH_CHECKER') === false) {
			return false;
		}
		else if (strpos(file_get_contents($pcSkinPath.'/proc/menuCategory.htm'), '#PAGECACHE_PATCH_CHECKER') === false) {
			return false;
		}
		else if (strpos(file_get_contents($pcSkinPath.'/mypage/_myBoxLayer.htm'), '#PAGECACHE_PATCH_CHECKER') === false) {
			return false;
		}
		else if (strpos(file_get_contents($pcSkinPath.'/mypage/_myCouponLayer.htm'), '#PAGECACHE_PATCH_CHECKER') === false) {
			return false;
		}
		else if (strpos(file_get_contents($pcSkinPath.'/mypage/_myLevelLayer.htm'), '#PAGECACHE_PATCH_CHECKER') === false) {
			return false;
		}
		else if (strpos(file_get_contents($mobileSkinPath.'/index.htm'), '#PAGECACHE_PATCH_CHECKER') === false) {
			return false;
		}
		else if (strpos(file_get_contents($mobileSkinPath.'/outline/_header.htm'), '#PAGECACHE_PATCH_CHECKER') === false) {
			return false;
		}
		else {
			return true;
		}
	}

	public function getExpireInteval()
	{
		if ($this->_cacheConfig['cacheUseType'] === 'default') {
			return $this->_cacheConfig['expireInterval'];
		}
		else if ($this->_cacheConfig['cacheUseType'] === 'advanced') {
			return $this->_cacheConfig['pageExpireInterval'][$this->_pathCode];
		}
		else {
			return null;
		}
	}

	public function setCache(&$tpl, $fid = 'tpl')
	{
		$tpl->caching = true;
		$tpl->cache_dir = dirname(__FILE__).'/../cache/page';
		if (file_exists($tpl->cache_dir) === false) {
			@mkdir($tpl->cache_dir, 0707);
		}
		if (file_exists($tpl->cache_dir)) {
			if ($this->_pathCode === '{:PC:}/goods/goods_list.php') {
				$cid = $_GET['category'];
			}
			else if ($this->_pathCode === '{:PC:}/goods/goods_review_list.php') {
				$cid = $_GET['goodsno'];
			}
			else if ($this->_pathCode === '{:PC:}/goods/goods_qna_list.php') {
				$cid = $_GET['goodsno'];
			}
			else if ($this->_pathCode === '{:PC:}/board/list.php') {
				$cid = $_GET['id'];
			}
			else if ($this->_pathCode === '{:MOBILE:}/board/list.php') {
				$cid = $_GET['id'];
			}
			else {
				$cid = '';
			}
			$tpl->setCache($fid, $this->getExpireInteval(), $this->_pathCode, $cid);
			$this->_cached = true;
			$tpl->assign('page_cache_enabled', $this->_cached);

			if ($tpl->isCached($fid) && $this->_pathCode !== '{:PC:}/board/list.php' && $this->_pathCode === '{:MOBILE:}/board/list.php') {
				$tpl->print_($fid);
				exit;
			}
		}
	}

	public function isCached()
	{
		return $this->_cached;
	}

	public function clearCache($path = null)
	{
		if (class_exists('Template_') === false) {
			include dirname(__FILE__).'/../Template_/Template_.class.php';
		}
		$tpl = new Template_();
		$tpl->caching = true;
		$tpl->cache_dir = dirname(__FILE__).'/../cache/page';
		if ($path === null) {
			$tpl->clearCache();
		}
		else {
			$tpl->clearCache($this->getPathCode($path));
		}
	}

	public function clearCacheByClass($class)
	{
		if (class_exists('Template_') === false) {
			include dirname(__FILE__).'/../Template_/Template_.class.php';
		}
		$tpl = new Template_();
		$tpl->caching = true;
		$tpl->cache_dir = dirname(__FILE__).'/../cache/page';
		if ($class === 'index') {
			$tpl->clearCache('{:PC:}/main/index.php');
			$tpl->clearCache('{:MOBILE:}/index.php');
		}
		else if ($class === 'goods') {
			$tpl->clearCache('{:PC:}/main/index.php');
			$tpl->clearCache('{:PC:}/goods/goods_list.php');
		}
		else if ($class === 'goods_review') {
			$tpl->clearCache('{:PC:}/goods/goods_review_list.php');
		}
		else if ($class === 'goods_qna') {
			$tpl->clearCache('{:PC:}/goods/goods_qna_list.php');
		}
		else if ($class === 'board') {
			$tpl->clearCache('{:MOBILE:}/board/list.php');
			$tpl->clearCache('{:PC:}/board/list.php');
		}
		else {
			return false;
		}
	}

	public function getPageUpdateScript()
	{
		return '<script id="page-updater" type="text/javascript" src="'.$this->_shopRoot.'/lib/js/onload_async_loader.php"'
			. ' data-shop-root="'.$this->_shopRoot.'" data-referer="'.($_SERVER['HTTP_REFERER']).'"'
			. ' data-script="'.($_SERVER['SCRIPT_NAME']).'" data-https="'.$_SERVER['HTTPS'].'"></script>';
	}
}