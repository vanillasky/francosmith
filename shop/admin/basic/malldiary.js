/*
2006-11-30 kwons
*/

	function diary_Request(date,type){
		var fm = document.malldiary_Fm;
		if( type == 'new' ){
			var diary_title = fm.diary_title.value;
			var diary_content = fm.diary_content.value.replace(/\n/gi, '%0A');
			var ch_mode = fm.ch_mode.value;
			for( i=0; i < fm.diary_alarm.length; i++ ){
				if( fm.diary_alarm[i].checked == true ){ var diary_alarm = fm.diary_alarm[i].value; }
			}
			var get_url = "&diary_title="+diary_title+"&diary_alarm="+diary_alarm+"&ch_mode="+ch_mode+"&diary_content="+diary_content;
		}else{
			if( type != "alarm" ){
				var get_url = '';
				if( type == "delete" || type == "change" ){
					var sno = fm.sno.value;
				}
			}else{
				//알람 설정시!!
				if( document.alarm_Fm.alarmtype_popup.checked == true ) var alarmtype_popup = 'y';
				else var alarmtype_popup = 'n';
				var dday = document.alarm_Fm.dday.options.value;
				if( document.alarm_Fm.alarmtype_sms.checked == true ) var alarmtype_sms = 'y';
				else var alarmtype_sms = 'n';
				var dday_sms = document.alarm_Fm.dday_sms.options.value;
				var dday_smsTime = document.alarm_Fm.dday_smsTime.options.value;
				var phone1 = document.alarm_Fm.phone1.options.value;
				var phone2 = document.alarm_Fm.phone2.value;
				var phone3 = document.alarm_Fm.phone3.value;

				if( alarmtype_sms == 'y' && ( phone2 == '' || phone3 == "" )){ alert('sms번호를 넣어주세요!!');return; }

				var get_url = "&alarmtype_popup="+ alarmtype_popup + "&dday="+ dday + "&alarmtype_sms="+ alarmtype_sms + "&dday_sms="+ dday_sms + "&dday_smsTime="+ dday_smsTime + "&phone1=" + phone1 + "&phone2="+ phone2 +"&phone3=" + phone3;
			}
		}

		var ajax = new Ajax.Request(
			"./malldiary_proc.php?date="+date+"&sno="+sno+"&mode="+type+get_url,
			{
			method : 'get',
			onComplete : diary_setResponse
			}
		);
	}

	function diary_setResponse(req){
		var fm = document.malldiary_Fm;
		//alert(req.responseText);
		var re_ajax = eval( '(' + req.responseText + ')' );
			//등록폼에 날짜 지정!!
			if( re_ajax.mode != "alarm" && re_ajax.mode != "alarm_view" ){
				var yndate = re_ajax.ndate.substr(0,4);
				var mndate = re_ajax.ndate.substr(4,2);
				var dndate = re_ajax.ndate.substr(6,2);
			}

		if( re_ajax.mode == "view" ){
			//등록폼 open
			document.getElementById('malldiary_formID').style.display = 'block';
			document.getElementById('ndateID').innerHTML = yndate+ '년 ' + mndate+ '월 ' + dndate + '일';
			if( re_ajax.data == false ){
				fm.reset();
				fm.diary_content.value = '한글 100자, 영문/숫자는 200자까지';
				fm.ch_mode.value = "new";
				fm.date.value = re_ajax.ndate;
			}else{
				fm.ch_mode.value = "change";
				fm.date.value = re_ajax.ndate;
				fm.sno.value = re_ajax.data['sno'];
				fm.diary_title.value = re_ajax.data['diary_title'];
				fm.diary_content.value = re_ajax.data['diary_content'];

				for( i=0; i < fm.diary_alarm.length; i++ ){
					if( re_ajax.data['diary_alarm'] == fm.diary_alarm[i].value ){ fm.diary_alarm[i].checked = true }
				}
			}
			//알람설정별!!
			if( re_ajax.info['alarmtype_popup'] == 'y' && re_ajax.info['alarmtype_sms'] == 'y' ){
				fm.diary_alarm[0].disabled = false;
				fm.diary_alarm[0].value = 'y';
				document.getElementById('alarmMsgID').innerHTML = "<font color='#0074BA'>팝업창&SMS 모두설정</font>";
			}
			else if( re_ajax.info['alarmtype_popup'] == 'y' && re_ajax.info['alarmtype_sms'] != 'y' ){
				fm.diary_alarm[0].disabled = false;
				fm.diary_alarm[0].value = 'py';
				document.getElementById('alarmMsgID').innerHTML = "<font color='#0074BA'>팝업창 설정</font>";
			}
			else if( re_ajax.info['alarmtype_popup'] != 'y' && re_ajax.info['alarmtype_sms'] == 'y' ){
				fm.diary_alarm[0].disabled = false;
				fm.diary_alarm[0].value = 'sy';
				document.getElementById('alarmMsgID').innerHTML = "<font color='#8A21FF'>SMS 설정</font>";
			}
			else{
				document.getElementById('alarmMsgID').innerHTML = "<font color='#0074BA'>알람기본설정 미등록</font>";
				fm.diary_alarm[0].disabled = true;
				fm.diary_alarm[1].checked = true;
				fm.diary_alarm[0].value = 'n';
			}
		}

		//등록 후 리턴
		else if( re_ajax.mode == "new" ){
			if( re_ajax.ch_mode == "new" ) alert(yndate+ '년' +mndate+ '월' +dndate + '일 일정 등록되었습니다.');
			else alert(yndate+ '년' +mndate+ '월' +dndate + '일 일정 수정되었습니다.');
			if( re_ajax.sms_sendok == 'n' ) alert("이번 등록 또는 수정건에대한 sms알람서비스는 \n기본설정이 오늘날짜보다 적어 발송되지 않습니다.");
			mndate = ((eval(mndate) * eval(10) ) / eval(10)) -1;
			var m_infoDate = yndate + '/' + mndate;
			diary_Request(m_infoDate,'month_info');
			div_close('malldiary_formID');
		}

		//해당월에 등록된 일 불러오기
		else if( re_ajax.mode == "month_info" ){

			var data_len = re_ajax.data.length;
			if( data_len > 0 ){
				for ( n = 0; n < data_len; n++ ){
					var date_ID = re_ajax.data[n]['diary_date'];
//					document.getElementById('dayst_'+date_ID).style.border = '1px #60B900 solid';
					//document.getElementById('dayst_'+date_ID).bgColor = '#E2F7E3';
					document.getElementById('dayst_'+date_ID).className = "content_img";
					document.getElementById('dayst_'+date_ID).style.color = '#333333';
//					document.getElementById('day_'+date_ID).innerHTML = "<font color='#3F85C2'>☎</font>";
					//document.getElementById('today_msg_'+date_ID).innerHTML = "<table width='150' border='0' style='border:2px #BDBDBD solid' bgcolor='#ffffff'><tr><td style='font-family:돋움;font-size:8pt;color:444444;letter-spacing:0'>"+re_ajax.data[n]['diary_title']+"</td></tr></table>";
					nd = new Date();
					if( nd.getDate() == date_ID ){
						document.getElementById('leftDiaryview_'+date_ID).innerHTML = "<b>"+re_ajax.data[n]['diary_title'] +"</b><br><textarea name='ndayCont' style='width:150;height:130;border:0px;overflow-y:hidden;' readOnly>"+re_ajax.data[n]['diary_content']+"</textarea>";
					}
				}
			}
		}

		else if( re_ajax.mode == "delete" ){ //삭제
			alert('정상삭제되었습니다.');
			document.getElementById('dayst_'+dndate).bgColor = '#ffffff';
			div_close('malldiary_formID');
		}

		else if( re_ajax.mode == "alarm" ){ //알람등록!!
			alert("알람설정되었습니다.");
			Alarm_info(re_ajax.data,re_ajax.mode);
		}

		else if( re_ajax.mode == "alarm_view" ){ //알람설정보기
			alarm_info('alarm_formID');
			if( re_ajax.godosms <= 0 ){
				document.alarm_Fm.alarmtype_sms.disabled = true;
				document.alarm_Fm.alarmtype_sms.checked = false;
			}else{
				document.alarm_Fm.alarmtype_sms.disabled = false;
			}
			Alarm_info(re_ajax.data,re_ajax.mode);
		}
		else alert(re_ajax.mode + '잘못된 리턴값입니다.');
	}


	function Alarm_info(date,type){
		fm = document.alarm_Fm;
		//관리자 로그인시!!
		if( date['alarmtype_popup'] == "y" ) fm.alarmtype_popup.checked = true;
		else fm.alarmtype_popup.checked = false;
		//관리자 로그인 일정
		for( j=0; j < fm.dday.options.length; j++ ){
			if( fm.dday.options[j].value == date['dday'] ) fm.dday.selectedIndex = j;
		}
		//sms 알림설정
		if( date['alarmtype_sms'] == "y" ) fm.alarmtype_sms.checked = true;
		else fm.alarmtype_sms.checked = false;
		//sms 일정
		for( d=0; d < fm.dday_sms.options.length; d++ ){
			if( fm.dday_sms.options[d].value == date['dday_sms'] ) fm.dday_sms.selectedIndex = d;
		}
		//sms 시간
		for( t=0; t < fm.dday_smsTime.options.length; t++ ){
			if( fm.dday_smsTime.options[t].value == date['dday_smsTime'] ) fm.dday_smsTime.selectedIndex = t;
		}
		//sms 전화번호
		if( type == 'alarm_view' ){

			var phone1 = date['phone'].substr(0,3);
			var phone2 = date['phone'].substr(3,4);
			var phone3 = date['phone'].substr(7,4);
			for( p=0; p < fm.phone1.options.length; p++ ){ if( fm.phone1.options[p].value == phone1 ) fm.phone1.selectedIndex = p; }
			fm.phone2.value = phone2;
			fm.phone3.value = phone3;
		}else{
			for( p=0; p < fm.phone1.options.length; p++ ){ if( fm.phone1.options[p].value == date['phone1'] ) fm.phone1.selectedIndex = p; }
			fm.phone2.value = date['phone2'];
			fm.phone3.value = date['phone3'];
		}
	}

	function alarm_form(thisID){
		document.getElementById('malldiary_formID').style.display = 'none';
		diary_Request('',thisID);
	}

	//각일 마우스롤오버시 보더컬러변경
	function onMover(obj){
		document.getElementById('Mover_div_'+obj).style.display = "block";
		//document.getElementById('dayst_'+obj).style.border = '1px #F15A23 solid';
	}
	function onMout(obj){
		//document.getElementById('dayst_'+obj).style.border = '1px #dcdcdc solid';
		document.getElementById('Mover_div_'+obj).style.display = "none";
	}

	now=new Date();
	static_now=new Date();
	week = new Array();
	week['0'] = "<img src='../img/sch_sun.gif'>";
	week['1'] = "<img src='../img/sch_mon.gif'>";
	week['2'] = "<img src='../img/sch_tue.gif'>";
	week['3'] = "<img src='../img/sch_wed.gif'>";
	week['4'] = "<img src='../img/sch_thu.gif'>";
	week['5'] = "<img src='../img/sch_fri.gif'>";
	week['6'] = "<img src='../img/sch_sat.gif'>";
	kor_Week = new Array('일','월','화','수','목','금','토');
	int_Week = now.getDay();
	eng_Month = new Array('January','February','March','April','May','June','July','August','September','October','November','December');

	//달력함수
	function calender(val,element_name,type,getFullYear,getMonth){
	var p;
	var z=0;

	if( type != "select" ){
		switch(val){
			case 1:now.setFullYear(now.getFullYear()-1);break;
			case 2:now.setMonth(now.getMonth()-1);break;
			case 3:now.setMonth(now.getMonth()+1);break;
			case 4:now.setFullYear(now.getFullYear()+1);break;
		}
	}else{
		now.setFullYear(getFullYear);
		now.setMonth(getMonth);
	}

	var now_scY = now.getFullYear()+"";
	var sc;
	sc = "<div onclick=\"calender(1,'"+element_name+"','button')\" style=\"cursor:pointer;float:left;padding-top:4px;\"><img src='../img/sch_btn_left_s.gif' border=0 align=absmiddle></div>";
	sc+= "<div style=\"float:left;font-size:8pt;letter-spacing:0px;font-family:tahoma;color:#EA1D5C\">" + now_scY + "</div>";
	sc+="<div onclick=\"calender(4,'"+element_name+"','button')\" style=\"cursor:pointer;float:left;padding-top:4px;\"><img src='../img/sch_btn_right_s.gif' border=0 align=absmiddle></div>";

	var now_scM = "<div style='font-size:40px;font-family:verdana;'>" + (now.getMonth()+1) + "</div>";
	var scML="<span onclick=\"calender(2,'"+element_name+"','button')\" style=\"cursor:pointer;\" ><img src='../img/sch_btn_left.gif' border=0></span>";
	var scMR="<span onclick=\"calender(3,'"+element_name+"','button')\" style=\"cursor:pointer;\"><img src='../img/sch_btn_right.gif' border=0></span>";


	var NowYear = now.getFullYear();
	var NowMonth = now.getMonth();
	var m_infoDate = NowYear+'/'+NowMonth;
	//해당 달에 저장되어져 있는 데이터값을 가지고옴! -- start
	diary_Request(m_infoDate,'month_info');

	//해당월 마지막 일자
	last_date = new Date(now.getFullYear(),now.getMonth()+1,1-1);

	//해당월 처음일자 요일
	first_date= new Date(now.getFullYear(),now.getMonth(),1);

	//스킨
	calender_area="<table cellspacing='0' cellpadding='0' width='98%' height='200' border='0' align=center>";
	calender_area+="<tr>";
	calender_area+="<td width='150'>";

	calender_area+="<table cellspacing='0' cellpadding='0' width='100%' border='0'>";
	calender_area+="<tr><td height='18' style='padding-left:55px'>"+ sc +"</td></tr>";

	calender_area+="<tr><td valign='top'>";
	calender_area+="<table width='100%' cellspacing='0' cellpadding='0' border='0'>";
	calender_area+="<tr><td align='center' valign='bottom'>"+now_scM+"</td></tr>";
	calender_area+="<tr>";
	calender_area+="<td style='line-height: 45px;font-size:22px;font-family:tahoma;letter-spacing:0px;' align='center'>"+scML+ eng_Month[now.getMonth()] +scMR+"</td>";
	calender_area+="</tr>";
	calender_area+="<tr><td style='font-size:12px;letter-spacing:0px;' align='center'>"+static_now.getFullYear()+"."+(static_now.getMonth()+1)+"."+static_now.getDate()+" ("+kor_Week[int_Week]+")"+"  <span id='nTimesID'></span></td></tr>";
	calender_area+="</table></td></tr>";

	calender_area+="<tr><td onclick='alarm_form(\"alarm_view\");' style=\"cursor:pointer;\" style='padding-top:10px;' align='center'><img src='../img/sch_btn_alram.gif' border=0></td></tr>";
	calender_area+="</table>";

	calender_area+="</td>";
	calender_area+="<td width='14' background='../img/sch_line_left.gif'></td>";
	calender_area+="<td width='5'></td>";
	calender_area+="<td align='center'>";
	calender_area+="<table cellpadding='0' cellspacing='0' border='0'> ";
	calender_area+="<tr>";

		//요일표시
		var color='#BABABA';
		for(i=0;i<week.length;i++){
			calender_area+="<td height='18' align='center'><b>"+week[i]+"</b></td>";
		}
			calender_area+="</tr><tr>";

		for(i=1;i<=first_date.getDay();i++){
			calender_area+="<td width='32'>&nbsp;</td>";
		}

		z=(i-1);
		var nday = '1';
		for (i=1;i<=last_date.getDate();i++)
		{
			z++;
			p=z%7;
			var pmonth=now.getMonth()+1;
			if(i<10){var ii="0"+i;}else{var ii=i;}
			if(pmonth<10){pmonth="0"+pmonth;}

			// td에 공동으로 들어가는 값
			//var mouse_st = "onmouseover=\"today_msgView('today_msgID_"+i+"');onMover('"+ii+"');\" onmouseout=\"today_msgOff('today_msgID_"+i+"');onMout('"+ii+"');\" ";
			var mouse_st = " onclick=\" today_msgView('today_msgID_"+i+"');onMout('"+ii+"'); \"";
			var Tdopt = "align='center' style='cursor:pointer;font:14px tahoma;letter-spacing:0px;' id='dayst_"+ii+"' onclick=\"showData('"+now.getFullYear()+"','"+pmonth+"','"+ii+"')\"";

			//등록괸 일정 메시지div

			var today_msg = "<div style='position:relative;'><div style='display:none;position:absolute;top:0;left:-80;' id='today_msgID_"+i+"'>";
			today_msg += "<table width='100' border='0' cellspacing='0' cellpadding='0'>";
			today_msg += "<tr><td id='today_msg_"+ii+"'></td></tr></table>";
			today_msg += "</div></div>";


			//마우스롤오버시
			var Mover_div = "<div style='position:relative;'><div style='display:none;position:absolute;top:0;left:-20;' id='Mover_div_"+ii+"'><font color='#F15A23'><b>*</b></font></div></div>";

			//오늘 td
			if(i == now.getDate() && now.getFullYear()==static_now.getFullYear() && now.getMonth()==static_now.getMonth()) {
				nday = ii;
				calender_area+="<td width='32' height='28' background='../img/sch_bg_green.gif'"+ Tdopt + mouse_st +" style='color:#ffffff;'><B>"+Mover_div+i+"</B><span style='color:#ffffff' id='day_"+ii+"'></span>"+today_msg+"</td>";
			}
			else if( p == 0 ){ //토요일
				calender_area+="<td width='32' height='28' "+ Tdopt + mouse_st + "><B>"+Mover_div+i+"</B><span style='color:#ffffff' id='day_"+ii+"'></span>"+today_msg+"</td>";
			}else if( p == 1 ){ //일요일
				calender_area+="<td width='32' height='28' "+ Tdopt + mouse_st + " style='color:#EA1D5C;'><B>"+Mover_div+i+"</B><span style='color:#ffffff' id='day_"+ii+"'></span>"+today_msg+"</td>";
			}else{ //평일
				calender_area+="<td width='32' height='28' "+ Tdopt + mouse_st + "><B>"+Mover_div+i+"</B><span style='color:#ffffff' id='day_"+ii+"'></span>"+today_msg+"</td>";
			}

			if(p==0 && last_date.getDate() != i){calender_area+="</tr><tr>";}
		}

		if(p !=0){
			for(i=p;i<7;i++){
					calender_area+="<td width='32'>&nbsp;</td>";
			}
		}

		//스킨
		calender_area+="</tr></table>";
		calender_area+="</td>";
		calender_area+="<td width='5'></td>";
		calender_area+="<td width='14' background='../img/sch_line_right.gif'></td>";
		calender_area+="<td width='155'>";
		calender_area+="<table width='100%' cellpadding='0' cellspacing='0' border='0'><tr>";
		calender_area+="<td style='padding-top:10px;padding-left:4px;'><img src='../img/sch_today.gif'></td>";
		calender_area+="<tr><td height='150' style='padding:5px 3px 3px 3px;' valign='top'><span id='leftDiaryview_"+nday+"'>등록하신 오늘의 스케줄이<br>없습니다.</span></td></tr>";
		//calender_area+="<tr><td><img src='../img/sch_btn_more.gif'></td></tr>";
		calender_area+="</table>";
		calender_area+="</td>";
		calender_area+="</tr></table>";
		s_area.innerHTML = calender_area;
	}

	function review(thisID){
	}


	function today_msgView(thisID){
		document.getElementById(thisID).style.display = 'block';
	}

	function today_msgOff(thisID){
		document.getElementById(thisID).style.display = 'none';
	}

	function check_mouse(val){

		calender('not',val);
		s_area.style.visibility="visible";
		s_area.style.left=event.clientX-10;
		s_area.style.top=event.clientY+0;
	}
	function change_date(val,element_name){

		eval(element_name).value=val;
		s_area.style.visibility="hidden";
	}

	//글쓰기 창 호출!!
	function showData(y,m,d){
		var date = y + m + d;
		diary_Request(date,'view');
	}



	function form_check(){
		var fm = document.malldiary_Fm;
		var date = fm.date.value;
		if(fm.diary_title.value==""){alert("제목을 입력해주세요!!");fm.diary_title.focus();return;}
		else if(fm.diary_content.value==""){alert("내용을 입력해주세요!!");fm.diary_content.focus();return;}
		else diary_Request(date,'new');
	}

	function datelist(year,month){
		alert(year+'/'+month);
	}

	function del(){
		var fm=document.malldiary_Fm;
		var ndate = fm.date.value;
		if( fm.sno.value == '' ){ alert('등록된 일정이 있어야 삭제가 가능합니다.');return;}

		if(confirm('삭제하시면 데이터를 복구할 수 없습니다.\n\n삭제하시겠습니까?')){
			diary_Request(ndate,'delete');
		}
		else return;
	}

	//지금의 시간 불러오기
	function ntime(){
		now=new Date();
		new_hou = now.getHours();
		new_min = now.getMinutes();
		new_sec = now.getSeconds();
		if( new_hou < 12 || new_hou == 24 ) var kerDay = "오전";
		else var kerDay = "오후";

		var return_Time = "<b>" + kerDay +' '+new_hou+':'+new_min+':'+new_sec + "</b>";
		document.getElementById("nTimesID").innerHTML = return_Time;
	}

	//해당 시간 계속 호출!

	function time_Loop(){
		var appname = navigator.appName.charAt(0);

		if( appname == "M" ) window.setInterval("ntime()",1);
		else window.setInterval(ntime,1);
	}

	 function CheckLen( form ) {
		var t;
		var msglen;
		var prelen = 0;
		msglen = 0;
		l = form.diary_content.value.length;
		for( k = 0; k < l; k++ ) {

		  if(msglen < 201 ) prelen = k;

		  t = form.diary_content.value.charAt( k );
		  if ( escape( t ).length > 4 ) {
			msglen += 2;
		  }
		  else {
			msglen++;
		  }
		}

		if(msglen > 200 ){
			alert('한글은 100자 까지 영문/숫자/기호는 200자 까지 가능합니다. \n다시 확인 후 작성해 주세요!');
			var msgStr=form.diary_content.value;
			var cutStr = msgStr.substr(0,prelen);
			form.diary_content.value=cutStr;
		}

	}

	function div_close(thisID){
		document.getElementById(thisID).style.display = 'none';
	}

	function contents_close(opt){
		var fm = document.malldiary_Fm;
		if( fm.ch_mode.value == "new" ) fm.diary_content.value = "";
	}

	function alarm_info(thisID){
		document.getElementById(thisID).style.display = 'block';
	}

	function alarm_pop(thisDay){
		window.open("malldiary_popup.php?thisDay="+thisDay,"_alarm","width=300, height=237,scrollbars=yes,top=200,left=200");
	}



