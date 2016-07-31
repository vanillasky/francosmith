<?
include "../_header.popup.php";
require_once ('./_inc/config.inc.php');
include "../../conf/config.pay.php";

$goodsno = isset($_GET['goodsno']) ? $_GET['goodsno'] : '';
$goodsno11st = isset($_GET['goodsno11st']) ? $_GET['goodsno11st'] : '';

if ($goodsno11st != '') {
	list($goodsno) = $db->fetch("select goodsno from ".GD_SHOPLE_GOODS_MAP." where 11st='{$goodsno11st}'");
}

$shople = Core::loader('shople');
$data = $shople->getGoods($goodsno);

### 필수옵션
$optnm = explode("|",$data['optnm']);
$query = "select * from ".GD_SHOPLE_GOODS_OPTION." where goodsno='$goodsno' ORDER BY `sort`";
$res = $db->query($query);
while ($tmp=$db->fetch($res)){
	$tmp = array_map("htmlspecialchars",$tmp);
	$opt1[] = $tmp['opt1'];
	$opt2[] = $tmp['opt2'];
	$opt[$tmp['opt1']][$tmp['opt2']] = $tmp;

	### 총재고량 계산
	$stock += $tmp['stock'];
}
if ($opt1) $opt1 = array_unique($opt1);
if ($opt2) $opt2 = array_unique($opt2);
if (!$opt){
	$opt1 = array('');
	$opt2 = array('');
}

$checked['tax'][$data['tax']] = "checked";
$checked['usestock'][$data['usestock']] = "checked";
$checked['runout'][$data['runout']] = "checked";
$checked['age_flag'][$data['age_flag']] = "checked";

$imgs = $urls = explode("|",$data['img_m']);

$checked[image_attach_method][file] = $checked[image_attach_method][url] = 'checked';

if (preg_match('/^http(s)?:\/\//',$imgs[0])) {
	$checked[image_attach_method][file] = '';
	$imgs	= array();
}
else {
	$urls	= array();
	$checked[image_attach_method][url] = '';
}

