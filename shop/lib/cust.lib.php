<?
if (class_exists('validParamGd') === false && is_file(dirname(__FILE__) . '/validParamGd.class.php') === true) include (dirname(__FILE__) . '/validParamGd.class.php');
if (class_exists('validParamGd') === true && isset($validParamGdAct) === false) $validParamGdAct = new validParamGd();

### ������ ������ ���� �Լ� ���̺귯�� ����

function enamoo_tmp()
{
	echo "- GODOmall eNAMOO !!";
}

?>