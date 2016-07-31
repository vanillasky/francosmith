<?
	include "../_header.popup.php";

	if($_POST['save'] == 'setting'){	

		if(	!$_POST['loofYN'] ){$_POST['loofYN'] = "n";}
		
		// 볼륨 저장 시 정해진 숫자 보다 크면 고정 볼륨으로 변경
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
		alert('저장되었습니다');
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
				alert("더이상 커질 수 없습니다.");
				return;
			}else{
				document.getElementsByName('volume')[0].value = Number(t_volume)+20;
			}
		}else if( mode == 'down' ){
			if( t_volume <= 0 ){
				alert("더이상 작아질 수 없습니다.");
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
<div class="title title_top">배경 음악 설정<span> 배경음악을 설정해주세요. </span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=14')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC">
<col class="cellL">
<tr>
	<td> 배경음악 사용 </td>
	<td>
		<input type="radio" name="bgmuesYN" style="border:0px" <?=$checked['bgmuesYN']['y']?> value="y"> 사용
		<input type="radio" name="bgmuesYN" style="border:0px" <?=$checked['bgmuesYN']['n']?><?=$checked['bgmuesYN']['']?> value="n"> 사용안함
	</td>
</tr>
<tr>
	<td> 배경음악 파일 </td>
	<td>
		<input type="text" name="fileLink" value="<?=$load_config_bgm['file']?>" style="width:400px;">
		<br><span style="font:8pt 돋움;color:#627dcf;">스트리밍 재생을 지원하는 wma, asf 확장자를 가진 음악 파일 주소만 입력합니다.</span>
		<br><span style="font:8pt 돋움;color:#627dcf;">예시) http://www.godo.co.kr/음악파일명.wma</span>
	</td>
</tr>
<tr>
	<td> 재 생 환 경 </td>
	<td>
		<!-- 볼륨 -->		
		<img src="../img/ico_sound.gif">
		<font size="6pt">
		<a href="javascript:volume_control('down');"><img src="../img/bgm_down.gif"></a><span id="volume_img"></span><a href="javascript:volume_control('up');"><img src="../img/bgm_up.gif"></a>
		</font>
		<input type="text" name="volume" onblur="javacript:volume_check();" value="<?=$load_config_bgm['volume']?>" style="width:30px;" maxlength="3"/> %
		<!-- // 볼륨 -->
		<input type="checkbox" name="loofYN" style="border:0px" <?=$checked['loofYN']['y']?>  value="y" />무한 반복 ( 최소 : 0 , 최대 : 100 )
	</td>
</tr>
</table>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"> <b>배경음악을 사용하기 위해서는 먼저 음악파일을 스트리밍 호스팅에 등록을 하셔야 합니다.</b><br/>
 &nbsp;&nbsp; 쇼핑몰에는 음악 파일을 올릴 수 없습니다.<br/>
 &nbsp;&nbsp; 별도로 스트리밍 호스팅을 신청하신 후, 해당 호스팅에 음악파일을 올리고 해당 주소를 삽입하시면 됩니다.<br/>
 &nbsp;&nbsp; <a href="http://hosting.godo.co.kr/streaminghosting/streaminghosting_info.php" target="_blank" style="color:#ffffff;font-weight:bold">[스트리밍 호스팅 자세히보기 ▶]</a>
</td></tr>
<tr><td style="padding-top:5px;"><img src="../img/icon_list.gif" align="absmiddle"> 배경음악은 저작권에 위배되지 않는 음악을 사용해야 합니다.<br/>
 &nbsp;&nbsp; 배경음악의 저작권 위반에 대한 책임은 쇼핑몰 운영자에 있습니다. 주의 부탁드립니다.
</td></tr>
</table>
</div>
<script>
cssRound('MSG01')
</script>

<br>
<div class="title title_top">인터넷 주소 출력<span>주소창에 표시되는 형식을 설정해주세요.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=14')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellL">
<tr>
	<td>
		<input type="radio" name="urlFix" value="n" <?=$checked['urlFix']['n']?><?=$checked['urlFix']['']?> style="border:0px"> 페이지 주소 출력 <span style="font:8pt 돋움;color:#627dcf;">(기본 설정으로 현재 페이지의 주소를 노출합니다)</span>
	</td>
<tr>
	<td>
		<input type="radio" name="urlFix" value="y" <?=$checked['urlFix']['y']?> style="border:0px"> 도메인 주소 고정 <span style="font:8pt 돋움;color:#627dcf;">(프레임을 사용하여 접속 도메인을 유지합니다. <strong>배경음악을 사용하려면 반드시 설정해야 합니다</strong>)<br><div style="padding-left:125px">도메인 주소 고정을 하게 되면 스킨단에서 설정한 파비콘은 적용되어지지 않습니다.</div></span>
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