<div class="title title_top">회원DB등록<span>대량의 회원DB를 빠르게 등록하실 수 있습니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>


<div style="padding-top:5px"></div>


<div style="padding-top:5px;padding-left:20;"><img src="../img/arrow_downorg.gif" align=absmiddle> <font class=small1 color=444444>잠깐! 회원등록방법을 우선 읽어보세요! <a href="javascript:popup('http://guide.godo.co.kr/guide/php/manual_data.php',870,800)"><img src="../img/btn_detail_csv.gif" align=absmiddle hspace=3 vspace=3></a></div>


<table cellpadding=0 cellspacing=0 border=0 class=small_tip width=100%>
<tr>
	<td style="padding-left:22px;padding-top:12px;">
	&nbsp;&nbsp;<font size=3 color=0074BA><b>①</b> </font>아래 샘플파일을 다운받아 엑셀에서 회원정보를 작성합니다.<br>
    <div class=noline style="padding-left:60px;padding-top:5px;text-align:left;"><a href="../data/csv_member.xls"><img src="../img/btn_goodcsv_sample.gif" alt="상품CSV 샘플파일 다운로드"></a></div>

	<div style="padding-top:10px"></div>
	</td>
</tr>
</table>



<div style="padding-top:15px"></div>



<form name=fm method=post action="../data/data_membercsv_indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this)">
<div style="padding-top:5px;padding-left:30;"><font size=3 color=0074BA><b>②</b></font> <font class=small1 color=444444>작성완료된 회원CVS파일을 올리세요.</div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>암호화 여부</td>
	<td><input type=checkbox name='chkpass' value='1' class=null checked> <font color=0074BA>비밀번호</font> 필드를 암호화하여 등록합니다.</td>
</tr>
<tr>
	<td width=240 height=35>회원CSV파일 올리기</td>
	<td><input type="file" name="file_excel" size="45" required label="CSV 파일"> &nbsp;&nbsp; <span class="noline"><input type=image src="../img/btn_regist_s.gif" align="absmiddle"></span></td>
</tr>
</table>

</form>

<div style="padding-top:15px"></div>

<div style="padding-top:5px;padding-left:30;"><font size=3 color=0074BA><b>③</b></font> <font class=small1 color=444444>등록이 완료되면 <a href="../member/list.php"><font color=0074BA><u>회원리스트</u></font></a> 에서 등록된 회원을 확인할 수 있습니다.</font></div>



<div style="padding-top:30px"></div>



<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_tip>
<tr>
	<td><img src="../img/icon_list.gif" align=absmiddle><font color=0074BA>회원필드설명</font>
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
<? foreach( parse_ini_file("../../conf/data_memberddl.ini", true) as $key => $arr ){ 
	if($key == 'resno1' || $key == 'resno2'){
		continue;
	}
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
<script>cssRound('MSG01','#F7F7F7')</script>