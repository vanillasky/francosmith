<?
/**
 * validParamGd class
 * Parameter 유효성검증
 */
class validParamGd
{
	var $nowPath;

	function validParamGd()
	{
		$this->getNowPath();
		if ($this->condition() !== true) return;

		$funcName = preg_replace('/^\//', '', $this->nowPath);
		$funcName = str_replace(array('/','.php'), array('_',''), $funcName);
		$funcName = str_replace('.', '_', $funcName);
		if (method_exists($this, $funcName) !== true) return;
		$this->$funcName();
	}

	/**
	 * 조건 체크
	 * @return bool
	 */
	function condition()
	{
		// 접속 경로 체크
		if ($this->nowPath === '') return false;

		// 폴더 네임 체크 (관리자 제외)
		if (preg_match('/^\/admin/i', $this->nowPath)) return false;

		// 파일 체크 (*indb.php 체크)
		if (preg_match('/indb.php/i', $this->nowPath)) return false;

		// GET 파라메터 체크
		if (empty($_GET) === true) return false;

		return true;
	}

	/**
	 * 접속 경로 정의
	 * @return void
	 * ----------------------------------------
	 * 어드민 : /admin/basic/adm_basic_index.php
	 * PC샵 : /main/index.php
	 * M샵 V2 : /m2/index.php
	 * M샵 V1 : /m/index.php
	 */
	function getNowPath()
	{
		// Shop 경로
		$shopRoot = realpath(dirname(__FILE__).'/../'); // "/www/계정/xxx/shop"
		$shopPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $shopRoot); // "/xxx/shop"

