<?
/*
 * ���̹� EP DB ���� ����
 * @version 1.0
 * @date 2016-06-30
 */
include dirname(__FILE__) . '/../lib/partner.class.php';

class naverPartner extends Partner
{
	var $tmp_filename = "";
	var $new_filename = "";
	var $offset = "";
	var $goods_cnt = '';
	var $page_cnt = '';
	var $partner = '';

	function __construct()
	{
		global $partner;

		$dirPath = dirname(__FILE__) . '/../conf/naver_ep';
		if (is_dir($dirPath) === false) {
			mkdir($dirPath);
			chmod($dirPath,0707);
		}

		if (!$partner) {
			@include dirname(__FILE__) . "/../conf/partner.php";
		}
		$this->partner = $partner;
		
		if ($this->partner['naver_version'] == '2') {
			$this->tmp_filename = $dirPath.'/naver2_tmp.txt';
			$this->new_filename = $dirPath.'/naver2_new.txt';
			$this->old_filename = $dirPath.'/naver2_old.txt';
		}
		else if ($this->partner['naver_version'] == '3') {
			$this->tmp_filename = $dirPath.'/naver3_tmp.txt';
			$this->new_filename = $dirPath.'/naver3_new.txt';
			$this->old_filename = $dirPath.'/naver3_old.txt';
		}
		
		$this->offset = 200000;
		$this->goods_cnt = 0;
		$this->page_cnt = 1;
	}

	/*
	 * ��ü EP ����
	 * @return bool
	 */
	function allEp()
	{
		global $db,$cfg,$cfgCoupon,$set,$cfgMobileShop;

		if (!$cfgCoupon) {
			@include dirname(__FILE__) . "/../conf/coupon.php";
		}
		if (!$set) {
			@include dirname(__FILE__) . "/../conf/config.pay.php";
		}
		if (!$cfgMobileShop) {
			@include dirname(__FILE__) . "/../conf/config.mobileShop.php";
		}

		$domain = '';
		if ($_SERVER['HTTP_HOST'] != '') {
			$domain = $_SERVER['HTTP_HOST'];
		}
		else if($cfg['shopUrl']){
			$domain = preg_replace('/http(s)?:\/\//' , '', $cfg['shopUrl']);
		}
		else {
			return false;
		}

		$url = "http://".$domain.$cfg['rootDir'];

		$columns = $this->checkColumnNaver();		// EP ������ �ʿ��� �÷� Ȯ��
		$couponData = $this->getCouponInfo();		// ����
		$memberdc = $this->getBasicDc();			// ȸ������
		$catnm = $this->getCatnm();					// ī�װ���
		$brandnm = $this->getBrand();				// �귣���
		$discountData = $this->getDiscount();		// ��ǰ����
		$review = $this->getReview();				// ���� ����
		$query = $this->getGoodsSql($columns);		// ��ǰ ���
		$res = $db->query($query);
	
		//���� �ʱ�ȭ
		$this->naverFileDrop("",'',"w");

		while ($v = $db->fetch($res,1)){
			// 499000�� ��ǰ�� ����
			if ($this->goods_cnt == 499000) break;
			
			//ī�װ�
			$length = strlen($v['category'])/3;
			for ($i=1;$i<=4;$i++) {
				$tmp=substr($v['category'],0,$i*3);
				$v['cate'.$i]=($i<=$length) ? strip_tags($catnm[$tmp]) : '';
				$v['caid'.$i]=($i<=$length) ? $tmp : '';
			}

			// ��ǰ�� ����
			$goodsDiscount = 0;
			if ($v['use_goods_discount'] == '1') {
				$goodsDiscount = $this->getDiscountPrice($discountData,$v['goodsno'],$v['goods_price']);
			}

			$couponVersion = false; // ���� ����
			if ($cfgCoupon['coupon'] && is_file(dirname(__FILE__).'/../data/skin/'.$cfg['tplSkin'].'/proc/popup_coupon_division.htm')) {
				$couponVersion = true;
			}

			//ȸ������
			$dcprice = 0;
			if ($this->partner['unmemberdc'] == 'N') {	// ȸ���������뿩��
				if (is_array($memberdc) === true) {
					$mdc_exc = chk_memberdc_exc($memberdc,$v['goodsno']); // ȸ������ ���ܻ�ǰ üũ
					if ($mdc_exc === false) $dcprice = getDcprice($v['goods_price'],$memberdc['dc'].'%');
				}
			}

			// ���� ���� ���� ����
			$coupon = 0;		// ���� ���� �ݾ�
			$couponReserve = 0;	// ���� ����

			if ($cfgCoupon['use_yn'] && $this->partner['uncoupon'] == 'N') {
				list($coupon,$couponReserve) = $this->getCouponPrice($couponData, $v['category'], $v['goodsno'], $v['goods_price']);
				if ($coupon > $v['goods_price'] - $dcprice - $goodsDiscount && $couponVersion === true) $coupon = $v['goods_price'] - $dcprice - $goodsDiscount;
			}

			// ���� ȸ������ �ߺ� ���� üũ
			if ($coupon > 0 && $dcprice > 0) {
				if ($cfgCoupon['range'] == 2) $dcprice = 0;
				if ($cfgCoupon['range'] == 1) {
					$coupon = 0;
				}
			}

			// ���� ����
			$price = 0;
			$price = $v['goods_price'] - $coupon - $dcprice - $goodsDiscount;

			// ��ۺ�
			$deliv = $this->getDeliveryPrice($v,$price);

			// �̹���
			$img_url = '';
			$img_name = '';
			if (!$v['img_l'] || $v['img_l'] == '') {
				if (!$v['img_m'] || $v['img_m'] == '') {
					continue;
				}
				else {
					$img_name = $v['img_m'];
				}
			}
			else {
				$img_name = $v['img_l'];
			}
			$img_url = $this->getGoodsImg($img_name,$url);

			// ������
			$point = 0;
			if ($v['use_emoney']=='0') {
				if (!$set['emoney']['chk_goods_emoney']) {
					if ($set['emoney']['goods_emoney']) {
						$dc=$set['emoney']['goods_emoney']."%";
						$tmp_price = $v['goods_price'];
						if ($set['emoney']['cut']) $po = pow(10,$set['emoney']['cut']);
						else $po = 100;
						$tmp_price = (substr($dc,-1)=="%") ? $tmp_price * substr($dc,0,-1) / 100 : $dc;
						$point =  floor($tmp_price / $po) * $po;

					}
				}
				else {
					$point = $set['emoney']['goods_emoney'];
				}
			}
			else {
				$point = $v['goods_reserve'];
			}
			$point += $couponReserve;

			$extra_info = gd_json_decode(stripslashes($row['extra_info']));
			$dlvDesc = '';
			$addPrice = '';
			$isDlv = '';
			$isAddPrice = '';
			$isCoupon = '';
			if (is_array($extra_info)) {
				foreach($extra_info as $key=>$val) {
					if($val['title'] == '��� �� ��ġ���'){
						$dlvDesc = $val['desc'];
					}
					if($val['title'] == '�߰���ġ���'){
						$addPrice = $val['desc'];
					}
				}
			}
			if ($dlvDesc) {
				$isDlv = 'Y';
			}
			if ($addPrice) {
				$isAddPrice = 'Y';
			}
			if ($coupon>0) {
				$isCoupon = 'Y';
			}

			// �귣��� ��������
			$v['brandnm'] = $brandnm[$v['brandno']];

			// ��ǰ�� �Ӹ��� ����
			$v['goodsnm'] = $this->getGoodsnm($this->partner,$v);

			// �̺�Ʈ
			$event = '';
			if ($this->partner['naver_event_common'] === 'Y' && empty($this->partner['eventCommonText']) === false) {	// ���� ����
				$event = $this->partner['eventCommonText'];
			}

			if ($this->partner['naver_event_goods'] === 'Y' && empty($v['naver_event']) === false) {	// ��ǰ�� ����
				if (empty($event) === false) $event .= ' , ';
				$event .= $v['naver_event'];
			}

			$v['event'] = strip_tags($event);

			$line_data = '<<<begin>>>'.chr(10);
			$line_data .= '<<<mapid>>>'.$v['goodsno'].chr(10);
			$line_data .= '<<<pname>>>'.$v['goodsnm'].chr(10);
			$line_data .= '<<<price>>>'.$price.chr(10);
			$line_data .= '<<<pgurl>>>'.$url.'/goods/goods_view.php?goodsno='.$v['goodsno'].'&inflow=naver'.chr(10);
			$line_data .= '<<<igurl>>>'.$img_url.chr(10);
			$line_data .= '<<<cate1>>>'.$v['cate1'].chr(10);
			$line_data .= '<<<caid1>>>'.$v['caid1'].chr(10);
			$line_data .= '<<<cate2>>>'.$v['cate2'].chr(10);
			$line_data .= '<<<caid2>>>'.$v['caid2'].chr(10);
			$line_data .= '<<<cate3>>>'.$v['cate3'].chr(10);
			$line_data .= '<<<caid3>>>'.$v['caid3'].chr(10);
			$line_data .= '<<<cate4>>>'.$v['cate4'].chr(10);
			$line_data .= '<<<caid4>>>'.$v['caid4'].chr(10);
			if($v['brandnm'])$line_data .= '<<<brand>>>'.$v['brandnm'].chr(10);
			if($v['maker'])$line_data .= '<<<maker>>>'.$v['maker'].chr(10);
			if($v['origin'])$line_data .= '<<<origi>>>'.$v['origin'].chr(10);
			$line_data .= '<<<deliv>>>'.$deliv.chr(10);
			$line_data .= '<<<event>>>'.$v['event'].chr(10);
			if ($coupon)$line_data .= '<<<coupo>>>'.$coupon.chr(10);
			if($this->partner['nv_pcard'])$line_data .= '<<<pcard>>>'.$this->partner['nv_pcard'].chr(10);
			if($point)$line_data .= '<<<point>>>'.$point.chr(10);
			$line_data .= '<<<revct>>>'.(!$review[$v['goodsno']]?0:$review[$v['goodsno']]).chr(10);
			if (isset($cfgMobileShop) && $cfgMobileShop['useMobileShop'] == '1' && $domain) $line_data .= '<<<mourl>>>http://'.$domain.'/m/goods/view.php?goodsno='.$v['goodsno'] .'&inflow=naver'.chr(10);
			else  $line_data .= '<<<mourl>>>'.chr(10);
			$line_data .= '<<<pcpdn>>>'.$isCoupon.chr(10);
			$line_data .= '<<<dlvga>>>'.$isDlv.chr(10);
			$line_data .= '<<<dlvdt>>>'.$dlvDesc.chr(10);
			$line_data .= '<<<insco>>>'.$isAddPrice.chr(10);
			$line_data .= '<<<ftend>>>'.chr(10);

			$fw = '';
			$fw = $this->naverFileDrop($line_data,$this->goods_cnt);
			unset($v);
			unset($line_data);
			if ($fw === false) return false;
			$this->goods_cnt++;
		}

		$this->naverFileMerge($this->page_cnt);
		return true;
	}

