<?
$location = "플러스치즈 소셜쇼핑 > 플러스치즈 설정/상태";
include "../_header.php";
include "../../lib/plusCheese.class.php";
@include "../../conf/config.plusCheeseCfg.php";

if($plusCheeseCfg['use'] == "Y") $chkUse['Y'] = "checked";
else  $chkUse['N'] = "checked";
if($plusCheeseCfg['test'] == "Y") $chkTest['Y'] = "checked";
else  $chkTest['N'] = "checked";
$_url = "http://pluscheese.godo.co.kr/listen.shop.php";

$plusCheese = new plusCheese($godo['sno']);
$plusCheeseKey = $plusCheese->getRelayKey();

//중계키를 받았는지 확인하기
if(empty($plusCheeseKey)){
	//중계키가 없다면 요청한다.
	include "indb.api.php";
	
	$result = substr($plusCheeseResult, 0, 4);
	if($result == "DONE"){
		$key = substr($plusCheeseResult, 4);
	}

	?><script type="text/javascript">
		var _url_config = "indb.php";
		if (window.XMLHttpRequest)
			xmlHttp = new XMLHttpRequest();
		else if (window.ActiveXObject)
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			
		if("<?=$result?>" == "DONE"){
			//이제 막 생성된 경우 설정 파일을 생성하기
			xmlHttp.open("POST", _url_config+"?mode=sno&key=<?=$key?>", true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4){
					if(xmlHttp.status==200){
						location.reload();
					}
				}
			}
			xmlHttp.send();
			
		}else if("<?=$plusCheeseResult?>" == "already created"){
			//이미 생성된 경우
		}else if("<?=$plusCheeseResult?>" == "shopsno not exist"){
			//샵이 없는 경우
			alert("신청 정보 조회중 오류가 발생되었습니다.\n고도에 문의 하여 주시기 바랍니다.\n\n오류코드:1");
		}
	</script>
	<?
}else{
	?>
<script type="text/javascript">
	function chkSetting(){
		var f = document.frmSetting;
		if(!f.use[0].checked && !f.use[1].checked){
			alert("사용여부를 설정해 주시기 바랍니다.");
		}else{
			f.submit();
		}
	}

	function list_goods(name){
		var category = '';
		open_box(name,true);
		var els = document.forms[0][name+'[]'];
		for (i=0;i<els.length;i++) if (els[i].value) category = els[i].value;
		var ifrm = eval("ifrm_" + name);
		var goodsnm = eval("document.forms[0].search_" + name + ".value");
		ifrm.location.href = "../goods/_goodslist.php?name=" + name + "&category=" + category + "&goodsnm=" + goodsnm;
	}
	function open_box(name,isopen){
		var mode;
		var isopen = (isopen || document.getElementById('obj_'+name).style.display!="block") ? true : false;
		mode = (isopen) ? "block" : "none";
		document.getElementById('obj_'+name).style.display = document.getElementById('obj2_'+name).style.display = mode;
	}
	function moveEvent(obj, name){
		obj.onclick = function(){ spoit(name,this); }
		obj.ondblclick = function(){ remove(name,this); }
	}
	function react_goods(name){
		var tmp = new Array();
		var obj = document.getElementById('tb_'+name);
		for (i=0;i<obj.rows.length;i++){
			tmp[tmp.length] = "<div style='float:left;width:0;border:1 solid #cccccc;margin:1px;' title='" + obj.rows[i].cells[1].getElementsByTagName('div')[0].innerText + "'>" + obj.rows[i].cells[0].innerHTML + "</div>";
		}
		document.getElementById(name+'X').innerHTML = tmp.join("") + "<div style='clear:both'>";
	}
	function view_goods(name){
		open_box(name,false);
	}
	function exec_add(){
		var ret;
		var str = new Array();
		var obj = document.forms[0]['cate[]'];
		for (i=0;i<obj.length;i++){
			if (obj[i].value){
				str[str.length] = obj[i][obj[i].selectedIndex].text;
				ret = obj[i].value;
			}
		}
		if (!ret){
			alert('카테고리를 선택해주세요');
			return;
		}
		for(var i=0;i<document.getElementsByName("category[]").length;i++){
			var tmpStr = document.getElementsByName("category[]")[i].value;
			if(tmpStr == ret.substr(0, tmpStr.length)){
				alert("이미 상위 분류가 추가 되어 있습니다!");
				return;
			}
		}
		var obj = document.getElementById('objCategory');
		oTr = obj.insertRow();
		oTd = oTr.insertCell();
		oTd.id = "currPosition";
		oTd.innerHTML = str.join(" > ");
		oTd = oTr.insertCell();
		oTd.innerHTML = "\<input type=text name=category[] value='" + ret + "' style='display:none'>";
		oTd = oTr.insertCell();
		oTd.innerHTML = "<a href='javascript:void(0)' onClick='cate_del(this.parentNode.parentNode)'><img src='../img/i_del.gif' align=absmiddle></a>";
	}
	function cate_del(el){
		idx = el.rowIndex;
		var obj = document.getElementById('objCategory');
		obj.deleteRow(idx);
	}
	function remove(name,obj)
	{
		var tb = document.getElementById('tb_'+name);
		tb.deleteRow(obj.rowIndex);
		react_goods(name);
	}
	function copy_txt(val){
		window.clipboardData.setData('Text', val);
	}
	function disableControls(){
		var fUse = document.getElementsByName("use")[1];
		var fTest = document.getElementsByName("test")[1];
		var setting = false;
		
		if(fUse.checked == true && fTest.checked == true){
			setting = true;
		}
		var f = document.getElementsByName("refer[]");
		f[0].disabled = setting;
		f[1].disabled = setting;
		f[2].disabled = setting;
		f[3].disabled = setting;

		document.getElementsByName("search_refer")[0].disabled = setting;

		var f = document.getElementsByName("cate[]");
		f[0].disabled = setting;
		f[1].disabled = setting;
		f[2].disabled = setting;
		f[3].disabled = setting;
	}
</script>
<?
}
$statusCond = $plusCheese->getStatusCond();
if(!empty($statusCond)){
?>
<div style="width:100%">
	<form method="post" name="frmSetting" action="indb.php"/>
		<input type="hidden" name="mode" value="set">
		<div class="title title_top">플러스치즈 소셜쇼핑 설정관리 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=30')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
		<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
			<col class="cellC"><col class="cellL">
			<tr height="30">
				<td class="noline">사용여부<br /></td>
				<td class="noline" style="padding-left:10px"><? if($plusCheese->getStatusCond()=="Y"){ ?><input type="radio" name="use" value="Y" id="useY" <?=$chkUse['Y']?> onclick="disableControls()" /><label for="useY">사용</label> <input type="radio" name="use" value="N" id="useN" <?=$chkUse['N']?> onclick="disableControls()" /><label for="useN">사용안함</label><? }else{ ?>승인되어야 설정할 수 있습니다.<? } ?></td>
			</tr>
			<tr height="30">
				<td class="noline">테스트하기<br /></td>
				<td class="noline" style="padding-left:10px"><? if($plusCheese->getStatusCond()=="Y"){ ?><input type="radio" name="test" value="Y" id="TestY" <?=$chkTest['Y']?> onclick="disableControls()" /><label for="TestY">사용</label> <input type="radio" name="test" value="N" id="TestN" <?=$chkTest['N']?> onclick="disableControls()" /><label for="TestN">사용안함</label><? }else{ ?>승인되어야 설정할 수 있습니다.<? } ?></td>
			</tr>
			<tr height="30">
				<td class="noline">샵신청상태<br /></td>
				<td class="noline" style="padding-left:10px"><?=$plusCheese->getStatusCondMsg($plusCheese->getStatusCond())?></td>
			</tr>
			<tr height="30">
				<td class="noline">플러스치즈 ID</td>
				<td class="noline" style="padding-left:10px"><?=$plusCheese->data['pc_entID']?></td>
			</tr>
			<tr height="30">
				<td class="noline">수수료율</td>
				<td class="noline" style="padding-left:10px"><?=$plusCheese->data['pc_commission'] / 1.1?>% (부가세 별도)</td>
			</tr>
			<tr height="30">
				<td class="noline">신청일</td>
				<td class="noline" style="padding-left:10px"><?=$plusCheese->data['registerDate']?></td>
			</tr>
			<tr height="30">
				<td class="noline">사용시작일</td>
				<td class="noline" style="padding-left:10px"><?=$plusCheese->data['approvalDate']?></td>
			</tr>
		</table>
		<br /><br />
		<div class="title title_top">플러스치즈 소셜쇼핑 예외상품설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=30')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
		<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
			<col class="cellC"><col class="cellL">
			<tr>
				<td>예외상품</td>
				<td>
					<div style=padding-left:8><font class=small1 color=FF0066><img src="../img/icon_list.gif" align="absmiddle">상품 선정 (상품검색 후 선정)</font></div>
					<div id=divRefer style="position:relative;z-index:99;padding-left:8">
					<div style="padding-bottom:3px"><script>new categoryBox('refer[]',4,'','');</script><input type=text name=search_refer onkeydown="return go_list_goods('refer')"><a href="javascript:list_goods('refer')"><img src="../img/i_search.gif" align=absmiddle></a><a href="javascript:view_goods('refer')"><img src="../img/i_openclose.gif" align=absmiddle></a></div>
					<div id=obj_refer class=box1><iframe id=ifrm_refer style="width:100%;height:100%" frameborder=0></iframe></div>
					<div id=obj2_refer class="box2 scroll" onselectstart="return false" onmousewheel="return iciScroll(this)">
						<div class=boxTitle>- 등록된 상품 <font class=small color=#F2F2F2>(삭제하려면 더블클릭)</font></div>
							<table id=tb_refer class=tb>
								<col width=50>
								<?
									$r_goods = $plusCheeseCfg['e_refer'];
									if ($r_goods){
										foreach ($r_goods as $k=>$v){
											$sql = "SELECT g.goodsno, g.img_s, g.goodsnm, go.price FROM ".GD_GOODS." g, ".GD_GOODS_OPTION." go WHERE g.goodsno=".$v." AND g.goodsno=go.goodsno AND go.link=1";
											$data = $db->fetch($sql);
								?>
								<tr onclick="spoit('refer',this)" ondblclick=remove('refer',this) class=hand>
									<td width=50 nowrap><a href="../../goods/goods_view.php?goodsno=<?=$v?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
									<td width=100%>
										<div><?=$data[goodsnm]?></div><b><?=number_format($data[price])?></b><input type=hidden name=e_refer[] value="<?=$data[goodsno]?>">
									</td>
								</tr>
								<?
										}
									}
								?>
							</table>
						</div>
						<div id=referX style="font:0"></div>
					</div><script>react_goods('refer');</script>
					<div></div>
				</td>
			</tr>
			<tr>
				<td>예외카테고리</td>
				<td>
					<script src="../../lib/js/categoryBox.js"></script>
					<div style="padding-top:3px"></div>
					<div style=padding-left:8><font class=small1 color=FF0066><img src="../img/icon_list.gif" align="absmiddle">카테고리 선정 (카테고리선택 후 오른쪽 선정버튼클릭)</font></div>
					<div style=padding-left:8><script>new categoryBox('cate[]',4,'','');</script><a href="javascript:exec_add()"><img src="../img/btn_coupon_cate.gif"></a></div>
					<div class="box" style="padding:10 0 0 10">
						<table  cellpadding=8 cellspacing=0 id=objCategory bgcolor=f3f3f3 border=0 bordercolor=#cccccc style="border-collapse:collapse">
						<?
							$r_category = $plusCheeseCfg['category'];
							if ($r_category){
								foreach ($r_category as $k=>$v){
						?>
							<tr>
								<td id=currPosition><?=strip_tags(currPosition($v))?></td>
								<td><input type=text name=category[] value="<?=$v?>" style="display:none"></td>
								<td><a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="../img/i_del.gif" border=0 align=absmiddle></a></td>
							</tr>
						<?
								}
							}
						?></table>
					</div>
				</td>
			</tr>
		</table>
		<br /><br />
		<div class="title title_top">플러스치즈 소셜쇼핑 버튼 삽입하기 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=30')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
		<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
			<col class="cellC"><col class="cellL">
			<tr>
				<td height="50px">치환코드</td>
				<td>{plusCheeseBtn} <a href="javascript:copy_txt('{plusCheeseBtn}')"><img src="../img/i_copy.gif"></a><br />복사하신 치환코드를 상품상세화면 페이지에 삽입하시면 플러스치즈 기능이 동작합니다.</td>
			</tr>
			<tr>
				<td height="25px">치환코드 삽입 방법</td>
				<td>"쇼핑몰 관리자 > 디자인관리" 좌측 트리 메뉴에서 "상품 > 상품상세화면"메뉴, [바로구매] 또는 [주문하기] 버튼 아래에 치환코드 삽입을 권장합니다.</td>
			</tr>
		</table>
		<div class="noline" style="margin-top:10px; text-align:center"><input type="image" src="../img/btn_save.gif" onclick="chkSetting()" /></div>
	</form>
</div>
<script type="text/javascript">disableControls();</script><?
	exit;
}else{
	if($_GET['ref'] == "lm") msg("신청 후 사용해 주시기 바랍니다.", "info.php");
?>
<script type="text/javascript">
	function chkAgree(){
		var f = document.frmAgree;
		if(!f.agree1[0].checked || !f.agree2[0].checked){
			alert("약관에 동의하여 주시기 바랍니다.");
			return false;
		}
		return true;
	}
</script>
<div style="width:800px">
	<form method="post" name="frmAgree" action="join.php" onsubmit="return chkAgree();" />
		<input type="hidden" name="key" value="<?=$plusCheeseKey?>">
		<div class="title title_top">정보 제공 동의 이용 약관<span>서비스를 원하시면 아래 이용약관을 반드시 읽고 동의해 주세요.</span></div>
		<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
			<col class="cellL">
			<tr height="50">
				<td class="noline">
					<div style="width:800px; height:140px; overflow:scroll;"><xmp style="word-break:break-all">개인정보 수집, 이용 및 개인정보 취급위탁
개인정보 수집자 : 고도소프트
수집하는 개인정보의 항목 : 쇼핑몰 URL, 쇼핑몰명, 담당자명, 담당자 핸드폰번호, 담당자 일반전화, 담당자 이메일주소
개인정보의 수집,이용 목적 : 플러스치즈 소셜 쇼핑 서비스 이용 중 고객과의 원활한 서비스 진행을 위해 
고객정보를 확인 하는 용도로만 이용됩니다.
제휴업체 : 플러스 치즈
제휴업체 문의 내용 : 플러스 치즈 소셜 쇼핑</xmp></textarea></div>
					<input type="radio" name="agree1" value="y" />동의합니다</label><label><input type="radio" name="agree1" value="n" />동의하지 않습니다</label>
				</td>
			</tr>
		</table>
		<p />
		<div class="title title_top">플러스치즈 서비스 이용 약관<span>서비스를 원하시면 아래 이용약관을 반드시 읽고 동의해 주세요.</span></div>
		<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
			<col class="cellL">
			<tr height="50">
				<td class="noline">
					<div style="width:100%; height:350px; overflow:scroll;"><xmp style="word-break:break-all">제1조(목적)
 이 약관은 (주)플러스치즈 (이하 "회사")가 제공하는 서비스의 이용과 관련하여 회사와 이용자와의 리,의무 및 책임사항, 기타 필요한
 사항을 규정함을 목적으로 합다. 
 
제2조(용어의 정의)  
 ① 이 약관에서 사용하는 용어의 의미는 다음과 같습니다.  
  1. 플러스치즈"라 함은, "회사"가 운영하는 소셜쇼핑플랫폼 서비스로서 1) 쇼핑 정보 중계자(이하 "쇼핑 정보 중계자") 와 구매
     서비스 이용자(이하 "구매자")가 "플러스치즈"를 이용하여 컨텐츠 제공자(이하 "원천 판매자")로부터 상품, 용역 또는 디지털
     컨텐츠 (이하 "상품"이라 함)을 구입 및 판매 이용 시 "플러스치즈"를 통해 "구매정보"를 "원천 판매자" 및 전자지급결제대행
     서비스업자 또는 결제대금예치서비스업자 (이하 통칭하여 "PG")와 송신하거나 수신하는 것과 2) "쇼핑 정보 중계자" 및 "구매자"
     자신이 "플러스치즈" 내에서 "중계내역" 과 "구매내역"을 관리할 수 있는 서비스를 의미합니다.
  2. 쇼핑 정보 중계자"라 함은 본 이용약관에 따라 "회사"와 이용계약을 체결하여 "회사"가 제공하는 "플러스치즈" 서비스를
     이용하여 쇼핑 정보 중계 이용자를 의미합니다. 
  3. 구매자"라 함은 본 이용약관에 따라 "회사"와 이용계약을 체결하여 "회사"가 제공하는 "플러스치즈" 구매 서비스 이용자를
     의미합니다. 
  4. 구매정보"라 함은 "서비스"의 "구매자"가 "회사"에 제공한 "구매자" 성명, 이메일, 연락처, 결제방법, 배송 메시지, 수취인 성명,
     수취인 주소, 수취인 연락처를 의미합니다.  
  5. 플러스치즈 계정"이라 함은 "상품"의 "중계내역" 과 "구매내역"을 관리할 수 있는 웹사이트상의 "쇼핑 정보 중계자" 와 "구매자"
     계정을 의미합니다.  
  6. 구매내역"이라 함은 "구매자"가 구입 및 이용한 "상품"에 대한 정보 (구매일시, 상품명, 원천 판매자명, 상품옵션정보, 상품 가격,
     상품 수량, "결제 방법", 결제일시, 배송비, 배송진행 정보, 취소, 반품, 교환의 신청 및 진행 내용)를 의미합니다.  
  7. 결제방법"이라 함은 "구매자"가 전자적 방법으로 "상품" 구입 또는 이용 시 선택한 지불방법을 의미합니다.  
  8. 수취인"이라 함은 "상품"을 실제로 수신 또는 이용하는 자를 의미합니다. "수취인"은 "구매자"와 동일인이거나 다른 사람일 수
     있습니다.  
  9. 구매확정기간"이라 함은 "구매자"가 "상품" 거래에 대하여 구매 종료(구매확정, 구매거절, 반품, 교환)의 의사표시를 하여야 하는
     기간으로 "회사"가 인지하고 있는 "수취인"의 물품수령일로부터 7일이 되는 날까지를 의미합니다. 
  10. 영업일"이라 함은 "회사"가 "플러스치즈 서비스"를 제공하는 날로서 토요일, 일요일 및 법정 공휴일을 제외한 날을 의미합니다.  
  11. 결제대금 보호서비스"란 "구매자"와 "원천 판매자"간에 "상품"에 대한 매매계약이 체결되고 "구매자"가 대금결제를 하였을 경우,
      "구매자"가 "원천 판매자"로부터 해당 "상품"을 받았을 때 미리 정한 소정의 "결제대금 보호서비스" 이용료를 공제한 후 결제대금
      보호서비스 사업자(이하 "에스크로사업자")가 해당 "원천 판매자"에게 "상품"에 대한 대금을 정산해 주지만, "구매자"가 "원천
      판매자"로부터 "상품"을 받지 못하였거나 받았더라도 "원천 판매자"에게 반품한 후 환불을 요청하였을 때에는 "구매자"가 "상품"의
      대가로서 결제한 금액을 "에스크로사업자"가 "구매자"에게 환불해주는 서비스를 의미합니다. 
  12. 배송지원업체"라 함은 "구매자"의 "구매내역"에 대한 "배송정보" 확인 및 "원천 판매자"의 "상품"에 대한 배송을 위한
      배송업체와의 전자적 커뮤니케이션 서비스를 제공하는 업체를 의미합니다.
  13. 배송정보"라 함은 "구매자"가 구매한 "상품"의 배송상태를 말합니다. 
