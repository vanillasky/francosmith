<?
if (!$_GET['page_num']) $_GET['page_num'] = 10;
$selected['page_num'][$_GET['page_num']] = "selected";

if (!$_GET['sort']) $_GET['sort'] = 'regdate';
$selected['sort'][$_GET['sort']] = "selected";
?>
<html>
<head>
	<title>'Godo Shoppingmall e���� Season4 �����ڸ��'</title>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="../../js/default.js"></script>
	<link rel="stylesheet" type="text/css" href="../../css/style.css"/>	
	<link rel="styleSheet" href="../../../admin/style.css">
	<link rel="styleSheet" href="../../../admin/_contextmenu/contextmenu.css?1349919008">
	<script src="../../../admin/common.js"></script>
	<style>
		/*** ���� ���̾ƿ� ���� ***/
		body {margin:0 0 0 0px}

		/* �������� */
		div.display_state {}
		div.display_state input {border:none;}
		div.display_state span {display:block;width:30px;height:12px;}
		div.display_state span.show {background:url(/shop/admin/img/icn_1.gif) no-repeat 50% 50%;}
		div.display_state span.hide {background:url(/shop/admin/img/icn_0.gif) no-repeat 50% 50%;}

		/* ����Ʈ ��� */
		.rndbg2 {
			background: url('../../images/bg_table_title.gif');
			letter-spacing:-1px;
			font:8pt ����;
			padding-top:2px;
			height:26px;
			color:#ffffff;
		}
	</style>
</head>
<body>

<!-- ������ �˾� ���̾� -->
<div id="popupContact">
	<a id="popupContactClose">[x]</a>
	<h1></h1>
	<p id="contactArea">
		<iframe id='DynamicPopup' frameborder="0" src="" style="border:0px solid #000000;"></iframe>				
	</p>
</div>
<div id="backgroundPopup"></div>
<!-- ������ �˾� ���̾� -->
<div class="title title_top">�ڵ𸮽�Ʈ<span><a href="javascript:manual('http://guide.godo.co.kr/season4/board/view.php?id=product&no=42')"><img src="../../../admin/img/btn_q.gif" border=0 align=absmiddle></a></span></div>
<div id="button" style="text-align:left;float:left;padding-bottom:5px;">&nbsp;<a href="javascript:" onclick="Newopen('I','')"><img src="../../images/btn_cody.gif"></a></div>
<form name="listform">  
<div style="text-align:right;padding-bottom:5px;">
	<select name="sort" onchange="this.form.submit()">
		<option value="regdate" <?=$selected['sort']['regdate']?>>����� ��
		<option value="like_cnt" <?=$selected['sort']['like_cnt']?>>���ƿ� ��
		<option value="recody_cnt" <?=$selected['sort']['recody_cnt']?>>��� ��
	</select>
	<select name="page_num" onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,30);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>�� ���
		<? } ?>
	</select>
</div>
</form>

<form name="f1" method="post" action="./indb.php">
<input type="hidden" name="fn" value="C" />
	<table width=100% cellpadding=0 cellspacing=0 border="0">
	<tr class="rndbg2">
		<th rowspan="2" style="width:50px;word-break:break-all;"><font class=small>��ȣ</font></th>
		<th rowspan="2"><font class=small>Cody</font></th>
		<th rowspan="2"><font class=small>�ڵ��̸�</font></th>
		<th rowspan="2" style="width:230px;word-break:break-all;"><font class=small>�����/��</font></th>
		<th rowspan="2" style="width:100px;word-break:break-all;"><font class=small>�ڵ�(set)����</font></th>
		<th colspan="2"><font class=small>��������</font></th>
		<th rowspan="2" style="width:100px;word-break:break-all;"><font class=small><span onclick="fnState();" style="cursor:pointer;">��������</span></font></th>
		<th rowspan="2"><font class=small>����</font></th>
		<th rowspan="2"><font class=small>����</font></th>
	</tr>
	<tr style="height:25px;background-color:#56636a;color:#ffffff;letter-spacing:-1px;font:8pt ����;padding-top:2px;">
		<th style="width:50px;word-break:break-all;">���ƿ�</th>
		<th style="width:50px;word-break:break-all;">���</th>
	</tr>
	<tr><td class=rnd colspan="10"></td></tr>