	/*
	 * EP ���� ���� �� ���� ����
	 * @return int
	 */
	function naverFileDrop($line_data,$goods_cnt,$mode="")
	{
		if ($goods_cnt > $this->offset) {
			$this->page_cnt = $this->page_cnt+1;
			$this->offset = $this->offset*$this->page_cnt;
		}

		if ($mode == 'w') {
			$handle = fopen($this->tmp_filename.$this->page_cnt, "w");
		} else {
			$handle = fopen($this->tmp_filename.$this->page_cnt, "a");
		}
		$rc = fwrite($handle,$line_data);
		if ($rc === false) {
			fclose($handle);
			unlink($this->tmp_filename);
			return false;
		}

		fclose($handle);
	}

	/*
	 * ���ҵ� EP ���� merge
	 * @return int
	 */
	function naverFileMerge($page_cnt)
	{
		//�ʱ�ȭ
		exec("cat /dev/null > ".$this->new_filename);

		for ($i=1; $i<=$page_cnt; $i++) {
			exec("cat ".$this->tmp_filename.$i." >> ".$this->new_filename);
			unlink($this->tmp_filename.$i);
		}

		chmod($this->new_filename,0707);
	}

	/*
	 * ���̹� EP���� ���Ǵ� �÷� Ȯ��
	 * @return Array
	 */
	function checkColumnNaver()
	{
		global $db;
		$columns = array();
		$naverColumns = array (
			'goodsno', 'goodsnm','goods_price','goods_reserve', 'origin','maker', 'brandno', 'delivery_type', 'goods_delivery', 'img_l', 'img_m', 'use_emoney', 'open_mobile', 'use_goods_discount', 'extra_info', 'naver_event', 'goods_status', 'min_ea', 'sales_unit', 'strprice','use_only_adult','exclude_member_discount', 'naver_import_flag', 'naver_product_flag', 'naver_age_group', 'naver_gender', 'naver_attribute', 'naver_search_tag', 'naver_category', 'naver_product_id'
			);

		$query = "desc gd_goods";
		$column_name = array();

		$res = $db->query($query);
		while ($column = $db->fetch($res)) {
			if (in_array($column['Field'],$naverColumns)) {
				$columns[] = 'a.'.$column['Field'];
			}
		}
		return $columns;
	}

