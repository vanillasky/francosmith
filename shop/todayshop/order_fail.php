<?

include "../_header.php";

if (!$sess) header("location:../member/login.php");

$query = "
select * from
	".GD_ORDER." a
	left join ".GD_LIST_BANK." b on a.bankAccount=b.sno
where
	a.ordno='$_GET[ordno]'
";
$data = $db->fetch($query,1);

### PG �������л���
if(preg_match('/������� : (.*)\n/',$data['settlelog'], $matched)){
	$data['pgfailreason'] = $matched[1];
}

$tpl->assign($data);
$tpl->print_('tpl');

?>