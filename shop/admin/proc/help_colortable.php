<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: 색상표
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/
?>
<html>
<head>
<title>색상표</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<SCRIPT language=javascript>

function init(){
//        if (opener.chwin)        statusInit();
        color=new colorSet(24,10,20);
        drawSwatches();
        color=new colorSet(23,10,20);
        drawPallete(3,0);
        document.getElementById('COLORTABLE').onmousemove=palleteStatus;
		document.getElementById('COLORTABLE').onmousedown=palleteSelect;
		document.getElementById('ADJUST').onmousemove=adjustStatus;
		document.getElementById('ADJUST').onmousedown=adjustSelect;
		document.getElementById('SWATCHES').onmousemove=swatchStatus;
		document.getElementById('SWATCHES').onmousedown=swatchSelect;
}
function test(){
        document.status.red.value=event.clientX;
}
function targetSelect(){
        obj = event.srcElement || event.target;
        if (obj.tagName=="BUTTON" && obj.className=='outset'){
                document.getElementById(color.target).className='outset';
                obj.className='inset';
                color.target=obj.id;
        }
}
function drawSwatches(){
        cols = color.cells+1;
        html= "<table cellpadding=0 bgcolor=black cellspacing=1 border=0 height=55 width="+ (cols*10 +25) +">";
        z=0;
        color.base=new Array(0,1,2);
        range=getRange(0,255,4);
        for (i=0;i<=4;i++){
                for (j=0;j<=4;j++){
                        for (k=0;k<=4;k++){
                                if (z% cols ==0)        html +="<tr height="+ (color.cellSize -1) +">";
                                setColor(range[i],range[j],range[k]);
                                html += getPalleteCells();
                                z++;
                                if (z% cols ==0)        html +="</tr>";
                        }
                }
        }
        if (z% cols !=0)        html+="</tr>";
        html +="</table>";
        paintPallete('SWATCHES');
}
function swatchSelect(){
        obj = event.srcElement || event.target;
        if (obj.tagName !="TD")        return;
        if (obj.bgColor.indexOf("#")==0)        color.hexValue=obj.bgColor.substring(1);
        else                                        color.hexValue=obj.bgColor;
        applySelection();

}
function swatchStatus(){
        obj = event.srcElement || event.target;
        if (obj.tagName !="TD")        return;
        if (obj.bgColor.indexOf("#")==0)        hexValue=obj.bgColor.substring(1);
        else                                        hexValue=obj.bgColor;
        document.getElementById('SAMPLE').style.backgroundColor=hexValue;
}
function drawPallete(mode,value){
        color.mode  = mode;
        color.value = value;
        colorInit();
        drawTable();
        drawAdjust();
}
function colorInit(){
        if (color.mode <=2){
                initRgbBase(color.value);
                setCurrentColor(color.value,0,0);
                color.degree=getHueDegree(color.current);
        }
        else if (color.mode ==3){
                color.degree=color.value;
                initHueBase(color.degree);
                setCurrentColor(255,color.hueValue,0);
        }
        else{
                 color.degree=changeScale(color.value,255,360);
                initHueBase(0);
                setCurrentColor(255,color.hueValue,0);
        }
}
function drawTable(){
        html= color.table;
        range=getRange(0,255,color.cells);
        for (i=0;i<=color.cells;i++)        html +="<col width=" + color.cellSize+ ">";
        if (color.mode <=2){
                for (i=color.cells;i>=0;i--){
                        html += color.tr;
                        for (j=0;j<=color.cells;j++){
                                setColor(color.value,range[i],range[j]);
                                html += getPalleteCells();
                        }
                        html +="</tr>";
                }
        }
        else if (color.mode ==3){
                end   = getRange(0,color.hueValue,color.cells);
                for (i=color.cells;i>=0;i--){
                        html += color.tr;
                        for (j=color.cells;j>=0;j--){
                                setColor(range[i],getCoord(end[i],range[i],j,color.cells),getCoord(0,range[i],j,color.cells));
                                html += getPalleteCells();
                        }
                        html +="</tr>";
                }
        }
        else if (color.mode ==4){
                for (i=color.cells;i>=0;i--){
                        html += color.tr;
                        for (j=0;j<=color.cells;j++){
                                degree=changeScale(j,(color.cells+1),360);
                                initHueBase(degree)
                                max=getCoord(color.hueValue,255,color.value,255);
                                setColor(range[i],getCoord(0,max,i,color.cells),getCoord(0,color.value,i,color.cells));
                                html += getPalleteCells();
                        }
                        html +="</tr>";
                }
        }
        else if (color.mode ==5){
                for (i=0;i<=color.cells;i++){
                        html += color.tr;
                        for (j=0;j<=color.cells;j++){
                                degree=changeScale(j,(color.cells+1),360);
                                initHueBase(degree)
                                min=getCoord(0,color.hueValue,color.value,255);
                                setColor(color.value,getCoord(min,color.value,i,color.cells),getCoord(0,color.value,i,color.cells));
                                html += getPalleteCells();
                        }
                        html +="</tr>";
                }
        }
        html +="</table>";
        paintPallete("COLORTABLE");
}
function palleteSelect(){
        applySelection();
        color.current=new Array(color.rgb[0],color.rgb[1],color.rgb[2]);
        if (color.mode !=3)        drawAdjust();
}
function drawAdjust(){
        html  = color.table+color.col;
        if (color.mode <=2){
                for (i=color.cells;i>=0;i--){
                        setColor(getCoord(0,255,i,color.cells),color.current[1],color.current[2]);
                        html +=getAdjustCells();
                }
        }
        else if (color.mode ==3){
                for (i=color.cells;i>=0;i--){
                        degree=changeScale(i,(color.cells+1),360);
                        initHueBase(degree);
                        setColor(255,color.hueValue,0);
                        html +=getAdjustCells();
                }
        }
        else if (color.mode ==4){
                for (i=0;i<=color.cells;i++){
                        setColor(color.current[color.base[0]],getCoord(color.current[color.base[1]],color.current[color.base[0]],i,color.cells),getCoord(0,color.current[color.base[0]],i,color.cells));
                        html +=getAdjustCells();
                }
        }
        else if (color.mode ==5){
                range = getRange(0,255,color.cells);
                for (i=color.cells;i>=0;i--){
                        setColor(range[i],getCoord(0,color.current[color.base[1]],i,color.cells),getCoord(0,color.current[color.base[2]],i,color.cells));
                        html +=getAdjustCells();
                }
        }
        html +="</table>";
        paintPallete("ADJUST");
}
function palleteStatus(){
        obj=document.getElementById('COLORTABLE');
        crdX=changeScale(event.clientX-obj.offsetLeft,obj.clientWidth,255);
        crdY=changeScale(event.clientY-obj.offsetTop,obj.clientHeight,255);
        if (color.mode <=2){
                setColor(color.value,255-crdY,crdX);
                color.degree=getHueDegree(color.rgb);
        }
        else if (color.mode ==3){
                crdY=255-crdY;
                crdX=255-crdX;
                initHueBase(color.value);
                start = getCoord(0,color.hueValue,crdY,255);
                setColor(crdY,getCoord(start,crdY,crdX,255),getCoord(0,crdY,crdX,255));
        }
        else if (color.mode ==4){
                crdY=255-crdY;
                color.degree=changeScale(crdX,255,360);
                initHueBase(color.degree);
                max=getCoord(color.hueValue,255,color.value,255);
                setColor(crdY,getCoord(0,max,crdY,255),getCoord(0,color.value,crdY,255));
        }
        else if (color.mode ==5){
                color.degree=changeScale(crdX,255,360);
                initHueBase(color.degree);
                min=getCoord(0,color.hueValue,color.value,255);
                setColor(color.value,getCoord(min,color.value,crdY,255),getCoord(0,color.value,crdY,255));
        }
        applyColor();
}
function adjustStatus(){
        obj=document.getElementById('ADJUST');
		var eventY = (event.y) ? event.y : event.clientY;
		crdY=changeScale((eventY-obj.offsetTop),obj.clientHeight,255); 

        if (color.mode <=2){
                color.adjust = 255-crdY;
                setColor(color.adjust,color.current[color.base[1]],color.current[color.base[2]]);
                color.degree=getHueDegree(color.rgb);
        }
        else if (color.mode ==3){
                color.degree=color.adjust = changeScale(255-crdY,255,360);
                initHueBase(color.adjust);
                setColor(255,color.hueValue,0);
        }
        else if (color.mode==4){
                color.adjust=crdY;
                setColor(color.current[color.base[0]],getCoord(color.current[color.base[1]],color.current[color.base[0]],crdY,255),getCoord(color.current[color.base[2]],color.current[color.base[0]],crdY,255));
        }
        else if (color.mode==5){
                color.adjust=255-crdY;
                setColor(getCoord(0,color.current[color.base[0]],color.adjust,255),getCoord(0,color.current[color.base[1]],color.adjust,255),getCoord(0,color.current[color.base[2]],color.adjust,255));
        }
        applyColor();
}
function applyColor(){
        color.hexValue=getHexValue();
        document.status.red.value=color.rgb[0];
        document.status.green.value=color.rgb[1];
        document.status.blue.value=color.rgb[2];
        document.status.current.value=color.hexValue;
        document.status.bri.value=changeScale(color.rgb[0],255,100);
        document.status.hue.value=color.degree;
        document.getElementById('SAMPLE').style.backgroundColor=color.hexValue;
        document.status.sat.value=getSatPercent();
}
function getSatPercent(){
        return changeScale((255 - color.rgb[color.base[2]]),255,100);
}
function applySelection(){
        obj=document.getElementById('SELECT');
        if (color.target=='bBgColor'){
                obj.style.backgroundColor=color.hexValue;
                document.status.background.value=color.hexValue;
        }
        else{
                obj.style.backgroundColor=color.hexValue;
                document.status.text.value=color.hexValue;
				<? if ($_GET['callback']) {?>
					if (opener && opener.<?=$_GET['callback']?>) opener.<?=$_GET['callback']?>(color.hexValue);
				<? } ?>
        }
}
function statusInit(){
        obj=document.getElementById('SELECT');
        name=opener.chwin.curName;
        obj.style.backgroundColor=opener.chwin.channel[name].color;
}

