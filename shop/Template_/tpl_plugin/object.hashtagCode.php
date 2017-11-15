<?php
class tpl_object_hashtagCode {

    function tpl_object_hashtagCode() {

    }

    function displayHashtag($type=1, $limit=1) {

        global $db, $_SERVER;

		$typeName = $hashtagHtml = '';
		$hashtagCodeList = array();

		//�ִ� ���ⰹ�� 50��
		if($limit > 50) $limit = 50;

		switch($type){
			case 1 : //��ǰ��ϼ���
				$typeName = 'code_goodsCount';
			break;

			case 2 : //�ֱٵ�ϼ�
				$typeName = 'code_newRegister';
			break;

			case 3 : //��������
				$typeName = 'code_name';
			break;

			case 4 : //����ڼ���
				$typeName = 'code_user';
			break;
		}

		if(!is_object($hashtag)){
			$hashtag = Core::loader('hashtag');
		}

		$param = array('limit'=>$limit);
		if($_SERVER['PHP_SELF']){
			$matchPath = array();
			preg_match('/\/m2\/|\/m\//', $_SERVER['PHP_SELF'], $matchPath);
			if($matchPath[0]){
				$param['mobilePath'] = $matchPath[0];
			}
		}

		$hashtagCodeList = $hashtag->getHashtagList($typeName, $param);
		$hashtagHtml = implode("", $hashtagCodeList);

		return $hashtagHtml;
    }
}
?>