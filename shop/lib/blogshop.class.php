<?php
class blogshop {
	var $linked;
	var $config;

	function blogshop() {
		if(is_file(dirname(__FILE__)."/../conf/blogshop.php")) {
			include dirname(__FILE__)."/../conf/blogshop.php";
		}
		
		if($blogshop['id']) {
			include_once(dirname(__FILE__)."/../lib/httpRequest.class.php");
			$this->config=$blogshop;
			$this->linked=true;
		}
		else {
			$this->linked=false;
		}

	}
	
	// 상품 정보 가지고 오기
	function get_goods_from_blog_goodsno($blog_goodsno) {
		if($this->linked==false) return false;

		$ar_api_url = parse_url($this->config['api_url']);
		
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'goodsno'=>$blog_goodsno,
			'mode'=>'GET_GOODS',
		);

		$poststr = $this->array_to_poststring($send_post);

		$h->send($poststr);
		$responseText = $h->responseText;
		
		if(!$responseText) return false;
		
		$data = unserialize($responseText);
		$this->array_utf8_to_euckr(&$data);
		return $data;
	}
	
	// 상품 정보 가지고 오기
	function get_goods_from_godoshop_key($godoshop_key) {
		if($this->linked==false) return false;

		$ar_api_url = parse_url($this->config['api_url']);
		
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'godoshop_key'=>$godoshop_key,
			'mode'=>'GET_GOODS_FROM_GODOSHOP_KEY',
		);

		$poststr = $this->array_to_poststring($send_post);

		$h->send($poststr);
		$responseText = $h->responseText;
		
		if(!$responseText) return false;
		
		$data = unserialize($responseText);
		$this->array_utf8_to_euckr(&$data);
		$data['cate']=$data['cate'][0];
		return $data;
	}
	
	// 상품 정보 보내기
	function send_goods($godoshop_key,$ar_data) {
		if($this->linked==false) return false;

		// 변수체크
		if(!$godoshop_key) return false;		

		include dirname(__FILE__)."/../conf/config.php";
		
		$ar_api_url = parse_url($this->config['api_url']);

		$ar_data['longdesc']=preg_replace('/src=\"\/shop/','src="http://'.$_SERVER['SERVER_NAME'].'/shop',$ar_data['longdesc']);
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'mode'=>'INPUT_GOODS',
		);
		
		$send_post['godoshop_key']=$godoshop_key;
		$send_post['godoshop_url']='http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/goods/goods_view.php?inflow=blogshop&goodsno='.$godoshop_key;
		if(array_key_exists('goodsnm', $ar_data)) $send_post['goodsnm'] = iconv('EUC-KR','UTF-8',$ar_data['goodsnm']);
		if(array_key_exists('maker', $ar_data)) $send_post['maker'] = iconv('EUC-KR','UTF-8',$ar_data['maker']);
		if(array_key_exists('origin', $ar_data)) $send_post['origin'] = iconv('EUC-KR','UTF-8',$ar_data['origin']);
		if(array_key_exists('brand', $ar_data)) $send_post['brand'] = iconv('EUC-KR','UTF-8',$ar_data['brand']);
		if(array_key_exists('launchdt', $ar_data)) $send_post['launchdt'] = $ar_data['launchdt'];
		if(array_key_exists('icon', $ar_data)) $send_post['icon'] = $ar_data['icon'];
		if(array_key_exists('price', $ar_data)) $send_post['price'] = $ar_data['price'];
		if(array_key_exists('longdesc', $ar_data)) $send_post['longdesc'] = iconv('EUC-KR','UTF-8',$ar_data['longdesc']);
		if(array_key_exists('part_no', $ar_data)) $send_post['part_no'] = $ar_data['part_no'];
		if(array_key_exists('cate_no', $ar_data)) $send_post['cate_no'] = $ar_data['cate_no'];
		if(array_key_exists('tags', $ar_data)) $send_post['tags'] = iconv('EUC-KR','UTF-8',$ar_data['tags']);
		if(array_key_exists('trackback', $ar_data)) $send_post['trackback'] = iconv('EUC-KR','UTF-8',$ar_data['trackback']);
		$poststr = $this->array_to_poststring($send_post);
	
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);
		
		if($ar_data['img']) {
			if (preg_match('/^http(s)?:\/\/.+$/',$ar_data['img']))
				$h->attachRemoteFile('image',$ar_data['img'],$ar_data['img']);
			else
				$h->attachFile('image',$ar_data['img'],$ar_data['img']);
		}

		$h->send($poststr);
		return true;
	}

	function delete_goods($godoshop_key) {
		if($this->linked==false) return false;

		$ar_api_url = parse_url($this->config['api_url']);
		
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'godoshop_key'=>$godoshop_key,
			'mode'=>'DEL_GOODS',
		);

		$poststr = $this->array_to_poststring($send_post);

		$h->send($poststr);
		$responseText = $h->responseText;
		return true;
	}

	function get_category() {
		if($this->linked==false) return false;

		$ar_api_url = parse_url($this->config['api_url']);
		
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'mode'=>'GET_CATEGORY',
		);

		$poststr = $this->array_to_poststring($send_post);

		$h->send($poststr);
		$responseText = $h->responseText;
		
		if(!$responseText) return false;
		
		$data = unserialize($responseText);
		if(is_array($data)) {
			$this->array_utf8_to_euckr(&$data);
		}
		return $data;


	}

	function add_category($catnm) {
		if($this->linked==false) return false;
		
		if(!$catnm) return false;

		$ar_api_url = parse_url($this->config['api_url']);
		
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'mode'=>'ADD_CATEGORY',
			'catnm'=>iconv("EUC-KR","UTF-8",$catnm),
		);

		$poststr = $this->array_to_poststring($send_post);

		$h->send($poststr);
		return true;
	}

	function modify_category($cate_no,$catnm) {
		if($this->linked==false) return false;
		
		if(!$catnm) return false;
		if(!$cate_no) return false;

		$ar_api_url = parse_url($this->config['api_url']);
		
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'mode'=>'MOD_CATEGORY',
			'cate_no'=>$cate_no,
			'catnm'=>iconv("EUC-KR","UTF-8",$catnm),
		);

		$poststr = $this->array_to_poststring($send_post);

		$h->send($poststr);
		return true;
	}

	function up_category($cate_no) {
		if($this->linked==false) return false;
		
		if(!$cate_no) return false;

		$ar_api_url = parse_url($this->config['api_url']);
		
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'mode'=>'UP_CATEGORY',
			'cate_no'=>$cate_no,
		);

		$poststr = $this->array_to_poststring($send_post);

		$h->send($poststr);
		return true;
	}

	function down_category($cate_no) {
		if($this->linked==false) return false;
		
		if(!$cate_no) return false;

		$ar_api_url = parse_url($this->config['api_url']);
		
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'mode'=>'DOWN_CATEGORY',
			'cate_no'=>$cate_no,
		);

		$poststr = $this->array_to_poststring($send_post);

		$h->send($poststr);
		return true;
	}

	function get_part() {
		if($this->linked==false) return false;

		$ar_api_url = parse_url($this->config['api_url']);
		
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'mode'=>'GET_PART',
		);

		$poststr = $this->array_to_poststring($send_post);

		$h->send($poststr);
		$responseText = $h->responseText;
		
		if(!$responseText) return false;
		
		$data = unserialize($responseText);
		$this->array_utf8_to_euckr(&$data);
		return $data;
	}

	function get_inip2p_goods() {
		if($this->linked==false) return false;

		$ar_api_url = parse_url($this->config['api_url']);
		
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'mode'=>'GET_INIP2P_GOODS_LIST',
		);

		$poststr = $this->array_to_poststring($send_post);

		$h->send($poststr);
		$responseText = $h->responseText;
		
		if(!$responseText) return false;
		
		$data = unserialize($responseText);
		$this->array_utf8_to_euckr(&$data);
		return $data;
	}

	function get_goods_image($godoshop_key) {


		if($this->linked==false) return false;

		$ar_api_url = parse_url($this->config['api_url']);
		
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);

		$tmp_file_path = "/tmp/".uniqid('shop_');
		$h->fileToSave=fopen($tmp_file_path,"w");
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'mode'=>'GET_GOODS_IMAGE',
		);

		$send_post['godoshop_key']=$godoshop_key;

		$poststr = $this->array_to_poststring($send_post);

		$h->send($poststr);
		
		//fclose($h->fileToSave);
		@chmod($tmp_file_path,0707); // 업로드된 파일 권한 변경
		if(filesize($tmp_file_path)) {
			return $tmp_file_path;
		}
		else {
			return false;
		}

	}

	function link_goods($blog_goodsno,$godoshop_key) {
		if($this->linked==false) return false;

		$ar_api_url = parse_url($this->config['api_url']);
		
		$h = new HTTPRequest($ar_api_url['host']);
		$h->setPath($ar_api_url['path']);
		
		$send_post=array(
			'id'=>$this->config['id'],
			'api_key'=>$this->config['api_key'],
			'mode'=>'LINK_GOODS',
		);
		$send_post['goodsno']=$blog_goodsno;
		$send_post['godoshop_key']=$godoshop_key;

		$poststr = $this->array_to_poststring($send_post);

		$h->send($poststr);
		return true;
	}

	
	// 배열에 있는 모든 utf8 정보를 euckr로 변경하는 함수 
	function array_utf8_to_euckr(&$arr) {
		foreach($arr as $k=>$v) {
			if(is_array($arr[$k])) {
				$this->array_utf8_to_euckr(&$arr[$k]);
			}
			else {
				$arr[$k]=iconv("UTF-8","EUC-KR",$v);
			}
		}
	}

	// 배열을 POST STRING으로 변경해주는 함수
	function array_to_poststring($arr) {
		$tmp=array();
		foreach($arr as $k=>$v) {
			$tmp[]=$k.'='.urlencode($v);
		}
		$poststr = implode('&',$tmp);
		return $poststr;
	}


	

}






?>