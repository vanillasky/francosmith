/*-------------------------------------
 CSS 라운드 테이블
 ------------------------------------*/
function cssRound(id,color,bg)
{
	if (!bg) bg = '#ffffff';
	var obj = document.getElementById(id);
	obj.style.backgroundColor = color;
	with (obj.style){
		margin = "5px 0";
		color = "#4c4c4c";
		font = "8pt dotum";
	}
	obj.innerHTML = "<div style='padding:8px 13px;'><img src='../../img/icn_chkpoint.gif'><br>" + obj.innerHTML + "</div>";

	cssRound_top(obj,bg,color);
	cssRound_bottom(obj,bg,color);
}

function cssRound_top(el,bg,color)
{
	var d=document.createElement("b");
	d.className="rOut";
	d.style.fontSize = 0;
	d.style.backgroundColor=bg;
	for(i=1;i<=4;i++){
		var x=document.createElement("b");
		x.className="r" + i;
		x.style.backgroundColor=color;
		d.appendChild(x);
	}
	el.style.paddingTop=0;
	el.insertBefore(d,el.firstChild);
}

function cssRound_bottom(el,bg,color){
	var d=document.createElement("b");
	d.className="rOut";
	d.style.fontSize = 0;
	d.style.backgroundColor=bg;
	for(i=4;i>0;i--){
		var x=document.createElement("b");
		x.className="r" + i;
		x.style.backgroundColor=color;
		d.appendChild(x);
	}
	el.style.paddingBottom=0;
	el.appendChild(d);
}