<?
include "../_header.popup.php";
include "./_header.crm.php";
include "../../lib/page.class.php";

list ($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_MEMBER_CRM." WHERE m_no = '".$m_no."'"); # ÃÑ ·¹ÄÚµå¼ö

if (!$_GET['page_num']) $_GET['page_num'] = 20;
$orderby = ($_GET['sort']) ? $_GET['sort'] : "sno desc"; # Á¤·Ä Äõ¸®

### ¸ñ·Ï
$db_table = GD_MEMBER_CRM;

$where[] = "m_no = '".$m_no."'";

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);
?>

<table width="100%">
<tr>
	<td colspan="10">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
		<td width=23><img src="../img/titledot.gif"></td>
		<td valign=bottom align="left"><b><?echo $name?>´ÔÀÇ »ó´ã³»¿ª</b></td>
		</tr>
		<tr><td colspan=5 height=5></td></tr>
		<tr><td colspan=5 bgcolor=cccccc height=3></td></tr>
		</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="10">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
		<td valign="top">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr align="center" bgcolor="F8F8F8">
			<td style="font-size:11px;font-family:µ¸¿ò, ±¼¸²;" width="45" height='30'><font class=small1 color=444444><b>No</b></td>
			<td style="font-size:11px;font-family:µ¸¿ò, ±¼¸²;" width="130"><font class=small1 color=444444><b>»ó´ãÀÏ</b></td>
			<td style="font-size:11px;font-family:µ¸¿ò, ±¼¸²;" width="88"><font class=small1 color=444444><b>Ã³¸®ÀÚ</b></td>
			<td style="font-size:11px;font-family:µ¸¿ò, ±¼¸²;"><font class=small1 color=444444><b>³»¿ë</b></td>
			<td style="font-size:11px;font-family:µ¸¿ò, ±¼¸²;" width="81"><font class=small1 color=444444><b>»ó´ã¼ö´Ü</b></td>
			</tr>
			<tr><td colspan="5" height='2' bgcolor='#DFDFDF'></td></tr>
			</table>

			<table id="counsel_tbl" width="100%" border="0" cellspacing="0" cellpadding="0">
			<col width="45"><col width="150"><col width="88"><col width="407"><col width="81">
			<?
			while($data = $db->fetch($res)){
				switch($data['counsel_Type']) {
					case 'p':
						$counsel_type = "ÀüÈ­";
						break;
					case 'm':
						$counsel_type = " ¸ÞÀÏ";
						break;
					default:
						$counsel_type = "±âÅ¸";
						break;
				}
			
			?>
			<tr>
				<td align="center"><a onclick="view('<?echo $data['sno']?>','<?echo $_GET['page']?>')" class="hand"><?=$pg->idx--?></a></td>
				<td align="center"><?echo $data['regdt']?></td>
				<td align="center"><?echo $data['counsel_id']?></td>
				<td onclick="div_in('memo_cont_<?echo $data['sno']?>')" class="hand"><?echo mb_substr($data['contents'],0,25,'EUC-KR').(mb_substr($data['contents'],0,25,'EUC-KR') != $data['contents'] ? "..." : "")?> <a onclick="view('<?echo $data['sno']?>','<?echo $_GET['page']?>')" class="hand"><img src='../img/btn_edit_qa.gif' hspace=2></a></td>
				<td align="center" style="border-right:none;"><?echo $counsel_type?></td>
			</tr>
			<tr id="memo_cont_<?echo $data['sno']?>" onclick="div_out('memo_cont_<?echo $data['sno']?>')" class="hand" style="display:none;">
				<td colspan="10" style="padding-left:55px; padding-right:55px; border-right:none;"><?echo nl2br($data['contents'])?></td>
			</tr>
			<? } ?>
			</table>

			<table width="" height='30' border="0" cellspacing="0" cellpadding="0" id="pageTr" align='center'>
			</table>
		</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<p class="center"><?=$pg->page['navi']?></p>

<?include "./_footer.crm.php";?>