function adjustSelect(){
        applySelection();
        color.value = color.adjust;
        drawTable();
}
function colorSet(cells,cellSize,adjustSize){
        this.rgb =new Array();
        this.current=new Array();
        this.cells =cells;
        this.cellSize=cellSize;
        this.adjustSize=adjustSize;
        this.curAdjust =0;
        this.hueMode =0;
        this.table="<table border=0 cellspacing=0 cellpadding=0 height="+ (cells+1)*cellSize +">";
        this.col ="<col width="+ adjustSize+">";
        this.tr ="<tr height="+ cellSize+">";
        this.target='bTextColor';

}
function getPalleteCells(){
        return "<td bgcolor="+ getHexValue() + "></td>";
}
function getHexValue(){
        return hex(color.rgb[0])+hex(color.rgb[1])+hex(color.rgb[2]);
}
function getAdjustCells(){
        return color.tr + getPalleteCells()+"</tr>";
}
function setCurrentColor(base0,base1,base2){
        color.current[color.base[0]]=base0;
        color.current[color.base[1]]=base1;
        color.current[color.base[2]]=base2;
}
function setColor(base0,base1,base2){
        color.rgb[color.base[0]]=base0;
        color.rgb[color.base[1]]=base1;
        color.rgb[color.base[2]]=base2;
}
function paintPallete(id){
        colorTable=document.getElementById(id);
        colorTable.innerHTML='';
        colorTable.innerHTML=html;
}
function getCoord(start,end,pos,cellNum){
        return  start + parseInt( (end-start)*pos/cellNum);
}
function initRgbBase(value){
        if (color.mode==0)                color.base=new Array(0,1,2);
        else if (color.mode==1)                color.base=new Array(1,0,2);
        else                                color.base=new Array(2,1,0);
}
function getHueDegree(rgb){
        r=rgb[0];
        g=rgb[1];
        b=rgb[2];
        if (r==b && r==g && b==g)        return 0;
        else if (r==b)                        return 300;
        else if (r==g)                        return 60;
        else if (b==g)                        return 180;
        else{
                if (r>g && r>b){
                        if (g>b)        base=new Array(0,1,2);
                        else                base=new Array(0,2,1);
                }
                else if (g>r && g>b){
                        if (b>r)        base=new Array(1,2,0);
                        else                base=new Array(1,0,2);
                }
                else if (b>g && b>r){
                        if (r>g)        base=new Array(2,0,1);
                        else                base=new Array(2,1,0);
                }
                f=rgb[base[0]];
                v=rgb[base[1]];
                n=rgb[base[2]];
                baseDegree=base[0]*120;
                if (n>v)        degree = baseDegree - parseInt(60*(n-v)/(f-v));
                else                degree = baseDegree + parseInt(60*(v-n)/(f-n));
                if (degree <0)        degree +=360;
                return degree;
        }
}
function initHueBase(degree){
        degree=degree % 360;
        if (degree < 60)        color.base=new Array(0,1,2);
        else if (degree < 120)        color.base=new Array(1,0,2);
        else if (degree < 180)        color.base=new Array(1,2,0);
        else if (degree < 240)        color.base=new Array(2,1,0);
        else if (degree < 300)        color.base=new Array(2,0,1);
        else if (degree < 360)        color.base=new Array(0,2,1);
        huePos=degree%60;
        if (degree %120 >= 60)        huePos=60 - huePos;
        color.hueValue = changeScale(huePos,60,255);
}
function changeScale(value,oldScale,newScale){
        newValue= parseInt(value*newScale/oldScale);
        if (newValue >newScale)        newValue=newScale;
        return newValue;
}
function getRange(start,end,cellNum){
        section=new Array();
        for (k=0;k<=cellNum;k++)        section[k]=getCoord(start,end,k,cellNum);
        return section;
}
function hex (dec) {
        var Hexstring = "0123456789ABCDEF";
        var a = dec % 16;
        var b = (dec - a)/16;
        value = "" + Hexstring.charAt(b) + Hexstring.charAt(a);
        return value;
}
function applyChannelColor(){
        obj=document.getElementById('SELECT');
        name = opener.chwin.curName;
        opener.chwin.channel[name].color=obj.style.backgroundColor;
//        opener.oc.color('fcolor','',obj.style.backgroundColor);
        self.close();
}
<?if($_GET['iconidx'] != '' && $_GET['target']  != '' ){?>
function goodsicon(){
	try
	{
		opener.document.getElementsByName('<?=$_GET['target']?>[]')[<?=$_GET['iconidx']?>].value = document.getElementsByName('text')[0].value;
	}
	catch (e)
	{
		opener.document.getElementsByName('<?=$_GET['target']?>[<?=$_GET['iconidx']?>]')[0].value = document.getElementsByName('text')[0].value;
	}
	self.close();
}
<?}?>
<?if($_GET['btnCallback']  != '' ){?>
function btnCallback() {
	if (opener && opener.<?=$_GET['btnCallback']?>) opener.<?=$_GET['btnCallback']?>(document.getElementsByName('text')[0].value);
}
<?}?>

