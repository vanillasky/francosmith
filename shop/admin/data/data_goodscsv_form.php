<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<script type="text/javascript">
function _chkForm(f) {

	if (!chkForm(f)) return false;

	f.target = "ifrmHidden";

	nsGodoLoadingIndicator.init({
		psObject : $$('iframe[name="ifrmHidden"]')[0]
	});

	nsGodoLoadingIndicator.show();

	return true;

}
</script>

<!-- 구) 엑셀 등록 & 수정 -->
<div class="title title_top">상품 DB등록<span>대량의 상품 DB를 빠르게 등록(Up Date) 하실 수 있습니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<div style="padding-top:5px"></div>

<form name=fm method=post action="../data/data_goodscsv_indb.php" target="_blank" enctype="multipart/form-data" onsubmit="return _chkForm(this)">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td width="150">샘플파일 다운로드</td>
	<td>
		<a href="../data/csv_goods.xls"><img src="../img/btn_goodcsv_sample.gif" alt="상품CSV 샘플파일 다운로드"></a>
		<p class="extext_t" style="margin:0;">
		샘플파일을 다운받아 엑셀에서 상품정보를 작성합니다. <br>
		작성시 상품분류(goodscate) 셀의 '셀서식 > 표시형식'을 텍스트 서식으로 변경해 주세요. 변경하지 않는 경우 상품분류 값이 ‘002’ -> ‘2’로 변경되어 등록될 수 있습니다.<br>
		셀에 많은 내용을 입력하여 CSV로 저장할 경우 엑셀 버전에 따라 ‘#####’으로 저장되어 상품정보가 정상적으로 등록되지 않을 수 있으니,<br>
		반드시 셀서식을 ‘일반’ 등으로 변경하여 ‘#####’가 되지 않도록 한 뒤 저장 및 업로드하여 주시기 바랍니다.
		</p>
	</td>
</tr>
<tr>
	<td>상품 CSV 파일 올리기</td>
	<td>
		<input type="file" name="file_excel" size="45" required label="CSV 파일"> &nbsp;&nbsp; <span class="noline"><input type=image src="../img/btn_regist_s.gif" align="absmiddle"></span>

		<p class="extext_t" style="margin:0;">
		작성 완료된 상품CSV 파일을 올리세요. <br>
		등록이 완료되면 [상품리스트] 에서 등록된 상품을 확인하실 수 있습니다.
		</p>
	</td>
</tr>
</table>
</form>

<style>
div.admin-necessarily-remark {border:4px solid #dce1e1;margin:10px 0 10px 0;padding:10px;}
div.admin-necessarily-remark h3 {margin:0;padding:0;font-size:12px;font-weight:bold;color:#0074BA;}
div.admin-necessarily-remark ol {margin:7px 0 0px 0px;}
div.admin-necessarily-remark ol li {list-style-type:none;color:#666666;margin:0 0 3px 0;}
</style>

<div class="admin-necessarily-remark">
	<h3>꼭! 알아두기</h3>

	<ol>
		<li>1) 새로 등록할 상품의 상품번호는 비워두십시오. 이미 등록된 상품번호를 쓰시면 해당 상품의 정보가 수정됩니다.</li>
		<li>2) 등록된 상품 수정/UpDate 시 상품번호, 상품명 값이 반드시 있어야 해당 상품으로 업로드가 됩니다.</li>
		<li>3) 필수항목(상품번호, 상품명)을 제외하고는 등록 내용이 없는 경우 공란으로 두시거나. 해당항목을 삭제 하고 등록하시면 됩니다. <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_data_goodscsv_1.html',870,523)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a></li>
		<li>4) 샘플양식에서 제공되는 영문 타이틀명은 반드시 존재하여야 하며, 변경/수정 및  공란으로 등록시 오류 처리되어 등록되지 않습니다.</li>
		<li>5) 등록된 내용을 삭제 및 내용없음 으로 변경하여 등록 및 UpDate 하실 경우 해당 입력란을 null 로 입력해 주셔야 합니다. 공란으로 등록시 변경되지 않습니다. <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_data_goodscsv_1.html',870,523)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a></li>
		<li>6) 파일을 저장하실 때에는(CSV)로 저장해 주세요.</li>
	</ol>
</div>



<!-- 구) 엑셀 등록 & 수정 -->
<div class="title title_top">신규 상품DB 일괄등록 <span>새로 등록할 상품 DB를 빠르게 쉽게 등록 하실 수 있습니다.</div>
<div style="padding-top:5px"></div>

