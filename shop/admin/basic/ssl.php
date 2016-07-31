<?
$location = "�⺻���� > ���ȼ��� �������� ��û/����";
include "../_header.php";

// ssl ��� üũ

function check_freeSSL($basicDomain,$rootdir){
	global $godo;
	$fp = @fsockopen("ssl://".$basicDomain, 443,$errno,$errstr,2);
	if ($fp){
		fwrite($fp, "GET $rootdir/freessl.php HTTP/1.0\r\nHost: $basicDomain\r\n\r\n");
		while (!feof($fp)){
		    $out .= fread($fp, 1024);
		}
		fclose($fp);
		$out = explode("\r\n\r\n",$out);
		array_shift($out);
		$out = implode("",$out);
		if(trim($out) == $godo['sno']){
		    return true;
		}
	}
	return false;
}

// ���Ẹ�ȼ������� �����ϱ�
$useFreeSSL = false;
if(!in_array($godo['webCode'],array('webhost_outside','webhost_server'))){
    $freedomain_result = readurl("http://gongji.godo.co.kr/userinterface/get.basicdomain.php?sno=".$godo['sno']);
    if(trim($freedomain_result)) $useFreeSSL = check_freeSSL($freedomain_result,$cfg[rootDir]);
}

### �ʱⰪ ����
if($cfg['ssl']== 1 && $cfg['ssl_port'] && !$cfg['ssl_type']) $cfg['ssl_type'] = 'godo';

if(!$cfg['ssl_seal']) $cfg['ssl_seal'] = '0';
if(!$cfg['free_ssl_seal']) $cfg['free_ssl_seal'] = '0';

if($cfg['ssl_type'] == 'free' && !$useFreeSSL) $cfg['ssl_type'] = "";
if( $cfg['ssl_type']=='free' && in_array($godo['webCode'],array('webhost_outside','webhost_server')) ){
    $cfg['ssl_type'] = "";
}else if( $cfg['ssl_type']=='godo' && $godo['webCode']=='webhost_outside' ){
    $cfg['ssl_type'] = "";
}else if( $cfg['ssl_type']=='direct' && $godo['webCode']!='webhost_outside' ){
    $cfg['ssl_type'] = "";
}

// ���Ẹ�ȼ���
if($cfg['ssl_domain'])
{
	$today = (int)date('Ymd');
	$ssl_sdate = (int)$cfg['ssl_sdate'];
	$ssl_edate = (int)$cfg['ssl_edate'];
	if($ssl_sdate <= $today && $ssl_edate >= $today)
	{
		if($cfg['ssl_type']=='godo')
		{
			$sslStep='used';
		}
		else
		{
			$sslStep='use';
		}
	}
	else
	{
		if($cfg['ssl_step']=='wait')
		{
			$sslStep='wait';
		}
		elseif($cfg['ssl_step']=='process')
		{
			$sslStep='process';
		}
		else
		{
			$sslStep='renewrequest';
		}
	}
}
else
{
	if($cfg['ssl_step']=='wait')
	{
		$sslStep='wait';
	}
	elseif($cfg['ssl_step']=='process')
	{
		$sslStep='process';
	}
	else
	{
		$sslStep='request';
	}
}
$arSslStep=array(
	'used'=>'�����',
	'use'=>'��밡��',
	'request'=>'��û���',
	'renewrequest'=>'�����û���',
	'wait'=>'�Աݴ����',
	'process'=>'ó����',
);


?>
<script>

