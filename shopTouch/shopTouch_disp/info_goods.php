<?
include dirname(__FILE__) . "/../_shopTouch_header.php"; 
@include $shopRootDir . "/lib/page.class.php";
@include $shopRootDir . "/conf/config.pay.php";

### ��۾ȳ� ###
$delivery_info_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'delivery_info');
$res_delivery_info = $db->_select($delivery_info_query);
$delivery_info = $res_delivery_info[0]['value'];

### ��ǰ�ȳ� ###
$return_info_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'return_info');
$res_return_info = $db->_select($return_info_query);
$return_info = $res_return_info[0]['value'];

if(!$delivery_info) $delivery_info = "�� ��ǰ�� ��� ������� ���Դϴ�.(�Ա� Ȯ�� ��) ��ġ ��ǰ�� ��� �ټ� �ʾ����� �ֽ��ϴ�.[��ۿ������� �ֹ�����(�ֹ�����)�� ���� �������� �߻��ϹǷ� ��� ����ϰ��� ���̰� �߻��� �� �ֽ��ϴ�.] 
�� ��ǰ�� ��� �������� �� �Դϴ�.
��� �������̶� �� ��ǰ�� �ֹ� �Ͻ� ���Ե鲲 ��ǰ ����� ������ �Ⱓ�� �ǹ��մϴ�. (��, ���� �� �������� �Ⱓ ���� �����ϸ� ���� �ֹ��� ��� �Ա��� ���� �Դϴ�.)";


if(!$return_info) $return_info = "��ǰ û��öȸ ���ɱⰣ�� ��ǰ �����Ϸ� ���� �� �̳� �Դϴ�.
��ǰ ��(tag)���� �Ǵ� �������� ��ǰ ��ġ �Ѽ� �ÿ��� �� �̳��� ��ȯ �� ��ǰ�� �Ұ����մϴ�.
���ܰ� ��ǰ, �Ϻ� Ư�� ��ǰ�� �� ���ɿ� ���� ��ȯ, ��ǰ�� ������ ��ۺ� �δ��ϼž� �մϴ�(��ǰ�� ����,��ۿ����� ����)
�Ϻ� ��ǰ�� �Ÿ� ���, ��ǰ���� ���� �� ������ �������� ������ ������ �� �ֽ��ϴ�.
�Ź��� ���, �ǿܿ��� ��ȭ�Ͽ��ų� ��������� �ִ� ��쿡�� ��ȯ/��ǰ �Ⱓ���� ��ȯ �� ��ǰ�� �Ұ��� �մϴ�.
����ȭ �� ���� �ֹ����ۻ�ǰ(������,�ߺ�,������ ����)�� ��쿡�� ���ۿϷ�, �μ� �Ŀ��� ��ȯ/��ǰ�Ⱓ���� ��ȯ �� ��ǰ�� �Ұ��� �մϴ�.
����,��ǰ ��ǰ�� ���, ��ǰ �� �� ��ǰ�� �ڽ� �Ѽ�, �н� ������ ���� ��ǰ ��ġ �Ѽ� �� ��ȯ �� ��ǰ�� �Ұ��� �Ͽ���, ���� �ٶ��ϴ�.
�Ϻ� Ư�� ��ǰ�� ���, �μ� �Ŀ��� ��ǰ ���ڳ� ������� ��츦 ������ ������ �ܼ����ɿ� ���� ��ȯ, ��ǰ�� �Ұ����� �� �ֻ����, �� ��ǰ�� ��ǰ�������� �� �����Ͻʽÿ�.";

$delivery_info = str_replace("\n", "<br />", $delivery_info);
$return_info = str_replace("\n", "<br />", $return_info);


$tpl->assign( '_set', $set);
$tpl->assign( 'delivery_info', $delivery_info );
$tpl->assign( 'return_info', $return_info );
$tpl->assign( 'pg', $pg );

### ���ø� ���
$tpl->print_('tpl');

?>