<?			
	foreach($objs as $obj){

		### �̹��� ����
		if(is_file($_SERVER[DOCUMENT_ROOT])."/setGoods/data/Tnail/100/100_".$obj->get('thumnail_name')){
			$file = "/setGoods/data/Tnail/100/100_".$obj->get('thumnail_name');
		}else{
			$file = "/setGoods/images/tmplate_thumbnail/t1b2.gif";
		}

		$info = getimagesize("../../..".$file);
		$imgh = 92;
		$imgw = $info[0] * $imgh / $info[1];
?>
	<tr height="92px" align="center">
		<td><?=$pos?></td>
		<td> 
			<div style="width:<?=$imgw?>px;height:<?=$imgh?>px;border:1px solid #cec9c6;margin:10px;">
			<a href="../../content.php?idx=<?=$obj->get('idx')?>" target="_new" ><img src="../../..<?=$file?>" height='87px' style="margin:2px;vertical-align:bottom;"></a>
			</div>
		</td>
		<td width="500px;"><div style="word-break:break-all;padding:10px;border 1px solid red;"><a href="javascript:" onclick="Newopen('M','<?=$obj->get('idx')?>')"><?=$obj->get('cody_name')?></a></div></td>
		<td><?=$obj->get('regdate')?></td>
		<td><?=number_format($obj->get('setCost'))?> ��</td>
		<td><font class=small color=#ED6D00><b><?=$obj->get('like_cnt')?></b></font></td>
		<td><font class=small color=#ED6D00><b><?=$obj->get('recody_cnt')?></b></font></div></td>
		<td>
			<div class="display_state">
				<span class="<?=($obj->get('state') == 'Y') ? 'show' : 'hide'?>"></span>
				<input type="checkbox" class="state_Y" name="state_Y[]" id="state_Y[]" value="<?=$obj->get('idx')?>" <?=($obj->get('state') == 'Y') ? 'checked' : ''?> onClick="fnToggleGoodsStat(this);">
				<input type="hidden" name="state_ALL[]" value="<?=$obj->get('idx')?>">
			</div>
		</td>
		<td><a href=""><a href="javascript:" onclick="Newopen('M','<?=$obj->get('idx')?>')"><img src="../../../admin/img/i_edit.gif"></a></td>		
		<td><a href="javascript:" onclick="delscript('<?=$obj->get('idx')?>')"><img src="../../../admin/img/i_del.gif"></a></td>		
	</tr>
	<tr><td colspan="10" class=rndline></td></tr>
<?	$pos--;		
	}
?>
	</table>
	
		<div id="button" style="text-align:left;float:left;padding-top:5px;">&nbsp;<a href="javascript:" onclick="Newopen('I','')"><img src="../../images/btn_cody.gif"></a></div>
		<div id="button" style="text-align:right;padding-top:5px;"><input type="image" src="../../images/btn_display.gif" alt="�������������ϱ�" style="border:0;" /></div>
		

	<div align=center class=pageNavi>	
		<font class=ver8> 
			<?=$paging?>
		</font>
	</div>
