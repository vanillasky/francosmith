<?php /* Template_ 2.2.7 2014/03/05 23:19:27 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/goods/popup_request_stocked_noti.htm 000004360 */ 
if (is_array($TPL_VAR["optList"])) $TPL_optList_1=count($TPL_VAR["optList"]); else if (is_object($TPL_VAR["optList"]) && in_array("Countable", class_implements($TPL_VAR["optList"]))) $TPL_optList_1=$TPL_VAR["optList"]->count();else $TPL_optList_1=0;?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>상품 재입고 알림 신청</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script src="/shop/data/skin/campingyo/common.js"></script>
<script type="text/javascript">
	var wHeight = 0;

	function selectOption(method){
		var el = document.getElementsByName("opt[]");
		for(var i=0;i<el.length;i++){
			document.getElementsByName("opt[]")[i].checked = method;
		}
	}
	
	function chkForm2(f){
		if(f.name.value == ""){
			alert("이름을 입력해 주세요");
			f.name.focus();
			return false;
		}
		if(f.phone.value == ""){
			alert("휴대폰 정보를 입력해 주세요");
			f.phone.focus();
			return false;
		}
		chkForm(f);
	}
</script>
<link rel="styleSheet" href="/shop/data/skin/campingyo/style.css">
</head>

<body>
<script>
	window.moveTo(0,0);
</script>
<form name="frmRequestStocked" method="post" action="<?php echo url("goods/indb.php")?>&" onSubmit="return chkForm2(this);">
<input type="hidden" name="mode" value="request_stocked_noti">
<input type="hidden" name="goodsno" value="<?php echo $TPL_VAR["goodsno"]?>">
<table width="360" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td style="background-image: url(/shop/data/skin/campingyo/img/stocked_noti/topbg.jpg); height:41px; color:#ffffff; font-family: 돋움; font-weight: bold; font-size: 15px; padding-left: 10px"><?php echo $TPL_VAR["goodsnm"]?></td>
</tr>
<tr>
	<td style="height:14px"></td>
</tr>
<tr>
<?php if($TPL_VAR["optCnt"]!="1"){?>
<tr>
	<td style="padding-left:15px; color:">옵션별 품절상품 리스트</td>
</tr>
 	<td align="center">
 		<table width="340" border="0" cellpadding="0" cellspacing="0" align="center" style="border-color:#E0E0E0; border-width:1px; border-style:solid; background-color:#F5F5F5">
 			<tr>
 				<td style="padding:5px;" valign="top"><div id="divOptions" style="overflow: scroll; height:0px; color:#666666; font-family: 돋움; font-size:11px; text-align: left"><?php if($TPL_VAR["optCnt"]== 1){?><?php }else{?><?php if($TPL_optList_1){foreach($TPL_VAR["optList"] as $TPL_V1){?><input type="checkbox" name="opt[]" value="<?php echo $TPL_V1["optno"]?>" /><?php echo $TPL_V1["opt1"]?>/<?php echo $TPL_V1["opt2"]?><br /><?php }}?><?php }?>
<?php if($TPL_optList_1){foreach($TPL_VAR["optList"] as $TPL_V1){?>
				<script type="text/javascript">
					f = document.getElementById("divOptions");
					if(wHeight >= 150){
						f.style.height = "150px";
						f.style.overflow = "scroll";
					}else{
						wHeight += 25;
						f.style.height = wHeight + "px";
						f.style.overflow = "";
					}
				</script>
<?php }}?>
				</div></td>
 			</tr>
		</table>
	</td>
</tr>
<tr>
	<td style="height:50px" align="center"><a href="javascript:selectOption(true)"><img src="/shop/data/skin/campingyo/img/stocked_noti/btn_allchoice.gif" /></a> <a href="javascript:selectOption(false)"><img src="/shop/data/skin/campingyo/img/stocked_noti/btn_allchoice_no.gif" /></a></td>
</tr>
<?php }?>
<tr>
	<td style="text-align:center;height:50px;">이름 <input type="text" name="name" value="<?php echo $TPL_VAR["memberName"]?>" style="width:75px;height:18px;border:1px solid #CACACA;background:#F7F7F7;" required label="이름">
		휴대폰 <input type="text" name="phone" value="<?php echo $TPL_VAR["mobile"]?>" style="width:130px;height:18px;border:1px solid #CACACA;background:#F7F7F7;" required label="휴대폰">
	</td>
</tr>
<tr>
	<td align="center" height="65px">해당 상품이 재입고 되면 SMS를 발송해 드립니다.<br />입고된 상품은 재입고 알림 신청접수 순서대로 발송 됩니다.</td>
</tr>
<tr>
	<td align="center"><input type="image" src="/shop/data/skin/campingyo/img/stocked_noti/btn_alarm.gif" /></td>
</tr>
</table>
</form>
<script type="text/javascript">
	window.resizeTo(376, wHeight + 360);
	window.moveTo((screen.width/2)-(window.document.body.clientWidth/2), (screen.height/2)-(window.document.body.clientHeight/2));
</script>
</body>
</html>