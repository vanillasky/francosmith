			</div>
		</div>
	</div>
	<div id="crm_counsel_area">
		<div id="crm_counsel_title"><img src="../img/CRM_member_counsel_title.jpg" /></div>
		<div id="crm_counsel_info">
		<? if ($m_no){ ?>
		<form name="infoFm" id="infoFm" method="post">
		<input type="hidden" name="page" value="1">
		<input type="hidden" name="m_no" value="<?=$m_no?>">
		<input type="hidden" name="list_close" value="n">
		<input type="hidden" name="sno" value="">
		<input type="hidden" name="c_sno" value="">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<col width="60"><col>
		<tr>
			<td>처리자ID : </td>
			<td><?=$sess['m_id']?></td>
		</tr>
		<tr>
			<td>상담수단</td>
			<td>
			<select name="counsel_Type" >
				<option value="p">전화</option>
				<option value="m">메일</option>
				<option value="h">기타</option>
			</select>
			</td>
		</tr>
		<tr>
			<td style="padding-bottom:10px;">상담시간</td>
			<td style="padding-bottom:10px;"><input type="text" name="regdt" value="<?=date('Y-m-d H:i:s');?>" style="width:100%;"></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-bottom:10px;"><textarea name="contents" style="width:100%;height:200px" class="line"></textarea></td>
		</tr>
		</table>
		<div class="center">
			<img src="../img/CRM_member_counsel_write.jpg" id="counsel_write" class="hand" onclick="view_Request('write',document.infoFm)" />
			<img src="../img/CRM_member_counsel_write.jpg" id="counsel_change" class="hand" onclick="view_Request('change',document.infoFm)" style="display:none;" />
		</div>
		</form>
		<? } else {?>
		선택된 회원이 없습니다.
		<? } ?>
		</div>
	</div>
</div>

<script>
<? if ( preg_match( "/^rental_mxfree/i", $godo[ecCode] ) == 0 && $menu_array[$_SERVER['PHP_SELF']] == 1){ ?>
view_Request('list');
<? } ?>

function div_in(thisID){
	var obj = document.getElementById(thisID);
	if( obj.style.display == '' ) obj.style.display = 'none';
	else obj.style.display = '';
}

function div_out(thisID){
	document.Crm_writeForm.reset();
	document.getElementById(thisID).style.display = 'none';
}

function view(sno,page){
	document.infoFm.c_sno.value = sno;
	document.infoFm.page.value = page;
	view_Request('view',document.infoFm);
}

function div_out(thisID){
	document.getElementById(thisID).style.display = 'none';
}

//ajax 전송!
function view_Request(mode,form){
	var m_no = '<?=$m_no?>';

	if( mode == "list" ){
		Table_close('addTr');
		Table_close('pageTr');
		document.getElementById('_lodingID').style.display = 'block';
		var page = document.infoFm.page.value;
		var get_out = "&m_no=<?=$m_no?>&page="+page;
	}else if( mode == "write" || mode == "change" ){
		var sno = form.c_sno.value;
		var page = form.page.value;
		var counsel_id = '<?=$sess[m_id]?>';
		var contents = form.contents.value.replace(/\n/gi, '%0A');
		var counsel_Type = form.counsel_Type.value;
		var regdt = form.regdt.value;

		if( !contents ){ alert('상담내용을 등록해주세요!');form.contents.focus();return; }

		var get_out = "&m_no=" + m_no +'&counsel_id='+ counsel_id +'&contents='+ encodeURIComponent(contents) +'&counsel_Type='+ counsel_Type +'&regdt='+ regdt+'&sno='+ sno+'&page='+ page;
	}else if( mode == "view" ){
		var sno = form.c_sno.value;
		var get_out = "&m_no=" + m_no +'&sno='+ sno;
	}
	
	var ajax = new Ajax.Request(
		"../member/Crm_view_proc.php?mode="+mode+ get_out +"&dummy="+new Date().getTime(),
		{
		method : 'get',
		onComplete : view_setResponse
		}
	);

}

