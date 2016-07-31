<?
if (!$_GET['page_num']) $_GET['page_num'] = 10;
$selected['page_num'][$_GET['page_num']] = "selected";

if (!$_GET['sort']) $_GET['sort'] = 'regdate';
$selected['sort'][$_GET['sort']] = "selected";
?>
<html>
<head>
	<title>'Godo Shoppingmall e나무 Season4 관리자모드'</title>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="../../js/default.js"></script>
	<link rel="stylesheet" type="text/css" href="../../css/style.css"/>	
	<link rel="styleSheet" href="../../../admin/style.css">
	<link rel="styleSheet" href="../../../admin/_contextmenu/contextmenu.css?1349919008">
	<script src="../../../admin/common.js"></script>
	<style>
		/*** 어드민 레이아웃 설정 ***/
		body {margin:0 0 0 0px}

		/* 진열상태 */
		div.display_state {}
		div.display_state input {border:none;}
		div.display_state span {display:block;width:30px;height:12px;}
		div.display_state span.show {background:url(/shop/admin/img/icn_1.gif) no-repeat 50% 50%;}
		div.display_state span.hide {background:url(/shop/admin/img/icn_0.gif) no-repeat 50% 50%;}

		/* 리스트 상단 */
		.rndbg2 {
			background: url('../../images/bg_table_title.gif');
			letter-spacing:-1px;
			font:8pt 돋움;
			padding-top:2px;
			height:26px;
			color:#ffffff;
		}
	</style>
</head>
<body>

<!-- 에디터 팝업 레이어 -->
<div id="popupContact">
	<a id="popupContactClose">[x]</a>
	<h1></h1>
	<p id="contactArea">
		<iframe id='DynamicPopup' frameborder="0" src="" style="border:0px solid #000000;"></iframe>				
	</p>
</div>
<div id="backgroundPopup"></div>
<!-- 에디터 팝업 레이어 -->
<div class="title title_top">코디리스트<span><a href="javascript:manual('http://guide.godo.co.kr/season4/board/view.php?id=product&no=42')"><img src="../../../admin/img/btn_q.gif" border=0 align=absmiddle></a></span></div>
<div id="button" style="text-align:left;float:left;padding-bottom:5px;">&nbsp;<a href="javascript:" onclick="Newopen('I','')"><img src="../../images/btn_cody.gif"></a></div>
<form name="listform">  
<div style="text-align:right;padding-bottom:5px;">
	<select name="sort" onchange="this.form.submit()">
		<option value="regdate" <?=$selected['sort']['regdate']?>>등록일 순
		<option value="like_cnt" <?=$selected['sort']['like_cnt']?>>좋아요 순
		<option value="recody_cnt" <?=$selected['sort']['recody_cnt']?>>댓글 순
	</select>
	<select name="page_num" onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,30);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>개 출력
		<? } ?>
	</select>
</div>
</form>

<form name="f1" method="post" action="./indb.php">
<input type="hidden" name="fn" value="C" />
	<table width=100% cellpadding=0 cellspacing=0 border="0">
	<tr class="rndbg2">
		<th rowspan="2" style="width:50px;word-break:break-all;"><font class=small>번호</font></th>
		<th rowspan="2"><font class=small>Cody</font></th>
		<th rowspan="2"><font class=small>코디이름</font></th>
		<th rowspan="2" style="width:230px;word-break:break-all;"><font class=small>등록일/시</font></th>
		<th rowspan="2" style="width:100px;word-break:break-all;"><font class=small>코디(set)가격</font></th>
		<th colspan="2"><font class=small>참여반응</font></th>
		<th rowspan="2" style="width:100px;word-break:break-all;"><font class=small><span onclick="fnState();" style="cursor:pointer;">진열상태</span></font></th>
		<th rowspan="2"><font class=small>수정</font></th>
		<th rowspan="2"><font class=small>삭제</font></th>
	</tr>
	<tr style="height:25px;background-color:#56636a;color:#ffffff;letter-spacing:-1px;font:8pt 돋움;padding-top:2px;">
		<th style="width:50px;word-break:break-all;">좋아요</th>
		<th style="width:50px;word-break:break-all;">댓글</th>
	</tr>
	<tr><td class=rnd colspan="10"></td></tr>