이 약관에서 사용하는 용어 중 본 조에서 정하지 아니한 것은 "플러스치즈 서비스" 내 안내 및 관계법령에서 정하는 바에 따르며, 그
외에는 일반 관례에 따릅니다.  
   
제3조 (목적)  
 ① 회사"는 이 약관의 내용을 "쇼핑 정보 중계자" 및 "구매자"(이하 "이용자") 가 쉽게 알 수 있도록 "플러스치즈 서비스"의 화면 내
    또는 링크로 연결된 화면에 게시합니다.  
 ② 회사"는 약관의규제에관한법률, 정보통신망이용촉진및정보보호등에관한법률등 관련법령을 위배하지 않는 범위에서 이 약관을 개정할
    수 있습니다. 
 ③ 회사"가 이 약관을 개정할 경우에는 적용일자 및 개정사유를 명시하여 제 1항의 방식에 따라 그 개정약관을 적용일자 7일 전부터
    적용일자 전일까지 공지합니다.
    다만, "이용자"에게 불리한 약관의 개정의 경우에는 적용일자 30일 이전에 공지하며, 공지 외에 일정기간 "이용자"의 전자우편,
    전자쪽지,로그인 시 동의창 등의 전자적 수단을 통해 따로 명확히 통지합니다. 
 ④ 회사"가 전항에 따라 개정약관을 공지 또는 통지하면서 "이용자"에게 30일 기간 내에 의사표시를 하지 않으면 의사표시가 표명된
    것으로 본다는 뜻을 명확하게 공지 또는 통지하였음에도 "이용자"가 명시적으로 거부의사를 표명하지 아니한 경우 "이용자"가
    개정약관에 동의한 것으로 봅니다. 
 ⑤ 이용자"가 개정약관의 내용에 동의하지 않는 경우 "회사"는 해당 "이용자"에 대하여 개정약관의 내용을 적용할 수 없으며, 이 경우
    "이용자"는 이용계약을 해지할 수 있습니다. 다만, "회사"가 개정약관에 부동의한 "이용자"에게 기존 약관을 적용할 수 없는
    특별한 사정이 있는 경우에는 "회사"는 해당 ‘이용자"와의 이용계약을 해지할 수 있습니다. 
 
