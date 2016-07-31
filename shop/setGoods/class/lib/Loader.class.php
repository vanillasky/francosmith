<?
require_once dirname(__FILE__) . "/../../../setGoods/class/lib/Mata.class.php";

class Loader {
	
	function Loader(){
	
	}

	function View($_path, $arrayList='', $_mod='html'){
		
		if(file_exists( $_path)){
			
			/* pathinfo 옵션
			 * dirname ->패스
			 * basename -> 파일이름
			 * extension -> 확장자
			 * filename -> 파일이름 확장자 제거
			 */
			
			$file_info = pathinfo($_path);
			
			if(is_array($arrayList)){
				while (list($key,$value) = each($arrayList)) { 
					${$key} = $value;
				} 
			}
			
			if($_mod == 'html'){
				include $_path;
			}

		}else{
			L_mata::alert('파일을 로드하는데 실패하였습니다.');
		}

	}
}
?>