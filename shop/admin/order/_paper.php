<?

$hiddenPrint = "style='display:none'";

include "../_header.popup.php";


?>
<body oncontextmenu="return false">
<style type="text/css"><!--
@media print { .notprint {display: none;} } /* �μ�� ���ʿ��� �κ� ��Ȱ��ȭ */
--></style>

<script language="javascript"><!--
window.onbeforeprint = function () // ���ݰ�꼭 ��½� ����
{
	var ele = eval("document.getElementsByName('taxtable')");
	for ( i=0; i<ele.length; i++ )
	{
		var jscript = document.createElement("script");
		jscript.src="../order/tax_indb.php?mode=print&sno=" + ele[i].taxsno;
		document.getElementById('dynamic').appendChild(jscript);
	}
}
--></script>

<DIV class="notprint">
<div style="padding-left:63px"><font color=#5B5B5B>�� <font class=small1>���ݰ�꼭 �μ�� ����(�����̹���)�� �μ�Ƿ��� �Ʒ��� ���� �����Ǿ� �־�� �����մϴ�.</div>
<div style="padding-top:3px"></div>
<div style="padding-left:80px">1) ���ͳ� �ͽ��÷η� : �������� [���� �޴�]-[���ͳݿɼ�]-[���]-[�μ�] ���� [���� �� �̹��� �μ�] üũ</div>
<div style="padding-top:3px"></div>
<div style="padding-left:80px">2) ���̾����� : �������� [����]-[�μ�ȭ�鼳��]-[���� �� ����]-[�ɼ�]���� [��� �μ�(���� �� �׸�)] üũ</div>
<div style="padding-top:10px"></div>
<div align=center><a href="javascript:window.print();"><img src="../img/btn_print.gif" border="0" align="absmiddle"></a></div>
<div style="padding-top:10px"></div>
</div>
<?


### �ֹ���ȣ �迭������ ����
$paper_orders = array();
switch ( $_GET[list_type] )
{
	case 'list':
		$paper_orders = explode( ";", $_GET[ordnos] );
		break;
	case 'term':
		if ($_GET[settlekind]) $where[] = "settlekind = '$_GET[settlekind]'";

		if ($_GET[regdt][0]){
			if (!$_GET[regdt][1]) $_GET[regdt][1] = date("Ymd");
			$where[] = "orddt between date_format({$_GET[regdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[regdt][1]},'%Y-%m-%d 23:59:59')";
		}

		if ($_GET[step]){
			$step = explode("_", $_GET[step]);
			if ( $step[0] == 'step' ) $where[] = "step = '{$step[1]}' and step2 = ''";
			else if ( $step[0] == 'step2' )
				switch ($step[1]){
					case "1": $where[] = "(step=0 and step2 between 1 and 49)"; break;
					case "2": $where[] = "(step in (1,2) and step2!=0)"; break;
					case "3": $where[] = "(step in (3,4) and step2!=0)"; break;
					default:  $where[] = "step2=$step[1]"; break;
				}
		}

		$where = ( count($where) ? " where " . implode(" and ", $where ) : "" );
		$query = "select ordno from ".GD_ORDER." {$where} order by ordno desc";
		$res = $db->query($query);
		while ($data=$db->fetch($res)) $paper_orders[] = $data[ordno];
		break;
	case 'tax_term':
		$where[] = "step>0";

		if ($_GET[regdt][0]){
			if (!$_GET[regdt][1]) $_GET[regdt][1] = date("Ymd");
			$where[] = "issuedate between date_format({$_GET[regdt][0]},'%Y-%m-%d') and date_format({$_GET[regdt][1]},'%Y-%m-%d')";
		}

		$where = ( count($where) ? " where " . implode(" and ", $where ) : "" );
		$query = "select ordno from ".GD_TAX." {$where} order by issuedate desc";
		$res = $db->query($query);
		while ($data=$db->fetch($res)) $paper_orders[] = $data[ordno];
		break;
	default :
		$paper_orders[] = $_GET[ordno];
		break;
}


### ����Ʈ
$taxErrmsg = <<<ENDH
<DIV class="notprint" style="margin:0 40 20 40;">
<div style="background-color:#eeeeee; padding: 15px 10px; text-align:center; line-height: 20px;">
<font class=small color=#0074BA><strong>{ordno}</strong></font> �ֹ��� <font color=EA0095>���ݰ�꼭�� ����� �� �����ϴ�.</font><br>
���ݰ�꼭 ������ ��û���� �ʾҰų�, ������ �ȵ� �����Դϴ�.
</div>
</div>
ENDH;

$paper_br = 0;
foreach ( $paper_orders as $paper_hr => $ordno )
{
	unset($item);

	if ( $paper_hr != 0 ) echo '<hr class="notprint" style="border-top:dashed 1px #000000;">' . "\n";

	if ( $_GET[type] == "tax" ){ # ���ݰ�꼭 �̽�û�� ���
		list( $cnt ) = $db->fetch("select count(*) from ".GD_TAX." where step>0 and ordno='$ordno'");
		if ( $cnt == 0 ){
			echo str_replace( "{ordno}", $ordno, $taxErrmsg );
			continue;
		}
	}

	if ( $paper_br != 0 ){ 
		echo '<div style="page-break-after:always;"></div>';
		echo '<br style="height:0; line-height:0;">' . "\n";
	}
	include "_paper.$_GET[type].php";
	$paper_br++;
}


?>
<script>
table_design_load();
//window.print();
</script>