<form name=fm2 method=post action="../data/data_goodscsv_indb_new.php" target="_blank" enctype="multipart/form-data" onsubmit="return _chkForm(this)">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td width="150">샘플파일 다운로드</td>
	<td>
		<a href="../data/csv_goods.new.xls"><img src="../img/btn_goodcsv_sample_new.gif" alt="NEW 상품CSV 샘플파일 다운로드"></a>
		<p class="extext_t" style="margin:0;">
		샘플파일을 다운받아 엑셀에서 상품정보를 작성합니다. <br>
		작성시 상품분류(goodscate) 셀의 '셀서식 > 표시형식'을 텍스트 서식으로 변경해 주세요. 변경하지 않는 경우 상품분류 값이 ‘002’ -> ‘2’로 변경되어 등록될 수 있습니다.<br>
		셀에 많은 내용을 입력하여 CSV로 저장할 경우 엑셀 버전에 따라 ‘#####’으로 저장되어 상품정보가 정상적으로 등록되지 않을 수 있으니,<br>
		반드시 셀서식을 ‘일반’ 등으로 변경하여 ‘#####’가 되지 않도록 한 뒤 저장 및 업로드하여 주시기 바랍니다.
		</p>
	</td>
</tr>
<tr>
	<td>상품 CSV 파일 올리기</td>
	<td>
		<input type="file" name="file_excel" size="45" required label="CSV 파일"> &nbsp;&nbsp; <span class="noline"><input type=image src="../img/btn_regist_s.gif" align="absmiddle"></span>

		<p class="extext_t" style="margin:0;">
		작성 완료된 상품CSV 파일을 올리세요. <br>
		등록이 완료되면 [상품리스트] 에서 등록된 상품을 확인하실 수 있습니다.
		</p>
	</td>
</tr>
</table>
</form>

<div class="admin-necessarily-remark">
	<h3>꼭! 알아두기</h3>





	<ol>

		<li>1) 신규 상품DB 일괄등록은 상품등록 전용기능이며, 옵션정보를 쉽고 간편하게 등록 할 수 있습니다.<br>
		       &nbsp;&nbsp;&nbsp;&nbsp;기존 상품정보 수정은 본 페이지 상단의 [상품DB등록]을 이용하여 주세요.<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_data_goodscsv_2.html',870,523)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a>
		</li>
		<li>2) 필수항목(상품명)을 제외하고는 등록 내용이 없는 경우 공란으로 두시거나, 해당항목을 삭제하고 등록하시면 됩니다. 상품명이 없으면 해당란은 등록이 되지 않습니다.</li>
		<li>3) 샘플양식에서 제공되는 영문 타이틀명은 반드시 존재하여야 하며, 변경/수정 및  공란으로 등록시 오류 처리되어 등록되지 않습니다.</li>
		<li>4) 상품분류번호를 입력하지 않고 등록하는 경우에는 [ 상품관리 > 빠른 이동/복사/삭제 ]에서 관리하실 수 있습니다.</li>
		<li>5) 옵션의 세부정보(가격, 및 재고량)는 <a href="../goods/stock.php" target="_blank"><font class="extext_l">[ 상품관리 > 가격/적립금/재고수정 ]</font></a> 에서 일괄 등록, 관리 하실 수 있습니다.</li>
		<li>6) 파일을 저장하실 때에는(CSV)로 저장해 주세요.</li>



	</ol>
</div>

<div style="padding-top:30px"></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr>
	<td>
	</td>
</tr>

<tr>
	<td><div style="padding: 5px 0px 2px 5px"><img src="../img/icon_list.gif" align=absmiddle>상품필드설명</div><br>
	<div style="width:100%;margin-left:10px;">
	<style>
	#field_table { border-collapse:collapse; }
	#field_table th { padding:4; }
	#field_table td { border-style:solid;border-width:1;border-color:#EBEBEB;color:#4c4c4c;padding:4; }
	#field_table i { color:green; font:8pt dotum; }
	</style>
	<table id="field_table">
	<tr bgcolor="#eeeeee">
		<th><font class=small1 color=444444><b>한글 타이틀</th>
		<th><font class=small1 color=444444><b>영문 타이틀</th>
		<th><font class=small1 color=444444><b>설명</th>
	</tr>
<? foreach( parse_ini_file("../../conf/data_goodsddl.ini", true) as $key => $arr ){
    if ($key == 'extra_info') $arr['desc'] = '<a href="javascript:popup(\'http://guide.godo.co.kr/guide/php/ex_data_goodscsv_3.html\',770,523)"><img src="../img/icon_sample.gif" border="0" align=right style="margin:10px;"></a>형식) {항목번호:항목명|항목내용}<br>각 항목별 \'{ }\'로 묶고 항목별 구분은 \',\'로 입력<br>{ } 안에 세부내역은 \':\'와 \'|\'로 구분하여 입력 <i>ex) {1:제품소재|소가죽}</i><br><u style=\'color:#bf0000;\'>인터파크 연동상품은 상품필수정보 일괄등록이 되지 않습니다.</u>';
?>
	<tr bgcolor="<?=( ++$idx % 2 == 0 ? '#ffffff' : '#ffffff' )?>">
		<td><font class=small1 color=444444><?=$arr['text']?></td>
		<td><font class=ver8 color=444444><?=$key?></td>
		<td><font class=small color=444444><?=nl2br( $arr['desc'] )?></td>
	</tr>
<? } ?>
	</table>
	</div>
	</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>