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
		
		//ȸ�������� �ȵ� ���, �޽��� �� ��û ����� �˷��ִ� �˾��� ����ش�. 
		alert('�޽��� ���� ��ġ�Ǿ� ���� �ʽ��ϴ�.!');
		popup("../../partner/pc080/download.php?mode=1",500,200);

		// pc080 ������ �Ǿ� �ִ� ���� �ٿ�ε� �������� ȣ���ϸ� ��.
		// window.open('�ٿ�ε�������');

		// pc080 �������� ���� ���
    	// window.open('�޽��� �� ���� ��û �������� �̵�');	


		return false;

   
    }else return true;

}

 


