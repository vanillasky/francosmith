function ierotator(config,me) {
	me=this;
	this.arDiv=new Array();
	this.arNum=new Array();
	this.nowNum=0;
	var i,count;
	this.area = document.getElementById(config.id);
	this.area.style.width=config.width;
	this.area.style.height=config.height;

	this.changeDiv = function(n) {
		if(me.nowNum==n) return;
		me.nowNum=n;
		for(var i=0;i<me.arNum.length;i++)
		{
			me.arNum[i].childNodes[0].style.display=(i==n?'inline':'none');
			me.arNum[i].childNodes[1].style.display=(i==n?'none':'inline');
		}

		if (me.bannerArea.filters)
		{
			me.bannerArea.filters[0].apply();
			for(i=0;i<me.arNum.length;i++)
			{
				me.arDiv[i].style.display=(i==n?'block':'none');
			}
			me.bannerArea.filters[0].play();
		}
		else {
			for(i=0;i<me.arNum.length;i++)
			{
				me.arDiv[i].style.display=(i==n?'block':'none');
			}
		}
		me.nowNum=n;
	}

	this.nextDiv = function() {
		if((me.nowNum+1)==me.arNum.length) me.changeDiv(0);
		else me.changeDiv(me.nowNum+1);
	}
	
	this.prevDiv = function() {
		if((me.nowNum-1)<0) me.changeDiv(me.arNum.length-1);
		else me.changeDiv(me.nowNum-1);
	}
	
	count=this.area.childNodes.length;
	for(i=0;i<count;i++)
	{
		if (this.area.childNodes[i].nodeType != 1) continue;
		this.arDiv.push(this.area.childNodes[i].cloneNode(true));
		if (config.numimg.length <= this.arDiv.length) {
			break;
		}
	}
	count = this.arDiv.length;
	this.area.innerHTML="";

	this.numArea = document.createElement("ul");
	//this.numArea.style.left=0;
	//this.numArea.style.top=0;
	//this.numArea.style.margin=0;
	this.numArea.style.padding=0;
	//this.numArea.style.position="absolute";
	this.numArea.style.zIndex=1;
	//this.numArea.style.width=config.width;
	this.numArea.style.width="50%";
	this.numArea.style.textAlign="right";
	this.numArea.style.listStyle="none";
	this.numArea.style.display=config.numDisplay;
	for(i=0;i<count;i++)
	{
		var oLi=document.createElement("li");
		oLi.style.display="inline";
		this.numArea.appendChild(oLi);
		this.arNum.push(oLi);
		oLi.num=i;
		oLi.onmouseover=function() {
			me.changeDiv(this.num);
		}

		var oImg=document.createElement("IMG");
		oImg.src=config.numimg[i][0];
		oImg.style.display=(i==0?'inline':'none');
		oLi.appendChild(oImg);

		var oImg=document.createElement("IMG");
		oImg.src=config.numimg[i][1];
		oImg.style.display=(i==0?'none':'inline');
		oLi.appendChild(oImg);
	}

	this.bannerArea = document.createElement("div");
	this.bannerArea.style.filter=config.effect;
	this.bannerArea.style.width=config.width;
	this.bannerArea.style.height=config.height;
	this.bannerArea.style.overflow='hidden';
	this.area.appendChild(this.numArea);
	this.area.appendChild(this.bannerArea);

	for(i=0;i<count;i++)
	{
		me.arDiv[i].style.display=(i==0?'block':'none');
		this.bannerArea.appendChild(me.arDiv[i]);
	}

	this.area.style.display="block";
	//console.log(this.area.innerHTML);
	
	this.area.onmouseover=function(){
		window.clearInterval(me.interval_moving);
	};

	this.area.onmouseout=function(){
		me.interval_moving = window.setInterval(me.nextDiv,config.wait);
	};
	this.interval_moving = window.setInterval(this.nextDiv,config.wait);

}