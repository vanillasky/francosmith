<?
$location = "SNS ���� > SNS �����ϱ� ��������";
include "../_header.php";
$cfgfile = "../../conf/sns.cfg.php";
if(file_exists($cfgfile)) require $cfgfile;

if (!$snsCfg['msg_kakao_shopnm']) $snsCfg['msg_kakao_shopnm'] = '{shopnm}';
if (!$snsCfg['msg_kakao_goodsnm']) $snsCfg['msg_kakao_goodsnm'] = '{goodsnm}';
if (!$snsCfg['msg_kakao_goodsurl']) $snsCfg['msg_kakao_goodsurl'] = '{goodsurl}';
if (!$snsCfg['msg_kakaoStory_shopnm']) $snsCfg['msg_kakaoStory_shopnm'] = '{shopnm}';
if (!$snsCfg['msg_kakaoStory_goodsnm']) $snsCfg['msg_kakaoStory_goodsnm'] = '{goodsnm}';
if (!$snsCfg['msg_kakaoStory_goodsurl']) $snsCfg['msg_kakaoStory_goodsurl'] = '{goodsurl}';
if (!$snsCfg['msg_twitter']) $snsCfg['msg_twitter'] = '[{shopnm}] {goodsnm}'.chr(10).'{goodsurl}';
if (!$snsCfg['msg_facebook']) $snsCfg['msg_facebook'] = '[{shopnm}] {goodsnm}';
if (!$snsCfg['msg_pinterest']) $snsCfg['msg_pinterest'] = '[{shopnm}] {goodsnm}';
if (!$snsCfg['useBtn']) $snsCfg['useBtn'] = 'n';
$checked['useBtn'][$snsCfg['useBtn']] = 'checked';

if(!$snsCfg['use_kakao'] || $snsCfg['use_kakao'] == 'y') $checked['use_kakao']['y'] = 'checked';
if(!$snsCfg['use_kakaoStory'] || $snsCfg['use_kakaoStory'] == 'y') $checked['use_kakaoStory']['y'] = 'checked';
if(!$snsCfg['use_twitter'] || $snsCfg['use_twitter'] == 'y') $checked['use_twitter']['y'] = 'checked';
if(!$snsCfg['use_facebook'] || $snsCfg['use_facebook'] == 'y') $checked['use_facebook']['y'] = 'checked';
if($snsCfg['use_pinterest'] == 'y') $checked['use_pinterest']['y'] = 'checked';
if(!$snsCfg['use_urlcopy'] || $snsCfg['use_urlcopy'] == 'y') $checked['use_urlcopy']['y'] = 'checked';

?>
<script type="text/javascript">

function copy_txt(val){
	window.clipboardData.setData('Text', val);
}

function enableForm(val) {
	document.getElementById("msg_twitter").disabled = !val;
	document.getElementById("msg_facebook").disabled = !val;
	document.getElementById("msg_pinterest").disabled = !val;
}