제4조(플러스치즈 서비스의 종류)  
 회사"가 "이용자"에게 제공하는 "플러스치즈 서비스"의 종류는 다음과 같습니다.  
 ① 구매관련 지원서비스 : "원천 판매자"와 "PG"에게 "구매자"가 "회사"에 제공한 "구매정보" 및 "구매내역"을 제공하여 "구매자"가
    "상품" 거래에 대한 대금결제를 완료할 수 있도록 지원하는 것과 "구매자"가 "플러스치즈 계정"을 통해 "구매내역"을 조회하고,
    "상품" 거래에 대한 취소 및 교환, 반품을 진행하기 위한 "원천 판매자"와 "구매자"간의 전자적 커뮤니케이션을 지원하는 것을
    의미합니다. 
 ② 쇼핑 정보 중계 지원서비스 : "원천 판매자"의 "상품"을 "쇼핑 정보 중계자"가 홍보 및 중계하여 제공되는 "중계 내역", 중계
    커뮤니티로의 바로 이동 서비스인 "미니샵", 판매 커뮤니티 영역의 설치형 서비스인 "치즈위젯" 등을 의미합니다. 
 ③ 기타 서비스 : "결제대금 보호서비스", 이용자문의 게시판 서비스 등 기타 정보 제공 서비스, "회사"가 직접 또는 제휴사와
    공동으로 제공하는 이벤트 서비스 등을 의미합니다.  
 
