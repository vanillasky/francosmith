<div class="title title_top">ȸ��DB���<span>�뷮�� ȸ��DB�� ������ ����Ͻ� �� �ֽ��ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>


<div style="padding-top:5px"></div>


<div style="padding-top:5px;padding-left:20;"><img src="../img/arrow_downorg.gif" align=absmiddle> <font class=small1 color=444444>���! ȸ����Ϲ���� �켱 �о����! <a href="javascript:popup('http://guide.godo.co.kr/guide/php/manual_data.php',870,800)"><img src="../img/btn_detail_csv.gif" align=absmiddle hspace=3 vspace=3></a></div>


<table cellpadding=0 cellspacing=0 border=0 class=small_tip width=100%>
<tr>
	<td style="padding-left:22px;padding-top:12px;">
	&nbsp;&nbsp;<font size=3 color=0074BA><b>��</b> </font>�Ʒ� ���������� �ٿ�޾� �������� ȸ�������� �ۼ��մϴ�.<br>
    <div class=noline style="padding-left:60px;padding-top:5px;text-align:left;"><a href="../data/csv_member.xls"><img src="../img/btn_goodcsv_sample.gif" alt="��ǰCSV �������� �ٿ�ε�"></a></div>

	<div style="padding-top:10px"></div>
	</td>
</tr>
</table>



<div style="padding-top:15px"></div>



<form name=fm method=post action="../data/data_membercsv_indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this)">
<div style="padding-top:5px;padding-left:30;"><font size=3 color=0074BA><b>��</b></font> <font class=small1 color=444444>�ۼ��Ϸ�� ȸ��CVS������ �ø�����.</div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>��ȣȭ ����</td>
	<td><input type=checkbox name='chkpass' value='1' class=null checked> <font color=0074BA>��й�ȣ</font> �ʵ带 ��ȣȭ�Ͽ� ����մϴ�.</td>
</tr>
<tr>
	<td width=240 height=35>ȸ��CSV���� �ø���</td>
	<td><input type="file" name="file_excel" size="45" required label="CSV ����"> &nbsp;&nbsp; <span class="noline"><input type=image src="../img/btn_regist_s.gif" align="absmiddle"></span></td>
</tr>
</table>

</form>

<div style="padding-top:15px"></div>

<div style="padding-top:5px;padding-left:30;"><font size=3 color=0074BA><b>��</b></font> <font class=small1 color=444444>����� �Ϸ�Ǹ� <a href="../member/list.php"><font color=0074BA><u>ȸ������Ʈ</u></font></a> ���� ��ϵ� ȸ���� Ȯ���� �� �ֽ��ϴ�.</font></div>



<div style="padding-top:30px"></div>



<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_tip>
<tr>
	<td><img src="../img/icon_list.gif" align=absmiddle><font color=0074BA>ȸ���ʵ弳��</font>
	<div style="width:100%;margin-left:10px;">
	<style>
	#field_table { border-collapse:collapse; }
	#field_table th { padding:4; }
	#field_table td { border-style:solid;border-width:1;border-color:#EBEBEB;color:#4c4c4c;padding:4; }
	#field_table i { color:green; font:8pt dotum; }
	</style>
	<table id="field_table">
	<tr bgcolor="#eeeeee">
		<th><font class=small1 color=444444><b>�ѱ� Ÿ��Ʋ</th>
		<th><font class=small1 color=444444><b>���� Ÿ��Ʋ</th>
		<th><font class=small1 color=444444><b>����</th>
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