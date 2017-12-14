<?
/**
 * validParamGd class
 * Parameter ��ȿ������
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
	 * ���� üũ
	 * @return bool
	 */
	function condition()
	{
		// ���� ��� üũ
		if ($this->nowPath === '') return false;

		// ���� ���� üũ (������ ����)
		if (preg_match('/^\/admin/i', $this->nowPath)) return false;

		// ���� üũ (*indb.php üũ)
		if (preg_match('/indb.php/i', $this->nowPath)) return false;

		// GET �Ķ���� üũ
		if (empty($_GET) === true) return false;

		return true;
	}

	/**
	 * ���� ��� ����
	 * @return void
	 * ----------------------------------------
	 * ���� : /admin/basic/adm_basic_index.php
	 * PC�� : /main/index.php
	 * M�� V2 : /m2/index.php
	 * M�� V1 : /m/index.php
	 */
	function getNowPath()
	{
		// Shop ���
		$shopRoot = realpath(dirname(__FILE__).'/../'); // "/www/����/xxx/shop"
		$shopPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $shopRoot); // "/xxx/shop"

		// Home ���
		$homeRoot = realpath(dirname(__FILE__).'/../../'); // "/www/����/xxx"
		$homePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $homeRoot); // "/xxx"

		// Shop ���, Home ��� ����
		$_php_self = $_SERVER['PHP_SELF'];
		if ($shopPath) $_php_self = preg_replace('/^' . addcslashes($shopPath, '/') . '/', '', $_php_self); // "/xxx/shop" ����
		if ($homePath) $_php_self = preg_replace('/^' . addcslashes($homePath, '/') . '/', '', $_php_self); // "/xxx" ����

		$this->nowPath = $_php_self;
	}

	/*===================================================================
	 * ��ǰ���� ������
	===================================================================*/

	/**
	 * PC > ��ǰ��
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_view.php
	 * �Ķ���� category, goodsno
	 */
	function goods_goods_view()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * PC > ��ǰ����Ʈ
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_list.php
	 * �Ķ���� category, page, page_num, sort
	 */
	function goods_goods_list()
	{
		if (!preg_match('/^[0-9]*$/', $_GET['category'])) exit ;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > ��ǰ�˻�
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_search.php
	 * �Ķ���� skey, sword, hid_sword, cate, price, ssColor, page, page_num, sort
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
	 * PC > �귣��
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_brand.php
	 * �Ķ���� brand, page, page_num, sort
	 */
	function goods_goods_brand()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['brand'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > �̺�Ʈ
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_event.php
	 * �Ķ���� sno, page, page_num, sort
	 */
	function goods_goods_event()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > ���λ�ǰ���� ������ 1
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_grp_01.php
	 * �Ķ���� page, page_num, sort
	 */
	function goods_goods_grp_01()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > ���λ�ǰ���� ������ 2
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_grp_02.php
	 * �Ķ���� page, page_num, sort
	 */
	function goods_goods_grp_02()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > ���λ�ǰ���� ������ 3
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_grp_03.php
	 * �Ķ���� page, page_num, sort
	 */
	function goods_goods_grp_03()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > ���λ�ǰ���� ������ 4
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_grp_04.php
	 * �Ķ���� page, page_num, sort
	 */
	function goods_goods_grp_04()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > ���λ�ǰ���� ������ 5
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_grp_05.php
	 * �Ķ���� page, page_num, sort
	 */
	function goods_goods_grp_05()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * PC > ��ǰ�� ���� ī�װ� ����Ʈ
	 * @return void
	 * ----------------------------------------
	 * shop/goods/ajax_cateList.php
	 * �Ķ���� goodsno, category, page_num, page
	 */
	function goods_ajax_cateList()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > ��ǰ�̹��� Ȯ�뺸��
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_popup_large.php
	 * �Ķ���� goodsno
	 */
	function goods_goods_popup_large()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * PC > �ؽ��±� ��ǰ����Ʈ
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_hashtag_list.php
	 * �Ķ���� hashtag, sort, page_num, page
	 */
	function goods_goods_hashtag_list()
	{
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['hashtag'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > �ؽ��±� ���̵� ����
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_guidedSelling_list.php
	 * �Ķ���� guided_no, step, sort, page_num, page, hashtagName
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
	 * PC > ǰ����ǰ ���԰� �˸���û
	 * @return void
	 * ----------------------------------------
	 * shop/goods/popup_request_stocked_noti.php
	 * �Ķ���� goodsno
	 */
	function goods_popup_request_stocked_noti()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * Mobile V2 > ��ǰ��
	 * @return void
	 * ----------------------------------------
	 * m2/goods/view.php
	 * �Ķ���� category, goodsno
	 */
	function m2_goods_view()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * Mobile V2 > ��ǰ������ ����
	 * @return void
	 * ----------------------------------------
	 * m2/goods/view_detail.php
	 * �Ķ���� goodsno
	 */
	function m2_goods_view_detail()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * Mobile V2 > ��ǰ����Ʈ & �˻�
	 * @return void
	 * ----------------------------------------
	 * m2/goods/list.php
	 * �Ķ���� category, kw
	 */
	function m2_goods_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['kw'])) exit;
	}

	/**
	 * Mobile V2 > �귣��
	 * @return void
	 * ----------------------------------------
	 * m2/goods/brand.php
	 * �Ķ���� brand
	 */
	function m2_goods_brand()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['brand'])) exit;
	}

	/**
	 * Mobile V2 > �̺�Ʈ
	 * @return void
	 * ----------------------------------------
	 * m2/goods/event.php
	 * �Ķ���� mevent_no
	 */
	function m2_goods_event()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['mevent_no'])) exit;
	}

	/**
	 * Mobile V2 > ī�װ�
	 * @return void
	 * ----------------------------------------
	 * m2/goods/category.php
	 * �Ķ���� now_cate
	 */
	function m2_goods_category()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['now_cate'])) exit;
	}

	/**
	 * Mobile V2 > ��ǰ�̹��� Ȯ�뺸��
	 * @return void
	 * ----------------------------------------
	 * m2/goods/view_bigimg.php
	 * �Ķ���� goodsno, category
	 */
	function m2_goods_view_bigimg()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
	}

	/**
	 * Mobile V2 > �ؽ��±� ��ǰ����Ʈ
	 * @return void
	 * ----------------------------------------
	 * m2/goods/goods_hashtag_list.php
	 * �Ķ���� hashtag
	 */
	function m2_goods_goods_hashtag_list()
	{
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['hashtag'])) exit;
	}

	/**
	 * Mobile V2 > �ؽ��±� ���̵� ����
	 * @return void
	 * ----------------------------------------
	 * m2/goods/goods_guidedSelling_list.php
	 * �Ķ���� guided_no, step, hashtagName
	 */
	function m2_goods_goods_guidedSelling_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['guided_no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['step'])) exit;
		if(is_array($_GET[hashtagName])) foreach ($_GET[hashtagName] as $v) if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$v)) exit;
	}

	/**
	 * Mobile V1 > ��ǰ��
	 * @return void
	 * ----------------------------------------
	 * m/goods/view.php
	 * �Ķ���� category, goodsno
	 */
	function m_goods_view()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * Mobile V1 > ��ǰ����Ʈ & �˻�
	 * @return void
	 * ----------------------------------------
	 * m/goods/list.php
	 * �Ķ���� category, kw
	 */
	function m_goods_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['kw'])) exit;
	}

	/**
	 * Mobile V1 > ��ǰ����Ʈ ������
	 * @return void
	 * ----------------------------------------
	 * m/goods/list.add.php
	 * �Ķ���� category, listSort, kw, page, page_num, listingCnt
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
	 * Mobile V1 > ��ǰ�� �ı� ������
	 * @return void
	 * ----------------------------------------
	 * m/goods/view.review.get.php
	 * �Ķ���� goodsno, pageNum, page, listingCnt
	 */
	function m_goods_view_review_get()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['pageNum'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['listingCnt'])) exit;
	}

	/*===================================================================
	 * �Խ��� ������
	===================================================================*/

	/**
	 * PC > �Խ��� ����Ʈ
	 * @return void
	 * ----------------------------------------
	 * shop/board/list.php
	 * �Ķ���� id, search['all'], search['name'], search['subject'], search['contents'], search['word'], page, subSpeech
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
	 * PC > �Խ��� ��
	 * @return void
	 * ----------------------------------------
	 * shop/board/view.php
	 * �Ķ���� id, no, search['all'], search['name'], search['subject'], search['contents'], search['word'], page, subSpeech
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
	 * PC > �Խ��� �ۼ�
	 * @return void
	 * ----------------------------------------
	 * shop/board/write.php
	 * �Ķ���� id, no, mode
	 */
	function board_write()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > �Խ��� ����
	 * @return void
	 * ----------------------------------------
	 * shop/board/delete.php
	 * �Ķ���� id, no, sno
	 */
	function board_delete()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
	}

	/**
	 * PC > �Խ��� �ٿ�ε�
	 * @return void
	 * ----------------------------------------
	 * shop/board/download.php
	 * �Ķ���� id, no, div
	 */
	function board_download()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['div'])) exit;
	}

	/**
	 * Mobile V2 > �Խ��� ����Ʈ
	 * @return void
	 * ----------------------------------------
	 * m2/board/list.php
	 * �Ķ���� id, search['all'], search['name'], search['subject'], search['contents'], search['word'], page, subSpeech
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
	 * Mobile V2 > �Խ��� ��
	 * @return void
	 * ----------------------------------------
	 * m2/board/view.php
	 * �Ķ���� id, no, search['all'], search['name'], search['subject'], search['contents'], search['word'], page
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
	 * Mobile V2 > �Խ��� �ۼ�
	 * @return void
	 * ----------------------------------------
	 * m2/board/write.php
	 * �Ķ���� id, no, mode
	 */
	function m2_board_write()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * Mobile V2 > �Խ��� ����
	 * @return void
	 * ----------------------------------------
	 * m2/board/delete.php
	 * �Ķ���� id, no, sno
	 */
	function m2_board_delete()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
	}

	/**
	 * Mobile V2 > �Խ��� �ٿ�ε�
	 * @return void
	 * ----------------------------------------
	 * m2/board/download.php
	 * �Ķ���� id, no, div
	 */
	function m2_board_download()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['div'])) exit;
	}

	/**
	 * PC > ��ǰ�ı� (��ü����)
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_review.php
	 * �Ķ���� skey, sword, cate, page, page_num, sort
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
	 * PC > ��ǰ�ı� (��ǰ��)
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_review_list.php
	 * �Ķ���� goodsno, page
	 */
	function goods_goods_review_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > ��ǰ�ı� �ۼ�
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_review_register.php
	 * �Ķ���� goodsno, sno, mode
	 */
	function goods_goods_review_register()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > ��ǰ�ı� ����
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_review_del.php
	 * �Ķ���� sno, mode
	 */
	function goods_goods_review_del()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * Mobile V2 > ��ǰ�ı� �ۼ�
	 * @return void
	 * ----------------------------------------
	 * m2/goods/review_register.php
	 * �Ķ���� goodsno, sno, mode, ordsno, ordno
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
	 * Mobile V2 > ��ǰ�ı� ����
	 * @return void
	 * ----------------------------------------
	 * m2/goods/review_delete.php
	 * �Ķ���� sno, mode, m_no
	 */
	function m2_goods_review_delete()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['m_no'])) exit;
	}

	/**
	 * PC > ��ǰ���� (��ü����)
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_qna.php
	 * �Ķ���� skey, sword, cate, page, page_num
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
	 * PC > ��ǰ���� (��ǰ��)
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_qna_list.php
	 * �Ķ���� goodsno, page
	 */
	function goods_goods_qna_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > ��ǰ���� �ۼ�
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_qna_register.php
	 * �Ķ���� goodsno, sno, mode
	 */
	function goods_goods_qna_register()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > ��ǰ���� ����
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_qna_del.php
	 * �Ķ���� sno, mode
	 */
	function goods_goods_qna_del()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > ��ǰ���� ����
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_qna_chk.php
	 * �Ķ���� sno, mode
	 */
	function goods_goods_qna_chk()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > ��ǰ���� ��й�ȣȮ��
	 * @return void
	 * ----------------------------------------
	 * shop/goods/goods_qna_pass.php
	 * �Ķ���� sno, mode
	 */
	function goods_goods_qna_pass()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * Mobile V2 > ��ǰ���� (��ü����)
	 * @return void
	 * ----------------------------------------
	 * m2/goods/goods_qna_list.php
	 * �Ķ���� goodsno
	 */
	function m2_goods_goods_qna_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * Mobile V2 > ��ǰ���� �ۼ�
	 * @return void
	 * ----------------------------------------
	 * m2/goods/goods_qna_register.php
	 * �Ķ���� goodsno, sno, mode
	 */
	function m2_goods_goods_qna_register()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * Mobile V2 > ��ǰ���� ����
	 * @return void
	 * ----------------------------------------
	 * m2/goods/goods_qna_delete.php
	 * �Ķ���� sno, mode, m_no
	 */
	function m2_goods_goods_qna_delete()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['m_no'])) exit;
	}

	/**
	 * PC > FAQ:�����ϴ� ����
	 * @return void
	 * ----------------------------------------
	 * shop/service/faq.php
	 * �Ķ���� ssno, sitemcd, sword
	 */
	function service_faq()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ssno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sitemcd'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['sword'])) exit;
	}

	/**
	 * PC > ���̹����� ��ǰ�ı� (��ü����)
	 * @return void
	 * ----------------------------------------
	 * shop/goods/checkout_review.php
	 * �Ķ���� skey, sword, cate, page, page_num, sort
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
	 * PC > ���̹����� ��ǰ�ı� (��ǰ��)
	 * @return void
	 * ----------------------------------------
	 * shop/goods/checkout_review_list.php
	 * �Ķ���� goodsno, page
	 */
	function goods_checkout_review_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/*===================================================================
	 * ���������� ������
	===================================================================*/

	/**
	 * PC > ���ǹ�������� ���/���/����
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mydelivery.php
	 * �Ķ���� mode, sno
	 */
	function mypage_mydelivery()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
	}

	/**
	 * PC > �ֹ�����/�����ȸ ���
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_orderlist.php
	 * �Ķ���� page
	 */
	function mypage_mypage_orderlist()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > �ֹ�����/�����ȸ ��
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_orderview.php
	 * �Ķ���� ordno
	 */
	function mypage_mypage_orderview()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * PC > �����ݳ���
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_emoney.php
	 * �Ķ���� page
	 */
	function mypage_mypage_emoney()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > ��ǰ������
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_wishlist.php
	 * �Ķ���� mode, page
	 */
	function mypage_mypage_wishlist()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 1:1���ǰԽ��� ���
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_qna.php
	 * �Ķ���� page
	 */
	function mypage_mypage_qna()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > 1:1���ǰԽ��� ���
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_qna_register.php
	 * �Ķ���� sno, mode
	 */
	function mypage_mypage_qna_register()
	{
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > 1:1���ǰԽ��� ����
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_qna_del.php
	 * �Ķ���� sno, mode
	 */
	function mypage_mypage_qna_del()
	{
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > 1:1���ǰԽ��� �ֹ���ȸ
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_qna_order.php
	 * �Ķ���� page
	 */
	function mypage_mypage_qna_order()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > ���� ��ǰ�ı�
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_review.php
	 * �Ķ���� page
	 */
	function mypage_mypage_review()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > ���� ��ǰ����
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_qna_goods.php
	 * �Ķ���� page
	 */
	function mypage_mypage_qna_goods()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > �ֱ� �� ��ǰ���
	 * @return void
	 * ----------------------------------------
	 * shop/mypage/mypage_today.php
	 * �Ķ���� page, page_num, sort
	 */
	function mypage_mypage_today()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * Mobile V2 > �ֹ�����/�����ȸ ���
	 * @return void
	 * ----------------------------------------
	 * m2/myp/orderlist.php
	 * �Ķ���� page
	 */
	function m2_myp_orderlist()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V2 > �ֹ�����/�����ȸ ��
	 * @return void
	 * ----------------------------------------
	 * m2/myp/orderview.php
	 * �Ķ���� ordno
	 */
	function m2_myp_orderview()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V2 > �����ݳ���
	 * @return void
	 * ----------------------------------------
	 * m2/myp/emoneylist.php
	 * �Ķ���� page
	 */
	function m2_myp_emoneylist()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V2 > ��ǰ������
	 * @return void
	 * ----------------------------------------
	 * m2/myp/wishlist.php
	 * �Ķ���� mode, page
	 */
	function m2_myp_wishlist()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V2 > 1:1���ǰԽ��� ���
	 * @return void
	 * ----------------------------------------
	 * m2/myp/qna.php
	 * �Ķ���� page
	 */
	function m2_myp_qna()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V2 > 1:1���ǰԽ��� ���
	 * @return void
	 * ----------------------------------------
	 * m2/myp/qna_register.php
	 * �Ķ���� sno, mode
	 */
	function m2_myp_qna_register()
	{
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * Mobile V2 > ���� ��ǰ�ı�
	 * @return void
	 * ----------------------------------------
	 * m2/myp/review.php
	 * �Ķ���� goodsno, page
	 */
	function m2_myp_review()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V1 > �ֹ�����/�����ȸ ���
	 * @return void
	 * ----------------------------------------
	 * m/myp/orderlist.php
	 * �Ķ���� page
	 */
	function m_myp_orderlist()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V1 > �ֹ�����/�����ȸ ��
	 * @return void
	 * ----------------------------------------
	 * m/myp/orderview.php
	 * �Ķ���� ordno
	 */
	function m_myp_orderview()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V1 > �����ݳ���
	 * @return void
	 * ----------------------------------------
	 * m/myp/emoneylist.php
	 * �Ķ���� page
	 */
	function m_myp_emoneylist()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * Mobile V1 > ��ǰ������
	 * @return void
	 * ----------------------------------------
	 * m/myp/wishlist.php
	 * �Ķ���� mode, page
	 */
	function m_myp_wishlist()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/*===================================================================
	 * �ֹ� ������
	===================================================================*/

	/**
	 * PC > ����� ��� �� �߰�/����
	 * @return void
	 * ----------------------------------------
	 * shop/order/order_delivery.php
	 * �Ķ���� page
	 */
	function order_order_delivery()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['pkind'])) exit;
	}

	/**
	 * PC > �ֹ��Ϸ�
	 * @return void
	 * ----------------------------------------
	 * shop/order/order_end.php
	 * �Ķ���� ordno
	 */
	function order_order_end()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * PC > �ֹ�����
	 * @return void
	 * ----------------------------------------
	 * shop/order/order_fail.php
	 * �Ķ���� ordno
	 */
	function order_order_fail()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * PC > �ֹ��Ϸ�/���� �б�
	 * @return void
	 * ----------------------------------------
	 * shop/order/order_return_url.php
	 * �Ķ���� ordno
	 */
	function order_order_return_url()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V2 > �ֹ��Ϸ�
	 * @return void
	 * ----------------------------------------
	 * m2/ord/order_end.php
	 * �Ķ���� ordno
	 */
	function m2_ord_order_end()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V2 > �ֹ�����
	 * @return void
	 * ----------------------------------------
	 * m2/ord/order_fail.php
	 * �Ķ���� ordno
	 */
	function m2_ord_order_fail()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V2 > �ֹ��Ϸ�/���� �б�
	 * @return void
	 * ----------------------------------------
	 * m2/ord/order_return_url.php
	 * �Ķ���� ordno
	 */
	function m2_ord_order_return_url()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V1 > �ֹ��Ϸ�
	 * @return void
	 * ----------------------------------------
	 * m/ord/order_end.php
	 * �Ķ���� ordno
	 */
	function m_ord_order_end()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V1 > �ֹ�����
	 * @return void
	 * ----------------------------------------
	 * m/ord/order_fail.php
	 * �Ķ���� ordno
	 */
	function m_ord_order_fail()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * Mobile V1 > �ֹ��Ϸ�/���� �б�
	 * @return void
	 * ----------------------------------------
	 * m/ord/order_return_url.php
	 * �Ķ���� ordno
	 */
	function m_ord_order_return_url()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/*===================================================================
	 * ���ݺ�
	===================================================================*/

	/**
	 * ���ݺ� ����
	 * @return void
	 * ----------------------------------------
	 * shop/engine/engine.php
	 * �Ķ���� mode, allmode, modeView
	 */
	function engine_engine()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['allmode'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['modeView'])) exit;
	}

	/**
	 * ������ ���ݺ�
	 * @return void
	 * ----------------------------------------
	 * shop/engine/enuri.php
	 * �Ķ���� type, category, page, sort
	 */
	function engine_enuri()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['type'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/**
	 * �������̾�
	 * @return void
	 * ----------------------------------------
	 * shop/engine/bb.php
	 * �Ķ���� page, sort
	 */
	function engine_bb()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
	}

	/*===================================================================
	 * proc ����
	===================================================================*/

	/**
	 * PC > ���� �ٿ�ε�
	 * @return void
	 * ----------------------------------------
	 * shop/proc/dn_coupon_goods.php
	 * �Ķ���� couponcd, goodsno
	 */
	function proc_dn_coupon_goods()
	{
		if(!preg_match("/^[0-9\\\\\\']*$/",$_GET['couponcd'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * PC > �ؽ��±� ����
	 * @return void
	 * ----------------------------------------
	 * shop/proc/hashtag_widget_list.php
	 * �Ķ���� sort, page_num, page
	 */
	function proc_hashtag_widget_list()
	{
		if(!preg_match('/^[^\s]*\s*[^\s]*\s*[^\s]*\s*[^\s]*$/',$_GET['sort'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > �ؽ��±� ���̵� ���� ����
	 * @return void
	 * ----------------------------------------
	 * shop/proc/guidedSellingWidget.php
	 * �Ķ���� guided_no, guided_widgetId
	 */
	function proc_guidedSellingWidget()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['guided_no'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['guided_widgetId'])) exit;
	}

	/**
	 * PC > ��Ƽ�˾�
	 * @return void
	 * ----------------------------------------
	 * shop/proc/multipopup_content.php
	 * �Ķ���� code
	 */
	function proc_multipopup_content()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['code'])) exit;
	}

	/**
	 * Mobile V2 > ���޴� ���̹����� ��ư ���
	 * @return void
	 * ----------------------------------------
	 * shop/proc/NaverCheckout_Button.php
	 * �Ķ���� goodsno
	 */
	function proc_NaverCheckout_Button()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
	}

	/**
	 * PC > �����ڿ��� ���Ϻ�����
	 * @return void
	 * ----------------------------------------
	 * shop/proc/popup_email.php
	 * �Ķ���� to, hidden
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
	 * �Ķ���� page_num, page, sitemcd
	 */
	function m2_proc_faq()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sitemcd'])) exit;
	}

	/**
	 * Mobile V2 > �ؽ��±� ���̵� ���� ����
	 * @return void
	 * ----------------------------------------
	 * m2/proc/guidedSellingWidget.php
	 * �Ķ���� guided_no, guided_widgetId
	 */
	function m2_proc_guidedSellingWidget()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['guided_no'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['guided_widgetId'])) exit;
	}

	/*===================================================================
	 * service ����
	===================================================================*/

	/**
	 * PC > ��Ȯ�� �Ա��� ����Ʈ
	 * @return void
	 * ----------------------------------------
	 * shop/service/ghostbanker.php
	 * �Ķ���� page, date, name
	 */
	function service_ghostbanker()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['date'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['name'])) exit;
	}

	/**
	 * Mobile V2 > ȸ������ �߰� ���� �׸�
	 * @return void
	 * ----------------------------------------
	 * m2/service/termsConsent.php
	 * �Ķ���� sno
	 */
	function m2_service_termsConsent()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
	}

	/*===================================================================
	 * �ڵ��ǰ
	===================================================================*/

	/**
	 * PC > �ڵ��ǰ ����Ʈ
	 * @return void
	 * ----------------------------------------
	 * shop/setGoods/index.php
	 * �Ķ���� pg, (sh), (sp), cody, ll
	 */
	function setGoods_index()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['pg'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['cody'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['ll'])) exit;
	}

	/**
	 * PC > �ڵ��ǰ ��
	 * @return void
	 * ----------------------------------------
	 * shop/setGoods/content.php
	 * �Ķ���� fn, idx
	 */
	function setGoods_content()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['fn'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['idx'])) exit;
	}

	/**
	 * PC > �ڵ��ǰ �� ���
	 * @return void
	 * ----------------------------------------
	 * shop/setGoods/comment/comment.php
	 * �Ķ���� idx
	 */
	function setGoods_comment_comment()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['idx'])) exit;
	}

	/**
	 * PC > �ڵ��ǰ �ֹ�
	 * @return void
	 * ----------------------------------------
	 * shop/setGoods/goodsView/goodsView.php
	 * �Ķ���� gidx
	 */
	function setGoods_goodsView_goodsView()
	{
		if(!preg_match('/^[0-9,]*$/',$_GET['gidx'])) exit;
	}

	/*===================================================================
	 * �����̼�
	===================================================================*/

	/**
	 * PC > �����̼� �Խ��� ����Ʈ
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/board/list.php
	 * �Ķ���� id, search['all'], search['name'], search['subject'], search['contents'], search['word'], page, subSpeech
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
	 * PC > �����̼� �Խ��� ��
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/board/view.php
	 * �Ķ���� id, no, search['all'], search['name'], search['subject'], search['contents'], search['word'], page, subSpeech
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
	 * PC > �����̼� �Խ��� �ۼ�
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/board/write.php
	 * �Ķ���� id, no, mode
	 */
	function todayshop_board_write()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > �����̼� �Խ��� ����
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/board/delete.php
	 * �Ķ���� id, no, sno
	 */
	function todayshop_board_delete()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
	}

	/**
	 * PC > �����̼� �Խ��� �ٿ�ε�
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/board/download.php
	 * �Ķ���� id, no, div
	 */
	function todayshop_board_download()
	{
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['no'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['div'])) exit;
	}

	/**
	 * PC > �����̼� ��ǰ�ı� (��ü����)
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/goods_review.php
	 * �Ķ���� skey, sword, cate, page, page_num, sort
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
	 * PC > �����̼� ��ǰ�ı� (��ǰ��)
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/goods_review_list.php
	 * �Ķ���� goodsno, page
	 */
	function todayshop_goods_review_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['goodsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > �����̼� ��ǰ�ı� �ۼ�
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/goods_review_register.php
	 * �Ķ���� sno, mode
	 */
	function todayshop_goods_review_register()
	{
		if($_GET['sno'] != 'null' && $_GET['sno'] != 'undefined' && !preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > �����̼� ��ǰ�ı� ����
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/goods_review_del.php
	 * �Ķ���� sno, mode
	 */
	function todayshop_goods_review_del()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['sno'])) exit;
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;
	}

	/**
	 * PC > �����̼� �ֹ��Ϸ�
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/order_end.php
	 * �Ķ���� ordno
	 */
	function todayshop_order_end()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * PC > �����̼� �ֹ�����
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/order_fail.php
	 * �Ķ���� ordno
	 */
	function todayshop_order_fail()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['ordno'])) exit;
	}

	/**
	 * PC > �����̼� ��ǰ Ķ����
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/calendar.php
	 * �Ķ���� year, month
	 */
	function todayshop_calendar()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['year'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['month'])) exit;
	}

	/**
	 * PC > �����̼� ��ǰ ��ü����
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/list.php
	 * �Ķ���� page, page_num, keyword, category
	 */
	function todayshop_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
		if(preg_match('/(select)(\s|\().*(from|sleep)(\s|\().*(from|sleep)/i',$_GET['keyword'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
	}

	/**
	 * PC > �����̼� ��ǰ ��
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/today_goods.php
	 * �Ķ���� tgsno, category
	 */
	function todayshop_today_goods()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['tgsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
	}

	/**
	 * PC > �����̼� ��ǰ ������ ����
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/today_list.php
	 * �Ķ���� category, year, month, day
	 */
	function todayshop_today_list()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['year'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['month'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['day'])) exit;
	}

	/**
	 * PC > �����̼� ��ǰ �� ��ũ
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/today_talk.php
	 * �Ķ���� tgsno, page
	 */
	function todayshop_today_talk()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['tgsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}

	/**
	 * PC > �����̼� ���� ���� ������ ��ǰ
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/today_thumb.php
	 * �Ķ���� tgsno
	 */
	function todayshop_today_thumb()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['tgsno'])) exit;
	}

	/**
	 * PC > �����̼� �̻�� ����
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/talks.php
	 * �Ķ���� page, page_num
	 */
	function todayshop_talks()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page_num'])) exit;
	}

	/**
	 * PC > �����̼� �̻�� ����
	 * @return void
	 * ----------------------------------------
	 * shop/todayshop/get_talk.php
	 * �Ķ���� tgsno, page
	 */
	function todayshop_get_talk()
	{
		if(!preg_match('/^[0-9]*$/',$_GET['tgsno'])) exit;
		if(!preg_match('/^[0-9]*$/',$_GET['page'])) exit;
	}
}


?>