제5조(대리행위 및 보증인의 부인) 
 ① "회사"는 "원천 판매자"와 "구매자" 간의 편리한 "상품"의 거래를 위한 시스템을 운영 및 관리, 제공할 뿐이며, "원천 판매자" 또는
    "구매자"를 대리하지 않습니다. "원천 판매자"와 "구매자" 사이에 성립된 "상품" 거래에 관련된 책임과 "원천 판매자" 및 "구매자"가
    제공한 정보에 대한 책임은 해당 "원천 판매자" 및 "구매자"가 직접 부담하여야 합니다. 
 ② "회사"는 "플러스치즈 서비스"를 통하여 이루어지는 "원천 판매자"와 "구매자" 간의 "상품" 거래와 관련하여 판매의사 또는 구매의사
    의 존부 및 진정성, 상품의 품질, 완전성, 안전성, 적법성 및 타인의 권리에 대한 비침해성, "구매자" 또는 "원천 판매자"가 입력한
    정보 및 그 정보를 통하여 링크된 URL에 게재된 자료의 진실성 또는 적법성 등 일체에 대하여 보증하지 아니하며, 이와 관련한
    일체의 위험은 해당 "원천 판매자" 또는 "구매자"가 전적으로 부담합니다. 
 
 ③ "회사"는 "구매자"에게 "상품"을 판매하거나 "원천 판매자"로부터 "상품"을 구매하지 않으며, 단지 "원천 판매자"와 "구매자"간의
    "상품" 거래의 편의성을 증진시키는 도구만을 개발 및 제공합니다.
 
제6조(플러스치즈 서비스의 이용) 
 ① "이용자"는 누구든지 무상으로 "플러스치즈 서비스"를 이용할 수 있습니다.
 ② "플러스치즈 서비스"를 이용하고자 하는 "이용자"는 이 약관의 내용에 동의하고, "플러스치즈 서비스"와 관련하여 "회사"가 요구하는
    사항(이용자 성명, 이용자 연락처, 이용자 이메일 주소, 수취인명, 수취인 연락처 1,2, 수취인 주소 등)을 "회사"에 제공함으로써
    "플러스치즈 서비스"를 이용할 수 있습니다.
 ③ "이용자"는 "플러스치즈 서비스" 가입 시 "회사"가 요청하는 정보를 정확하게 작성하셔야 합니다. 
 
 
