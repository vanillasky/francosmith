<?
$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";
?>

<script language="javascript"><!--
function intro_sample_view(){

	var no = document.getElementById('intro_sample').value;
	var tag = "<img src='<?=$cfg['rootDir']?>/data/skin/<?=$cfg['tplSkinWork']?>/img/main/coming_" + no + ".gif' border='0'>";

	var txt = tag;
	txt = txt.replace( /\</, '&lt;' );
	txt = txt.replace( /\>/, '&gt;' );

	document.getElementById('intro_tag').innerHTML = txt;
	document.getElementById('intro_img').innerHTML = tag;
	setHeight_ifrmCodi();
}
--></script>


<form name="fm" method="post" action="../design/indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="intro_save" />
<input type=hidden name=tplSkinWork value="<?=$cfg['tplSkinWork']?>">

<div class="title title_top">인트로/공사중 페이지 디자인<span>일반적인 인트로/공사중 페이지의 디자인을 수정합니다.</span></div>

<?=$workSkinStr?>


<!--<div style="margin:10px 0 10px 0;"><font class=extext>공사중 페이지를 보려면 '<a href="/shop/main/intro.php" target="_blank"><font class=ver7 color="#0074BA"><b><u>http://도메인명</u></b></font></a>' 을 클릭하세요.</div>
<div style="margin:10px 0 10px 0;"><font class=extext>메인페이지를 보려면 '<a href="/shop/main/index.php" target="_blank"><font class=ver7 color="#0074BA"><b><u>http://도메인명/shop/main/index.php</u></b></font></a>' 를 클릭하세요.</div>-->

<?=gen_design_history_tag('skin', $cfg['tplSkinWork'], 'main/intro.htm'); ?>

<?
{ // 디자인코디툴

	$tmp = array();
	$tmp['t_name']		= 'content';
	$tmp['t_width']		= '99%';
	$tmp['t_rows']		= 20;
	$tmp['t_property']	= ' required label="HTML 소스"';
	$tmp['tplFile']		= "/main/intro.htm";

	echo "<script>DCTM.write('{$tmp['t_name']}', '{$tmp['t_width']}', '{$tmp['t_rows']}', '{$tmp['t_property']}', '{$tmp['tplFile']}');</script>";
}
?>

<div style="padding-top:20px;"></div>


<table class="tb">
<col class="cellC"><col>
<tr>
	<td valign="top">
	<select id="intro_sample" onchange="intro_sample_view()">
	<option value="01">공사중 예제 1</option>
	<option value="02">공사중 예제 2</option>
	<option value="03">공사중 예제 3</option>
	<option value="04">공사중 예제 4</option>
	<option value="05">공사중 예제 5</option>
	</select>
	</td>
	<td>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td id="intro_tag" style="padding:10px;background-color:#0071BB;color:#FFFFFF;"></td>
			</tr>
			<tr>
				<td id="intro_img" style="padding:10px;"></td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;&nbsp;<font class="small" color="#6d6d6d">위 파란박스내의 코드부분을 복사해서 사용하세요.</font></td>
			</tr>
		</table>
	</td>
</tr>
</table>

<input type="hidden" name="skin_file" value="<?=$tmp['tplFile']?>"/>
<input type="hidden" name="gd_preview" value=""/>
<div style="padding:20px" align="center">
<a onclick="preview()" style="cursor:pointer"><img src="../img/codi/btn_editview.gif" /></a>
<input type="image" src="../img/btn_register.gif" class="null" />
</div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class=small_ex>
<tr><td>인트로 페이지 또는 공사중 페이지를 설정할 수 있습니다.</td></tr>
<tr><td>예제이미지 5개를 제공해드립니다. 위 파란박스내의 소스를 복사 후 에디터에 넣어 활용하세요.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>성인인증 인트로 페이지는 유형에 따라 2종류로 제공됩니다.</td></tr>
<tr><td>① 메인 페이지 접속이 성인 또는 회원만 접근 가능한 인트로 페이지</td></tr>
<tr><td>&nbsp;- 성인 또는 회원만 접근이 가능한 사이트에 사용됩니다. 성인을 인증할 수 있는 본인확인 인증서비스를 신청하고 이용하여 주세요.</td></tr>
<tr><td>② 메인 페이지 접속이 회원만 접근 가능한 인트로 페이지</td></tr>
<tr><td>&nbsp;- 회원만 접근이 가능한 사이트에 사용되며, 상품 구매는 회원만 가능합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<script>
// 디자인관리 인트로 미리보기
function preview() {
	DCPV.design_preview = window.open('about:blank');
	var fobj = document.fm;
	var ori_target = fobj.target;

	try {
		if (DCTM.editor_type == "codemirror" && DCTM.textarea_view_id == DCTM.textarea_merge_body) {
			DCTC.ed1.setValue(DCTC.merge_ed.editor().getValue());
		}
	}
	catch(e) {}

	fobj.gd_preview.value = '1';
	fobj.target = "ifrmHidden";
	fobj.submit();

	fobj.target = ori_target;
	fobj.gd_preview.value = '';
}

// 디자인관리 인트로 미리보기 콜백함수
function preview_popup() {
	var fobj = document.fm;
	var skin_file = fobj.skin_file.value.substring(1);
	DCPV.preview_popup("../../" + skin_file.replace(/\.htm/gi, ".php") + "?tplSkin=" + fobj.tplSkinWork.value, skin_file);
}

intro_sample_view();
table_design_load();
setHeight_ifrmCodi();
</script>