<?			
	foreach($objs as $obj){

		### 이미지 상태
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
		<td><?=number_format($obj->get('setCost'))?> 원</td>
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
		<div id="button" style="text-align:right;padding-top:5px;"><input type="image" src="../../images/btn_display.gif" alt="진열상태저장하기" style="border:0;" /></div>
		

	<div align=center class=pageNavi>	
		<font class=ver8> 
			<?=$paging?>
		</font>
	</div>
</form>
	

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">* 코디 등록하기 </td></tr>
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">- 코디 생성시 적용되는 이미지는 상품의 메인확대(원본) 이미지만 적용이 됩니다.  (상세, 리스트, 메인 이미지와 다를 수 있습니다.)</td></tr>
</table>
<br/>
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../../../admin/img/icon_list.gif" align="absmiddle">* 진열상태 변경</td></tr>
<tr><td style="padding-left:10">- 등록 완료된 코디상품은 처음 진열상태가 <font style="color:#ef2869;font-weight:bold;">NO</font> 입니다. 리스트에 적용된 코디상품을 확인하신 후 진열상태를 <font style="color:#37a3ee;font-weight:bold;">YES</font>로 변경해 주세요.</td></tr>
<!--tr><td style="padding-left:10">&nbsp;&nbsp;&nbsp;(텍스트 '<font style="color:#ef2869;font-weight:bold;">NO</font>'를 클릭하시면 '<font style="color:#37a3ee;font-weight:bold;">YES</font>'로 변경됩니다.)</td></tr-->
<tr><td style="padding-left:10">- 코디상품 내에 일부 상품이 미진열/삭제 처리되면 코디상품의 진열 상태가 <font style="color:#ef2869;font-weight:bold;">NO</font>로 변경됩니다.</td></tr>
<tr><td style="padding-left:10">&nbsp;&nbsp;&nbsp;해당 일부상품이 추후 진열상태로 되어도 코디상품의 진열상태는 <font style="color:#37a3ee;font-weight:bold;">YES</font>로 자동변경되지 않습니다. </td></tr>
<tr><td style="padding-left:10">&nbsp;&nbsp;&nbsp;운영자가 확인 하신 후, 해당 코디상품의 진열여부를 변경해 주셔야 합니다.</td></tr>
</table>
<br/>
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="/shop/admin/img/icon_list.gif" align="absmiddle">* 수정 하기</td></tr>
<tr><td style="padding-left:10">&nbsp;&nbsp;<font style="color:#ffffff;font-weight:bold">[수정]</font> 버튼을 클릭하여 등록된 코디명과 스토리를 수정 하실 수 있습니다. </td></tr>
<tr><td style="padding-left:10">- 코디 이미지는 수정이 불가능 합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01',null,null,'../../../admin/')</script>

<script>
function delscript(idx){

	if(confirm("해당 코디를 삭제하시겠습니까? 삭제시 복원되지 않습니다.")){ 
		jQuery.ajax({
			type:"POST",
			url:"./indb.php",
			data:{fn:"D",gidx:idx},
			dataType: "html",
			success: function(data){
				alert('해당 코디가 삭제되었습니다.');
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
		
		if(jQuery(".state_Y").eq(i).is(":checked")) { //checked=ture 이면
			jQuery("input.state_Y").eq(i).removeAttr("checked");          //체크해제
			jQuery("div.display_state > span").eq(i).removeClass('show').addClass('hide');	//NO로 바꿈
		} else {
			jQuery("input.state_Y").eq(i).attr("checked", true);          //체크표시 
			jQuery("div.display_state > span").eq(i).removeClass('hide').addClass('show'); //YES로 바꿈
		}
	}		
}
</script>

</body>
</html>