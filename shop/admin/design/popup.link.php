<?
include "../_header.popup.php";

include "./codi/code.class.php";

if(!$_GET['selectDir']) $_GET['selectDir']	= "goods";

$codi	= new codiTree;
$codi->getTree($_GET['selectDir'].'/');
$design_skin = $codi->getSection($_GET['selectDir'].'/');
$exceptFile	= array(
	'member/_form.htm',
	'mypage/_myBox.htm',
	'proc/_captcha.htm',
	'proc/_cashreceipt.htm',
	'proc/_cashreceiptOrder.htm',
	'proc/ccsms.htm',
	'proc/overture_cc.htm',
	'proc/scroll.js',
);

$arrMenu	= array('goods' => '상품','order' => '주문','member' => '회원','mypage' => '마이페이지','service' => '고객서비스','proc' => '기타페이지');
?>
<script type="text/javascript">
function copyTxt(val){
	var copy = eval("document.copyFrm.copy_"+val+"_Url");
	if (val !="" && val !=null && copy){
		if (window.clipboardData){
			window.clipboardData.setData('Text', copy.value );
			alert( '페이지주소(URL)를 복사했습니다. \n원하는 곳에 붙여넣기(Ctrl+V)를 하시면 됩니다.' );
		} else {
			prompt("코드를 클립보드로 복사(Ctrl+C) 하시고. \n원하는 곳에 붙여넣기(Ctrl+V)를 하시면 됩니다~", copy.value);
		}
	}
}
</script>
<div class="title title_top">각 폴더별 페이지주소<span>링크작업시 필요한 페이지 주소를 쉽게 확인하고 복사해서 사용하세요.</span></div>

<table width=100% border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr bgcolor=#4a4641 height=45 align=center>
<?
	$tableWidth	= 100 / count($arrMenu);
	foreach ($arrMenu as $key => $val) {
?>
	<td width="<?=$tableWidth?>%" style="padding-top:3px"><a href="popup.link.php?selectDir=<?=$key?>"><font color="white"><b><?=$val?></b></font><br /><font color="white"><?=$key?></font></a></td>
<?	}?>
</tr>
</table>

<div style="padding-top: 5px"></div>

<form name="copyFrm">
<table width=100% border=2 bordercolor=#cccccc style="border-collapse:collapse">
<tr align=center height=25 bgcolor=#f6f3ef>
	<td  style="padding-top: 3px"><font class=small1 color=#555555><b>폴더명</b></font></td>
	<td style="padding-top: 3px"><font class=small1 color=#555555><b>페이지설명</b></font></td>
	<td style="padding-top: 3px"><font class=small1 color=#555555><b>페이지주소</b></font></td>
</tr>
<?
$i = 0;
foreach ($design_skin as $key => $val) {
	if($val['folder'] == "doc") {
		if(!in_array($val['id'],$exceptFile)){
			$resultUrlChk	= $codi->get_fileinfo($val['id']);
			if(substr($resultUrlChk['linkurl'],0,20) == "main/html.php?htmid="){
				$resultUrl		= $cfg['rootDir']."/".$resultUrlChk['linkurl'];
			}else{
				$resultUrl		= $cfg['rootDir']."/".str_replace(".htm",".php",$val['id']);
			}
?>
<tr height=29>
	<td align=center><font class=ver8 color=#555555><?=$_GET['selectDir']?></td>
	<td style="padding:5px 0 0 9px"><font class=ver8 color=#666666><?=$val['catnm']?></td>
	<td style="padding-left: 9px"><font class=ver8 color=#554c47><?=$resultUrl?></font>
		<a href="javascript:copyTxt('<?=$i?>');" onfocus="this.blur()" style="color:#535353" onmouseover="(window.status='클릭하면 복사됩니다');return true;" onmouseout="(window.status='');">
		<img src="/shop/admin/img/webftp/bu_addcopy.gif" align=absmiddle>
		</a>
		<input type="hidden" id="copy_<?=$i?>_Url" value="<?=$resultUrl?>"></td>
</tr>
<?
		$i++;
		}
	}
}
?>
</table>
</form>
<script>table_design_load();</script>