?>
<script type="text/javascript" src="./_inc/common.js"></script>
<script type="text/javascript">
	/* 옵션 부분 삭제 */
	function delopt1part(rid)
	{
		var obj = document.getElementById(rid);
		var tbOption = document.getElementById('tbOption');
		if (tbOption.rows.length>2) tbOption.deleteRow(obj.rowIndex);
	}
	function delopt2part(cid)
	{
		var obj = document.getElementById(cid);
		var tbOption = document.getElementById('tbOption');

		if (tbOption.rows[0].cells.length<3) return;
		for (i=0;i<tbOption.rows.length;i++){
			tbOption.rows[i].deleteCell(obj.cellIndex);
		}
	}

	/*** 폼체크 ***/
	function chkForm2(obj)
	{
		if (!chkOption()) return false;
		if (!chkForm(obj)) return false;
	}

	/*** 상품 가격/재고 ***/
	function addopt1()
	{
		var name;
		var fm = document.forms[0];
		var tbOption = document.getElementById('tbOption');
		var Rcnt = tbOption.rows.length;
		oTr = tbOption.insertRow();
		oTr.id = "trid_" + Rcnt;

		for (i=0;i<tbOption.rows[0].cells.length;i++){
			oTd = oTr.insertCell();
			switch (i){
				case 0: oTd.innerHTML = "<input type=text class='opt gray' name=opt1[] value='옵션명1' required label='1차옵션명' ondblclick=\"delopt1part('"+oTr.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\" style='width:110px;'>";
				break;
				case 1:	oTd.innerHTML = "<input type=text name=option[price][] class='opt gray' value='' style='width:65px;'>"; break;
				case 2:	oTd.innerHTML = "<input type=text name=option[consumer][] class='opt gray' value='' style='width:65px;'>"; break;
				default: oTd.innerHTML = "<input type=text name=option[stock][] class='opt gray' value='재고' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>"; break;
			}
		}
	}

	function addopt2()
	{
		var name;
		var tbOption = document.getElementById('tbOption');
		if (tbOption.rows.length<2){
			alert('1차옵션을 먼저 추가해주세요');
			return;
		}

		var Ccnt = tbOption.rows[0].cells.length;

		for (i=0;i<tbOption.rows.length;i++){
			oTd = tbOption.rows[i].insertCell();
			if(!i)oTd.id = "tdid_"+Ccnt;
			oTd.innerHTML = (i) ? "<input type='text' name=option[stock][] class='opt gray' value='재고' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">" : "<input type='text' class='opt gray' name=opt2[] value='옵션명2' required label='2차옵션명' ondblclick=\"delopt2part('"+oTd.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
		}
	}

	function delopt1()
	{
		var tbOption = document.getElementById('tbOption');
		if (tbOption.rows.length>2) tbOption.deleteRow();
	}
	function delopt2()
	{
		var tbOption = document.getElementById('tbOption');
		if (tbOption.rows[0].cells.length<7) return;
		for (i=0;i<tbOption.rows.length;i++){
			tbOption.rows[i].deleteCell();
		}
	}

	/*** 자동으로 가격필드에 입력값 저장 ***/
	function autoPrice(obj)
	{
		var name = obj.name;
		var el = document.getElementsByName('option[' + name + '][]');
		el[0].value = obj.value;
	}

	function vOption()
	{
		document.fm.stock.disabled = !document.fm.stock.disabled;
		openLayer('objOption');
	}

	function chkOptName(obj){
		if(obj.value=='옵션명2' || obj.value=='옵션명1'){
			obj.className = 'fldtitle';
			obj.value = '';
		}
		if(obj.value=='재고'){
			obj.className = 'opt';
			obj.value = '';
		}
	}

	function chkOptNameOver(obj){
		if(obj.value==''){
			obj.className = 'opt gray';
			if(obj.name == 'opt1[]') obj.value = '옵션명1';
			if(obj.name == 'opt2[]') obj.value = '옵션명2';
			if(obj.name == 'option[stock][]') obj.value = '재고';
		}
	}

	function chkOption(){
		var obj = document.getElementsByName('opt1[]');
		var chk = false;
		for(var i=0;i < obj.length;i++){
			 chkOptName(obj[i]);
			 if(obj[i].value == '' && obj.length > 1){
				alert('옵션 1은 필수 항목입니다.');
				obj[i].focus();
				return false;
			 }
			 if( (obj[i].value || obj.length > 1) && !chk) chk = true;
		}

		var obj = document.getElementsByName('opt2[]');
		for(var i=0;i < obj.length;i++){
			chkOptName(obj[i]);
			if(chk && obj[i].value == '' && obj.length > 1){
				alert('옵션 2은 필수 항목입니다.');
				obj[i].focus();
				return false;
			}
		}

		var obj = document.getElementsByName('option[stock][]');
		for(var i=0;i < obj.length;i++){
			chkOptName(obj[i]);
		}

		return true;
	}

	function setShipDisabled()
	{
		obj = document.getElementsByName('delivery_type');
		for (i = 0; i < obj.length; i++){
			isDisabled = (obj[i].checked == true ? false : true);
			inputObj = obj[i].parentNode.parentNode.getElementsByTagName('td')[1].getElementsByTagName('input');

			for (j = 0; j < inputObj.length; j++){
				inputObj[j].disabled = isDisabled;
				inputObj[j].style.backgroundColor = (isDisabled ? '#DDDDDD' : '#FFFFFF');
			}
		}
	}

	function fnSetImageAttachForm() {

		var m, obj = document.fm.image_attach_method;

		for (var i=0; i <obj.length; i++) {
			if (obj[i].checked)
			 var m = obj[i].value;
		}

		if (m == 'file') {
			document.getElementById('image_attach_method_upload_wrap').style.display = 'block';
			document.getElementById('image_attach_method_link_wrap').style.display = 'none';
		}
		else {
			document.getElementById('image_attach_method_upload_wrap').style.display = 'none';
			document.getElementById('image_attach_method_link_wrap').style.display = 'block';
		}

	}
</script>

<div class="title title_top" style="margin-top:10px;">쇼플 제휴판매 상품 개별등록 <span>11번가로 전송할 데이타형식 확인 후 개별적으로 등록합니다. &nbsp;&nbsp; <font color="#FF1800"><b>*</b></font> 표시된 항목은 필수입력사항입니다.</span></div>

<form name="fm" method="post" action="./indb.goods.php" enctype="multipart/form-data" onsubmit="return chkForm2(this)" target="ifrmHidden">
<input type="hidden" name="goodsno" value="<?=$goodsno?>">

