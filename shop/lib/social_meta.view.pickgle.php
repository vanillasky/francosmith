<?
$_meta_site_info['pickgle'] = array('name'=>'픽글','url'=>'http://www.pickgle.net');

// 픽글 뷰
class social_meta_view_pickgle extends social_meta_view {

	var $data = array();

	function social_meta_view_pickgle() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<coupon_feed>';
		$_xml .= '<doc_ver>1</doc_ver>';
		$_xml .= '<name><![CDATA['.$this->cfg['shopName'].']]></name>';
		$_xml .= '<url><![CDATA[http://'.$this->cfg['shopUrl'].']]></url>';
		$todayshop_logo = $_SERVER['DOCUMENT_ROOT'].'/shop/data/todayshop/todayshop_logo.jpg';
		if (is_file($todayshop_logo)) {
			$_img_url = str_replace($_SERVER['DOCUMENT_ROOT'],'http://'.$_SERVER['SERVER_NAME'],$todayshop_logo);
			$_xml .= '<logo_image><![CDATA['.$_img_url.']]></logo_image>';
		}
		$_xml .= '<deals>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row['status'] = '진행중';

			$row = $this->encode($row);

			$_xml .= '
			<deal>
				<meta_id><![CDATA['.$row['tgsno'].']]></meta_id>
				<start_at><![CDATA['.$row['startdt'].']]></start_at>
				<end_at><![CDATA['.$row['enddt'].']]></end_at>
				<coupon_start_at><![CDATA['.$row['usestartdt'].']]></coupon_start_at>
				<coupon_end_at><![CDATA['.$row['useenddt'].']]></coupon_end_at>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<url><![CDATA['.$row['url'].']]></url>
				<mobile_url><![CDATA[]]></mobile_url>
				<support_mobile_transaction>N</support_mobile_transaction>
				<original>'.$row['consumer'].'</original>
				<discount>'.$row['dc_rate'].'</discount>
				<price>'.$row['price'].'</price>
				<max_count>'.$row['maxstock'].'</max_count>
				<min_count>'.$row['limit_ea'].'</min_count>
				<now_count>'.$row['buyercnt'].'</now_count>
				<status>'.$row['status'].'</status>
				<category><![CDATA[]]></category>
				<images>
						<image><![CDATA['.$row['image'].']]></image>
						<image><![CDATA[]]></image>
						<image><![CDATA[]]></image>
						<image><![CDATA[]]></image>
					</images>
				<shops>
					<shop>
						<shop_name><![CDATA['.$this->cfg['shopName'].']]></shop_name>
						<shop_tel><![CDATA['.$row['usable_spot_phone'].']]></shop_tel>
						<region><![CDATA['.$row['area'].']]></region>
						<shop_address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></shop_address>
					</shop>
				</shops>
			</deal>
			';

		}	// for



		$_xml .= '</deals>';
		$_xml .= '</coupon_feed>';

		return $_xml;

	}
}
?>