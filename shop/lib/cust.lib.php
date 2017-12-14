<?
if (class_exists('validParamGd') === false && is_file(dirname(__FILE__) . '/validParamGd.class.php') === true) include (dirname(__FILE__) . '/validParamGd.class.php');
if (class_exists('validParamGd') === true && isset($validParamGdAct) === false) $validParamGdAct = new validParamGd();

### 독립형 사용고객을 위한 함수 라이브러리 파일

function enamoo_tmp()
{
	echo "- GODOmall eNAMOO !!";
}

?>