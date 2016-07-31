<?

$location = "문의관리 > FAQ관리";
include "../_header.php";

$returnUrl = ($_GET[returnUrl]) ? $_GET[returnUrl] : $_SERVER[HTTP_REFERER];

$parseUrl = parse_url( $returnUrl );
$listUrl = ( $returnUrl ? $parseUrl[query] : $_SERVER['QUERY_STRING'] );
$listUrl = 'faq.php?' . preg_replace( "'(mode|sno)=[^&]*(&|)'is", '', $listUrl );

if (!$_GET[mode]) $_GET[mode] = "register";

if ($_GET[mode]=="modify"){
	$data = $db->fetch("select * from ".GD_FAQ." where sno='" . $_GET['sno'] . "'",1);
	$data['question'] = htmlspecialchars( $data['question'] );
	$data['descant'] = htmlspecialchars( $data['descant'] );
	$data['answer'] = htmlspecialchars( $data['answer'] );
}
?>

<div id=goods_form>

<form method=post action="faq_indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">
<input type=hidden name=returnUrl value="<?=$returnUrl?>">

<div class="title title_top">FAQ 질문<span></span></div>
<table class=tb>
<col class=cellC><col class=cellL>

<tr>
	<td nowrap>분류 설정</td>
	<td nowrap>
	<SELECT NAME="itemcd" required label="분류">
	<option value="">↓ FAQ분류을 선택하세요.</option>
	<?foreach ( codeitem('faq') as $k => $v ){?>
	<option value='<?=$k?>' <?=( $k == $data['itemcd'] ? 'selected' : '' )?>><?=$v?></option>
	<?}?>
	</SELECT>
	</td>
</tr>
<TR>
	<td nowrap>질문 (단문)</td>
	<td nowrap><input type="text" name="question" size="55" value="<?=$data['question']?>" required label="질문" class=line></td>
</tr>
<TR>
	<td nowrap>질문 (장문)</td>
	<td nowrap>

	<table width="90%" border="0" cellpadding="0" cellspacing="0" style="margin:5 0 10 0;">
	<tr>
		<td width="200" height="100%" valign="top" style="border:3px #f6f6f6 solid" bgcolor="#ffffff">
		<div style="border:1px #e6e6e6 solid; width:100%; height:100%;">
			<div align="center" style="padding:5;"><font color="298CC7"><b>자주 쓰는 명칭</b></font></div>
			<div align="center" style="padding-bottom:5;">
			<SELECT NAME="autorr" size=6 onchange="javascript:document.getElementsByName('getppwordz')[0].value=this.options[this.options.selectedIndex].value;">
			<optGroup label="------ 선택 ------">
			<option value="__shopname__"> 쇼핑몰이름 </option>
			<option value="__shopdomain__"> 쇼핑몰주소 </option>
			<option value="__shopcpaddr__"> 사업장주소 </option>
			<option value="__shopcoprnum__"> 사업자등록번호 </option>
			<option value="__shopcpmallceo__"> 쇼핑몰 대표 </option>
			<option value="__shopcpmanager__"> 개인정보관리자 </option>
			<option value="__shoptel__"> 쇼핑몰 전화 </option>
			<option value="__shopfax__"> 쇼핑몰 팩스 </option>
			<option value="__shopmail__"> 쇼핑몰 이메일 </option>
			</SELECT>
			</div>
		</div>
		</td>
		<td height="100%" valign="top" style="border:3px #74BBF5 solid" bgcolor="#ffffff">
		<div style="border:1px #298CC7 solid; width:100%; height:100%;">
			<div align="center" style="padding:5;"><b><font color="298CC7">코드명</font></b></div>
			<div align="center" style="padding-bottom:5;"><textarea NAME="getppwordz" readonly style="width:90%;height:100" class=tline></textarea></div>
		</div>
		</td>
	</tr>
	</table>

	<div><font class=extext>* 자주 쓰는 명칭을 클릭하면 오른쪽에 코드명이 보입니다. 코드명을 사용해서 질문과 답변에 활용하세요.</div>
    <div style="padding-top:2px">* 해당 코드명들은 '쇼핑몰 기본관리' 에서 이미 입력되어 있는 정보들을 불러옵니다.</div> 
	<div style="padding-top:2px">* 문장에서 코드명을 활용하면 나중에 일일이 수정해야하는 번거로움을 덜게 됩니다.</font></div>

	<TEXTAREA NAME="descant" ROWS="10" COLS="100" style="width:90%;" class=tline><?=$data['descant']?></TEXTAREA>
	</td>
</tr>
<tr>
	<td nowrap>베스트 등록</td>
	<td nowrap><span class=noline>
	<input name="best" type="radio" value="Y" <?if ( $data['best'] == "Y" )echo"checked";?>> 추가
	<input name="best" type="radio" value="N" <?if ( $data['best'] != "Y" )echo"checked";?>> 추가안함&nbsp;&nbsp;&nbsp;&nbsp;</span>
	순서 : <input name="bestsort" type="text" size="5" value="<?=$data['bestsort']?>" style="width:30;text-align:center" class=line>
	</td>
</tr>
</table>

<div class="title">FAQ 답변<span></span></div>
<table class=tb>
<col class=cellC><col class=cellL>
<TR>
	<td nowrap>답변</td>
	<td nowrap><TEXTAREA NAME="answer" ROWS="18" COLS="100" style="width:90%;" required label="답변" class=tline><?=$data['answer']?></TEXTAREA></td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_<?=$_GET[mode]?>.gif">
<a href="<?=$listUrl?>"><img src='../img/btn_list.gif'></a>
</div>

</form>
</div>

<? include "../_footer.php"; ?>