function clickDirect() {
	var dr=document.getElementById('tableDirect');
	if(dr) dr.style.display='block';
	var fr=document.getElementById('tableFree');
	if(fr) fr.style.display='none';
	var gd=document.getElementById('tableGodo');
	if(gd) gd.style.display='none';
	var nn=document.getElementById('tableNone');
	if(nn) nn.style.display='none';

}
function clickNone() {
	var dr=document.getElementById('tableDirect');
	if(dr) dr.style.display='none';
	var fr=document.getElementById('tableFree');
	if(fr) fr.style.display='none';
	var gd=document.getElementById('tableGodo');
	if(gd) gd.style.display='none';
	var nn=document.getElementById('tableNone');
	if(nn) nn.style.display='block';
}
function clickFree() {
	var dr=document.getElementById('tableDirect');
	if(dr) dr.style.display='none';
	var fr=document.getElementById('tableFree');
	if(fr) fr.style.display='block';
	var gd=document.getElementById('tableGodo');
	if(gd) gd.style.display='none';
	var nn=document.getElementById('tableNone');
	if(nn) nn.style.display='none';
}
function clickGodo() {
	var dr=document.getElementById('tableDirect');
	if(dr) dr.style.display='none';
	var fr=document.getElementById('tableFree');
	if(fr) fr.style.display='none';
	var gd=document.getElementById('tableGodo');
	if(gd) gd.style.display='block';
	var nn=document.getElementById('tableNone');
	if(nn) nn.style.display='none';
}


</script>

<? if($godo['webCode'] == 'webhost_server'): ?>
	<form name=form method=post action="indb.php" >
	<input type=hidden name=mode value="ssl">
	<div class="title title_top">���ȼ��� �������� ��û/����<span></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=25')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>���ȼ��� ��뿩��</td>
		<td>
		<input type="radio" name="ssl_type" value="" style='border:0px' onclick="clickNone()" <?if($cfg['ssl_type']==''){?>checked<?}?>>
		������
		&nbsp;
		<input type="radio" name="ssl_type" value="godo" style='border:0px' onclick="clickGodo()" <?if($cfg['ssl_type']=='godo'){?>checked<?}?>>
		���� ���ȼ��� ���
		</td>
	</tr>
	</table>

	<table class=tb style='border-top:none;display:none' id="tableNone">
	<col class=cellC><col class=cellL>
	<tr>
		<td style='border-top:none'>���ȼ��� ���Ⱓ</td>
		<td style='border-top:none'></td>
	</tr>
	<tr>
		<td>���ȼ��� ������</td>
		<td></td>
	</tr>
	<tr>
		<td>���ȼ��� ��Ʈ</td>
		<td></td>
	</tr>
	</table>



	<table class=tb style='border-top:none;display:none' id="tableGodo">
	<col class=cellC><col class=cellL>
	<tr>
	<td style='border-top:none'>���ȼ��� ��뿩��</td>
	<td style='border-top:none'><font color=0a9a14><b>
	<?=$arSslStep[$sslStep]?>
	</b></font>

	<? if(in_array($sslStep,array('request','renewrequest'))) : ?>
	&nbsp;&nbsp;<font class=extext>(���ȼ��� �ȳ� �� ����û�� <a href="http://hosting.godo.co.kr/valueadd/ssl_service.php" target=_blank><font class=extext_l>[����]</font></a> �� Ŭ���ϼ���)</font>
	<? endif; ?>
	</td>
	</tr>
	<tr>
		<td>���ȼ��� ���Ⱓ</td>
		<td><?if($cfg[ssl_sdate] && $cfg[ssl_edate]){?><?=$cfg[ssl_sdate]?> - <?=$cfg[ssl_edate]?><?}?></td>
	</tr>
	<tr>
		<td>���ȼ��� ������</td>
		<td><font class=ver8><b>https://<?=$cfg[ssl_domain]?></b></font></td>
	</tr>
	<tr>
		<td>���ȼ��� ��Ʈ</td>
		<td><font class=ver8><b><?=$cfg[ssl_port]?></b></font></td>
	</tr>
	<tr>
		<td>���ȼ��� �������</td>
		<td>
		�α���, ȸ������, ȸ����������, �ֹ�, �ϴ��Ϲ���, ���޹���, �Խ��� ���� ����������
		�̿����� �������� �����Ͱ� ��ȣȭ�Ǿ� ���۵�<br>
		(�ַ�ǳ��� ���� �� member, order ���� �������鿡 ������ ��� SSL ������ ����)<br>
		<font style='color:red'>������ : ���ȼ��� ����� ����Ǹ� �ݵ�� �ֹ�(��������) �׽�Ʈ��
		���������� �ֹ��� �̷������� Ȯ���Ͻñ� �ٶ��ϴ�.</font>
		</td>
	</tr>
	</table>

	<div class="button">
	<? if(in_array($sslStep,array('request','renewrequest'))) { ?>
	<a href="http://hosting.godo.co.kr/valueadd/ssl_service.php" target=_blank><img src="../img/btn_register.gif"></a>
	<? }else{ ?>
	<input type=image src="../img/btn_register.gif">
	<? } ?>
	</div>

	</form>
	<?if($cfg['ssl_type']==''){?><script>clickNone()</script><?}?>
	<?if($cfg['ssl_type']=='godo'){?><script>clickGodo()</script><?}?>
	<?if($cfg['ssl_type']=='free'){?><script>clickFree()</script><?}?>