<!-- 카테고리 선택 -->
<input type="hidden" name="category" value="<?=$data['full_dispno']?>" id="catnm" required label="11번가 카테고리">
<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> ① 11번가 카테고리 매칭</b></font> <font class="small1" color="#6d6d6d">(내 쇼핑몰 상품을 11번가 카테고리와 매칭하여 등록하세요.)</font></div>
<div class="box" style="padding-left:0px">
<table width="100%" cellpadding=1 cellspacing=0 border=1 bordercolor="#cccccc" style="border-collapse:collapse">
<tr>
	<td style="padding:20px 10px" bgcolor=f8f8f8 id="catnm_text">
	<a href="javascript:popupLayer('../shople/popup.config.category.php?full_dispno=<?=$data[full_dispno]?>&idnm=catnm',750,550);">
	<?=($data[full_name] ? $data[full_name] : '이곳을 클릭해서 카테고리를 입력해주세요.' )?>
	</a>
	</td>
</tr>
</table>
</div>

<!-- 상품기본정보 -->
<div style="height:30px"></div>
<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> ② 상품기본정보</b></font> <font class="small1" color="#6d6d6d">(상품명, 모델명, 브랜드명을 확인하시고, 11번가에 등록하기 위해 수정이 필요한 부분은 수정하세요.)</font></div>
<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<tr>
	<td width="120" nowrap>상품명<font color="#FF1800"><b>*</b></font></td>
	<td width="50%"><input type="text" name="goodsnm" style="width:100%" value="<?=$data['goodsnm']?>" required label="상품명"></td>
	<td width="120" nowrap>모델명(상품코드)<font color="#FF1800"><b>*</b></font></td>
	<td width="50%"><input type="text" name="goodscd" style="width:100%" value="<?=$data['goodscd']?>" required label="모델명"></td>
</tr>

<tr>
	<td>이용등급</td>
	<td class="noline">
	<input type="radio" name="age_flag" value="N" <?=$checked['age_flag']['N']?>> 미성년자 구매가능
	<input type="radio" name="age_flag" value="Y" <?=$checked['age_flag']['Y']?>> 미성년자 구입불가
	</td>
</tr>
</table>

<!-- 가격/재고 -->
<div style="height:30px"></div>
<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> ③ 가격/재고</b></font> <font class="small1" color="#6d6d6d">(가격, 재고를 확인하시고, 11번가에 등록하기 위해 수정이 필요한 부분은 수정하세요.)</font></div>
<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<tr>
	<td width="120" nowrap>판매가<font color="#FF1800"><b>*</b></font></td>
	<td width="50%"><input type="text" name="price" size="10" value="<?=$data['price']?>" required label="판매가">원</td>
	<td width="120" nowrap>재고량<font color="#FF1800"><b>*</b></font></td>
	<td width="50%"><input type="text" name="stock" size="10" value="<?=$stock?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)">개</td>
</tr>
<tr>
	<td>정가<font color="#FF1800"><b>*</b></font></td>
	<td><input type="text" name="consumer" size="10" value="<?=$data['consumer']?>" required label="정가">원</td>
	<td>최대구매 허용수량</td>
	<td><input type="text" name="max_count" size="10" value="<?=$data['max_count']?>">개 <font class="small1" color="#6d6d6d">무제한 설정시 0을 입력하세요.</font></td>
</tr>
<tr>
	<td>재고량연동</td>
	<td class=noline><input type=checkbox name=usestock <?=$checked[usestock][o]?>> 주문시 재고량빠짐 <font class=small color=444444>(체크안하면 재고량 상관없이 무한정판매)</font></td>
	<td>품절상품</td>
	<td class=noline><input type=checkbox name=runout value=1 <?=$checked[runout][1]?>> 품절된 상품입니다</td>
</tr>
<tr>
	<td>과세/비과세<font color="#FF1800"><b>*</b></font></td>
	<td class="noline">
	<input type="radio" name="tax" value="1" <?=$checked['tax'][1]?> required label="과세/비과세"> 과세
	<input type="radio" name="tax" value="0" <?=$checked['tax'][0]?> required label="과세/비과세"> 비과세
	</td>
	<td></td>
	<td></td>
</tr>
</table>

