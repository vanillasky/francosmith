<?
require_once dirname(__FILE__) . "/../../../setGoods/class/lib/Mata.class.php";

class Loader {
	
	function Loader(){
	
	}

	function View($_path, $arrayList='', $_mod='html'){
		
		if(file_exists( $_path)){
			
			/* pathinfo �ɼ�
			 * dirname ->�н�
			 * basename -> �����̸�
			 * extension -> Ȯ����
			 * filename -> �����̸� Ȯ���� ����
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
			L_mata::alert('������ �ε��ϴµ� �����Ͽ����ϴ�.');
		}

	}
}
?>