//ajax 출력!!
function view_setResponse(req){
	var re_ajax = eval( '(' + req.responseText + ')' );
	
	if( re_ajax.mode == "write" ){
		if( re_ajax.insert_sno > 0 ){
			alert('상담내용이 등록되었습니다.');
			location.href = "./Crm_counsel.php?m_no=<?=$m_no?>";
		}else{
			alert('등록 중 오류가 발생하여 다시한번 저장해 주시기 바랍니다.');
		}
	}else if( re_ajax.mode == "view" ){
		var fm = document.infoFm;
		for(i=0; i < fm.counsel_Type.options.length; i++){
			if( fm.counsel_Type.options[i].value == re_ajax.data['counsel_Type'] ) fm.counsel_Type.selectedIndex = i;
		}
		fm.regdt.value = re_ajax.data['regdt'];
		fm.contents.value = re_ajax.data['contents'];
		fm.c_sno.value = re_ajax.data['sno'];

		document.getElementById("counsel_write").style.display='none';
		document.getElementById("counsel_change").style.display='';
		fm.contents.focus();
	}else if( re_ajax.mode == "change" ){
		if (location.href.search('Crm_view.php')>-1) {
			view_Request('list');
			document.infoFm.page.value = '1';
			document.infoFm.sno.value = '';
			document.infoFm.c_sno.value = '';
			document.infoFm.counsel_Type.value = 'p';
			document.infoFm.regdt.value = "<?=date('Y.m.d H:i:s')?>";
			document.infoFm.contents.value = '';

			document.getElementById("counsel_write").style.display = '';
			document.getElementById("counsel_change").style.display = 'none';
		} else {
			location.href = "./Crm_counsel.php?m_no=<?=$m_no?>&page=" + re_ajax.page;
		}
	}else if( re_ajax.mode == "list" ){
		var pageing_len = re_ajax.pageing.length;
		var data_len = re_ajax.data.length;
		var addr_Tr = document.getElementById('addTr');
		document.getElementById('_lodingID').style.display = 'none';
		//리스트 출력start

		if( data_len > 0 ){
			pageing(pageing_len,re_ajax.pageing); //페이징 처리!!
		//	document.getElementById('sou_list_onID').innerHTML = "[리스트보기("+re_ajax.totalCnt+"개)]";

			for ( n = 0; n < data_len; n++ ){
				var oTr = addr_Tr.insertRow(-1);
				oTr.height='30';
				for( f = 0; f < 9; f++ ){
					var oTd = oTr.insertCell(-1);
					if( f == 0 ){   //번호
					oTd.innerHTML = "<div style='cursor:pointer;color:#666666' onclick='view(\""+re_ajax.data[n]['sno']+"\");' title='수정'>" + re_ajax.data[n]['myno'] + "<div>";
					oTd.className ='my_line_no1';
					oTd.style.padding='0 0 0 0';
					oTd.align='center';
					}else if( f == 1 ){   //구분
					oTd.innerHTML = "<img src=../img/item_line1.gif>";
					oTd.className ='my_line';
					oTd.style.padding='0 5 0 5';
					}else if( f == 2 ){   //상담일
					oTd.innerHTML = "<div style='color:#666666'>" + re_ajax.data[n]['regdt'] + "</div>";
					oTd.className ='my_line_no1';
					oTd.style.padding='0 0 0 0';
					oTd.align='center';
					}else if( f == 3 ){   //구분
					oTd.innerHTML = "<img src=../img/item_line1.gif>";
					oTd.className ='my_line';
					oTd.style.padding='0 5 0 5';
					}else if( f == 4 ){   //상담자 ID
					oTd.innerHTML ="<div style='color:#666666'>" +  re_ajax.data[n]['counsel_id'] + "</div>";
					oTd.className ='my_line_no1';
					oTd.style.padding='0 0 0 0';
					oTd.align='center';
					}else if( f == 5 ){   //구분
					oTd.innerHTML = "<img src=../img/item_line1.gif>";
					oTd.className ='my_line';
					}else if( f == 6 ){   //상담내용
					var contents = "<div style='cursor:pointer;font:9pt 굴림;color:#444444' title='내용보기' onclick='div_in(\"memo_cont_"+re_ajax.data[n]['sno']+"\")'>" + re_ajax.data[n]['contents'].substr(0,30) + (re_ajax.data[n]['contents'] != re_ajax.data[n]['contents'].substr(0,30) ? "..." : "") + "&nbsp;<a href='javascript:view(\""+re_ajax.data[n]['sno']+"\",\""+re_ajax.pageing[1]+"\");'><img src='../img/btn_edit_qa.gif' hspace=2></a><div>";
					oTd.innerHTML = contents;
					oTd.className ='my_line_no1';
					oTd.style.padding='0 0 0 8';
					oTd.align='left';
					}else if( f == 7 ){   //구분
					oTd.innerHTML = "<img src=../img/item_line1.gif>";
					oTd.className ='my_line';
					}else if( f == 8 ){   //상담내용
					if( re_ajax.data[n]['counsel_Type'] == "p" ) var counsel_Type = "<div style='font:8pt 돋움;color:#666666'>전화</div>";
					else if( re_ajax.data[n]['counsel_Type'] == "m" ) var counsel_Type = "<div style='font:8pt 돋움;color:#666666'>메일</div>";
					else var counsel_Type = "<div style='font:8pt 돋움;color:#666666'>기타</div>";
					oTd.innerHTML = counsel_Type;
					oTd.className ='my_line_no1';
					oTd.style.padding='0 0 0 0';
					oTd.align='center';
					}
				} // f for end

				var oTr = addr_Tr.insertRow(-1);
				var oTd = oTr.insertCell(-1);
				oTd.colSpan = '9';
				oTd.className ='my_line_no1';
				oTd.innerHTML = "<div id='memo_cont_"+re_ajax.data[n]['sno']+"' style='padding-left:5px;padding:5 60 5 60;font:9pt 굴림;color:#444444;letter-spacing:0;display:none;cursor:pointer;' onclick='div_out(\"memo_cont_"+re_ajax.data[n]['sno']+"\")' title='클릭시닫기'>"+re_ajax.data[n]['contents'].replace(/\n/gi, '<br>')+"</div>";
			} //n for end
		}
	}
}

window.resizeTo(1233,820);
table_design_load();
document.getElementById("crm_area").style.height = document.body.scrollHeight;
</script>