<div style="margin:10px 0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:vOption()" onfocus="blur()"><img src="../img/btn_priceopt_add.gif" align="absmiddle"></a> <font class="small" color="#444444">이상품의 옵션이 여러개인경우 등록하세요</font></div>

<div id="objOption" style="display:none; margin-left:20px;">
<div style="padding-bottom:10px">
<font class="small" color="black"><b>옵션명1</b> : <input type="text" name="optnm[]" value="<?=$optnm[0]?>">
<a href="javascript:addopt1()" onfocus="blur()"><img src="../img/i_add.gif" align="absmiddle"></a> <a href="javascript:delopt1()" onfocus="blur()"><img src="../img/i_del.gif" align="absmiddle"></a><span style="width:20px"></span>
<b>옵션명2</b></font> : <input type="text" name="optnm[]" value="<?=$optnm[1]?>">
<a href="javascript:addopt2()" onfocus="blur()"><img src="../img/i_add.gif" align="absmiddle"></a> <a href="javascript:delopt2()" onfocus="blur()"><img src="../img/i_del.gif" align="absmiddle"></a><span style="width:20px"></span>
</div>
<?if(count($opt)>1 || $opt1[0] != null || $opt2[0] != null){?><script type="text/javascript">vOption();</script><?}?>
<div style="margin:10px 0"><font class="small" color="#444444">등록한 옵션명1과 옵션명2를 더블클릭하시여 옵션을 삭제하실 수 있습니다.<br>
옵션명1은 최대 9개 이내, 옵션명2는 최대 30개까지만 입력하실 수 있습니다. 범위를 초과한 데이터는 반영되지 않을 수 있습니다.</font></div>

	<table id="tbOption" border="1" bordercolor="#cccccc" style="border-collapse:collapse">
	<tr align="center">
		<td>&nbsp;</td>
		<td><span style="color:#333333;font-weight:bold;">판매가</span></td>
		<td><span style="color:#333333;font-weight:bold;">정가</span></td>
		<?
			$j=4;
			foreach ($opt2 as $v){
			$j++;
		?>
		<td id='tdid_<?=$j?>'><input type="text" name="opt2[]" <?if($v != ''){?>class=fldtitle value="<?=$v?>"<?}else{?>class="opt gray" value='옵션명2'<?}?> <?if($j>5){?> ondblclick="delopt2part('tdid_<?=$j?>')"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"></td>
		<? } ?>
	</tr>
		<?
		$i=0;
		$op2=$opt2[0]; foreach ($opt1 as $op1){
		$i++;
		?>
	<tr id="trid_<?=$i?>">
		<td nowrap><input type="text" name="opt1[]" <?if($op1 != ''){?>class=fldtitle value="<?=$op1?>"<?}else{?>class="opt gray" value='옵션명1'<?}?> <?if($i != 1){?>ondblclick="delopt1part('trid_<?=$i?>')"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)" style="width:110px;"></td>
		<td><input type="text" name="option[price][]" class="opt gray" value="<?=$opt[$op1][$op2][price]?>" style="width:65px;"></td>
		<td><input type="text" name="option[consumer][]" class="opt gray" value="<?=$opt[$op1][$op2][consumer]?>" style="width:65px;"></td>
		<? foreach ($opt2 as $op2){ ?>
		<td><input type="text" name="option[stock][]" <?if($opt[$op1][$op2][stock]){?>class="opt" value="<?=$opt[$op1][$op2][stock]?>"<?}else{?>class="opt gray" value="재고"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"><input type="hidden" name="option[optno][]" value="<?=$opt[$op1][$op2][optno]?>"></td>
		<? } ?>
	</tr>
	<? } ?>
	</table>


</div>

<!-- 상품 이미지 -->
	<div style="height:30px"></div>
	<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> ④ 상품 이미지</b></font> <font class="small1" color="#6d6d6d">(11번가에 노출할 이미지를 확인하시고, 11번가에 등록하기 위해 수정이 필요한 부분은 수정하세요.)</font></div>

	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>이미지등록방식</td>
		<td class="noline">
		<label><input type="radio" name="image_attach_method" value="file" onClick="fnSetImageAttachForm();" <?=$checked[image_attach_method]['file']?>>직접 업로드</label>
		<label><input type="radio" name="image_attach_method" value="url" onClick="fnSetImageAttachForm();" <?=$checked[image_attach_method]['url']?>>이미지호스팅 URL 입력</label>

		</td>
	</tr>
	</table>

	<div id="image_attach_method_upload_wrap">
		<!-- 직접 업로드 -->
		<table class="tb">
		<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
		<? $t = array_map("toThumb",$imgs); ?>
		<tr>
			<td>상세이미지</td>
			<td>
			<table>
			<col valign="top" span="2">
			<? for ($i=0;$i<4;$i++){ ?>
			<tr>
				<td>
				<span><input type="file" name="imgs[]" style="width:400px"></span>
				</td>
				<td>
				<?=goodsimg($t[$i],20,"style='border:1px solid #cccccc' onclick=popupImg('../data/goods/$imgs[$i]','../') class=hand",2)?>
				</td>
				<td>
				<? if ($imgs[$i]){ ?>
				<div style="padding:0" class="noline"><input type="checkbox" name="del[imgs][<?=$i?>]"><font class="small" color="#585858">삭제 (<?=$imgs[$i]?>)</font></div>
				<? } ?>
				</td>
			</tr>
			<? } ?>
			</table>

			</td>
		</tr>
		</table>
		<!--//직접 업로드 -->
	</div>

	<div id="image_attach_method_link_wrap">
		<!-- URL 입력 -->
		<table class="tb">
		<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">

		<tr>
			<td>상세이미지</td>
			<td>

			<table>
			<col valign="top">
			<? for ($i=0;$i<4;$i++){ ?>
			<tr>
				<td>
				<span><input type="text" name="urls[]" style="width:400px" value="<?=$urls[$i]?>"></span>
				</td>
				<td>
				<?=goodsimg($urls[$i],20,"style='border:1px solid #cccccc' onclick=popupImg('$urls[$i]','../') class=hand",2)?>
				</td>
			</tr>
			<? } ?>
			</table>

			</td>
		</tr>
		</table>
		<!--//URL 입력 -->
	</div>
	<script type="text/javascript">
	fnSetImageAttachForm();
	</script>

<!-- 상품 설명 -->
	<div style="height:30px"></div>
	<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> ⑤ 상품 설명</b></font> <font class="small1" color="#6d6d6d">(홍보문구 및 상품설명을 확인하시고, 11번가에 등록하기 위해 수정이 필요한 부분은 수정하세요.)</font></div>

	<table border="1" bordercolor="#cccccc" style="border-collapse:collapse">
	<tr><td>
	<table cellpadding="0" cellspacing="0" bgcolor="#f8f8f8">
	<tr><td style="padding:10px 10px 5px 10px"><font class="small1" color="#444444"><font color="#E6008D">이미지 외부링크</font> 및 <font color="#E6008D">11번가</font> 판매를 위한 이미지를 등록하시려면 <font color="#E6008D">반드시 이미지호스팅 서비스</font>를 이용하셔야 합니다.</a></td></tr>
	<tr><td style="padding:0 10px 7px 10px"><font class="small1" color="#444444">이미지호스팅을 신청하셨다면 <a href="javascript:popup('http://image.godo.co.kr/login/imghost_login.php',980,700)" name="navi"><img src="../img/btn_imghost_admin.gif" align="absmiddle"></a>, 아직 신청안하셨다면 <a href="http://hosting.godo.co.kr/imghosting/service_info.php" target="_blank"><img src="../img/btn_imghost_infoview.gif" align="absmiddle"></a> 를 참조하세요!</td></tr>
	</table>
	</td></tr></table>

	<div style="padding-top:5px"></div>

	<table class="tb">
	<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
	<tr>
		<td>홍보문구</td>
		<td>
		<input name="shortdesc" style="width:400px;" class="line" maxlength="25" value="<?=htmlspecialchars($data['shortdesc'])?>" onkeydown="chkLen(this, 25, 'sLength')" onkeyup="chkLen(this, 25, 'sLength')">
		(<span id="sLength">0</span>/25)
		<div class="small1" style="color:#6d6d6d; padding-top:5px;">(홍보를 위한 추가문구가 * 표시와 함께 물품명 하단에 노출되며,검색어로는 적용되지 않습니다. 한/영문 25자 이내로 입력하셔야 합니다.)</div>
		<script type="text/javascript">_ID('sLength').innerHTML = document.getElementsByName('shortdesc')[0].value.length;</script>
		</td>
	</tr>
	</table>
	<div style="height:6px;font-size:0"></div>

	<textarea name="longdesc" style="width:100%;height:400px" type="editor"><?=$data['longdesc']?></textarea>

<!-- 배송ㆍA/S -->
	<div style="height:30px"></div>
	<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> ⑥ 배송ㆍA/S</b></font> <font class="small1" color="#6d6d6d">(배송 및 A/S를 상품별로 설정할 수 있습니다.)</font></div>

	<script type="text/javascript">
	function chk_delivery_type(){
		var obj = document.getElementsByName('delivery_type');
		if(obj[2].checked == true) document.getElementById('gdi').style.display="inline";
		else document.getElementById('gdi').style.display="none";

		if(obj[3].checked == true) document.getElementById('gdi2').style.display="inline";
		else document.getElementById('gdi2').style.display="none";
	}
	</script>

	<table class="tb" id="shipAS">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>배송비</td>
		<td>
		<table cellspacing="0" cellpadding="0" border="0">
		<tr height=40>
			<td>
			<input type="hidden" name="delivery_price" value="2">
			<input type="text" name="delivery_price" value="<?=(!empty($data['delivery_price'])) ? $data['delivery_price'] : $set['delivery']['default'] ?>" size="8" onkeydown="onlynumber()">원
		</tr>
		</table>
		<div><font class=extext>기본배송정책과 상품별 배송비 정책은 <a href="../basic/delivery.php" target=_blank><font class=extext_l>[기본관리 > 배송/택배사 설정]</font></a> 에서 관리 하실 수 있습니다.</font></div>
		</td>
	</tr>
	<!--tr>
		<td>배송비</td>
		<td>
		<table cellspacing="0" cellpadding="0" border="0">
		<tr height=40>
			<td>

			<input type="radio" name="delivery_type" value="0" class="null" onclick="chk_delivery_type();">기본배송정책에 따름
			<input type="radio" name="delivery_type" value="1" checked class="null" onclick="chk_delivery_type();"> 무료배송
			<input type="radio" name="delivery_type" value="2" class="null" onclick="chk_delivery_type();">상품별 배송비 입력 <span style="display:none;" id="gdi">&nbsp;<input type="text" name="delivery_price" value="0" size="8" onkeydown="onlynumber()">원</span>
			<input type="radio" name="delivery_type" value="3" class="null" onclick="chk_delivery_type();">착불배송비 <span style="display:none;" id="gdi2">&nbsp;<input type="text" name="delivery_price2" value="0" size="8" onkeydown="onlynumber()">원</span></td>

		</tr>
		</table>
		<div><font class=extext>기본배송정책과 상품별 배송비 정책은 <a href="../basic/delivery.php" target=_blank><font class=extext_l>[기본관리 > 배송/택배사 설정]</font></a> 에서 관리 하실 수 있습니다.</font></div>
		</td>
	</tr-->
	<tr>
		<td>A/S 정보<br>(안내문구)</td>
		<td>
		<input name="as_info" style="width:500px;" class="line" maxlength="40" value="<?=htmlspecialchars($data['as_info'])?>" onkeydown="chkLen(this, 40, 'vLength')" onkeyup="chkLen(this, 40, 'vLength')">
		(<span id="vLength">0</span>/40)
		<div class="small1" style="color:#6d6d6d; padding-top:5px;">(A/S 연락처,기간 등을 입력하세요. 한/영문 40자 이내로 입력하셔야 합니다.)</div>
		<script type="text/javascript">_ID('vLength').innerHTML = document.getElementsByName('as_info')[0].value.length;</script>
		</td>
	</tr>
	</table>


	<div class="button">
	<input type="image" src="../img/btn_11st.gif" alt="11번가 상품전송">
	</div>
</form>

<!-- 웹에디터 활성화 스크립트 -->
<script type="text/javascript" src="../../lib/meditor/mini_editor.js"></script>
<script type="text/javascript">mini_editor("../../lib/meditor/");</script>
<SCRIPT type="text/javascript" SRC="../proc/warning_disk_js.php"><!-- not_delete --></SCRIPT>
<script type="text/javascript">table_design_load();</script>

<div style="padding-top:15px"></div>
</body>
</html>