</form>
	

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">* �ڵ� ����ϱ� </td></tr>
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">- �ڵ� ������ ����Ǵ� �̹����� ��ǰ�� ����Ȯ��(����) �̹����� ������ �˴ϴ�.  (��, ����Ʈ, ���� �̹����� �ٸ� �� �ֽ��ϴ�.)</td></tr>
</table>
<br/>
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">* �������� ����</td></tr>
<tr><td style="padding-left:10">- ��� �Ϸ�� �ڵ��ǰ�� ó�� �������°� <font style="color:#ef2869;font-weight:bold;">NO</font> �Դϴ�. ����Ʈ�� ����� �ڵ��ǰ�� Ȯ���Ͻ� �� �������¸� <font style="color:#37a3ee;font-weight:bold;">YES</font>�� ������ �ּ���.</td></tr>
<!--tr><td style="padding-left:10">&nbsp;&nbsp;&nbsp;(�ؽ�Ʈ '<font style="color:#ef2869;font-weight:bold;">NO</font>'�� Ŭ���Ͻø� '<font style="color:#37a3ee;font-weight:bold;">YES</font>'�� ����˴ϴ�.)</td></tr-->
<tr><td style="padding-left:10">- �ڵ��ǰ ���� �Ϻ� ��ǰ�� ������/���� ó���Ǹ� �ڵ��ǰ�� ���� ���°� <font style="color:#ef2869;font-weight:bold;">NO</font>�� ����˴ϴ�.</td></tr>
<tr><td style="padding-left:10">&nbsp;&nbsp;&nbsp;�ش� �Ϻλ�ǰ�� ���� �������·� �Ǿ �ڵ��ǰ�� �������´� <font style="color:#37a3ee;font-weight:bold;">YES</font>�� �ڵ�������� �ʽ��ϴ�. </td></tr>
<tr><td style="padding-left:10">&nbsp;&nbsp;&nbsp;��ڰ� Ȯ�� �Ͻ� ��, �ش� �ڵ��ǰ�� �������θ� ������ �ּž� �մϴ�.</td></tr>
</table>
<br/>
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="/shop/admin/img/icon_list.gif" align="absmiddle">* ���� �ϱ�</td></tr>
<tr><td style="padding-left:10">&nbsp;&nbsp;<font style="color:#ffffff;font-weight:bold">[����]</font> ��ư�� Ŭ���Ͽ� ��ϵ� �ڵ��� ���丮�� ���� �Ͻ� �� �ֽ��ϴ�. </td></tr>
<tr><td style="padding-left:10">- �ڵ� �̹����� ������ �Ұ��� �մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01',null,null,'../../../admin/')</script>

<script>
function delscript(idx){

	if(confirm("�ش� �ڵ� �����Ͻðڽ��ϱ�? ������ �������� �ʽ��ϴ�.")){ 
		jQuery.ajax({
			type:"POST",
			url:"./indb.php",
			data:{fn:"D",gidx:idx},
			dataType: "html",
			success: function(data){
				alert('�ش� �ڵ� �����Ǿ����ϴ�.');
				location.reload();
			}		
		});
	}
}

/*function Newopen(){
	var obj = window.showModalDialog("/setGoods/admin/codyEditer/",self,"dialogWidth:1000px;dialogHeight:650px;scroll:0;help:0;status:0;");
	
}*/

function Newopen(fn,idx){
	var wsize=912;
	var hsize=800;
	var posx=0;
	var posy=0;
	var url = '';
	posx = (screen.width-wsize)/2-1;
	posy = (screen.height-hsize)/2-1;
	if(fn == 'I'){
		url = "../codyEditer/?fn=E";
	}else{
		url = "../codyEditer/modify.php?fn=M&idx="+idx;
	}
	window.open(url,"edit","scrollbars=no,toolbar=no,location=no,directories=no,status=no,width="+wsize+",height="+hsize+",resizable=no,menubar=no,top="+posy+",left="+posx+",topmargin=0,leftmargin=0");
}



function fnToggleGoodsStat(o){

	var indicator, css = 'hide';

	if (o.checked == true){
		css = 'show';
	}

	for (indicator=o.parentNode.firstChild; indicator.nodeType !== 1; indicator=indicator.nextSibling);
		indicator.className = css;
	return;
}

function fnState(){  
	for(i=0; i<jQuery(".state_Y").length; i++) {
		
		if(jQuery(".state_Y").eq(i).is(":checked")) { //checked=ture �̸�
			jQuery("input.state_Y").eq(i).removeAttr("checked");          //üũ����
			jQuery("div.display_state > span").eq(i).removeClass('show').addClass('hide');	//NO�� �ٲ�
		} else {
			jQuery("input.state_Y").eq(i).attr("checked", true);          //üũǥ�� 
			jQuery("div.display_state > span").eq(i).removeClass('hide').addClass('show'); //YES�� �ٲ�
		}
	}		
}
</script>

</body>
</html>