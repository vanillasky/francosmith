<?
$noDemoMsg = 1;
include "../_header.php";

$dong = $_GET['dong'];
$form = $_GET['form'];

if ($dong){

	$_param = array(
		'keyword' => $dong,
		'where' => 'dong',
		'page' => isset($_GET['page']) ? $_GET['page'] : 1,
		'page_size' => 10,
	);

	$result = Core::loader('Zipcode')->get($_param);
	$pg		= $result->page;

	$loop = &$result->toArray();

}

$tpl->assign(array(
			'pg'	=> $pg,
			));
$tpl->print_('tpl');

?>