// firefox event 호환
if(navigator.userAgent.indexOf('Firefox') >= 0){
	var eventArray = new Array("click","mousemove","mousedown");  
	for (var i = 0; i < eventArray.length; i++){
		window.addEventListener(eventArray[i], function(e){
			window.event = e;
		}, true);
	}
};
</SCRIPT>

<STYLE type=text/css>
body,table,input { font:9pt tahoma; color:#000000; }
td { min-width:10px; }
#SUBJECT {  Z-INDEX: 3; LEFT: 0px; WIDTH: 400px; POSITION: absolute; TOP: 0px; HEIGHT: 24px }
#CLOSE { Z-INDEX: 2; LEFT: 0px; WIDTH: 400px; POSITION: absolute; TOP: 390px; HEIGHT: 15px }
#SWATCHES { Z-INDEX: 2; LEFT: 10px; WIDTH: 275px; POSITION: absolute; TOP: 330px; HEIGHT: 54px }
#COLORTABLE {  Z-INDEX: 2; LEFT: 10px; WIDTH: 242px; CURSOR: hand; POSITION: absolute; TOP: 80px; HEIGHT: 242px }
#ADJUST {  Z-INDEX: 2; LEFT: 260px; WIDTH: 20px; POSITION: absolute; TOP: 80px; HEIGHT: 240px }
#RANGE { Z-INDEX: 2; LEFT: 290px; WIDTH: 105px; POSITION: absolute; TOP: 80px; HEIGHT: 125px }
#STATUS {   Z-INDEX: 2; LEFT: 290px; WIDTH: 100px; POSITION: absolute; TOP: 208px; HEIGHT: 178px;  BACKGROUND-COLOR: #cccccc }
#SAMPLE {  Z-INDEX: 2; LEFT: 400px; WIDTH: 90px; POSITION: absolute; TOP: 80px; HEIGHT: 55px; BACKGROUND-COLOR: white }
#SELECT {  Z-INDEX: 2; LEFT: 400px; WIDTH: 90px; POSITION: absolute; TOP: 90px; HEIGHT: 55px; BACKGROUND-COLOR: white }
.inset {  BORDER-RIGHT: #69bbe7 1px solid; BORDER-TOP: #292929 1px solid; BORDER-LEFT: #292929 1px solid; BORDER-BOTTOM: #69bbe7 1px solid }
.outset { BORDER-RIGHT: #292929 1px solid; BORDER-TOP: #69bbe7 1px solid; BORDER-LEFT: #69bbe7 1px solid; BORDER-BOTTOM: #292929 1px solid }
</STYLE>
</HEAD>
<BODY onload=init()>
<DIV id=SUBJECT>
<table cellspacing=0 cellpadding=0 width="100%" border=0>
  <tr>
    <td align=center>
      <table width="95%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td background="../img/pop_titlebg.gif" align=center><img src="../img/ptitle_colorchart.gif" alt="색상표" border="0" align="absmiddle"></td>
        </tr>
      </table>
    </td>
  </tr>
</TABLE>
    </DIV>
 <DIV id=RANGE>
<TABLE height="100%" width=96% cellSpacing=0 cellPadding=0 border=0>
<tr><td class=text1 style="font:7pt verdana"><input type=button onclick=drawPallete(0,255) style="border-width:1px; border-color:#366670; border-style:solid;font:7pt">&nbsp;RED</td></tr>
<tr><td class=text1 style="font:7pt verdana"><input type=button onclick=drawPallete(1,255) style="border-width:1px; border-color:#366670; border-style:solid;font:7pt">&nbsp;GREEN</td></tr>
<tr><td class=text1 style="font:7pt verdana"><input type=button onclick=drawPallete(2,255) style="border-width:1px; border-color:#366670; border-style:solid;font:7pt">&nbsp;BLUE</td></tr>
<tr><td class=text1 style="font:7pt verdana"><input type=button onclick=drawPallete(3,0) style="border-width:1px; border-color:#366670; border-style:solid;font:7pt">&nbsp;HUE</td></tr>
<tr><td class=text1 style="font:7pt verdana"><input type=button onclick=drawPallete(4,0) style="border-width:1px; border-color:#366670; border-style:solid;font:7pt">&nbsp;SATURATION</td></tr>
<tr><td class=text1 style="font:7pt verdana"><input type=button onclick=drawPallete(5,255) style="border-width:1px; border-color:#366670; border-style:solid;font:7pt">&nbsp;BRIGHTNESS </td></tr>
</table>
</DIV>
<DIV class=inset id=COLORTABLE></DIV>
<DIV class=inset id=ADJUST></DIV>
<DIV class=inset id=STATUS>
<TABLE height="100%" width=96% cellSpacing=0 cellPadding=0 border=0>
  <FORM name=status>
  <tr>
  <td class=text1>
  <DIV class=inset id=SAMPLE style="position:absolute; width:90px; height:20px; left:5px; top:102px; z-index:2;font:8pt 돋움;">
  <div style="padding-top:4px" align=center><FONT color=white>현재 색상값</FONT></div></DIV>
<DIV class=inset id=SELECT style="position:absolute; width:90px; height:20px; left:5px; top:20px; z-index:2;font:8pt 돋움;"><div style="padding-top:4px" align=center><FONT color=white>선택한 색상값</FONT></div></DIV>
</td>
</tr>
<tr><td height=40></td></tr>
  <TR><TD style="padding-left:2px" valign=top class=text1>선택 <INPUT  size=7 value=0 name=text>
    <?if($_GET['iconidx']  != '' && $_GET['target']  != '' ){?><div align="center" style="padding-top:5px"><a href="javascript:goodsicon()"><img src="../img/btn_color_select.gif" align="absmiddle" border="0"></a></div><?}?>
    <?if($_GET['btnCallback']  != '' ){?><div align="center" style="padding-top:5px"><a href="javascript:btnCallback()"><img src="../img/btn_color_select.gif" align="absmiddle" border="0"></a></div><? } ?>
  </TD></TR>
  <INPUT type=hidden class=inset value=0 name=background>
  <tr><td height=30></td></tr>
  <TR><TD style="padding-left:2px" valign=top class=text1>현재 <INPUT  size=7 value=0 name=current></TD></TR>
   <INPUT type=hidden class=inset  value=0 name=hue>
   <INPUT type=hidden class=inset  value=0 name=sat>
 <INPUT type=hidden class=inset  value=0 name=bri>
    <INPUT type=hidden class=inset  value=0 name=red>
  <INPUT  type=hidden class=inset value=0 name=green>
  <INPUT type=hidden class=inset  value=0 name=blue>
  </FORM></TABLE></DIV>
<DIV class=inset id=SWATCHES></DIV>
<DIV id=CLOSE>
</DIV></BODY></HTML>

