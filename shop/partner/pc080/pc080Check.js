document.write('<OBJECT ID="MyPC080BIZ"' + 
		'CLASSID="CLSID:D7B800A6-CEE6-4EBA-B41F-8212AFDD43AC"' +
		'width="0" height="0">' +
		'<PARAM NAME="AutoInstall" VALUE="0">' +
		'</OBJECT>');


function check_ActiveX(pid)

{

    try {

        var PC080CTRL;

        PC080CRTL = new ActiveXObject(pid);   			
		
		return true;

    } catch (e) {

        return false;

    }

    

}



function check_obj()

{

var progid = "PC080BIZ.PC080BizCtrl.1";

    if (!check_ActiveX(progid) || !MyPC080BIZ.GetPC080InstallState() ) {        
		
		//회원가입이 안된 경우, 메신저 폰 신청 방법을 알려주는 팝업을 띄워준다. 
		alert('메신저 폰이 설치되어 있지 않습니다.!');
		popup("../../partner/pc080/download.php?mode=1",500,200);

		// pc080 가입이 되어 있는 경우는 다운로드 페이지를 호출하면 됨.
		// window.open('다운로드페이지');

		// pc080 가입하지 않은 경우
    	// window.open('메신저 폰 가입 신청 페이지로 이동');	


		return false;

   
    }else return true;

}

 


