<?
	include "../_header.popup.php";

	if($_POST['save'] == 'setting'){	

		if(	!$_POST['loofYN'] ){$_POST['loofYN'] = "n";}
		
		// ���� ���� �� ������ ���� ���� ũ�� ���� �������� ����
		if( $_POST['volume'] > 100 ){ $_POST['volume'] = 100; }else if($_POST['volume'] < 0){ $_POST['volume'] = 0; }

		$config_bgm = array(
			'urlFix'=>(string)$_POST['urlFix'],
			'use'	=>(string)$_POST['bgmuesYN'],
			'file'	=>(string)$_POST['fileLink'],
			'volume'=>(string)$_POST['volume'],
			'loof'	=>(string)$_POST['loofYN'],
		);
		$config->save('bgm',$config_bgm);	
		
		echo "
		<script>
		alert('����Ǿ����ϴ�');
		self.location.href='iframe.bgm.setting.php';
		</script>
		";
		exit;
	}

	$load_config_bgm = $config->load('bgm');

	if( !$load_config_bgm['volume'] ){ $load_config_bgm['volume'] = "50";}

	$checked['urlFix'][$load_config_bgm['urlFix']] = "checked";
	$checked['bgmuesYN'][$load_config_bgm['use']] = "checked";
	$checked['loofYN'][$load_config_bgm['loof']] = "checked";
?>
<script>
	function volume_control(mode){
		var t_volume = document.getElementsByName('volume')[0].value;

		if( mode == 'up' ){
			if( t_volume >= 100 ){
				alert("���̻� Ŀ�� �� �����ϴ�.");
				return;
			}else{
				document.getElementsByName('volume')[0].value = Number(t_volume)+20;
			}
		}else if( mode == 'down' ){
			if( t_volume <= 0 ){
				alert("���̻� �۾��� �� �����ϴ�.");
				return;
			}else{
				document.getElementsByName('volume')[0].value = Number(t_volume)-20;
			}
		}	
		
		volume_state();
	}

	function volume_state(){
		document.getElementById('volume_img').innerHTML = "";

		var f_volume = document.getElementsByName('volume')[0].value;

		for(i=0;i<=10;i=i+2)
		{
			if( f_volume.length != '3' ){
				if( f_volume.substr(0,1) == i || (Number(f_volume.substr(0,1))+1) == i ){
					document.getElementById('volume_img').innerHTML += "<img src='../img/bgm_scroll.gif'>";
				}else{
					document.getElementById('volume_img').innerHTML += "<img src='../img/bgm_bg.gif'>";
				}
			}else{
				if( f_volume.substr(0,2) == i ){
					document.getElementById('volume_img').innerHTML += "<img src='../img/bgm_scroll.gif'>";
				}else{
					document.getElementById('volume_img').innerHTML += "<img src='../img/bgm_bg.gif'>";
				}
			}
		}
	}

	function volume_check(){		
		var c_volume = document.getElementsByName('volume')[0].value;
		if( c_volume < 0 ){	document.getElementsByName('volume')[0].value = 0; }else if(c_volume > 100){ document.getElementsByName('volume')[0].value = 100; }
	}

	onload = volume_state;

</script>
<form id="frmBgm" action="iframe.bgm.setting.php" method="post">
<input type="hidden" name="save" value="setting">
<div class="title title_top">��� ���� ����<span> ��������� �������ּ���. </span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=14')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC">
<col class="cellL">
<tr>
	<td> ������� ��� </td>
	<td>
		<input type="radio" name="bgmuesYN" style="border:0px" <?=$checked['bgmuesYN']['y']?> value="y"> ���
		<input type="radio" name="bgmuesYN" style="border:0px" <?=$checked['bgmuesYN']['n']?><?=$checked['bgmuesYN']['']?> value="n"> ������
	</td>