<? elseif(!in_array($godo['webCode'],array('webhost_outside','webhost_server'))): ?>
	<script>
	function check_request(obj){
	    var free = '<?=$useFreeSSL?>';
	    var godo = '<?=$sslStep?>';
	    if(obj.ssl_type[1].checked == true && !free){
		alert("����SSL��ġ��û�� ���� ���ֽñ� �ٶ��ϴ�.\n����SSL�� ��ġ�� �� ���� ���ȼ��� ����� �����Ͻ� �� �ֽ��ϴ�.");
		return false;
	    }
	    if(obj.ssl_type[2].checked == true && (godo == 'request' || godo == 'renewrequest')){
		window.open("http://hosting.godo.co.kr/valueadd/ssl_service.php");
		return false;
	    }
	    return true;
	}
	</script>
	<form name=form method=post action="indb.php" onsubmit="return check_request(this);">
	<input type=hidden name=mode value="ssl">
	<input type=hidden name='ssl_freedomain' value="<?=$freedomain_result?>">
	<div class="title title_top">���ȼ��� �������� ��û/����<span></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=25')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>���ȼ��� ��뿩��</td>
		<td>
		<input type="radio" name="ssl_type" value="" style='border:0px' onclick="clickNone()" <?if($cfg['ssl_type']==''){?>checked<?}?>>
		������
		&nbsp;
		<input type="radio" name="ssl_type" value="free" style='border:0px' onclick="clickFree()" <?if($cfg['ssl_type']=='free'){?>checked<?}?>>
		���� ���ȼ��� ���
		<input type="radio" name="ssl_type" value="godo" style='border:0px' onclick="clickGodo()" <?if($cfg['ssl_type']=='godo'){?>checked<?}?>>
		���� ���ȼ��� ���
		</td>
	</tr>
	</table>

	<table class=tb style='border-top:none;display:none' id="tableNone">
	<col class=cellC><col class=cellL>
	<tr>
		<td style='border-top:none'>���ȼ��� ���Ⱓ</td>
		<td style='border-top:none'></td>
	</tr>
	<tr>
		<td>���ȼ��� ������</td>
		<td></td>
	</tr>
	<tr>
		<td>���ȼ��� ��Ʈ</td>
		<td></td>
	</tr>
	</table>

	<table class=tb style='border-top:none;display:none' id="tableFree">
	<col class=cellC><col class=cellL>
	<tr>
	<td style='border-top:none'>���ȼ��� ��뿩��</td>
	<td style='border-top:none'><font color=0a9a14><b>
	<?if($cfg['ssl_type']=='free'){?>�����<?}?>
	<?if($cfg['ssl_type']!='free' && $useFreeSSL){?>��밡��(��Ϲ�ư�� ��������)<?}?>
	<?if($cfg['ssl_type']!='free' && !$useFreeSSL){?><a href="freessl.php" target="ifrmHidden">[����SSL��ġ��û]</a><?}?>
	</b></font> </td>
	</tr>
	<tr>
		<td>���ȼ��� ���Ⱓ</td>
		<td>���Ѿ���</td>
	</tr>
	<tr>
		<td>���ȼ��� �������</td>
		<td>
		�α���, ȸ������, ȸ���������� ���������� �̿����� �������� �����Ͱ� ��ȣȭ�Ǿ� ���۵�
		</td>
	</tr>
	<tr>
		<td>������ <a href="<?=$guideUrl?>board/view.php?id=basic&no=25" target='_blank'><img src="../img/btn_q.gif" align="absmiddle"></a></td>
		<td>
			<div>ȸ�� �α���,ȸ������ form�� action�±��� ���� �����ϴ� ġȯ�ڵ�� �����Ͽ��� �մϴ�.</div>
		    <div class="small extext">
		    <div style="padding-top:5"><b>������ġ : </b>ȸ�� �α��� form, ȸ������ form</div>
		    <div><b>�α��� form ġȯ�ڵ� : </b>{loginActionUrl}</div>
		    <div><b>ȸ������ form ġȯ�ڵ� : </b>{memActionUrl}</div>
		    </div>
		</td>
	</tr>
	<!-- ���Ẹ�ȼ��� ������ũǥ�� -->
	<tr>
		<td rowspan="2">������ũ ǥ��</td>
		<td>
			<input type="radio" name="free_ssl_seal" value="0" style='border:0px' <?if($cfg['free_ssl_seal']=='0'){?>checked<?}?>>ǥ����������&nbsp;
			<input type="radio" name="free_ssl_seal" value="1" style='border:0px' <?if($cfg['free_ssl_seal']=='1'){?>checked<?}?>>ǥ����
		</td>
	</tr>
	<tr>
		<td>
			<div class="small extext">
				<div style="padding-top:5"><b>* ��Ų ������ ���� �� ���濡 ���� �������� ������ũ ǥ�� ������</b></div>
				<div>- ��Ų �ҽ��� �����Ͽ��ų�, ��Ų�� �������� ���, �Ǵ� ���� ��Ų�� ���� ��츦 ���� ǥ�� ����Դϴ�.</div>
				<div>- ��Ų�� ���� �ϴܼҽ��� Table������ �ٸ���, �� �κ� �����ؼ� ���ϴ� ��ġ�� ġȯ�ڵ带 �־��ּ���.</div>
				<div>- ������ ������ũ ǥ�ÿ��θ� 'ǥ����'���� ���� ��, </div>
				<div width=70% style='padding-left:10px'><font class=extext><a href='../design/codi.php?design_file=outline/footer/main_footer.htm' target=_blank><font class=extext><b>[�����ΰ��� > ��ü���̾ƿ� ������ > �ϴܵ����� > ���ο� �ϴ����� > html�ҽ� ��������]</b></font></a> �� ����<br> ġȯ�ڵ� <font class=ver8 color=000000><b>{=displaySSLSeal()}</b></font> �� �����ϼ���. <a href='../design/codi.php?design_file=outline/footer/main_footer.htm' target=_blank><font class=extext_l>[�ٷΰ���]</font></a></font></div>
				<div width=70% style='padding-left:10px'><font class=extext><a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext><b>[�����ΰ��� > ��ü���̾ƿ� ������ > �ϴܵ����� > html�ҽ� ��������]</b></font></a> �� ����<br> ġȯ�ڵ� <font class=ver8 color=000000><b>{=displaySSLSeal()}</b></font> �� �����ϼ���. <a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext_l>[�ٷΰ���]</font></a></font></div>
			</div>
		</td>
	</tr>
	</table>

	<table class=tb style='border-top:none;display:none' id="tableGodo">
	<col class=cellC><col class=cellL>
	<tr>
	<td style='border-top:none'>���ȼ��� ��뿩��</td>
	<td style='border-top:none'><font color=0a9a14><b>
	<?=$arSslStep[$sslStep]?>
	</b></font>

	<? if(in_array($sslStep,array('request','renewrequest'))) : ?>
	&nbsp;&nbsp;<font class=extext>(���ȼ��� �ȳ� �� ����û�� <a href="http://hosting.godo.co.kr/valueadd/ssl_service.php" target=_blank><font class=extext_l>[����]</font></a> �� Ŭ���ϼ���)</font>
	<? endif; ?>
	</td>
	</tr>
	<tr>
		<td>���ȼ��� ���Ⱓ</td>
		<td><?if($cfg[ssl_sdate] && $cfg[ssl_edate]){?><?=$cfg[ssl_sdate]?> - <?=$cfg[ssl_edate]?><?}?></td>
	</tr>
	<tr>
		<td>���ȼ��� ������</td>
		<td><font class=ver8><b>https://<?=$cfg[ssl_domain]?></b></font></td>
	</tr>
	<tr>
		<td>���ȼ��� ��Ʈ</td>
		<td><font class=ver8><b><?=$cfg[ssl_port]?></b></font></td>
	</tr>
	<tr>
		<td>���ȼ��� �������</td>
		<td>
		�α���, ȸ������, ȸ����������, �ֹ�, �ϴ��Ϲ���, ���޹���, �Խ��� ���� ����������
		�̿����� �������� �����Ͱ� ��ȣȭ�Ǿ� ���۵�<br>
		(�ַ�ǳ��� ���� �� member, order ���� �������鿡 ������ ��� SSL ������ ����)<br>
		<font style='color:red'>������ : ���ȼ��� ����� ����Ǹ� �ݵ�� �ֹ�(��������) �׽�Ʈ��
		���������� �ֹ��� �̷������� Ȯ���Ͻñ� �ٶ��ϴ�.</font>
		</td>
	</tr>
	<!-- ���Ẹ�ȼ��� ������ũǥ�� -->
	<tr>
		<td rowspan="5">������ũ ǥ��</td>
		<td>
			<input type="radio" name="ssl_seal" value="0" style='border:0px' onclick="seal_view(this.value)" <?if($cfg['ssl_seal']=='0'){?>checked<?}?>>ǥ����������
		</td>
	</tr>
	<tr>
		<td>
			<div style="width:100px;float:left;">
				<input type="radio" name="ssl_seal" value="g" style='border:0px' onclick="seal_view(this.value)"  <?if($cfg['ssl_seal']=='g'){?>checked<?}?>>GlobalSign : 
			</div>
			<div style="width:100px;float:left;">
				<select name="globalsign" id="globalsign" onchange="globalsign_sel(this.value)" disabled>
					<option value="a" <?if($cfg['globalsign']=='a' || $cfg['globalsign']==''){?>selected<?}?>>Alpha SSL</option>
					<option value="q" <?if($cfg['globalsign']=='q'){?>selected<?}?>>Quick SSL</option>
				</select>
			</div>
			<div id="globalsign_1" style="display:none;">
				<img src="../../lib/ssl/GlobalSign/alpha.jpg" alt="globalsign" width="100px" height="50px" />
			</div>
			<div id="globalsign_2" style="display:none;">
				<img src="../../lib/ssl/GlobalSign/quick.jpg" alt="globalsign" width="100px" height="50px" />
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div style="width:100px;float:left;">
				<input type="radio" name="ssl_seal" value="c" style='border:0px' onclick="seal_view(this.value)"  <?if($cfg['ssl_seal']=='c'){?>checked<?}?>>Comodo :
			</div>
			<div style="width:100px;float:left;">
				<select name="comodo" id="comodo" disabled>
					<option value="b" <?if($cfg['comodo']=='b'){?>selected<?}?>>������</option>
					<option value="g" <?if($cfg['comodo']=='g'){?>selected<?}?>>�۷ι�</option>
					<option value="p" <?if($cfg['comodo']=='p'){?>selected<?}?>>����</option>
					<option value="a" <?if($cfg['comodo']=='a'){?>selected<?}?>>�����̾�</option>
					<option value="w" <?if($cfg['comodo']=='w'){?>selected<?}?>>���ϵ�ī��</option>
				</select>
			</div>
			<div id="comodo_1" style="display:none;">
				<img src="../../lib/ssl/Comodo/standard_logo/comodo_seal_52x63.png" alt="comodo" width="52px" height="63px" />
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="small extext">
				<div style="padding-top:5">���θ� �ϴ� ��������� ������ ������ ������ũ�� ǥ�õ˴ϴ�.</div>
				<div>*����Ͻô� ���ȼ��� ���� ��ǰ�� ǥ���ϰ��� �ϴ� ���ȼ��� ������ũ�� ������ Ȯ���Ͻ� ��, ������ּ���.</div>
				<div style="padding-top:5">���񽺻�ǰ Ȯ�� �� ������ <a href='http://www.godo.co.kr/mygodo/my_godo_secure_list.php' target="_blank"><font class="extext"><b>[���̰� > ���θ����� > ���ȼ�������]</b></font></a>���� �����մϴ�.</div>
				<div>�������� �ƴ� ������ũ ǥ�� ��Ͻ� ������ �߻� �� �� �ֽ��ϴ�.</div>
				
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="small extext">
				<div style="padding-top:5"><b>* ��Ų ������ ���� �� ���濡 ���� �������� ������ũ ǥ�� ������</b></div>
				<div>- ��Ų �ҽ��� �����Ͽ��ų�, ��Ų�� �������� ���, �Ǵ� ���� ��Ų�� ���� ��츦 ���� ǥ�� ����Դϴ�.</div>
				<div>- ��Ų�� ���� �ϴܼҽ��� Table������ �ٸ���, �� �κ� �����ؼ� ���ϴ� ��ġ�� ġȯ�ڵ带 �־��ּ���.</div>
				<div>- ������ ������ũ ǥ�ÿ��θ� 'ǥ����'���� ���� ��, </div>
				<div width=70% style='padding-left:10px'><font class=extext><a href='../design/codi.php?design_file=outline/footer/main_footer.htm' target=_blank><font class=extext><b>[�����ΰ��� > ��ü���̾ƿ� ������ > �ϴܵ����� > ���ο� �ϴ����� > html�ҽ� ��������]</b></font></a> �� ����<br> ġȯ�ڵ� <font class=ver8 color=000000><b>{=displaySSLSeal()}</b></font> �� �����ϼ���. <a href='../design/codi.php?design_file=outline/footer/main_footer.htm' target=_blank><font class=extext_l>[�ٷΰ���]</font></a></font></div>
				<div width=70% style='padding-left:10px'><font class=extext><a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext><b>[�����ΰ��� > ��ü���̾ƿ� ������ > �ϴܵ����� > �ϴܱ⺻Ÿ�� > html�ҽ� ��������]</b></font></a> �� ����<br> ġȯ�ڵ� <font class=ver8 color=000000><b>{=displaySSLSeal()}</b></font> �� �����ϼ���. <a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext_l>[�ٷΰ���]</font></a></font></div>
			</div>
		</td>
	</tr>
	</table>

	<div class="button">
	<input type=image src="../img/btn_register.gif">
	</div>

	</form>
	<?if($cfg['ssl_type']==''){?><script>clickNone()</script><?}?>
	<?if($cfg['ssl_type']=='godo'){?><script>clickGodo()</script><?}?>
	<?if($cfg['ssl_type']=='free'){?><script>clickFree()</script><?}?>