	/*
	 * ��ǰ ������ ����
	 * @param Array $columns (checkColumnNaver ���ϰ�)
	 * @return string
	 */
	function getGoodsSql($columns)
	{
		$checkNaverShoppingCategoryCount = false;
		$checkNaverShoppingCategoryCount = $this->checkNaverShoppingCategoryCount();
		if ($checkNaverShoppingCategoryCount === true) {
			$query = "
				SELECT 
					".implode(',',$columns).", b.category 
				FROM 
					".GD_GOODS_LINK." AS b
				INNER JOIN
					".GD_GOODS." AS a
				ON
					b.goodsno=a.goodsno
					AND a.open=1
					AND !( a.runout = 1 OR (a.usestock = 'o' AND a.usestock IS NOT NULL AND a.totstock < 1) )
					AND ( (a.sales_range_start < UNIX_TIMESTAMP() AND UNIX_TIMESTAMP() < a.sales_range_end)
					OR (a.sales_range_start < UNIX_TIMESTAMP() AND a.sales_range_end = '0')
					OR (UNIX_TIMESTAMP() < a.sales_range_end AND a.sales_range_start = '0')
					OR (a.sales_range_start = '0' AND a.sales_range_end = '0') )
				WHERE 
					EXISTS (SELECT c.category FROM ".GD_CATEGORY." AS c INNER JOIN ".GD_NAVERSHOPPING_CATEGORY." AS d ON LEFT(c.category,LENGTH(d.category))=d.category WHERE c.hidden=0 and c.category=b.category )
				GROUP BY 
					a.goodsno
				ORDER BY 
					a.goodsno DESC
				LIMIT 
					500000
			";
		}
		else {
			$query = "SELECT ".implode(',',$columns)." , b.category FROM ".GD_GOODS." a inner join ";
			$query .= "(SELECT c.goodsno,c.category FROM ".GD_GOODS_LINK." c inner join ".GD_CATEGORY." gc on gc.category=c.category WHERE gc.category!='' AND gc.category IS NOT NULL AND c.hidden='0' GROUP BY c.goodsno) b ";
			$query .= "WHERE a.goodsno=b.goodsno ";
			$query .= "AND a.open=1 ";
			$query .= "AND !( a.runout = 1 OR (a.usestock = 'o' AND a.usestock IS NOT NULL AND a.totstock < 1) ) ";
			$query .= "AND ( (a.sales_range_start < UNIX_TIMESTAMP() AND UNIX_TIMESTAMP() < a.sales_range_end) ";
			$query .= "OR (a.sales_range_start < UNIX_TIMESTAMP() AND a.sales_range_end = '0') ";
			$query .= "OR (UNIX_TIMESTAMP() < a.sales_range_end AND a.sales_range_start = '0') ";
			$query .= "OR (a.sales_range_start = '0' AND a.sales_range_end = '0') ) ";
			$query .= "ORDER BY a.goodsno DESC LIMIT 500000";
		}

		return $query;
	}

	function checkNaverShoppingCategoryCount()
	{
		global $db;

		$cnt = 0;
		$result = false;
		list($cnt) = $db->fetch("SELECT COUNT(*) FROM ".GD_NAVERSHOPPING_CATEGORY);
		if($cnt > 0){
			$result = true;
		}

		return $result;
	}

	/*
	 * ���� ���� ���
	 * @param $couponData(����������),$category(��ǰī�װ�),$goodsno(��ǰ��ȣ),$price(��ǰ����)
	 * @return Array ($coupon : �������ΰ���, $couponReserve : ������)
	 */
	function getCouponPrice($couponData,$category,$goodsno,$price)
	{
		global $cfgCoupon;
		$arCategory = array();
		$couponcd = array();
		$coupon = 0;
		$mobileCoupon = 0;

		// ī�װ� �з����� �з�
		for($i=3; $i<=strlen($category); $i=$i+3) {
			$arCategory[] = substr($category,0,$i);
		}

		for ($i=0; $i<count($couponData); $i++) {
			// �ѹ� ���� �����̸� ����
			if (in_array($couponData[$i]['couponcd'],$couponcd)) {
				continue;
			}
			// �ݾ� ������ ��ǰ ���ݺ��� ������ ����
			else if ($couponData[$i]['excPrice'] > $price) {
				continue;
			}

			$couponTemp = 0;
			$reserveTemp = 0;
			if ($couponData[$i]['goodstype'] == '0' ||	// ��ü��ǰ �߱� �����϶�
				($couponData[$i]['goodstype'] == '1' && $couponData[$i]['goodsno'] != '' && $goodsno == $couponData[$i]['goodsno']) ||	// Ư�� ��ǰ �߱� �����϶�
				($couponData[$i]['goodstype'] == '1' && $couponData[$i]['category'] != '' && in_array($couponData[$i]['category'],$arCategory))) {	// Ư�� ī�װ� �߱� �����϶�
				$couponcd[] = $couponData[$i]['couponcd'];	// ����� ������ȣ �迭�� ����

				// ������ ����
				if ($couponData[$i]['ability'] == '1') {
					if (strpos($couponData[$i]['price'],'%') == true) {
						$reserveTemp = substr($couponData[$i]['price'] , 0, -1);
						$reserveTemp = $reserveTemp/100*$price;
					}
					else $reserveTemp = $couponData[$i]['price'];
					$reserveTemp = $this->cut($reserveTemp);	// ����

					// ���� ��� �ߺ� ����
					if ($cfgCoupon['double'] == '1' && $couponData[$i]['c_screen'] != 'm') {
						$couponReserve += $reserveTemp;
					}
					// ���� �ߺ� ��� �Ұ�
					else if ($reserveTemp > $couponReserve && $couponData[$i]['c_screen'] != 'm') {
						$couponReserve = $reserveTemp;
					}
				}
				// �ݾ� ���� ����
				else if ($couponData[$i]['ability'] == '0') {
					if (strpos($couponData[$i]['price'],'%') == true) {
						$couponTemp = substr($couponData[$i]['price'] , 0, -1);
						$couponTemp = $couponTemp/100*$price;
					}
					else $couponTemp = $couponData[$i]['price'];
					$couponTemp = $this->cut($couponTemp);	// ����

					// ���� ��� �ߺ� ����
					if ($cfgCoupon['double'] == '1' && $couponData[$i]['c_screen'] != 'm') {
						$coupon += $couponTemp;
					}
					// ���� �ߺ� ��� �Ұ�
					else if ($couponTemp > $coupon && $couponData[$i]['c_screen'] != 'm') {
						$coupon = $couponTemp;
					}
				}
			}
		}

		$return = array($coupon,$couponReserve);
		return $return;
	}