</tr>
<tr>
	<td> ������� ���� </td>
	<td>
		<input type="text" name="fileLink" value="<?=$load_config_bgm['file']?>" style="width:400px;">
		<br><span style="font:8pt ����;color:#627dcf;">��Ʈ���� ����� �����ϴ� wma, asf Ȯ���ڸ� ���� ���� ���� �ּҸ� �Է��մϴ�.</span>
		<br><span style="font:8pt ����;color:#627dcf;">����) http://www.godo.co.kr/�������ϸ�.wma</span>
	</td>
</tr>
<tr>
	<td> �� �� ȯ �� </td>
	<td>
		<!-- ���� -->		
		<img src="../img/ico_sound.gif">
		<font size="6pt">
		<a href="javascript:volume_control('down');"><img src="../img/bgm_down.gif"></a><span id="volume_img"></span><a href="javascript:volume_control('up');"><img src="../img/bgm_up.gif"></a>
		</font>
		<input type="text" name="volume" onblur="javacript:volume_check();" value="<?=$load_config_bgm['volume']?>" style="width:30px;" maxlength="3"/> %
		<!-- // ���� -->
		<input type="checkbox" name="loofYN" style="border:0px" <?=$checked['loofYN']['y']?>  value="y" />���� �ݺ� ( �ּ� : 0 , �ִ� : 100 )
	</td>
</tr>
</table>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"> <b>��������� ����ϱ� ���ؼ��� ���� ���������� ��Ʈ���� ȣ���ÿ� ����� �ϼž� �մϴ�.</b><br/>
 &nbsp;&nbsp; ���θ����� ���� ������ �ø� �� �����ϴ�.<br/>
 &nbsp;&nbsp; ������ ��Ʈ���� ȣ������ ��û�Ͻ� ��, �ش� ȣ���ÿ� ���������� �ø��� �ش� �ּҸ� �����Ͻø� �˴ϴ�.<br/>
 &nbsp;&nbsp; <a href="http://hosting.godo.co.kr/streaminghosting/streaminghosting_info.php" target="_blank" style="color:#ffffff;font-weight:bold">[��Ʈ���� ȣ���� �ڼ������� ��]</a>
</td></tr>
<tr><td style="padding-top:5px;"><img src="../img/icon_list.gif" align="absmiddle"> ��������� ���۱ǿ� ������� �ʴ� ������ ����ؾ� �մϴ�.<br/>
 &nbsp;&nbsp; ��������� ���۱� ���ݿ� ���� å���� ���θ� ��ڿ� �ֽ��ϴ�. ���� ��Ź�帳�ϴ�.
</td></tr>
</table>
</div>
<script>
cssRound('MSG01')
</script>

<br>
<div class="title title_top">���ͳ� �ּ� ���<span>�ּ�â�� ǥ�õǴ� ������ �������ּ���.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=14')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellL">
<tr>
	<td>
		<input type="radio" name="urlFix" value="n" <?=$checked['urlFix']['n']?><?=$checked['urlFix']['']?> style="border:0px"> ������ �ּ� ��� <span style="font:8pt ����;color:#627dcf;">(�⺻ �������� ���� �������� �ּҸ� �����մϴ�)</span>
	</td>
<tr>
	<td>
		<input type="radio" name="urlFix" value="y" <?=$checked['urlFix']['y']?> style="border:0px"> ������ �ּ� ���� <span style="font:8pt ����;color:#627dcf;">(�������� ����Ͽ� ���� �������� �����մϴ�. <strong>��������� ����Ϸ��� �ݵ�� �����ؾ� �մϴ�</strong>)<br><div style="padding-left:125px">������ �ּ� ������ �ϰ� �Ǹ� ��Ų�ܿ��� ������ �ĺ����� ����Ǿ����� �ʽ��ϴ�.</div></span>
	</td>
</tr>
</table>

<div class="button">
<input type=image src="../img/btn_register.gif">
</div>

</form>
<script>
table_design_load();
setHeight_ifrmCodi();
</script>