<? elseif($godo['webCode'] == 'webhost_outside'): ?>
	<form name=form method=post action="indb.php" >
	<input type=hidden name=mode value="ssl">

	<div class="title title_top">���ȼ��� �������� ��û/����<span></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=25')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>���ȼ��� ��뿩��</td>
		<td>
		<input type="radio" name="ssl_type" value="" style='border:0px' onclick="clickNone()"
		<?if($cfg['ssl_type']==''){?>checked<?}?>>������
		&nbsp;
		<input type="radio" name="ssl_type" value="direct" style='border:0px' onclick="clickDirect()"	<?if($cfg['ssl_type']=='direct'){?>checked<?}?>>��������
		</td>
	</tr>
	</table>

	<table class=tb style='border-top:none;display:none' id="tableNone">
	<col class=cellC><col class=cellL>
	<tr>
		<td style='border-top:none'>���Ⱓ</td>
		<td style='border-top:none'></td>
	</tr>
	<tr>
		<td>���ȼ��� ������</td>
		<td></td>
	</tr>
	<tr>
		<td>���ȼ��� ��Ʈ</td>
		<td></td>
	</tr>
	</table>

	<table class=tb style='border-top:none;display:none' id="tableDirect">
	<col class=cellC><col class=cellL>
	<tr>
		<td style='border-top:none'>���Ⱓ</td>
		<td style='border-top:none'><input type=text name=ssl_sdate value="<?=$cfg['ssl_sdate']?>" onclick="calendar(event)" class=line> - <input type=text name=ssl_edate value="<?=$cfg['ssl_edate']?>" onclick="calendar(event)" class=line></td>
	</tr>
	<tr>
		<td>���ȼ��� ������</td>
		<td><input type=text name='ssl_domain' value="<?=$cfg['ssl_domain']?>" class=line></td>
	</tr>
	<tr>
		<td>���ȼ��� ��Ʈ</td>
		<td><input type=text name='ssl_port' value="<?=$cfg['ssl_port']?>" class=line></td>
	</tr>
	<tr>
		<td>���ȼ��� �������</td>
		<td>
		�α���, ȸ������, ȸ����������, �ֹ� ���� ����������
		�̿����� �������� �����Ͱ� ��ȣȭ�Ǿ� ���۵�<br>
		(�ַ�ǳ��� ���� �� member, order, admin/login, admin/order, admin/member ���� �������鿡 ������ ��� SSL ������ ����)<br>
		<font style='color:red'>������ : ���ȼ��� ����� ����Ǹ� �ݵ�� �ֹ�(��������) �׽�Ʈ��
		���������� �ֹ��� �̷������� Ȯ���Ͻñ� �ٶ��ϴ�.</font>
		</td>
	</tr>
	</table>

	<div class="button">
	<input type=image src="../img/btn_register.gif">
	</div>

	</form>

	<?if($cfg['ssl_type']==''){?><script>clickNone()</script><?}?>
	<?if($cfg['ssl_type']=='direct'){?><script>clickDirect()</script><?}?>