function display_msg(mode) {
	var shopnm = "���θ���";
	var goodsnm = "��ǰ�̸�";
	var goodsurl = "http://test.com";

	if(mode == "kakao") {
		var res = "[" + document.getElementsByName("msg_kakao_shopnm")[0].value + "]<br />";
		res += document.getElementsByName("msg_kakao_goodsnm")[0].value + "<br />";
		res += document.getElementsByName("msg_kakao_goodsurl")[0].value;
		res = res.replace(/{shopnm}/g, shopnm);
		res = res.replace(/{goodsnm}/g, goodsnm);
		res = res.replace(/{goodsurl}/g, goodsurl);
	} else if (mode == "kakaoStory"){
		var res = document.getElementsByName("msg_kakaoStory_goodsurl")[0].value + " <br />";
		res += document.getElementsByName("msg_kakaoStory_goodsnm")[0].value + " <br />";
		res += document.getElementsByName("msg_kakaoStory_shopnm")[0].value;
		
		res = res.replace(/{shopnm}/g, shopnm);
		res = res.replace(/{goodsnm}/g, goodsnm);
		res = res.replace(/{goodsurl}/g, goodsurl);
	} else {
		var res = document.getElementsByName("msg_"+mode)[0].value;
		res = res.replace(/\n/g, "<br />");
		res = res.replace(/{shopnm}/g, shopnm);
		res = res.replace(/{goodsnm}/g, goodsnm);
		res = res.replace(/{goodsurl}/g, goodsurl);
	}

	switch(mode) {
		case 'kakao' : {
			res = res.replace(/(http:\/\/[^\s]*)/gi, "<a href='$1' target='_blank'>$1</a>");
			document.getElementById("dp_msg_kakao").innerHTML = res;
			break;
		}
		case 'kakaoStory' : {
			res = res.replace(/(http:\/\/[^\s]*)/gi, "<a href='$1' target='_blank'>$1</a>");
			document.getElementById("dp_msg_kakaoStory").innerHTML = res;
			break;
		}
		case 'twitter' : {
			res = res.replace(/(http:\/\/[^\s]*)/gi, "<a href='$1' target='_blank'>$1</a>");
			document.getElementById("dp_msg_twitter").innerHTML = res;
			break;
		}
		case 'facebook' : {
			res = res.replace(/\"([^\"]*)\":(http:\/\/[^\s]*)/gi, "<a href='$2' target='_blank'>$1</a>");
			document.getElementById("dp_msg_facebook").innerHTML = res;
			break;
		}
		case 'pinterest' : {
			res = res.replace(/\"([^\"]*)\":(http:\/\/[^\s]*)/gi, "<a href='$2' target='_blank'>$1</a>");
			document.getElementById("dp_msg_pinterest").innerHTML = res;
			break;
		}
	}
}

</script>
<div style="width:800">
<form name="frm" method="post" action="sns.indb.php" target="ifrmHidden">
<div class="title title_top">SNS �����ϱ� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=14')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="30">��뼳��</td>
	<td class="noline">
		<label><input type="radio" name="useBtn" value="y" <?=$checked['useBtn']['y']?> onclick="enableForm(true)" />�����</label>
		<label><input type="radio" name="useBtn" value="n" <?=$checked['useBtn']['n']?> onclick="enableForm(false)" />������</label>
	</td>
</tr>
<tr>
	<td height="30">ġȯ�ڵ�</td>
	<td class="noline">
		<div style="padding:10px 0px;">
			<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="95%">
			<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
			<tr>
				<td>���θ���</td><td>{shopnm}</td>
				<td>��ǰ��</td><td>{goodsnm}</td>
				<td>��ǰurl</td><td>{goodsurl}</td>
			</tr>
			</table>
			<div style="padding-top:5px;" class="small1 extext">
				<div>�⺻���� ���� ġȯ�ڵ�� SNS����Ʈ�� ���۵Ǵ� ��ǰ���� �����Դϴ�.</div>
				<div>�⺻���� ���θ���, ��ǰ��, ��ǰURL������ ������ �ǵ��� �����Ǿ� �ֽ��ϴ�.</div>
				<div>���� �ÿ��� �̸����⸦ ���� ���� ������ Ȯ���� �ּ���.</div>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td height="30">�⺻���� ����</td>
	<td class="noline">
		<div>
			<div style="margin:10px 5px 2px 5px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="bottom">
					<td align="left"><img src="../img/icon_arrow.gif" /> <b>īī�����丮</b></td>
					<td align="right" style="padding-right:35px;"><input type="checkbox" name="use_kakaoStory" id="use_kakaoStory" value="y" <?=$checked['use_kakaoStory']['y']?> /> <label for="use_kakaoStory">��¿���</label></td>
				</tr>
				</table>
			</div>
			<div style="vertical-align:middle">
				<table width="95%" border="1" bordercolor="#e1e1e1" style="border-collapse:collapse">
				<col class="cellC"><col class="cellL">
				<tr>
					<td>��ǰURL</td>
					<td><input type="text" name="msg_kakaoStory_goodsurl" id="msg_kakaoStory_goodsurl" value="<?=$snsCfg['msg_kakaoStory_goodsurl']?>" style="border:1px solid #CCCCCC; width:95%;" class="line" /></td>
				</tr>
				<tr>
					<td>��ǰ�̸�</td>
					<td><input type="text" name="msg_kakaoStory_goodsnm" id="msg_kakaoStory_goodsnm" value="<?=$snsCfg['msg_kakaoStory_goodsnm']?>" style="border:1px solid #CCCCCC; width:95%;" class="line" /></td>
				</tr>
				<tr>
					<td>���θ���</td>
					<td><input type="text" name="msg_kakaoStory_shopnm" id="msg_kakaoStory_shopnm" value="<?=$snsCfg['msg_kakaoStory_shopnm']?>" style="border:1px solid #CCCCCC; width:95%;" class="line" /></td>
				</tr>			
				</table>
			</div>
			<div style="width:95%; height:25px; padding:5px; text-align:right;"><a onclick="display_msg('kakaoStory')" style="cursor:pointer"><img src="../img/btn_freeview.gif" /></a></div>
			<div id="dp_msg_kakaoStory" style="width:95%; height:50px; border:dashed 1px #72CDFF; padding:5px;"></div>
			<div style="padding-top:5px;" class="small1 extext">
				<div>
				īī�����丮 ����� <font color="red">����ϼ�V2</font> ������ ���� �մϴ�. <a href="../mobileShop/mobile_set.php" style="font-weight:bold;" class="extext">[����ϼ� ���� �ٷΰ���]</a><br>
				īī�����丮�� Ÿ��Ʋ�� �����մϴ�. ��ǰURL�� �ڵ����� ���Ե˴ϴ�.
				</div>
			</div>
		</div>
		<div>
			<div style="margin:10px 5px 2px 5px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="bottom">
					<td align="left"><img src="../img/icon_arrow.gif" /> <b>īī����</b></td>
					<td align="right" style="padding-right:35px;"><input type="checkbox" name="use_kakao" id="use_kakao" value="y" <?=$checked['use_kakao']['y']?> /> <label for="use_kakao">��¿���</label></td>
				</tr>
				</table>
			</div>
			<div style="vertical-align:middle">
				<table width="95%" border="1" bordercolor="#e1e1e1" style="border-collapse:collapse">
				<col class="cellC"><col class="cellL">
				<tr>
					<td>���θ��̸�</td>
					<td><input type="text" name="msg_kakao_shopnm" id="msg_kakao_shopnm" value="<?=$snsCfg['msg_kakao_shopnm']?>" style="border:1px solid #CCCCCC; width:95%;" class="line" /></td>
				</tr>
				<tr>
					<td>���� ����</td>
					<td><input type="text" name="msg_kakao_goodsnm" id="msg_kakao_goodsnm" value="<?=$snsCfg['msg_kakao_goodsnm']?>" style="border:1px solid #CCCCCC; width:95%;" class="line" /></td>
				</tr>
				<tr>
					<td>���� URL</td>
					<td><input type="text" name="msg_kakao_goodsurl" id="msg_kakao_goodsurl" value="<?=$snsCfg['msg_kakao_goodsurl']?>" style="border:1px solid #CCCCCC; width:95%;" class="line" /></td>
				</tr>
				</table>
			</div>
			<div style="width:95%; height:25px; padding:5px; text-align:right;"><a onclick="display_msg('kakao')" style="cursor:pointer"><img src="../img/btn_freeview.gif" /></a></div>
			<div id="dp_msg_kakao" style="width:95%; height:50px; border:dashed 1px #72CDFF; padding:5px;"></div>
			<div style="padding-top:5px;" class="small1 extext">
				<div>īī���� ����� ����ϼ������� ���� �մϴ�. <a href="../mobileShop/mobile_set.php" style="font-weight:bold;" class="extext">[����ϼ� ���� �ٷΰ���]</a></div>
			</div>
		</div>
		<div>
			<div style="margin:20px 5px 2px 5px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="bottom">
					<td align="left"><img src="../img/icon_arrow.gif" /> <b>Ʈ����</b></td>
					<td align="right" style="padding-right:35px;"><input type="checkbox" name="use_twitter" id="use_twitter" value="y" <?=$checked['use_twitter']['y']?> /> <label for="use_twitter">��¿���</label></td>
				</tr>
				</table>
			</div>
			<div style="vertical-align:middle">
				<textarea name="msg_twitter" id="msg_twitter" style="width:95%; height=60px;" ><?=html_entity_decode($snsCfg['msg_twitter'])?></textarea>
			</div>
			<div style="width:95%; height:25px; padding:5px; text-align:right;"><a onclick="display_msg('twitter')" style="cursor:pointer"><img src="../img/btn_freeview.gif" /></a></div>
			<div id="dp_msg_twitter" style="width:95%; height:50px; border:dashed 1px #72CDFF; padding:5px;"></div>
			<div style="padding-top:5px;" class="small1 extext">
				<div>Ʈ���ʹ� 140�ڱ����� �Է��� �����մϴ�.</div>
				<div>��ǰ���� �����Ͽ� 140�� �̻��� ��� ��ǰ���� �ٿ� ���ڼ� ���߰� �˴ϴ�. ������ �ʹ� �� ��쿡�� ������ �߻��� �� �ֽ��ϴ�.</div>
			</div>
		</div>
		<div>
			<div style="margin:20px 5px 2px 5px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="bottom">
					<td align="left"><img src="../img/icon_arrow.gif" /> <b>���̽���</b></td>
					<td align="right" style="padding-right:35px;"><input type="checkbox" name="use_facebook" id="use_facebook" value="y" <?=$checked['use_facebook']['y']?> /> <label for="use_facebook">��¿���</label></td>
				</tr>
				</table>
			</div>
			<div style="display:inline-block; width:100%;">
				<textarea name="msg_facebook" id="msg_facebook" style="width:95%; height=60px;" ><?=html_entity_decode($snsCfg['msg_facebook'])?></textarea>
			</div>
			<div style="width:95%; height:25px; padding:5px; text-align:right;"><a onclick="display_msg('facebook')" style="cursor:pointer"><img src="../img/btn_freeview.gif" /></a></div>
			<div id="dp_msg_facebook" style="width:95%; height:50px; border:dashed 1px #72CDFF; padding:5px;"></div>
			<div style="padding-top:5px;padding-bottom:5px;" class="small1 extext">
				<div>���̽����� Ÿ��Ʋ�� �����մϴ�. ��ǰURL�� �ڵ����� ���Ե˴ϴ�. </div>
			</div>
		</div>
		<div>
			<div style="margin:20px 5px 2px 5px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="bottom">
					<td align="left"><img src="../img/icon_arrow.gif" /> <b>���ͷ���Ʈ</b></td>
					<td align="right" style="padding-right:35px;"><input type="checkbox" name="use_pinterest" id="use_pinterest" value="y" <?=$checked['use_pinterest']['y']?> /> <label for="use_pinterest">��¿���</label></td>
				</tr>
				</table>
			</div>
			<div style="display:inline-block; width:100%;">
				<textarea name="msg_pinterest" id="msg_pinterest" style="width:95%; height=60px;" ><?=html_entity_decode($snsCfg['msg_pinterest'])?></textarea>
			</div>
			<div style="width:95%; height:25px; padding:5px; text-align:right;"><a onclick="display_msg('pinterest')" style="cursor:pointer"><img src="../img/btn_freeview.gif" /></a></div>
			<div id="dp_msg_pinterest" style="width:95%; height:50px; border:dashed 1px #72CDFF; padding:5px;"></div>
			<div style="padding-top:5px;padding-bottom:5px;" class="small1 extext">
				<div>���ͷ���Ʈ�� Ÿ��Ʋ�� �����մϴ�. ��ǰURL�� �ڵ����� ���Ե˴ϴ�. </div>
			</div>
		</div>
		<div>
			<div style="margin:20px 5px 2px 5px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="bottom">
					<td align="left"><img src="../img/icon_arrow.gif" /> <b>URL����</b></td>
					<td align="right" style="padding-right:35px;"><input type="checkbox" name="use_urlcopy" id="use_urlcopy" value="y" <?=$checked['use_urlcopy']['y']?> /> <label for="use_urlcopy">��¿���</label></td>
				</tr>
				</table>
			</div>
			<div style="padding-top:5px;padding-bottom:5px;" class="small1 extext">
				<div>2015�� 08�� 27�� ���� ���� ���� ��Ų�� ����Ͻô� ��� �ݵ�� ��Ų��ġ�� �����ؾ� ��� ����� �����մϴ�. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2225" target="_blank">[��ġ �ٷΰ���]</a></div>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td height="50">�����ϱ� ġȯ�ڵ�</td>
	<td>
		<div style="padding-top:5;">{snsBtn} <img class="hand" src="../img/i_copy.gif" onclick="copy_txt('{snsBtn}')" alt="�����ϱ�" align="absmiddle"/></div>
		<div style="padding-top:10;" class="small1 extext">
			<div>�����ϱ� ġȯ�ڵ�� SNS �����ϱ⸦ ����Ͽ����� ��ǰ�������� ������ ���� �ʴ� ��쿡 ����Ͻø� �˴ϴ�.</div>
		</div>
	</td>
</tr>
<tr>
	<td><div>�����ϱ� ġȯ�ڵ�</div><div style="padding:5 0 5 0">���� ���</div></td>
	<td>
		<div style="padding-top:5">���ԵǴ� �ҽ� ������ : <a href="../../admin/design/codi.php" target="_blank">"���θ� ������ > �����ΰ���"</a> ���� Ʈ�� �޴����� "��ǰ > ��ǰ��ȭ��" �޴� Ŭ��</div>
	</td>
</tr>
</table>

<div class=button>
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</form>
<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>SNS�����ϱ� ���񽺸� '�����'���� �����Ͻø� ��ǰ�� �������� Ʈ���ͷ� ��ǰURL������ ���� �� �ִ� ������ �̹����� �ڵ����� ����˴ϴ�.</td></tr>
</table>
</div>
<script type="text/javascript">enableForm(<?=($snsCfg['useBtn']=='y')?'true':'false'?>)</script>
<script>cssRound('MSG02')</script>

</div>
<? include "../_footer.php"; ?>