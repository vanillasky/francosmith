<!--

function OpenHelp()
{
	window.open( 'http://ui.teledit.com/Danal/Teledit/Web/Guide.php?IsUseCI='+IsUseCI+'&CIURL='+CIURL+'&BgColor='+BgColor,'HelpWindow','scrollbars=no,width=520,height=700' );
};

function OpenCallCenter()
{
	window.open( 'http://www.danalpay.co.kr/cscenter/cscenter_faq.aspx','CenterWindow','' );
};

document.onmousedown=function(e)
{
	if( typeof(e)!="undefined" )
	{
		click(e);
	}
	else
	{
		click();
	}
}

document.onkeydown=function(e)
{
	if( typeof(e)!="undefined" )
	{
		keypressed(e);
	}
	else
	{
		keypressed();
	}
}

function click(e)
{
	if(e==null)
	{
		if( (event.button==2) || (event.button==3) )
		{
			alert("������ ��ư�� ����Ͻ� �������ϴ�");
		}
	}
	else
	{
		if( (e.button==2) || (e.button==3) )
		{
			alert("������ ��ư�� ����Ͻ� �������ϴ�");
		}
	}
}
	
function keypressed(e)
{
	if(e==null)
	{
		if( event.keyCode == 123 || event.keyCode == 17 )
		{
			event.returnValue = false;
		}
	}
	else
	{
		if( e.which == 17 )
		{
			e.returnValue = false;
		}
	}
}
	
//<![CDATA[
function OrtChange(){
	if( window.orientation == 90 || window.orientation == -90 ){
		$('body').addClass('horizontal');
        }
	else{
		$('body').removeClass('horizontal');
	}

	$(window).bind("orientationchange", function(event){
		if(event.orientation == "portrait"){
			$('body').removeClass('horizontal');
		}       
		else{
			$('body').addClass('horizontal');
		}       
	});
};
//]]>

-->