<? endif;?>


<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse;margin-bottom:20px;" width="100%">
<tr><td style="padding:7px 0 10px 10px; color:#666666;">
<div style="padding-top:5px;"><font class="g9" color="#0074BA"><b>�� ���ȼ���(SSL)���� �ǹ� ��ȭ �ȳ�</b></font></div>
<div style="padding-top:7px;">������Ÿ��� ������ ���� ���ȼ���(SSL) ���� �ǹ������� ���ݽ� �ο��� �߻��� ��� <u>������� ���� �ְ� 3,000������ ���·ᰡ �ΰ��˴ϴ�.</u></div>
<ul style="margin:7px 0 0 25px;">
<li>�������� : 2012�� 8�� 18��</li>
<li>�������� : ���ȼ��� �̱��� ������Ʈ�� ���� ������� ���� �ִ� 3õ������ ���� �ΰ�</li>
<li>������ : �¶��� ���θ� �� ȸ������/�α���/�ֹ�/����/�Խ��� ���� �̿�������� �̸�, �ֹε�Ϲ�ȣ, ����ó ���� ����ϴ� ������Ʈ</li>
</ul>
<div style="padding-top:7px;">��ϴ� ����Ʈ�� �������� ��� ���θ� �ݵ�� Ȯ���Ͻð�, �ǹ����� ���� ��� �ش��ϴ� ����Ʈ�� ���ȼ��� ����� �ʼ��� �����Ͽ� �ּ���.</div>
<div style="padding-top:10px;">"[���Ẹ�ȼ��� ���]���� ���� �� �ֹ�/���� ���� �Ϻ� ������������ ���ȼ����� ������� �ʽ��ϴ�.<br />
���� ���ȼ����� �̿����� �������� �ݵ�� [���� ���ȼ��� ���] ���� ��ȯ�Ͻþ�, �������� ���� �ʵ��� ��ġ�� �ֽñ� �ٶ��ϴ�. ���� ���ȼ����� ��ġ�Ⱓ�� �ټ�(2��~7��) �ҿ�� �� �ֽ��ϴ�."</div>
<div style="padding-top:5px;"><a href="http://www.godo.co.kr/news/notice_view.php?board_idx=722&page=2" target="_blank">[�ڼ�������]</a></div>
</table>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
    <tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ���ȼ���(SSL) ���� ��ġ�ð��� ��û �� �ִ� �Ϸ� �Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ���ȼ���(SSL) ������ ó���ܰ�� '��û���' �� '�Աݴ����' �� 'ó����' �� '�����' �Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ���ȼ���(SSL) ���񽺴� ���������� ������ �Ϸ�Ǹ� '�����'���� ����˴ϴ�.</td></tr>