제7조(플러스치즈 서비스 이용계약 체결)  
 ① "회사"는 이 약관 제 6조에서 정한 기재항목을 사실대로 정확하게 기재하고 이 약관에 동의한 자에게만 "플러스치즈 서비스"의
    이용을 승낙하는것을 원칙으로 합니다. 
 ② "회사"는 다음 각호의 사유가 있는 경우, 이용신청에 대한 승낙을 거부할 수 있습니다. 
  1. 1.만 14세 미만의 자가 법정대리인의 명시적인 동의 없이 이용신청을 하는 경우 
  2. 기재 내용에 허위, 기재누락, 오기 등이 있는 경우 
  3. "회사"로부터 이용정지 당한 "이용자"가 그 이용정지 기간 중에 이용계약을 임의 해지하고 재이용신청을 하는 경우 
  4. 과거에 이 약관의 위반 등의 사유로 "플러스치즈 서비스" 이용계약이 해지 당한 경력이 있는 경우  
  5. 기타 이 약관에 위배되거나 위법 또는 부당한 이용신청임이 확인된 경우 
 ③ "회사"는 서비스 관련설비의 여유가 없거나, 기술상 또는 업무상 문제가 있는 경우에는 승낙을 유보할 수 있습니다. 
 ④ 본 조 제 2항과 제 3항에 따라 이용자가입 신청의 승낙을 하지 아니하거나 유보한 경우 "회사"는 원칙적으로 이를 이용신청자에게
    알립니다. 
 ⑤ 이용 계약의 성립 시기는 "회사"가 이용신청 완료를 신청절차 상에서 표시한 시점으로 합니다.  
 
제8조(이용계약의 해제, 해지 등)  
 ① "이용자"는 언제든지 "플러스치즈 서비스" 초기화면의 내정보보기 메뉴 등을 통하여 이용계약 해지 신청을 할 수 있으며, "회사"는
    관련법 등이 정하는 바에 따라 이를 즉시 처리하여야 합니다. 
 ② "이용자"가 이용계약을 해지할 경우, "회사"는 관련법 및 개인정보취급방침에 따라 "이용자" 정보를 보유하는 경우를 제외하고는
    해지 즉시 "이용자"의 모든 데이터를 삭제합니다.  
 ③ 이용계약 해지로 인해 발생한 불이익에 대한 책임은 "이용자" 본인이 져야 하며, 이용계약이 종료되면 "회사"는 "이용자"에게
    부가적으로 제공한 각종 혜택을 회수할 수 있습니다.  
 ④ "회사"는 다음 각호에서 정한 사유가 발생할 경우 즉시 이용계약을 해지할 수 있습니다. 
  1. "이용자"가 "플러스치즈 서비스"의 원활한 진행을 방해하는 행위를 하거나 시도한 경우 
  2. "이용자"가 고의로 "회사"의 영업을 방해한 경우 
  3. "이용자"가 이 약관에 위배되는 행위를 하거나 이 약관에서 정한 해지사유가 발생한 경우 
  4. "이용자"에게 7조에서 정한 이용계약의 승낙거부사유가 있음이 확인된 경우 
  5. "이용자"가 "회사"가 인정하지 아니 하는 방법으로 적립금 또는 쿠폰을 취득 또는 사용하는 경우 
  6. 기타 "회사"가 합리적인 판단에 의하여 "이용자"에 대한 서비스의 제공을 거부할 필요가 있다고 인정할 경우 
 ⑤ "회사"가 이용계약을 해지하는 경우 "회사"는 "이용자"에게 e-mail 등으로 해지 사유를 밝혀 해지의사를 통지합니다. 이용계약은
    "회사"의 해지의사를 "이용자"에게 통지한 시점에 종료됩니다. 
 ⑥ "회사"가 이용계약을 해지하더라도 이용계약의 해지 이전에 이미 체결된 "이용자"와 "판매자"간의 "상품" 거래계약의 완결에
    관해서는 이 약관이 계속 적용됩니다. 단, 본 조 제4항 5호의 사유로 이용계약이 해지되는 경우 "회사"는 해당 거래에 사용되는
    적립금 또는 쿠폰에 대해 책임을 부담하지 아니 하며, "이용자"는 해당 적립금 및 쿠폰에 해당하는 비용을 스스로 부담하여야 합니다. 
 ⑦ 이용계약이 종료되는 경우 "이용자"의 재이용 신청에 대하여 "회사"는 그 승낙을 거절할 수 있습니다. 
 ⑧ 이용계약의 종료와 관련하여 발생한 손해는 이용계약이 종료된 해당 "이용자"가 책임을 부담하여야 하고, "회사"는 일체의 책임을
    지지 않습니다. 
 
 
제9조("회사"의 의무) 
 ① "회사"는 관련법과 이 약관이 금지하거나 미풍양속에 반하는 행위를 하지 않으며, 계속적이고 안정적으로 "플러스치즈 서비스"를
    제공하기 위하여 최선을 다하여 노력합니다. 
 ② "회사"는 "회원"이 안전하게 "플러스치즈 서비스"를 이용할 수 있도록 개인정보(신용정보 포함)보호를 위한 보안시스템을 갖추며,
    개인정보취급방침을 공지하고 이를 준수합니다. 
  ③ "회사"는 서비스이용과 관련하여 "이용자"로부터 제기된 의견이나 불만이 정당하다고 인정할 경우에는 이를 처리하여야 합니다.
    "이용자"가 제기한 의견이나 불만사항에 대해서는 게시판을 활용하거나 전자우편 등을 통하여 "이용자"에게 처리과정 및 결과를
    전달합니다. 
 