		// Home 경로
		$homeRoot = realpath(dirname(__FILE__).'/../../'); // "/www/계정/xxx"
		$homePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $homeRoot); // "/xxx"

		// Shop 경로, Home 경로 제거
		$_php_self = $_SERVER['PHP_SELF'];
		if ($shopPath) $_php_self = preg_replace('/^' . addcslashes($shopPath, '/') . '/', '', $_php_self); // "/xxx/shop" 제거
		if ($homePath) $_php_self = preg_replace('/^' . addcslashes($homePath, '/') . '/', '', $_php_self); // "/xxx" 제거

		$this->nowPath = $_php_self;
	}

	/*===================================================================
	 * 상품진열 페이지
	===================================================================*/

	/**
	 * PC > 상품상세
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_view.php
	 * 파라미터 category, goodsno
	 */
	function goods_goods_view()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * PC > 상품리스트
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_list.php
	 * 파라미터 category, page, page_num, sort
	 */
	function goods_goods_list()
	{
		if (!preg_match('/^[0-9]*$/', $_GET['category'])) exit ;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > 상품검색
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_search.php
	 * 파라미터 skey, sword, hid_sword, cate, price, ssColor, page, page_num, sort
	 */
	function goods_goods_search()
	{
		if(!preg_match('/^[a-z0-9_\.]*$/i',$_GET['skey'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['sword'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['hid_sword'])) exit;
		if(is_array($_GET[cate])) foreach ($_GET[cate] as $v) if(!preg_match('/^[0-9]*$/',$v)) exit;
		if(is_array($_GET[price])) foreach ($_GET[price] as $v) if(!preg_match('/^[0-9]*$/',$v)) exit;
		if(!preg_match('/^[#a-z0-9]*$/i',$_GET['ssColor'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['sort'])) exit;
	}

	/**
	 * PC > 브랜드
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_brand.php
	 * 파라미터 brand, page, page_num, sort
	 */
	function goods_goods_brand()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['brand'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > 이벤트
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_event.php
	 * 파라미터 sno, page, page_num, sort
	 */
	function goods_goods_event()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > 메인상품진열 더보기 1
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_grp_01.php
	 * 파라미터 page, page_num, sort
	 */
	function goods_goods_grp_01()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > 메인상품진열 더보기 2
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_grp_02.php
	 * 파라미터 page, page_num, sort
	 */
	function goods_goods_grp_02()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > 메인상품진열 더보기 3
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_grp_03.php
	 * 파라미터 page, page_num, sort
	 */
	function goods_goods_grp_03()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > 메인상품진열 더보기 4
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_grp_04.php
	 * 파라미터 page, page_num, sort
	 */
	function goods_goods_grp_04()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > 메인상품진열 더보기 5
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_grp_05.php
	 * 파라미터 page, page_num, sort
	 */
	function goods_goods_grp_05()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > 상품상세 우측 카테고리 리스트
	 * @return void
	 * ----------------------------------------
	 * shop/goods/ajax_cateList.php
	 * 파라미터 goodsno, category, page_num, page
	 */
	function goods_ajax_cateList()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 상품이미지 확대보기
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_popup_large.php
	 * 파라미터 goodsno
	 */
	function goods_goods_popup_large()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * PC > 해시태그 상품리스트
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_hashtag_list.php
	 * 파라미터 hashtag, sort, page_num, page
	 */
	function goods_goods_hashtag_list()
	{
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['hashtag'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 해시태그 가이드 셀링
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_guidedSelling_list.php
	 * 파라미터 guided_no, step, sort, page_num, page, hashtagName
	 */
	function goods_goods_guidedSelling_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['guided_no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['step'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(is_array($_GET[hashtagName])) foreach ($_GET[hashtagName] as $v) if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$v)) exit;

	}

	/**
	 * PC > 품절상품 재입고 알림신청
	 * @return void
	 * ----------------------------------------
	 * shop/goods/popup_request_stocked_noti.php
	 * 파라미터 goodsno
	 */
	function goods_popup_request_stocked_noti()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * Mobile V2 > 상품상세
	 * @return void
	 * ----------------------------------------
	 * m2/goods/view.php
	 * 파라미터 category, goodsno
	 */
	function m2_goods_view()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * Mobile V2 > 상품상세정보 보기
	 * @return void
	 * ----------------------------------------
	 * m2/goods/view_detail.php
	 * 파라미터 goodsno
	 */
	function m2_goods_view_detail()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * Mobile V2 > 상품리스트 & 검색
	 * @return void
	 * ----------------------------------------
	 * m2/goods/list.php
	 * 파라미터 category, kw
	 */
	function m2_goods_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['kw'])) exit;
	}

	/**
	 * Mobile V2 > 브랜드
	 * @return void
	 * ----------------------------------------
	 * m2/goods/brand.php
	 * 파라미터 brand
	 */
	function m2_goods_brand()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['brand'])) exit;
	}

	/**
	 * Mobile V2 > 이벤트
	 * @return void
	 * ----------------------------------------
	 * m2/goods/event.php
	 * 파라미터 mevent_no
	 */
	function m2_goods_event()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['mevent_no'])) exit;
	}

	/**
	 * Mobile V2 > 카테고리
	 * @return void
	 * ----------------------------------------
	 * m2/goods/category.php
	 * 파라미터 now_cate
	 */
	function m2_goods_category()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['now_cate'])) exit;
	}

	/**
	 * Mobile V2 > 상품이미지 확대보기
	 * @return void
	 * ----------------------------------------
	 * m2/goods/view_bigimg.php
	 * 파라미터 goodsno, category
	 */
	function m2_goods_view_bigimg()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
	}

	/**
	 * Mobile V2 > 해시태그 상품리스트
	 * @return void
	 * ----------------------------------------
	 * m2/goods/goods_hashtag_list.php
	 * 파라미터 hashtag
	 */
	function m2_goods_goods_hashtag_list()
	{
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['hashtag'])) exit;
	}

	/**
	 * Mobile V2 > 해시태그 가이드 셀링
	 * @return void
	 * ----------------------------------------
	 * m2/goods/goods_guidedSelling_list.php
	 * 파라미터 guided_no, step, hashtagName
	 */
	function m2_goods_goods_guidedSelling_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['guided_no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['step'])) exit;
		if(is_array($_GET[hashtagName])) foreach ($_GET[hashtagName] as $v) if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$v)) exit;
	}

	/**
	 * Mobile V1 > 상품상세
	 * @return void
	 * ----------------------------------------
	 * m/goods/view.php
	 * 파라미터 category, goodsno
	 */
	function m_goods_view()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * Mobile V1 > 상품리스트 & 검색
	 * @return void
	 * ----------------------------------------
	 * m/goods/list.php
	 * 파라미터 category, kw
	 */
	function m_goods_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['kw'])) exit;
	}

	/**
	 * Mobile V1 > 상품리스트 더보기
	 * @return void
	 * ----------------------------------------
	 * m/goods/list.add.php
	 * 파라미터 category, listSort, kw, page, page_num, listingCnt
	 */
	function m_goods_list_add()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['listSort'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['kw'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['listingCnt'])) exit;
	}

	/**
	 * Mobile V1 > 상품상세 후기 더보기
	 * @return void
	 * ----------------------------------------
	 * m/goods/view.review.get.php
	 * 파라미터 goodsno, pageNum, page, listingCnt
	 */
	function m_goods_view_review_get()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['pageNum'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['listingCnt'])) exit;
	}

	/*===================================================================
	 * 게시판 페이지
	===================================================================*/

	/**
	 * PC > 게시판 리스트
	 * @return void
	 * ----------------------------------------
	 * shop/board/list.php
	 * 파라미터 id, search['all'], search['name'], search['subject'], search['contents'], search['word'], page, subSpeech
	 */
	function board_list()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['all'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['name'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['subject'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['contents'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['search']['word'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;

		include dirname(__FILE__) . '/../conf/bd_'.$_GET['id'].'.php';
		if(empty($_GET['subSpeech']) === false && strpos($bdSubSpeech, $_GET['subSpeech']) === false) exit;
	}

	/**
	 * PC > 게시판 상세
	 * @return void
	 * ----------------------------------------
	 * shop/board/view.php
	 * 파라미터 id, no, search['all'], search['name'], search['subject'], search['contents'], search['word'], page, subSpeech
	 */
	function board_view()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(is_array($_GET['sel'])) foreach ($_GET['sel'] as $v) if(!preg_match('/^[0-9]*$/',$v)) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['all'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['name'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['subject'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['contents'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['search']['word'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;

		include dirname(__FILE__) . '/../conf/bd_'.$_GET['id'].'.php';
		if(empty($_GET['subSpeech']) === false && strpos($bdSubSpeech, $_GET['subSpeech']) === false) exit;
	}

	/**
	 * PC > 게시판 작성
	 * @return void
	 * ----------------------------------------
	 * shop/board/write.php
	 * 파라미터 id, no, mode
	 */
	function board_write()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > 게시판 삭제
	 * @return void
	 * ----------------------------------------
	 * shop/board/delete.php
	 * 파라미터 id, no, sno
	 */
	function board_delete()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
	}

	/**
	 * PC > 게시판 다운로드
	 * @return void
	 * ----------------------------------------
	 * shop/board/download.php
	 * 파라미터 id, no, div
	 */
	function board_download()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['div'])) exit;
	}

	/**
	 * Mobile V2 > 게시판 리스트
	 * @return void
	 * ----------------------------------------
	 * m2/board/list.php
	 * 파라미터 id, search['all'], search['name'], search['subject'], search['contents'], search['word'], page, subSpeech
	 */
	function m2_board_list()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['all'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['name'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['subject'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['contents'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['search']['word'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;

		include dirname(__FILE__) . '/../conf/bd_'.$_GET['id'].'.php';
		if(empty($_GET['subSpeech']) === false && strpos($bdSubSpeech, $_GET['subSpeech']) === false) exit;
	}

	/**
	 * Mobile V2 > 게시판 상세
	 * @return void
	 * ----------------------------------------
	 * m2/board/view.php
	 * 파라미터 id, no, search['all'], search['name'], search['subject'], search['contents'], search['word'], page
	 */
	function m2_board_view()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['all'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['name'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['subject'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['contents'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['search']['word'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V2 > 게시판 작성
	 * @return void
	 * ----------------------------------------
	 * m2/board/write.php
	 * 파라미터 id, no, mode
	 */
	function m2_board_write()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * Mobile V2 > 게시판 삭제
	 * @return void
	 * ----------------------------------------
	 * m2/board/delete.php
	 * 파라미터 id, no, sno
	 */
	function m2_board_delete()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
	}

	/**
	 * Mobile V2 > 게시판 다운로드
	 * @return void
	 * ----------------------------------------
	 * m2/board/download.php
	 * 파라미터 id, no, div
	 */
	function m2_board_download()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['div'])) exit;
	}

	/**
	 * PC > 상품후기 (전체보기)
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_review.php
	 * 파라미터 skey, sword, cate, page, page_num, sort
	 */
	function goods_goods_review()
	{
		if(!preg_match('/^[a-z0-9_\.]*$/i',$_GET['skey'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['sword'])) exit;
		if(is_array($_GET[cate])) foreach ($_GET[cate] as $v) if(!preg_match('/^[0-9]*$/',$v)) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > 상품후기 (상품상세)
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_review_list.php
	 * 파라미터 goodsno, page
	 */
	function goods_goods_review_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 상품후기 작성
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_review_register.php
	 * 파라미터 goodsno, sno, mode
	 */
	function goods_goods_review_register()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > 상품후기 삭제
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_review_del.php
	 * 파라미터 sno, mode
	 */
	function goods_goods_review_del()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * Mobile V2 > 상품후기 작성
	 * @return void
	 * ----------------------------------------
	 * m2/goods/review_register.php
	 * 파라미터 goodsno, sno, mode, ordsno, ordno
	 */
	function m2_goods_review_register()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['ordsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V2 > 상품후기 삭제
	 * @return void
	 * ----------------------------------------
	 * m2/goods/review_delete.php
	 * 파라미터 sno, mode, m_no
	 */
	function m2_goods_review_delete()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['m_no'])) exit;
	}

	/**
	 * PC > 상품문의 (전체보기)
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_qna.php
	 * 파라미터 skey, sword, cate, page, page_num
	 */
	function goods_goods_qna()
	{
		if(!preg_match('/^[a-z0-9_\.]*$/i',$_GET['skey'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['sword'])) exit;
		if(is_array($_GET[cate])) foreach ($_GET[cate] as $v) if(!preg_match('/^[0-9]*$/',$v)) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
	}

	/**
	 * PC > 상품문의 (상품상세)
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_qna_list.php
	 * 파라미터 goodsno, page
	 */
	function goods_goods_qna_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 상품문의 작성
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_qna_register.php
	 * 파라미터 goodsno, sno, mode
	 */
	function goods_goods_qna_register()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > 상품문의 삭제
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_qna_del.php
	 * 파라미터 sno, mode
	 */
	function goods_goods_qna_del()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > 상품문의 내용
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_qna_chk.php
	 * 파라미터 sno, mode
	 */
	function goods_goods_qna_chk()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > 상품문의 비밀번호확인
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_qna_pass.php
	 * 파라미터 sno, mode
	 */
	function goods_goods_qna_pass()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * Mobile V2 > 상품문의 (전체보기)
	 * @return void
	 * ----------------------------------------
	 * m2/goods/goods_qna_list.php
	 * 파라미터 goodsno
	 */
	function m2_goods_goods_qna_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * Mobile V2 > 상품문의 작성
	 * @return void
	 * ----------------------------------------
	 * m2/goods/goods_qna_register.php
	 * 파라미터 goodsno, sno, mode
	 */
	function m2_goods_goods_qna_register()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * Mobile V2 > 상품문의 삭제
	 * @return void
	 * ----------------------------------------
	 * m2/goods/goods_qna_delete.php
	 * 파라미터 sno, mode, m_no
	 */
	function m2_goods_goods_qna_delete()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['m_no'])) exit;
	}

	/**
	 * PC > FAQ:자주하는 질문
	 * @return void
	 * ----------------------------------------
	 * shop/service/faq.php
	 * 파라미터 ssno, sitemcd, sword
	 */
	function service_faq()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ssno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sitemcd'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['sword'])) exit;
	}

	/**
	 * PC > 네이버페이 상품후기 (전체보기)
	 * @return void
	 * ----------------------------------------
	 * shop/goods/checkout_review.php
	 * 파라미터 skey, sword, cate, page, page_num, sort
	 */
	function goods_checkout_review()
	{
		if(!preg_match('/^[a-z0-9_\.]*$/i',$_GET['skey'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['sword'])) exit;
		if(is_array($_GET[cate])) foreach ($_GET[cate] as $v) if(!preg_match('/^[0-9]*$/',$v)) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > 네이버페이 상품후기 (상품상세)
	 * @return void
	 * ----------------------------------------
	 * shop/goods/checkout_review_list.php
	 * 파라미터 goodsno, page
	 */
	function goods_checkout_review_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/*===================================================================
	 * 마이페이지 페이지
	===================================================================*/

	/**
	 * PC > 나의배송지관리 목록/등록/수정
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mydelivery.php
	 * 파라미터 mode, sno
	 */
	function mypage_mydelivery()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
	}

	/**
	 * PC > 주문내역/배송조회 목록
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_orderlist.php
	 * 파라미터 page
	 */
	function mypage_mypage_orderlist()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 주문내역/배송조회 상세
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_orderview.php
	 * 파라미터 ordno
	 */
	function mypage_mypage_orderview()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * PC > 적립금내역
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_emoney.php
	 * 파라미터 page
	 */
	function mypage_mypage_emoney()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 상품보관함
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_wishlist.php
	 * 파라미터 mode, page
	 */
	function mypage_mypage_wishlist()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 1:1문의게시판 목록
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_qna.php
	 * 파라미터 page
	 */
	function mypage_mypage_qna()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 1:1문의게시판 등록
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_qna_register.php
	 * 파라미터 sno, mode
	 */
	function mypage_mypage_qna_register()
	{
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > 1:1문의게시판 삭제
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_qna_del.php
	 * 파라미터 sno, mode
	 */
	function mypage_mypage_qna_del()
	{
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > 1:1문의게시판 주문조회
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_qna_order.php
	 * 파라미터 page
	 */
	function mypage_mypage_qna_order()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 나의 상품후기
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_review.php
	 * 파라미터 page
	 */
	function mypage_mypage_review()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 나의 상품문의
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_qna_goods.php
	 * 파라미터 page
	 */
	function mypage_mypage_qna_goods()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 최근 본 상품목록
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_today.php
	 * 파라미터 page, page_num, sort
	 */
	function mypage_mypage_today()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * Mobile V2 > 주문내역/배송조회 목록
	 * @return void
	 * ----------------------------------------
	 * m2/myp/orderlist.php
	 * 파라미터 page
	 */
	function m2_myp_orderlist()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V2 > 주문내역/배송조회 상세
	 * @return void
	 * ----------------------------------------
	 * m2/myp/orderview.php
	 * 파라미터 ordno
	 */
	function m2_myp_orderview()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V2 > 적립금내역
	 * @return void
	 * ----------------------------------------
	 * m2/myp/emoneylist.php
	 * 파라미터 page
	 */
	function m2_myp_emoneylist()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V2 > 상품보관함
	 * @return void
	 * ----------------------------------------
	 * m2/myp/wishlist.php
	 * 파라미터 mode, page
	 */
	function m2_myp_wishlist()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V2 > 1:1문의게시판 목록
	 * @return void
	 * ----------------------------------------
	 * m2/myp/qna.php
	 * 파라미터 page
	 */
	function m2_myp_qna()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V2 > 1:1문의게시판 등록
	 * @return void
	 * ----------------------------------------
	 * m2/myp/qna_register.php
	 * 파라미터 sno, mode
	 */
	function m2_myp_qna_register()
	{
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * Mobile V2 > 나의 상품후기
	 * @return void
	 * ----------------------------------------
	 * m2/myp/review.php
	 * 파라미터 goodsno, page
	 */
	function m2_myp_review()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V1 > 주문내역/배송조회 목록
	 * @return void
	 * ----------------------------------------
	 * m/myp/orderlist.php
	 * 파라미터 page
	 */
	function m_myp_orderlist()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V1 > 주문내역/배송조회 상세
	 * @return void
	 * ----------------------------------------
	 * m/myp/orderview.php
	 * 파라미터 ordno
	 */
	function m_myp_orderview()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V1 > 적립금내역
	 * @return void
	 * ----------------------------------------
	 * m/myp/emoneylist.php
	 * 파라미터 page
	 */
	function m_myp_emoneylist()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V1 > 상품보관함
	 * @return void
	 * ----------------------------------------
	 * m/myp/wishlist.php
	 * 파라미터 mode, page
	 */
	function m_myp_wishlist()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/*===================================================================
	 * 주문 페이지
	===================================================================*/

	/**
	 * PC > 배송지 목록 및 추가/수정
	 * @return void
	 * ----------------------------------------
	 * shop/order/order_delivery.php
	 * 파라미터 page
	 */
	function order_order_delivery()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['pkind'])) exit;
	}

	/**
	 * PC > 주문완료
	 * @return void
	 * ----------------------------------------
	 * shop/order/order_end.php
	 * 파라미터 ordno
	 */
	function order_order_end()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * PC > 주문실패
	 * @return void
	 * ----------------------------------------
	 * shop/order/order_fail.php
	 * 파라미터 ordno
	 */
	function order_order_fail()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * PC > 주문완료/실패 분기
	 * @return void
	 * ----------------------------------------
	 * shop/order/order_return_url.php
	 * 파라미터 ordno
	 */
	function order_order_return_url()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V2 > 주문완료
	 * @return void
	 * ----------------------------------------
	 * m2/ord/order_end.php
	 * 파라미터 ordno
	 */
	function m2_ord_order_end()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V2 > 주문실패
	 * @return void
	 * ----------------------------------------
	 * m2/ord/order_fail.php
	 * 파라미터 ordno
	 */
	function m2_ord_order_fail()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V2 > 주문완료/실패 분기
	 * @return void
	 * ----------------------------------------
	 * m2/ord/order_return_url.php
	 * 파라미터 ordno
	 */
	function m2_ord_order_return_url()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V1 > 주문완료
	 * @return void
	 * ----------------------------------------
	 * m/ord/order_end.php
	 * 파라미터 ordno
	 */
	function m_ord_order_end()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V1 > 주문실패
	 * @return void
	 * ----------------------------------------
	 * m/ord/order_fail.php
	 * 파라미터 ordno
	 */
	function m_ord_order_fail()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V1 > 주문완료/실패 분기
	 * @return void
	 * ----------------------------------------
	 * m/ord/order_return_url.php
	 * 파라미터 ordno
	 */
	function m_ord_order_return_url()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/*===================================================================
	 * 가격비교
	===================================================================*/

	/**
	 * 가격비교 엔진
	 * @return void
	 * ----------------------------------------
	 * shop/engine/engine.php
	 * 파라미터 mode, allmode, modeView
	 */
	function engine_engine()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['allmode'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['modeView'])) exit;
	}

	/**
	 * 에누리 가격비교
	 * @return void
	 * ----------------------------------------
	 * shop/engine/enuri.php
	 * 파라미터 type, category, page, sort
	 */
	function engine_enuri()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['type'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * 베스바이어
	 * @return void
	 * ----------------------------------------
	 * shop/engine/bb.php
	 * 파라미터 page, sort
	 */
	function engine_bb()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/*===================================================================
	 * proc 폴더
	===================================================================*/

	/**
	 * PC > 쿠폰 다운로드
	 * @return void
	 * ----------------------------------------
	 * shop/proc/dn_coupon_goods.php
	 * 파라미터 couponcd, goodsno
	 */
	function proc_dn_coupon_goods()
	{
		if(!preg_match("/^[0-9\\\\\\']*$/",$_GET['couponcd'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * PC > 해시태그 위젯
	 * @return void
	 * ----------------------------------------
	 * shop/proc/hashtag_widget_list.php
	 * 파라미터 sort, page_num, page
	 */
	function proc_hashtag_widget_list()
	{
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 해시태그 가이드 셀링 위젯
	 * @return void
	 * ----------------------------------------
	 * shop/proc/guidedSellingWidget.php
	 * 파라미터 guided_no, guided_widgetId
	 */
	function proc_guidedSellingWidget()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['guided_no'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['guided_widgetId'])) exit;
	}

	/**
	 * PC > 멀티팝업
	 * @return void
	 * ----------------------------------------
	 * shop/proc/multipopup_content.php
	 * 파라미터 code
	 */
	function proc_multipopup_content()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['code'])) exit;
	}

	/**
	 * Mobile V2 > 퀵메뉴 네이버페이 버튼 출력
	 * @return void
	 * ----------------------------------------
	 * shop/proc/NaverCheckout_Button.php
	 * 파라미터 goodsno
	 */
	function proc_NaverCheckout_Button()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * PC > 관리자에게 메일보내기
	 * @return void
	 * ----------------------------------------
	 * shop/proc/popup_email.php
	 * 파라미터 to, hidden
	 */
	function proc_popup_email()
	{
		if(!ereg("(^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*$)",$_GET['to'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['hidden'])) exit;
	}

	/**
	 * Mobile V2 > FAQ
	 * @return void
	 * ----------------------------------------
	 * m2/proc/faq.php
	 * 파라미터 page_num, page, sitemcd
	 */
	function m2_proc_faq()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sitemcd'])) exit;
	}

	/**
	 * Mobile V2 > 해시태그 가이드 셀링 위젯
	 * @return void
	 * ----------------------------------------
	 * m2/proc/guidedSellingWidget.php
	 * 파라미터 guided_no, guided_widgetId
	 */
	function m2_proc_guidedSellingWidget()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['guided_no'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['guided_widgetId'])) exit;
	}

	/*===================================================================
	 * service 폴더
	===================================================================*/

	/**
	 * PC > 미확인 입금자 리스트
	 * @return void
	 * ----------------------------------------
	 * shop/service/ghostbanker.php
	 * 파라미터 page, date, name
	 */
	function service_ghostbanker()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['date'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['name'])) exit;
	}

	/**
	 * Mobile V2 > 회원가입 추가 동의 항목
	 * @return void
	 * ----------------------------------------
	 * m2/service/termsConsent.php
	 * 파라미터 sno
	 */
	function m2_service_termsConsent()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
	}

	/*===================================================================
	 * 코디상품
	===================================================================*/

	/**
	 * PC > 코디상품 리스트
	 * @return void
	 * ----------------------------------------
	 * shop/setGoods/index.php
	 * 파라미터 pg, (sh), (sp), cody, ll
	 */
	function setGoods_index()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['pg'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['cody'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['ll'])) exit;
	}

	/**
	 * PC > 코디상품 상세
	 * @return void
	 * ----------------------------------------
	 * shop/setGoods/content.php
	 * 파라미터 fn, idx
	 */
	function setGoods_content()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['fn'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['idx'])) exit;
	}

	/**
	 * PC > 코디상품 상세 댓글
	 * @return void
	 * ----------------------------------------
	 * shop/setGoods/comment/comment.php
	 * 파라미터 idx
	 */
	function setGoods_comment_comment()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['idx'])) exit;
	}

	/**
	 * PC > 코디상품 주문
	 * @return void
	 * ----------------------------------------
	 * shop/setGoods/goodsView/goodsView.php
	 * 파라미터 gidx
	 */
	function setGoods_goodsView_goodsView()
	{
		if(!preg_match('/^[0-9,]*$/',$_GET['gidx'])) exit;
	}

	/*===================================================================
	 * 투데이샵
	===================================================================*/

	/**
	 * PC > 투데이샵 게시판 리스트
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/board/list.php
	 * 파라미터 id, search['all'], search['name'], search['subject'], search['contents'], search['word'], page, subSpeech
	 */
	function todayshop_board_list()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['all'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['name'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['subject'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['contents'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['search']['word'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;

		include dirname(__FILE__) . '/../conf/bd_'.$_GET['id'].'.php';
		if(empty($_GET['subSpeech']) === false && strpos($bdSubSpeech, $_GET['subSpeech']) === false) exit;
	}

	/**
	 * PC > 투데이샵 게시판 상세
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/board/view.php
	 * 파라미터 id, no, search['all'], search['name'], search['subject'], search['contents'], search['word'], page, subSpeech
	 */
	function todayshop_board_view()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(is_array($_GET['sel'])) foreach ($_GET['sel'] as $v) if(!preg_match('/^[0-9]*$/',$v)) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['all'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['name'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['subject'])) exit;
		if(!preg_match('/^[a-zA-Z0-9]*$/',$_GET['search']['contents'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['search']['word'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;

		include dirname(__FILE__) . '/../conf/bd_'.$_GET['id'].'.php';
		if(empty($_GET['subSpeech']) === false && strpos($bdSubSpeech, $_GET['subSpeech']) === false) exit;
	}

	/**
	 * PC > 투데이샵 게시판 작성
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/board/write.php
	 * 파라미터 id, no, mode
	 */
	function todayshop_board_write()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > 투데이샵 게시판 삭제
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/board/delete.php
	 * 파라미터 id, no, sno
	 */
	function todayshop_board_delete()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
	}

	/**
	 * PC > 투데이샵 게시판 다운로드
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/board/download.php
	 * 파라미터 id, no, div
	 */
	function todayshop_board_download()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['div'])) exit;
	}

	/**
	 * PC > 투데이샵 상품후기 (전체보기)
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/goods_review.php
	 * 파라미터 skey, sword, cate, page, page_num, sort
	 */
	function todayshop_goods_review()
	{
		if(!preg_match('/^[a-z0-9_\.]*$/i',$_GET['skey'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['sword'])) exit;
		if(is_array($_GET[cate])) foreach ($_GET[cate] as $v) if(!preg_match('/^[0-9]*$/',$v)) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > 투데이샵 상품후기 (상품상세)
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/goods_review_list.php
	 * 파라미터 goodsno, page
	 */
	function todayshop_goods_review_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 투데이샵 상품후기 작성
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/goods_review_register.php
	 * 파라미터 sno, mode
	 */
	function todayshop_goods_review_register()
	{
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > 투데이샵 상품후기 삭제
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/goods_review_del.php
	 * 파라미터 sno, mode
	 */
	function todayshop_goods_review_del()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > 투데이샵 주문완료
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/order_end.php
	 * 파라미터 ordno
	 */
	function todayshop_order_end()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * PC > 투데이샵 주문실패
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/order_fail.php
	 * 파라미터 ordno
	 */
	function todayshop_order_fail()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * PC > 투데이샵 상품 캘린더
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/calendar.php
	 * 파라미터 year, month
	 */
	function todayshop_calendar()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['year'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['month'])) exit;
	}

	/**
	 * PC > 투데이샵 상품 전체보기
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/list.php
	 * 파라미터 page, page_num, keyword, category
	 */
	function todayshop_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['keyword'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
	}

	/**
	 * PC > 투데이샵 상품 상세
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/today_goods.php
	 * 파라미터 tgsno, category
	 */
	function todayshop_today_goods()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['tgsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
	}

	/**
	 * PC > 투데이샵 상품 지역별 보기
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/today_list.php
	 * 파라미터 category, year, month, day
	 */
	function todayshop_today_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['year'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['month'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['day'])) exit;
	}

	/**
	 * PC > 투데이샵 상품 상세 토크
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/today_talk.php
	 * 파라미터 tgsno, page
	 */
	function todayshop_today_talk()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['tgsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 투데이샵 우측 날개 오늘의 상품
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/today_thumb.php
	 * 파라미터 tgsno
	 */
	function todayshop_today_thumb()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['tgsno'])) exit;
	}

	/**
	 * PC > 투데이샵 미사용 파일
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/talks.php
	 * 파라미터 page, page_num
	 */
	function todayshop_talks()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
	}

	/**
	 * PC > 투데이샵 미사용 파일
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/get_talk.php
	 * 파라미터 tgsno, page
	 */
	function todayshop_get_talk()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['tgsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}
}


?>