</table>
</div>

<script type="text/javascript">
<!--
function seal_view(type) {

	var type2 = document.getElementById('globalsign').value;

	if (type == "g")	{
		document.getElementById('globalsign').disabled = false;
		document.getElementById('comodo').disabled = true;
		if(type2 == "a") {
			document.getElementById('globalsign_1').style.display = "";
			document.getElementById('globalsign_2').style.display = "none";
		} else if(type2 == "q") {
			document.getElementById('globalsign_1').style.display = "none";
			document.getElementById('globalsign_2').style.display = "";
		} else {
			
		}		
		document.getElementById('comodo_1').style.display = "none";
	} else if (type == "c")	{
		document.getElementById('comodo').disabled = false;
		document.getElementById('globalsign').disabled = true;
		document.getElementById('globalsign_1').style.display = "none";
		document.getElementById('globalsign_2').style.display = "none";
		document.getElementById('comodo_1').style.display = "";
		
	} else if (type == "0")	{
		document.getElementById('comodo').disabled = true;
		document.getElementById('globalsign').disabled = true;
		document.getElementById('globalsign_1').style.display = "none";
		document.getElementById('globalsign_2').style.display = "none";
		document.getElementById('comodo_1').style.display = "none";
	}
}	
function globalsign_sel(type) {
	if(type == "a" || type == "") {
		document.getElementById('globalsign_1').style.display = "";
		document.getElementById('globalsign_2').style.display = "none";
	} else {
		document.getElementById('globalsign_1').style.display = "none";
		document.getElementById('globalsign_2').style.display = "";
	}
}

//-->
</script>
<script>cssRound('MSG01')</script>
<? if ($cfg['ssl_seal'] == "g" && $cfg['globalsign'] == "a") { ?><script>seal_view('g');globalsign_sel("a");</script><? } ?>
<? if ($cfg['ssl_seal'] == "g" && $cfg['globalsign'] == "q") { ?><script>seal_view('g');globalsign_sel("q");</script><? } ?>
<? if ($cfg['ssl_seal'] == "c") { ?><script>seal_view('c');</script><? } ?>

<? include "../_footer.php"; ?>