제10조("구매자"의 의무")  
 ① "구매자"는 "상품"을 구매하기 전에 반드시 "판매자"의 사이트 내에 작성되어 있는 "상품"의 상세 내용과 거래의 조건을 정확하게
    확인한 후 구매를 하여야 합니다. 구매하려는 "상품"의 내용과 거래의 조건을 확인하지 않고 구매하여 발생한 모든 손실, 손해는
    "구매자"에게 있습니다. 
 ② "구매자"는 이 약관 및 "회사"가 "플러스치즈 서비스" 화면에서 고지하는 내용을 준수하여야 하며, 약관 및 고지내용을 위반하거나
    이행하지 아니하여 발생하는 모든 손실, 손해에 대하여 책임을 부담합니다. 
 ③ "회사"는 "원천 판매자"의 "상품" 내용과 거래 조건에 대해서 어떠한 보증이나 대리를 하지 않습니다. 따라서 "구매자"는 "상품"
    구매 시 스스로의 책임 하에 "상품"을 구매하여야 합니다.  
 ④ "구매자"는 구매한 "상품"에 청약철회의 원인이 발생한 경우 수령한 "상품"을 임의로 사용하거나 훼손되도록 방치하여서는 아니
    됩니다. "구매자"는 청약철회 "상품"의 임의사용이나 상품보관의 미흡으로 인하여 발생한 "상품"의 훼손에 대하여 합당한 비용을
    부담합니다. 
 ⑤ "구매자"는 "원천 판매자"와 상품매매 절차에서 분쟁이 발생한 경우 분쟁의 해결을 위하여 성실히 임하여야 하며, 분쟁해결의
    불성실로 인하여 "원천 판매자"와 "회사"에 발생한 모든 손실, 손해를 부담하여야 합니다. 
 ⑥ "구매자"는 "상품"의 구매 시 결제방법을 사용함에 있어 반드시 본인 명의의 결제수단을 사용하여야 하며, 타인의 결제수단의
    임의사용 등을 하여서는 안됩니다. 타인의 결제수단을 임의 사용함으로써 발생하는 "회사", 결제수단의 적법한 소유자, "PG",
    "판매자"의 손실과 손해에 대한 모든 책임은 "구매자"에게 있습니다.
 ⑦ "상품" 매매대금의 결제와 관련하여 "구매자"가 입력한 정보 및 그 정보와 관련하여 발생한 책임과 불이익은 전적으로 "구매자"가
    부담하여야 합니다. 
 ⑧ "구매자"는 매매대금 결제 시 정당하고, 적법한 사용권한을 가지고 있는 결제수단을 사용하여야 하며, "회사"는 그 여부를 확인할
    수 있습니다. 또한 "회사"는 "구매자" 결제수단의 적법성 등에 대한 확인이 완료될 때까지 거래진행을 중지하거나 해당 거래를
    취소할 수 있습니다. 
 ⑨ "구매자"는 "회사"가 "플러스치즈 서비스"를 안전하게 제공할 수 있도록 "회사"에 협조하여야 하며, "회사"가 "구매자"의 이 약관
    위반행위를 발견하여 "구매자"에게 해당 위반행위에 대하여 소명을 요청할 경우 "구매자"는 "회사"의 요청에 적극 응하여야 합니다. 
 ⑩ "회사"가 "구매자"의 "플러스치즈 서비스" 이용 편의를 위해 제휴업체로부터 정보를 제공받아 게재하거나 제 3자가 제공하는
    방식으로 "플러스치즈 서비스" 사이트 내 또는 링크 방식으로 참조용 정보나 컨텐츠를 제공하는 경우라도, "구매자"는 "상품" 구매
    시 자신의 판단과 책임으로 결정하여 구매하여야 하며 "회사"는 어떠한 경우에도 "구매자"의 구매결정에 대하여 책임을 부담하지
    않습니다 
 ⑪ "구매자"는 "회사"가 정하고 인정하는 방식에 따라 적립금 또는 쿠폰을 사용하여야 합니다. 
 ⑫ 미성년자가 "플러스치즈 서비스"를 이용하여 "상품" 구매 시 법정대리인이 해당 계약에 대하여 동의를 하여야 정상적인 "상품"
    구매계약이 체결될 수 있습니다. 미성년자의 거래에 관한 상세한 사항은 관련 법령이 정한 바에 의합니다 
 
제11조("쇼핑 정보 중계자의 의무") 
 ① "쇼핑 정보 중계자"는 "플러스치즈 서비스"가 제공하는 쇼핑 정보 측정결과에 대해 이의를 제기하지 않는다.
 
 ② "쇼핑 정보 중계자"는 정당하고 합법적인 방법으로 방문자들에게 "상품"을 소개 또는 권유하여야 한다. 허가받지 않은 스팸메일의
    발송, 타 사이트 게시판에의 광고물 게시, 부당 제작 광고물 활용, 불법 S/W의 활용 등 운영자의 명성에 해를 끼칠 수 있는 방법을
    사용하지 않아야 한다 
 ③ "쇼핑 정보 중계자"는 서비스 이용시 다음 각 호의 행위를 할 수 없으며 적발시 가입자 자격은 즉시 박탈된다.  
  1. 다른 가입자의 ID를 부정하게 사용하는 행위 
  2. "회사" , "원천 판매자" , "제3자"의 저작권을 포함한 지적재산권 기타 권리를 침해하는 행위. 
  3. 공공질서 및 미풍양속에 위반되는 내용의 정보,문장,도형 등을 타인에게 유포하는 행위. 
  4. 범죄와 결부되는 행위. 
  5. 허가받지 않은 스팸메일, 게시판 등의 활용으로 "회사"의 명성에 해를 끼치는 행위. 
  6. 관계법령에 위배되는 행위. 
  7. 기타 명백한 부정 행위. 
  8. 기타 명백한 부정 행위. 
 ④ "쇼핑 정보 중계자"는 서비스의 이용권한, 기타 약관상 지위를 타인에게 양도, 대여 할 수 없다. 
 
제12조("플러스치즈 서비스" 이용 제한 등) 
 ① "회사"는 "플러스치즈 서비스"를 통한 거래의 안정성과 신뢰성을 위하여 아래 각호의 사유가 발생하는 경우 "이용자"의 "플러스치즈
    서비스" 이용을 일시 정지할 수 있습니다. "플러스치즈 서비스"의 이용이 일시 정지된 "이용자"는 해당 기간 동안 "플러스치즈
    서비스"를 이용할 수 없습니다.  
  1. 허위주문행위가 발견된 경우  
  2. 불법카드거래행위인 경우  
  3. 부당 또는 부정하게 타인의 아이디를 사용하여 "플러스치즈 서비스"를 이용한 경우  
  4. "회사"가 인정하지 않는 방법으로 적립금 또는 쿠폰을 취득한 경우  
 ② "회사"는 제1항의 경우, "이용자"가 해당 사유를 소명하거나 거래 상대방의 양해가 있었음을 소명하는 등 "회사"가 정하는 기준을
    충족하는 경우 이용정지 조치를 해소할 수 있습니다.  
 
제13조(개인정보) 
 "회사"는 "이용자"가 안전하게 "플러스치즈 서비스"를 이용할 수 있도록 "이용자"의 개인정보보호를 위하여 개인정보보호정책을
 실시하며, 이에 따라 "이용자"의 개인정보보호를 하여야 할 의무가 있습니다. "회사"의 개인정보보호정책은 "회사"의 홈페이지 또는
 "플러스치즈 서비스" 에 링크된 화면을 통하여 확인할 수 있습니다. 
 
제14조(결제대금 보호서비스의 이용)  
 ① "쇼핑 정보 중계자"는 "플러스치즈 서비스"가 제공하는 쇼핑 정보 측정결과에 대해 이의를 제기하지 않는다.
 ② "결제대금 보호서비스"는 "회사"가 "에스크로사업자"와의 제휴를 통하여 "구매자"에게 제공하며, "구매자"가 "상품" 대금 결제 시
    "결제대금 보호서비스" 이용을 선택하는 경우에 적용됩니다. "이용자"가 "결제대금 보호서비스"를 이용하는 경우 "회사"는
    "결제대금 보호서비스"를 통해 이루어지는 "상품" 대금의 정산 및 환불에 대하여 어떠한 책임도 부담하지 않습니다. 
 ③ "에스크로사업자"를 통한 "결제대금 보호서비스"의 제공은 "원천 판매자" 또는 "구매자"를 대리하는 것이 아니며, "상품"의 매매와
    관련하여 "원천 판매자" 또는 "구매자"의 의무를 대행하는 것이 아닙니다. 
 ④ "결제대금 보호서비스"를 이용하는 "구매자"는 "상품"의 배송완료 시 "회사"에 구매확정, 교환 또는 반품의 의사표시를 하여야
    합니다. 
 ⑤ "구매자"의 구매확정의 의사가 있거나 객관적으로 구매확정의 의사가 있는 것으로 간주하는 상황이 발생하는 경우 "결제대금
    보호서비스"는 종료됩니다. 
 ⑥ 배송완료가 되었음에도 "구매확정기간" 내에 "구매자"로부터 교환 또는 반품의 의사표시가 없는 경우 "회사"는 "구매자"가
    구매확정의 의사가 있다고 간주합니다. 
 ⑦ "회사"는 "구매자"의 구매확정의 의사가 있거나 구매확정의 의사가 있다고 간주하는 경우 전자상거래등에서의 소비자보호에관한법률
    의 관련 규정에 따라 관련 대금을 "상품"의 "원천 판매자"에게 송금하도록 "PG" 또는 "에스크로사업자"에게 요청할 수 있습니다. 
 ⑧ "결제대금 보호서비스"가 종료된 이후에 청약철회, 취소, 해제, 무효 등의 사유가 발생하는 경우 "구매자"는 "원천 판매자"와 직접
    청약철회, 취소, 해제 및 대금 환불 등에 관한 절차를 진행하여야 합니다. 
 ⑨ 기타 본 조항에서 정하지 아니한 사항이나 해석에 대해서는 "에스크로사업자"의 이용약관에 따릅니다. 
 
제15조(배송 및 거래 완료) 
 ① "회사"는 "구매자"의 대금결제에 대한 확인통지를 "PG"로부터 받은 후 3영업일 이내에 "구매자"에게 "상품" 배송에 필요한 조치를
    취할 수 있도록 정보를 제공합니다. 
 ② 배송소요기간은 "상품" 대금의 입금 또는 대금결제 확인일의 익일을 기산일로 하여 배송이 완료되기까지의 기간을 말합니다. 공휴일
    및 기타 휴무일 또는 천재지변 등 불가항력적인 사유가 발생한 경우 그 해당기간은 배송소요기간에서 제외됩니다. 
 ③ "회사"는 "상품"의 배송과 관련하여 "원천 판매자"와 "구매자", 배송업체 등과의 사이에 발생한 분쟁은 당사자들 간의 해결을
    원칙으로 하며, "회사"는 이에 대하여 어떠한 책임도 부담하지 않습니다. 단, 금융기관 사이에 발생한 분쟁은 "회사"의 해결을
    원칙으로 한다.
 
제16조(반품/교환/환불/취소)  
 ① "구매자"는 구매한 "상품"에 대해 "원천 판매자"가 주문확인하기 전까지 구매를 취소할 수 있으며, 배송 중인 경우에는 취소가 아닌
    반품절차에 따라 처리됩니다. 
 ② "구매자"가 "상품"에 대한 대금결제를 완료한 후라도 "원천 판매자"의 주문확인 이전에서는 취소신청 접수 시 특별한 사정이 없는
    한 즉시 취소처리가 완료됩니다. 
 ③ "구매자"는 "구매확정기간" 내에 반품을 신청할 수 있습니다. 단 "구매자"가 특정 "상품"에 대하여 "결제대금 보호서비스"를 신청
    후, "구매자"가 해당 "상품"에 대하여 구매확정을 하는 경우에는 "구매자"가 반품을 신청할 수 없습니다. 
 ④ 반품에 소요되는 비용은 반품에 대한 귀책사유가 있는 자에게 일반적으로 귀속됩니다. 즉 "구매자"의 단순변심인 경우는 "구매자"가
    비용을 부담하며, "상품"의 하자로 인한 반품의 경우는 "원천 판매자"가 반품비용을 부담합니다. 
 ⑤ 반품 신청 시 "플러스치즈 서비스"에서 제공하는 반송서비스를 이용하지 않거나 반품사유에 관하여 "원천 판매자"에게 정확히 통보
    (또는 서면)하지 않을 시 반품처리 및 환불이 지연될 수 있습니다. 
 ⑥ 반품에 소요되는 비용을 "구매자"가 부담하여야 하는 경우 반품에 소요되는 비용의 추가 결제가 이루어지지 않으면 환불이 지연될
    수 있습니다. 
 ⑦ 반품에 관한 일반적인 사항은 전자상거래등에서의소비자보호에관한법률 등 관련법령이 "원천 판매자"가 제시한 조건보다 우선합니다. 
 ⑧ "구매자"는 "구매확정기간" 내에 교환을 신청할 수 있습니다. 단 "구매자"가 특정 "상품"에 대하여 "결제대금 보호서비스"를 신청
    후, "구매자"가 해당 "상품"에 대하여 구매확정을 하는 경우에는 "구매자"가 교환을 신청할 수 없습니다.
 ⑨ "구매자"가 "상품"에 대하여 교환신청을 하더라도 "원천 판매자"에게 교환할 "상품"의 재고가 없는 경우에는 교환이 불가능하며,
    이 경우에 해당 교환신청은 반품으로 처리됩니다. 
 ⑩ 취소 및 반품 처리에 따른 환불은 신용카드결제 및 "결재대금 보호서비스"가 적용된 현금 결제의 경우 취소절차가 완료된 즉시
    "PG"에 의해 결제가 취소되며, 일반 현금결제의 경우에는 "구매자"의 환불신청일로부터 3영업일 이내에 "원천 판매자"가 "구매자"가
    지정하는 계좌로 직접 환불을 진행할 수 있도록 정보를 제공합니다. 
 ⑪ 신용카드결제를 통한 "상품" 구매건의 환불은 신용카드결제 취소를 통해서만 가능합니다. 
 ⑫ "구매자"는 다음 각호의 경우에는 "구매자"가 환불 또는 교환을 요청할 수 없습니다. 
  1. "구매자"의 귀책사유로 인하여 "상품"이 멸실, 훼손된 경우  
  2. "구매자"의 "상품" 사용 또는 일부 소비에 의하여 "상품"의 가치가 현저히 감소한 경우  
  3. 시간의 경과에 의하여 재판매가 곤란할 정도로 "상품"의 가치가 현저히 감소한 경우  
  4. 복제가 가능한 "상품"의 포장을 훼손한 경우  
  5. 기타 "구매자"가 환불 또는 교환을 요청할 수 없는 합리적인 사유가 있는 경우  
 
제17조(적립금)  
 ① "회사"는 "구매자"가 "상품"의 구매, "상품평"의 작성, 이벤트 참여 등 "플러스치즈 서비스"를 이용하는 경우, 회사의 정책에 따라
    "구매자"에게 일정한 적립금을 부여할 수 있습니다. 
 ② "구매자"는 회사의 정책에 따라 "플러스치즈 서비스"를 통하여 "원천 판매자"와 거래 시 단독 또는 다른 결제 수단과 혼합하여 결제
    수단으로써 적립금을 사용할 수 있습니다. 
 ③ 적립금의 부여 및 사용에 관한 상세한 사항은 "회사"가 정한 정책에 따르며, "회사"는 "플러스치즈 서비스" 페이지를 통하여 이를
    "이용자"에게 안내합니다. 
 ④ 적립금은 현금으로 전환 가능합니다. 
 ⑤ "플러스치즈 서비스" 이용 계약이 해지되는 경우 "이용자"의 모든 적립금이 소멸됩니다. 
 ⑥ "이용자"는 적립금을 본인의 거래에 대해서만 사용할 수 있으며, 어떠한 경우라도 적립금을 타인에게 매매 또는 양도하거나,
    실질적으로 매매 또는 양도와 동일하게 볼 수 있는 행위를 할 수 없습니다. 
 ⑦ "이용자"가 부당 또는 부정하게 적립금을 취득한 경우 "이용자"는 적립금을 사용할 수 없으며 "회사"는 이를 회수할 수 있습니다. 
 
제18조(민원처리 및 분쟁조정) 
 ① "회사"는 "이용자"와 "원천 판매자"간의 "상품" 매매에 관여하지 않으며, 이에 대하여 어떠한 책임도 부담하지 않는 것을 원칙으로
    합니다. 다만 "이용자"와 "원천 판매자"간 또는 "이용자"와 제3자 사이의 민원 및 분쟁이 발생할 경우, "회사"는 합리적인 범위
    내에서 이를 조정할 수 있습니다. 
 ② "회사"가 민원처리 및 분쟁조정을 하는 경우 "회사"는 제3자의 입장에서 공정하게 조정에 임합니다. 
 ③ "회사"의 분쟁조정에 대하여 "이용자"는 신의성실원칙에 따라 성실히 응하여야 합니다. 
 
제19조("플러스치즈 서비스"의 중단)
 ① "회사"는 통신, 전력 등의 공급이 중단되는 불가피한 경우는 물론 정보통신설비의 보수점검, 증설, 교체, 이전 등의 유지 관리
    업무를 수행하기 위해 필요한 경우 "플러스치즈 서비스"의 제공을 일시적으로 중단할 경우 7일 이전에 "플러스치즈 서비스"의
    중단을 공지합니다. 다만, 불가피하게 사전 공지를 할 수 없는 경우 "회사"는 사후 공지할 수 있습니다.
 ② "회사"는 천재지변, 전쟁, 폭동, 테러, 해킹 등 불가항력적 사유로 "플러스치즈 서비스"가 중단된 경우 즉시 이러한 사실을 공지하되,
    만일 정보통신설비의 작동불능 등의 불가피한 사유로 인해 사전공지가 불가능한 경우에는 이러한 사정이 해소된 이후 즉시 이러한
    사실을 공지합니다. 
 ③ "회사"는 본 조 제1항 내지 제2항의 사유가 발생한 경우 최대한 빠른 시간 내에 "플러스치즈 서비스"를 재개하도록 최선의 노력을
    다합니다. 
 ④ "회사"는 본 조 제1항 내지 제2항의 사유가 발생한 경우 최대한 빠른 시간 내에 "플러스치즈 서비스"를 재개하도록 최선의 노력을
    다합니다. 

제20조("회사"의 면책) 
 ① "원천 판매자"와 "구매자"간에 이루어지는 "상품"의 매매와 관련하여 발생하는 "상품"의 배송, 청약철회 또는 교환, 반품 및 환불
    등의 거래진행은 거래의 당사자인 "원천 판매자"와 "구매자" 각각의 책임 하에 이루어집니다. "회사"는 "원천 판매자"와 "구매자"
    간의 "상품" 거래에 관여하지 않으며, 이에 대하여 어떠한 책임도 부담하지 않습니다. 
 ② 제19조 사유로 인하여 "플러스치즈 서비스"를 중단하는 경우 "회사"는 "플러스치즈 서비스"의 중단에 대하여 어떠한 책임도 부담하지
    않습니다. 
 ③ "회사"는 "구매자"의 귀책사유로 인한 "플러스치즈 서비스" 이용의 장애에 대하여 책임을 지지 않습니다. 
 ④ "구매자"가 자신의 개인정보를 타인에게 제공하거나, "구매자"의 관리소홀로 유출됨으로써 발생하는 피해에 대해서 "회사"는 책임을
    지지 않습니다. 
 ⑤ "회사"의 "플러스치즈 서비스" 화면에서 링크, 배너 등을 통하여 연결된 다른 회사와 "구매자"간에 이루어진 거래에 "회사"는
    개입하지 않으며, 해당 거래에 대하여 책임을 지지 않습니다. 
 ⑥ "회사"는 "플러스치즈 서비스" 화면에 표시되는 "원천 판매자" 또는 제3자가 제공하는 "상품" 및 정보 등의 정확성, 적시성, 타당성
    등에 대하여 보증하지 않으며, 그와 관련하여 어떠한 책임도 부담하지 아니 합니다. 
 
제21조(준거법 및 재판관할) 
 ① 이 약관과 관련된 사항에 대하여는 대한민국법을 준거법으로 합니다. 
 ② "회사"와 "원천 판매자", "이용자"간 발생한 분쟁에 관한 소송은 민사소송법 상의 관할법원에 제소합니다. 

[부칙]
이 약관은 2011년 1월 1일부터 적용됩니다.</xmp></textarea></div>
					<input type="radio" name="agree2" value="y" />동의합니다</label><label><input type="radio" name="agree2" value="n" />동의하지 않습니다</label>
				</td>
			</tr>
		</table>
		<br/><div class="noline" style="text-align:center"><input type="image" src="../img/btn_agree.gif" /></div>
	</form>
</div>
<?
}
?>
<? include "../_footer.php"; ?>