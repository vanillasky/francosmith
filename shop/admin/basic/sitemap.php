<?

$location = "�⺻���� > ����Ʈ��";
include "../_header.php";
include_once dirname(__FILE__)."/../../conf/menu.unable.php";

# ����Ʈ�� Big Menu
$_sitemap = array();
$_sitemap[] = array( "basic", "���θ��⺻����" );
$_sitemap[] = array( "design", "�����ΰ���" );
$_sitemap[] = array( "goods", "��ǰ����" );
$_sitemap[] = array( "order", "�ֹ�����" );
$_sitemap[] = array( "member", "ȸ������" );
$_sitemap[] = array( "board", "�Խ��ǰ���" );
$_sitemap[] = array( "event", "�̺�Ʈ����" );
$_sitemap[] = array( "log", "����/������" );
$_sitemap[] = array( "data", "�����Ͱ���" );
?>

<style>
.table_Round1 {background:#746A63; height:2px; border-left:2px #ffffff solid; border-right:2px #ffffff solid;}
.title_Sub1		 {font-size:9pt; font-family:"����"; letter-spacing:-1; font-weight:bold; color:#ffffff; height:30px;background:#746A63;}
.f   {font-size:9pt; font-family:"����"; letter-spacing:0; color:#4B4B4B;}
</style>

<div class="title title_top">����Ʈ��<span>������ȭ���� ��� �����޴��� �Ѵ��� Ȯ���ϼ���</span></div>
<div style="padding-top:5px"></div>


<!------------------------------ Map start ------------------------------------->
<?
$columns = 3;
foreach ( $_sitemap as $idx => $map ){
	$column = $idx % $columns;

	if ( $column == 0 ){
echo <<<ENDH
<table border="0" cellspacing="0" cellpadding="0" style="margin-bottom:30px;">
<tr>
ENDH;
	}
	else {
echo <<<ENDH
	<td width=50></td>
ENDH;
	}

echo <<<ENDH
	<td valign=top>

	<!-- {$map[1]} -->
	<table border="0" cellspacing="0" cellpadding="0" width=200>
	<tr>
		<td class="table_Round1"></td>
	</tr>
	<tr>
		<td align=center class="title_Sub1">{$map[1]}</td>
	</tr>
	<tr>
		<td class="table_Round1"></td>
	</tr>
ENDH;

	$menu = parse_ini_file("../{$map[0]}/_menu.ini",true);

	foreach ($menu as $k=>$v){

echo <<<ENDH
	<tr><td height=8></td></tr>
	<tr><td bgcolor=E7E7E7 height=1></td></tr>
	<tr><td style="padding:10 0 0 10"><b>{$k}</b></td></tr>
	<tr>
		<td style="padding:10 0 10 20">
ENDH;

		foreach ($v as $name=>$link){
			if( preg_match( "/^rental_mxfree/i", $godo[ecCode] ) && in_array( "{$map[0]}/{$link}", $menu_unfree ) ){ // ������ �޴�����
echo <<<ENDH
		<div style="padding-top:3px"><a href="javascript:popup('http://www.godo.co.kr/userinterface/guide.php',420,230)"><font class=f>{$name} <img src="http://www.godo.co.kr/userinterface/img/btn_nofree.gif" border=0></a></div>
ENDH;
			} else if ( !in_array( "{$map[0]}/{$link}", $menu_unable ) ) {
echo <<<ENDH
		<div style="padding-top:3px"><a href="../{$map[0]}/{$link}"><font class=f>{$name}</a></div>
ENDH;
			}
		}

echo <<<ENDH
		</td>
	</tr>
ENDH;
	}

echo <<<ENDH
	</table>
	<!-- {$map[1]} end -->

	</td>
ENDH;

	if ( ($column + 1) == $columns ){
echo <<<ENDH
</tr>
</table>
ENDH;
	}
}

?>
<!------------------------------ Map end --------------------------------------->


<? include "../_footer.php"; ?>