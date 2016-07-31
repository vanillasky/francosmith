<?

/**
 * Admin Menu class
 */

@include dirname(__FILE__)."/../conf/menu.unable.php";

class Menu
{

	function Menu()
	{
		global $menu_unable, $menu_unfree, $combine_menu;
		// 다른 경로의 메뉴사용시 (원하는경로/해당파일명으로 사용.)
		// ex) auctionIpay 경로의 orderlist.php 파일의 좌측메뉴를 order의 경로의 좌측메뉴로 사용시.
		$matchUrl['real'][] = '/auctionIpay/orderlist.php'; // 실제경로
		$matchUrl['virtual'][] = '/order/orderlist.php'; // 수정경로

		for($i = 0; $i < count($matchUrl['real']); $i++) {
			$matchUrl['real'][$i] = '/'.str_replace('/', '\\/', $matchUrl['real'][$i]).'/';
		}
		$curUrl = preg_replace($matchUrl['real'], $matchUrl['virtual'], $_SERVER[PHP_SELF]);

		// 다른 경로의 메뉴사용시
		$div = explode("/",$curUrl);
		$this->section = $div[count($div)-2];
		if (strpos($_SERVER[PHP_SELF], "/interpark/transmit_action.php") !== false && basename(dirname($_SERVER[HTTP_REFERER])) !== 'interpark') $this->section = 'goods';
		$this->menu_unable = $menu_unable;
		$this->menu_unfree = $menu_unfree;
		$this->combine_menu = $combine_menu;
		$this->getGroupKey();

		// 실명확인 메뉴 컨트롤
		$this->realname_menu();
	}

	/**
	 * 실명확인 메뉴 컨트롤
	 *
	 * @author pr
	 * @return bool
	 *
	 */
	function realname_menu() {
		global $realname;
		@include dirname(__FILE__)."/../conf/fieldset.php";
		// 서비스 종료(기존 유지, 신규 제재)
		if ($realname['id'] == '') {
			array_push($this->menu_unable, 'member/realname.php');
		}
		return true;
	}

	function getMenu()
	{
		$menu = array();
		foreach($this->combine_menu[$this->cmKey] as $dirnm){
			if (file_exists("../{$dirnm}/_menu.ini")){

				$fp = file("../{$dirnm}/_menu.ini");
				foreach($fp as $v){
					if(trim($v))$v = trim($v);
					if(substr($v,0,1) == "[" && substr($v,-1,1) == "]"){
						$link['title'][] = str_replace(array('[',']'),"",$v);
					}else{
						$k = count($link[title]) - 1;
						$tmp = explode('= ',$v);
						if(trim($tmp[0])){
							$link['subject'][$k][] = $tmp[0];
							$url = trim(str_replace('"','',$tmp[1]));
							if (preg_match("/^..\//", $url)) $link['value'][$k][] = $url;
							else if (preg_match("/^javascript/i", $url)) $link['value'][$k][] = $url;
							else $link['value'][$k][] = "../{$dirnm}/{$url}";
							// 메뉴 타켓 설정 2010.12.29 by slowj
							$link['target'][$k][] = (isset($tmp[2]))? $tmp[2] : '';
						}
					}
				}

				$menu = array_merge($menu, $link);
			}
		}
		$this->cutMenu($menu);



		return $menu;
	}

	function getGroupKey()
	{
		$keys = array_keys($this->combine_menu);
		$m = array_map("in_array", array_fill(0, count($this->combine_menu), $this->section), $this->combine_menu);
		$idx = array_search('1', $m);
		if ($idx === false) $this->cmKey = '';
		else $this->cmKey = $keys[$idx];
	}

	function cutMenu(&$menu)
	{
		global $godo, $cfg;

		foreach($menu['value'] as $k => $v){
			foreach($v as $k2 => $v2){
				if (preg_match("/^..\//", $v2)) $v2 = preg_replace("/^..\//", "", $v2);
				if (preg_match("/^rental_mxfree/i", $godo[ecCode]) && in_array($v2, $this->menu_unfree)) $menu['value'][$v][$k2] = 'unfree'; // 무료형 메뉴제어
				if (in_array($v2, $this->menu_unable)){
					//unset($menu['value'][$k][$k2],$menu['subject'][$k][$k2]);
					// 배열에서 삭제. 키값 재정의.
					array_splice($menu['value'][$k], $k2, 1);
					array_splice($menu['subject'][$k], $k2, 1);
				}
			}
		}


		## 추가 제거
		# 디자인스킨이 'easy' 경우만 '디자인코디 (easy스킨용)' 메뉴출력
		foreach($menu['title'] as $k => $v){
			if($v == '디자인코디 (easy스킨용)')$key = $k;
		}
		if ($cfg['tplSkinWork'] != "easy" && $this->section == "design"){
			$menu['title'][$key] = $menu['subject'][$key] = $menu['value'][$key] = '';
		}
	}

	function isAccess()
	{
		global $godo;
		$isAccess = true;
		$link1 = "{$this->section}/" . basename($_SERVER[PHP_SELF]);
		$link2 = $link1 . ($_SERVER['QUERY_STRING'] ? "?{$_SERVER['QUERY_STRING']}" : "");
		if (preg_match("/^rental_mxfree/i", $godo[ecCode]) && in_array($link1, $this->menu_unfree)) $isAccess = false; // 무료형 메뉴제어
		else if (preg_match("/^rental_mxfree/i", $godo[ecCode]) && in_array($link2, $this->menu_unfree)) $isAccess = false; // 무료형 메뉴제어
		else if (in_array($link1, $this->menu_unable)) $isAccess = false;
		else if (in_array($link2, $this->menu_unable)) $isAccess = false;
		return $isAccess;
	}

}
?>