var MyUtils = {
    setValue: function(form,name,value) {
		var ipts = form.select("[name='"+name+"']");
		if(ipts[0].type=='text' || ipts[0].type=='hidden' || ipts[0].match('textarea')) {
			if(ipts.length==1) {
				ipts[0].value=value;
			}
			else {
				ipts.each(function(ele,idx){
					ele.value=value;
				});
			}
		}
		else if(ipts[0].type=='radio') {
			ipts.each(function(ele){
				if(ele.value==value) { ele.checked=true;}
			});
		}
		else if(ipts[0].match('select')) {
			if(ipts.length==1) {
				var tmp=value;
				value=[];
				value[0]=tmp;
			}
			ipts.each(function(ele,idx) {
				var i,length = ele.options.length;
				for(i=0;i<length;i++) {
					if(ele.options[i].value==value) {
						ele.selectedIndex=i;
						break;
					}
				}
			});
		}
		else if(ipts[0].type=='checkbox') {
			ipts.each(function(ele,idx){
				if(ele.value==value) ele.checked=true;
			});
		}
    },
	getText: function(element) {
		element = $(element);
		return element.innerHTML.strip().stripTags().replace(/\n/g,' ').replace(/\s+/g,' ');
	}
}
Element.addMethods(MyUtils);