	/*
	 * ���̹� EP �ڵ����� ��� ���� üũ
	 * @return bool
	 */
	function epAutoUseChk()
	{
		if ($this->partner['auto_create_use'] == 'Y') {
			return true;
		}
		else {
			return false;
		}
	}

	/*
	 * ���̹� EP ������ ���� ���� üũ
	 * @param $file : ���ϸ�
	 * @return bool
	 */
	function epFileChk($file)
	{
		if (file_exists($file)) {
			return true;
		}
		else {
			return false;
		}
	}

	/*
	 * ���̹� EP ���� ����� ���ϸ� ����
	 * return void
	 */
	function epPrint()
	{
		// new ������ �������� 24�ð�(���� �ֱ�) �ʰ��Ǿ��ٸ� �ٽ� ����
		$fileTime = time() - filemtime($this->new_filename);
		if ($fileTime/3600 > 24) {
			$this->epCreatePrint();
		}
		else {
			if ($this->epFileChk($this->new_filename) === true) {
				copy($this->new_filename,$this->old_filename);
				$location = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->new_filename);
				header("Location:" . $location);
				exit;
			}
		}
	}

	/*
	 * ���̹� EP ���� ���� �� ���� ��� �� ���� ����
	 * return void
	 */
	function epCreatePrint()
	{
	// EP ���� ����
		if ($this->partner['naver_version'] == '2') {
			$result = $this->allEp();
		}
		else if ($this->partner['naver_version'] == '3') {
			$result = $this->allEp3();
		}

		// ���Ͼ��� ���� ����� old���� ���
		if ($result === false) {
			if ($this->epFileChk($this->old_filename) === true) {
				$location = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->old_filename);
				header("Location:" . $location);
				exit;
			}
		}
		// ������ ���� �����ְ� new ���� old�� ����
		else {
			if ($this->epFileChk($this->new_filename) === true) {
				copy($this->new_filename,$this->old_filename);
				$location = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->new_filename);
				header("Location:" . $location);
				exit;
			}
		}
	}

	/*
	 * ��� EP ����
	 * @return bool
	 */
	function summaryEp()
	{
		global $db, $cfgCoupon, $cfg, $cfgMobileShop;

		$memberdc = $this->getBasicDc();		// �⺻ ȸ�� ������
		$discountData = $this->getDiscount();	// ��ǰ ���� ������
		$couponData = $this->getCouponInfo();	// ���� ������

		$tmp = date("Y-m-d 00:00:00");
		$db->query("delete from ".GD_GOODS_UPDATE_NAVER." where utime < '$tmp'");
		$query = "select
					no,class,mapid,utime,pname,price,pgurl,igurl,cate1,cate2,cate3,cate4,caid1,caid2,caid3,caid4,
					model,brand,maker,origi,pdate,deliv,event,coupo,pcard,point,modig,score,mvurl,ptype,dterm,risky
					from ".GD_GOODS_UPDATE_NAVER." order by no asc";
		$result = $db->query($query);

		$couponVersion = false; // ���� ����
		if($cfgCoupon['coupon'] && is_file(dirname(__FILE__).'/../data/skin/'.$cfg['tplSkin'].'/proc/popup_coupon_division.htm')) {
			$couponVersion = true;
		}

		while($row = $db->fetch($result,1))
		{
			$query = "select a.goodsnm, b.price, a.maker, c.brandnm, a.sales_range_start, a.sales_range_end, d.category from ".GD_GOODS." as a left join ".GD_GOODS_OPTION." as b on a.goodsno=b.goodsno and go_is_deleted <> '1' and go_is_display = '1' left join ".GD_GOODS_BRAND." as c on a.brandno=c.sno left join ".GD_GOODS_LINK." as d on a.goodsno=d.goodsno where b.link=1 and a.goodsno='$row[mapid]'";
			$_row = $db->fetch($query);

			// �Ǹ� ����(�Ⱓ �� ����)�� ��� ����
			if (($_row['sales_range_start'] > time() && time() > $_row['sales_range_end']) ||
				($_row['sales_range_start'] > time() && $_row['sales_range_end'] == '') ||
				(time() > $_row['sales_range_end'] && $_row['sales_range_start'] == '') ||
				($_row['sales_range_start'] == '' && $_row['sales_range_end'] == '')) {
				continue;
			}

			// ȸ����������
			$dcprice = 0;
			if ($this->partner['unmemberdc'] === 'N') {
				if (is_array($memberdc) === true) {
					$mdc_exc = chk_memberdc_exc($memberdc, $row['mapid']); // ȸ������ ���ܻ�ǰ üũ
					if ($mdc_exc === false) {
						$dcprice = getDcprice($_row['price'], $memberdc['dc'].'%');
					}
				}
			}

			// ���ΰ���
			$discountPrice = $this->getDiscountPrice($discountData,$row['mapid'],$_row['price']);

			// �Ｎ��������
			$coupon = 0;
			if ($cfgCoupon['use_yn'] && $this->partner['uncoupon'] === 'N') {
				list($couponDiscount, $couponEmoney) = $this->getCouponPrice($couponData, $row['category'], $row['mapid'], $_row['price']);
				if ($couponDiscount) {
					$coupon = getDcprice($_row['price'], $couponDiscount);
				}
			}

			// ���� ȸ������ �ߺ� ���� üũ
			if ($coupon > 0 && $dcprice > 0) {
				if ($cfgCoupon['range'] == 2) {
					$dcprice = 0;
				}
				if ($cfgCoupon['range'] == 1) {
					$coupon = 0;
				}
			}

			// ���� ����
			$coupon += 0;
			$dcprice += 0;
			if ($coupon > $_row['price'] - $dcprice - $discountPrice && $couponVersion === true) $coupon = $_row['price'] - $dcprice - $discountPrice;
			$_row['price'] = $_row['price'] - $coupon - $dcprice - $discountPrice;

			if ($_row) extract($_row);

			if($this->partner['goodshead']){
				$goodsnm=str_replace(array('{_maker}','{_brand}'),array($maker,$brandnm),$this->partner['goodshead']).strip_tags($goodsnm);
			}else{
				$goodsnm=strip_tags($goodsnm);
			}

			// �̺�Ʈ
			if ($row['event'] != null) {
				$event = '';
				if ($this->partner['naver_event_common'] === 'Y' && empty($this->partner['eventCommonText']) === false) {	// ���� ����
					$event = $this->partner['eventCommonText'];
				}

				if ($this->partner['naver_event_goods'] === 'Y' && empty($row['event']) === false) {	// ��ǰ�� ����
					if (empty($event) === false) $event .= ' , ';
					$event .= $row['event'];
				}

				$row['event'] = strip_tags($event);
			}

			$mapid = $row['mapid'];
			$class = $row['class'];
			$utime = $row['utime'];

			unset($row['no']);
			unset($row['mapid']);
			unset($row['class']);
			unset($row['utime']);
			unset($row['pname']);
			unset($row['price']);

			echo "<<<begin>>>\n";
			echo '<<<mapid>>>'.$mapid."\n";
			if($class != 'D'){
				echo "<<<pname>>>".$goodsnm."\n";
				echo '<<<price>>>'.$price."\n";
				if (isset($cfgMobileShop) && $cfgMobileShop['useMobileShop'] == '1') {
					$row['mourl'] = 'http://'.$_SERVER['HTTP_HOST'].'/m/goods/view.php?goodsno='.$mapid.'&inflow=naver';
				}
				else {
					$row['mourl'] = '';
				}
			}
			foreach($row as $key=>$value)
			{
				if($key == 'pdate') continue;
				if(!is_null($value)) echo '<<<'.$key.'>>>'.$value."\n";
			}
			echo '<<<class>>>'.$class."\n";
			echo '<<<utime>>>'.$utime."\n";
			echo "<<<ftend>>>\n";
		}
	}

	/*
	 * ���̹� ���� ���÷��� ī�װ� ����
	 * @param array $postData
	 * @return boolean
	 */
	function saveDisplayCategory($postData)
	{
		global $db;

		$processStop = false;

		//������̺��
		$backupTableName = GD_NAVERSHOPPING_CATEGORY . "_backup_".date("YmdHis");

		//���
		$res = $db->query("CREATE TABLE ".$backupTableName." SELECT * FROM ".GD_NAVERSHOPPING_CATEGORY);
		if(!$res) return false;

		//���� ����
		$res = $db->query("TRUNCATE TABLE ".GD_NAVERSHOPPING_CATEGORY);
		if(!$res) return false;

		//����
		$insertCategoryArray = array();
		$insertCategoryArray = @array_chunk($postData, 100);
		if(count($insertCategoryArray) > 0){
			foreach($insertCategoryArray as $categoryArray){
				$query = "INSERT INTO ".GD_NAVERSHOPPING_CATEGORY." (category) VALUES ('".implode("'),('", $categoryArray)."')";
				$result = $db->query($query);
				if(!$result){
					$processStop = true;
					break;
				}
			}
		}

		//����
		if($processStop === true){
			$res = $db->query("TRUNCATE TABLE ".GD_NAVERSHOPPING_CATEGORY);
			if(!$res) return false;

			$res = $db->query("INSERT INTO ".GD_NAVERSHOPPING_CATEGORY." SELECT * FROM ".$backupTableName);
			if(!$res) return false;

			return false;
		}
		else {
			$db->query("DROP TABLE ".$backupTableName);
		}

		return true;
	}

	/*
	 * ���õ� ī�װ� ��ǰ ���� ����
	 * @param Array $category
	 * @return string
	 */
	function getSelectGoodsCount($category='')
	{
		$categoryList = array();
		$categoryArray = array();
		$categoryIn = array();
		$where = '';
		$temp = '';

		if ($category) {
			$categoryArray = explode(',',$category);
			for ($i=0; $i<count($categoryArray); $i++) {
				$categoryList[] = $this->getLowCategoryNumber($categoryArray[$i]);
			}

			$categoryIn = implode("','",$categoryList);
			if ($categoryIn) {
				$where = "c.category IN ('".$categoryIn."') AND ";
			}
		}

		$query = "SELECT count(*) FROM ".GD_GOODS." a ,";
		$query .= "(SELECT c.goodsno FROM ".GD_GOODS_LINK." c left join ".GD_CATEGORY." gc on gc.category=c.category WHERE ".$where." c.hidden='0' GROUP BY c.goodsno) b ";
		$query .= "WHERE a.goodsno=b.goodsno ";
		$query .= "AND a.open=1 ";
		$query .= "AND !( a.runout = 1 OR (a.usestock = 'o' AND a.usestock IS NOT NULL AND a.totstock < 1) ) ";
		$query .= "AND ( (a.sales_range_start < UNIX_TIMESTAMP() AND UNIX_TIMESTAMP() < a.sales_range_end) ";
		$query .= "OR (a.sales_range_start < UNIX_TIMESTAMP() AND a.sales_range_end = '0') ";
		$query .= "OR (UNIX_TIMESTAMP() < a.sales_range_end AND a.sales_range_start = '0') ";
		$query .= "OR (a.sales_range_start = '0' AND a.sales_range_end = '0') )";

		return $query;
	}

	/*
	 * �� ��ǰ ���� ����
	 * @return string
	 */
	function getGoodsAllCount()
	{
		$query = "SELECT count(a.goodsno) FROM gd_goods a ";
		$query .= "where exists (SELECT c.goodsno FROM gd_goods_link c inner join ".GD_CATEGORY." gc on gc.category=c.category WHERE gc.category!='' AND gc.category IS NOT NULL AND c.hidden='0' AND a.goodsno=c.goodsno ";
		$query .= "AND a.open=1 ";
		$query .= "AND !( a.runout = 1 OR (a.usestock = 'o' AND a.usestock IS NOT NULL AND a.totstock < 1) ) ";
		$query .= "AND ( (a.sales_range_start < UNIX_TIMESTAMP() AND UNIX_TIMESTAMP() < a.sales_range_end) ";
		$query .= "OR (a.sales_range_start < UNIX_TIMESTAMP() AND a.sales_range_end = '0') ";
		$query .= "OR (UNIX_TIMESTAMP() < a.sales_range_end AND a.sales_range_start = '0') ";
		$query .= "OR (a.sales_range_start = '0' AND a.sales_range_end = '0') ) GROUP BY c.goodsno)";

		return $query;
	}

	/*
	 * ī�װ��� ��ǰ ���� ����
	 * @param Array $category
	 * @return string
	 */
	function getGoodsCount($category='')
	{
		$query = "SELECT count(a.goodsno) FROM ".GD_GOODS." a inner join ";
		$query .= "(SELECT c.goodsno FROM ".GD_GOODS_LINK." c inner join ".GD_CATEGORY." gc on gc.category=c.category WHERE c.category like '".$category."%' AND gc.category!='' AND gc.category IS NOT NULL AND c.hidden='0' GROUP BY c.goodsno) b ";
		$query .= "WHERE a.goodsno=b.goodsno ";
		$query .= "AND a.open=1 ";
		$query .= "AND !( a.runout = 1 OR (a.usestock = 'o' AND a.usestock IS NOT NULL AND a.totstock < 1) ) ";
		$query .= "AND ( (a.sales_range_start < UNIX_TIMESTAMP() AND UNIX_TIMESTAMP() < a.sales_range_end) ";
		$query .= "OR (a.sales_range_start < UNIX_TIMESTAMP() AND a.sales_range_end = '0') ";
		$query .= "OR (UNIX_TIMESTAMP() < a.sales_range_end AND a.sales_range_start = '0') ";
		$query .= "OR (a.sales_range_start = '0' AND a.sales_range_end = '0') )";

		return $query;
	}

	/*
	 * ���� ī�װ� ��ȣ ��ȯ
	 * @param $category
	 * @return array
	 */
	function getLowCategoryNumber($category)
	{
		global $db;

		$categoryList = array();
		$res = $db->query("select category from ".GD_CATEGORY." where category like '".$category."%'");
		while ($data = $db->fetch($res,1)) {
			$categoryList[] = $data['category'];
		}

		return implode("','",$categoryList);
	}

	/*
	 * ���÷��� ī�װ� ��ȯ
	 * @param void
	 * @return array
	 */
	function getCategoryList()
	{
		global $db;

		$categoryList = array();
		$res = $db->query("SELECT category FROM gd_navershopping_category");
		while ($data = $db->fetch($res,1)) {
			$categoryList[] = $this->getLowCategoryNumber($data['category']);
		}

		return $categoryList;
	}

	/*
	 * ���÷��� ī�װ� ���� �� �̸� ��ȯ
	 * @param void
	 * @return array
	 */
	function getCategoryDetailed()
	{
		global $db;

		$categoryList = array();
		$query = "
			SELECT a.category, b.catnm FROM
				".GD_NAVERSHOPPING_CATEGORY." AS a
			INNER JOIN 
				".GD_CATEGORY." AS b
			ON
				a.category=b.category AND hidden=0
		";

		$res = $db->query($query);
		if($res){
			$i=0;
			while($category = $db->fetch($res, 1)){

				//ī�װ� ��ȣ
				$categoryList[$i]['category'] = $category['category'];
				//īƼ�� ��
				$categoryList[$i]['catnm'] = $category['catnm'];
		
				$query = "
					SELECT COUNT(DISTINCT a.goodsno) AS cnt FROM 
						".GD_GOODS_LINK." AS a
					INNER JOIN
						".GD_GOODS." AS b
					ON 
						a.goodsno=b.goodsno 
						AND 
						b.open=1 
						AND 
						!( b.runout = 1 OR (b.usestock = 'o' AND b.usestock IS NOT NULL AND b.totstock < 1) ) 
						AND 
						( (b.sales_range_start < UNIX_TIMESTAMP() AND UNIX_TIMESTAMP() < b.sales_range_end) OR (b.sales_range_start < UNIX_TIMESTAMP() AND b.sales_range_end = '0') OR (UNIX_TIMESTAMP() < b.sales_range_end AND b.sales_range_start = '0') OR (b.sales_range_start = '0' AND b.sales_range_end = '0') ) 
					WHERE
						a.category LIKE '".$category['category']."%'
				";

				//ī��Ʈ
				list($categoryList[$i]['count']) = $db->fetch($query);

				$i++;
			}
		}

		return $categoryList;
	}
	
	/*
	 * ��ü EP 3.0 ����
	 * @return bool
	 */
	function allEp3()
	{
		global $db,$cfg,$cfgCoupon,$set,$cfgMobileShop;
	
		if (!$cfgCoupon) {
			@include dirname(__FILE__) . "/../conf/coupon.php";
		}
		if (!$set) {
			@include dirname(__FILE__) . "/../conf/config.pay.php";
		}
		if (!$cfgMobileShop) {
			@include dirname(__FILE__) . "/../conf/config.mobileShop.php";
		}
	
		$domain = '';
		if ($_SERVER['HTTP_HOST'] != '') {
			$domain = $_SERVER['HTTP_HOST'];
		}
		else if($cfg['shopUrl']){
			$domain = preg_replace('/http(s)?:\/\//' , '', $cfg['shopUrl']);
		}
		else {
			return false;
		}
	
		$url = "http://".$domain.$cfg['rootDir'];
	
		$columns = $this->checkColumnNaver();		// EP ������ �ʿ��� �÷� Ȯ��
		$couponData = $this->getCouponInfo();		// ����
		$memberdc = $this->getBasicDc();			// ȸ������
		$catnm = $this->getCatnm();					// ī�װ���
		$brandnm = $this->getBrand();				// �귣���
		$discountData = $this->getDiscount();		// ��ǰ����
		$review = $this->getReview();				// ���� ����
		$query = $this->getGoodsSql($columns);		// ��ǰ ���
		$res = $db->query($query);
	
		//���� �ʱ�ȭ
		$this->naverFileDrop("",'',"w");
	
		while ($v = $db->fetch($res,1)){
			// 499000�� ��ǰ�� ����
			if ($this->goods_cnt == 499000) break;
	
			// �ǹ��� �������� ġȯ
			$v = str_replace(chr(9),' ',$v);
			$this->partner['nv_pcard'] = str_replace(chr(9),' ',$this->partner['nv_pcard']);
			$this->partner['goodshead'] = str_replace(chr(9),' ',$this->partner['goodshead']);
			$this->partner['eventCommonText'] = str_replace(chr(9),' ',$this->partner['eventCommonText']);
	
			// ���ݴ�ü���� üũ
			if ($v['strprice']) continue;
	
			// �̹���
			$img_url = '';
			$img_name = '';
			if (!$v['img_l'] || $v['img_l'] == '' || $v['img_l'] == 'Ȯ��(����)�̹���') {
				if (!$v['img_m'] || $v['img_m'] == '' || $v['img_m'] == '���̹���') {
					continue;
				}
				else {
					$img_name = $v['img_m'];
				}
			}
			else {
				$img_name = $v['img_l'];
			}
			$img_url = $this->getGoodsImg($img_name,$url);
	
			// ī�װ�
			$length = strlen($v['category'])/3;
			for ($i=1;$i<=4;$i++) {
				$tmp=substr($v['category'],0,$i*3);
				$v['cate'.$i]=($i<=$length) ? strip_tags($catnm[$tmp]) : '';
			}
	
			// ��ǰ�� ����
			$goodsDiscount = 0;
			if ($v['use_goods_discount'] == '1') {
				$goodsDiscount = $this->getDiscountPrice($discountData,$v['goodsno'],$v['goods_price']);
			}
	
			$couponVersion = false; // ���� ����
			if ($cfgCoupon['coupon'] && is_file(dirname(__FILE__).'/../data/skin/'.$cfg['tplSkin'].'/proc/popup_coupon_division.htm')) {
				$couponVersion = true;
			}
	
			// ȸ������
			$dcprice = 0;
			if ($this->partner['unmemberdc'] == 'N' && $v['exclude_member_discount'] != 1) {	// ȸ���������뿩��
				if (is_array($memberdc) === true) {
					$mdc_exc = chk_memberdc_exc($memberdc,$v['goodsno']); // ȸ������ ���ܻ�ǰ üũ
					if ($mdc_exc === false) $dcprice = getDcprice($v['goods_price'],$memberdc['dc'].'%');
				}
			}
	
			// ���� ���� ���� ����
			$coupon = 0;		// ���� ���� �ݾ�
			$couponReserve = 0;	// ���� ����
	
			if ($cfgCoupon['use_yn'] && $this->partner['uncoupon'] == 'N') {
				list($coupon,$couponReserve) = $this->getCouponPrice($couponData, $v['category'], $v['goodsno'], $v['goods_price']);
				if ($coupon > $v['goods_price'] - $dcprice - $goodsDiscount && $couponVersion === true) $coupon = $v['goods_price'] - $dcprice - $goodsDiscount;
			}
	
			// ���� ȸ������ �ߺ� ���� üũ
			if ($coupon > 0 && $dcprice > 0) {
				if ($cfgCoupon['range'] == 2) $dcprice = 0;
				if ($cfgCoupon['range'] == 1) {
					$coupon = 0;
				}
			}
	
			// ���� ����
			$price = 0;
			$price = $v['goods_price'] - $dcprice - $goodsDiscount;
	
			// �ּұ��ż��� * ���� ����
			if ($v['min_ea'] > 0) {
				$price = $price * $v['min_ea'];
			}
			else if ($v['min_ea'] == 0 && $v['sales_unit'] > 0) {
				$price = $price * $v['sales_unit'];
			}
	
			$price = $price - $coupon;
			if ($price < 1) continue;
	
			// ��ۺ�
			$deliv = $this->getDeliveryPrice($v,$price);
	
			// �߰� �̹��� URL
			$addImgUrl = '';
			if ($v['img_m']) {
				$addImgUrl = explode('|',$v['img_m']);
				for ($i=0; $i<count($addImgUrl); $i++) {
					if(!preg_match('/^http(s)?:\/\//',$addImgUrl[$i])) $addImgUrl[$i] = $url.'/data/goods/'.$addImgUrl[$i];
				}
				$addImgUrl = implode('|',$addImgUrl);
			}
	
			// ������
			$point = 0;
			if ($v['use_emoney']=='0') {
				if (!$set['emoney']['chk_goods_emoney']) {
					if ($set['emoney']['goods_emoney']) {
						$dc=$set['emoney']['goods_emoney']."%";
						$tmp_price = $v['goods_price'];
						if ($set['emoney']['cut']) $po = pow(10,$set['emoney']['cut']);
						else $po = 100;
						$tmp_price = (substr($dc,-1)=="%") ? $tmp_price * substr($dc,0,-1) / 100 : $dc;
						$point =  floor($tmp_price / $po) * $po;
	
					}
				}
				else {
					$point = $set['emoney']['goods_emoney'];
				}
			}
			else {
				$point = $v['goods_reserve'];
			}
			$point += $couponReserve;
	
			// ��ǰ �ʼ� ����
			$extra_info = gd_json_decode(stripslashes($v['extra_info']));
			$dlvDesc = '';
			$addPrice = '';
			if (is_array($extra_info)) {
				foreach($extra_info as $key=>$val) {
					if($val['title'] == '��� �� ��ġ���'){
						$dlvDesc = $val['desc'];
					}
					if($val['title'] == '�߰���ġ���'){
						$addPrice = $val['desc'];
					}
				}
			}
	
			// �귣��� ��������
			$v['brandnm'] = $brandnm[$v['brandno']];
	
			// ��ǰ�� �Ӹ��� ����
			$v['goodsnm'] = $this->getGoodsnm($this->partner,$v);
	
			// �̺�Ʈ
			$event = '';
			if ($this->partner['naver_event_common'] === 'Y' && empty($this->partner['eventCommonText']) === false) {	// ���� ����
				$event = $this->partner['eventCommonText'];
			}
	
			if ($this->partner['naver_event_goods'] === 'Y' && empty($v['naver_event']) === false) {	// ��ǰ�� ����
				if (empty($event) === false) $event .= ' , ';
				$event .= $v['naver_event'];
			}
			$v['event'] = strip_tags($event);
	
			// ��ǰ����
			switch ($v['goods_status']) {
				case 'N':
					$v['goods_status'] = '�Ż�ǰ';
					break;
				case 'U':
					$v['goods_status'] = '�߰�';
					break;
				case 'P':
					$v['goods_status'] = '����';
					break;
				case 'E':
					$v['goods_status'] = '����';
					break;
				case 'R':
					$v['goods_status'] = '��ǰ';
					break;
				case 'S':
					$v['goods_status'] = '��ũ��ġ';
					break;
				default :
					$v['goods_status'] = '';
					break;
			}
	
			// ���� �� ���� ����
			$import_flag = '';
			$parallel_import = '';
			$naver_order_made = '';
			switch ($v['naver_import_flag']) {
				case '1':
					$import_flag = 'Y';
					break;
				case '2':
					$parallel_import = 'Y';
					break;
				case '3':
					$naver_order_made = 'Y';
					break;
				default :
					break;
			}
	
			// �ǸŹ�� ����
			switch ($v['naver_product_flag']) {
				case '1':
					$v['naver_product_flag'] = '����';
					break;
				case '2':
					$v['naver_product_flag'] = '��Ż';
					break;
				case '3':
					$v['naver_product_flag'] = '�뿩';
					break;
				case '4':
					$v['naver_product_flag'] = '�Һ�';
					break;
				case '5':
					$v['naver_product_flag'] = '�����Ǹ�';
					break;
				case '6':
					$v['naver_product_flag'] = '���Ŵ���';
					break;
				default :
					$v['naver_product_flag'] = '';
					break;
			}
	
			// �ֿ� ��� ���ɴ�
			switch ($v['naver_age_group']) {
				case '1':
					$v['naver_age_group'] = 'û�ҳ�';
					break;
				case '2':
					$v['naver_age_group'] = '�Ƶ�';
					break;
				case '3':
					$v['naver_age_group'] = '����';
					break;
				default :
					$v['naver_age_group'] = '����';
					break;
			}
	
			// �ֿ� ��� ����
			switch ($v['naver_gender']) {
				case '1':
					$v['naver_gender'] = '����';
					break;
				case '2':
					$v['naver_gender'] = '����';
					break;
				case '3':
					$v['naver_gender'] = '��������';
					break;
				default :
					$v['naver_gender'] = '';
					break;
			}
	
			// �˻� �±� ���� ����
			$v['naver_search_tag'] = str_replace(' ','',$v['naver_search_tag']);
	
			$mobile_url = '';
			if (isset($cfgMobileShop) && $cfgMobileShop['useMobileShop'] == '1' && $domain) {
				$mobile_url = 'http://'.$domain.'/m/goods/view.php?goodsno='.$v['goodsno'] .'&inflow=naver';
			}
	
			// ù�� ��� ����
			if ($this->goods_cnt == 0) {
				$epArray = array(
						'id',							// ��ǰ��ȣ
						'title',						// ��ǰ��
						'price_pc',						// ��ǰ����
						'link',							// ��ǰ �� ������ URL
						'image_link',					// ��ǰ �̹��� URL
						'mobile_link',					// ����� �� ������ URL
						'add_image_link',				// �߰� �̹��� URL (���̹���)
						'category_name1',				// ī�װ��� ��з�
						'category_name2',				// ī�װ��� �ߺ���
						'category_name3',				// ī�װ��� �Һз�
						'category_name4',				// ī�װ��� ���з�
						'naver_category',				// ���̹� ī�װ� ID
						'naver_product_id',				// ���̹� ���ݺ� ������ ID
						'condition',					// ��ǰ����
						'import_flag',					// �ؿܱ��Ŵ��� ����
						'parallel_import',				// ������� ����
						'order_made',					// �ֹ����ۻ�ǰ
						'product_flag',					// �ǸŹ�� ����
						'adult',						// �̼����� ���źҰ� ��ǰ ����
						'brand',						// �귣��
						'maker',						// ������
						'origin',						// ������
						'event_words',					// �̺�Ʈ
						'coupon',						// ���� ���� �ݾ�
						'partner_coupon_download',		// ���� �ٿ�ε� �ʿ� ����
						'interest_free_event',			// ī�� ������ �Һ� ����
						'point',						// ������
						'installation_costs',			// ���� ��ġ�� ����
						'search_tag',					// �˻��±�
						'minimum_purchase_quantity',	// �ּұ��ż���
						'review_count',					// ��ǰ�� ����
						'shipping',						// ��ۺ�
						'delivery_grade',				// �����ۺ� ����
						'delivery_detail',				// �����ۺ� ����
						'attribute',					// ��ǰ�Ӽ�
						'age_group',					// �� �̿� ����
						'gender'						// ����
				);
				if ($mobile_url == '') {
					unset($epArray[5]);
				}
				$line_data = implode(chr(9),$epArray).chr(10);
			}
	
			$line_data .= $v['goodsno'].chr(9);
			$line_data .= $v['goodsnm'].chr(9);
			$line_data .= $price.chr(9);
			$line_data .= $url.'/goods/goods_view.php?goodsno='.$v['goodsno'].'&inflow=naver'.chr(9);
			$line_data .= $img_url.chr(9);
			$line_data .= ($mobile_url ? $mobile_url.chr(9) : '');
			$line_data .= $addImgUrl.chr(9);							// �߰� �̹��� URL
			$line_data .= $v['cate1'].chr(9);							// ī�װ��� ��з�
			$line_data .= $v['cate2'].chr(9);							// ī�װ��� �ߺз�
			$line_data .= $v['cate3'].chr(9);							// ī�װ��� �Һз�
			$line_data .= $v['cate4'].chr(9);							// ī�װ��� ���з�
			$line_data .= $v['naver_category'].chr(9);					// ���̹� ī�װ� ID
			$line_data .= $v['naver_product_id'].chr(9);				// ���ݺ� ������ ID
			$line_data .= $v['goods_status'].chr(9);					// ��ǰ����
			$line_data .= $import_flag.chr(9);							// �ؿܱ��Ŵ��� ����
			$line_data .= $parallel_import.chr(9);						// ������� ����
			$line_data .= $naver_order_made.chr(9);						// �ֹ����ۻ�ǰ
			$line_data .= $v['naver_product_flag'].chr(9);				// �ǸŹ�� ����
			$line_data .= ($v['use_only_adult'] ? 'Y' : '').chr(9);		// �̼����� ���źҰ� ��ǰ ����
			$line_data .= $v['brandnm'].chr(9);							// �귣��
			$line_data .= $v['maker'].chr(9);							// ������
			$line_data .= $v['origin'].chr(9);							// ������
			$line_data .= $v['event'].chr(9);							// �̺�Ʈ
			$line_data .= (($coupon == 0)? '':$coupon).chr(9);			// ���� ���� �ݾ�
			$line_data .= ($coupon > 0 ? 'Y' : '').chr(9);				// ���� �ٿ�ε� �ʿ� ����
			$line_data .= $this->partner['nv_pcard'].chr(9);			// ī�� ������ �Һ� ����
			$line_data .= ($point > 0 ? '���θ���ü����Ʈ^'.$point : '').chr(9);		// ������
			$line_data .= ($addPrice ? 'Y' : '').chr(9);				// ���� ��ġ�� ����
			$line_data .= $v['naver_search_tag'].chr(9);				// �˻��±�
			$line_data .= $v['min_ea'].chr(9);							// �ּұ��ż���
			$line_data .= ($review[$v['goodsno']]?$review[$v['goodsno']]:0).chr(9);	// ��ǰ�� ����
			$line_data .= $deliv.chr(9);								// ��ۺ�
			$line_data .= ($dlvDesc ? 'Y' : '').chr(9);					// �����ۺ� ����
			$line_data .= ($dlvDesc ? $dlvDesc : '').chr(9);			// �����ۺ� ����
			$line_data .= $v['naver_attribute'].chr(9);					// ��ǰ �Ӽ�
			$line_data .= $v['naver_age_group'].chr(9);					// �� �̿� ����
			$line_data .= $v['naver_gender']		;					// ����
			$line_data .= chr(10);
	
			$fw = '';
			$fw = $this->naverFileDrop($line_data,$this->goods_cnt);
			unset($v);
			unset($line_data);
			if ($fw === false) return false;
			$this->goods_cnt++;
		}
	
		$this->naverFileMerge($this->page_cnt);
		return true;
	}
}
?>