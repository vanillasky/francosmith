var Category = function(objnm) {
	this.objnm = objnm;
}

Category.prototype = {
	set: function(n, category) {
		var self = this;
		for(var i = n; i < 4; i++) {
			var obj = document.getElementsByName(self.objnm)[i];
			while(obj.options.length > 1) obj.options.remove(1);
		}
		if (!category) category = "";
		if (n != 0 && !category) return;
		new Ajax.Request("../todayshop/indb.category.php?mode=getCategory&category="+category+"&dummy="+new Date().getTime(), {
			method: "get",
			asynchronous:false,
			onComplete: function(req) {
				var data = eval(req.responseText);
				var obj = document.getElementsByName(self.objnm)[n];
				for(var i = 0; i < data.length; i++) {
					var opt = document.createElement("OPTION");
					obj.add(opt);
					opt.value = data[i].category;
					opt.innerHTML = data[i].catnm;
				}
			}
		});
	},
	change: function(obj) {
		var objs = document.getElementsByName(this.objnm);
		var n = 0;
		for(var i = 0; i < objs.length; i++) {
			if (objs[i] == obj) {
				n = i;
				break;
			}
		}
		this.set(n + 1, obj[obj.selectedIndex].value);
	},
	select: function(category) {
		this.set(0);
		for(var i = 1; i <= category.length / 3; i++) {
			var cate = category.substring(0, i * 3);
			var obj = document.getElementsByName(this.objnm)[i - 1];
			for(j = 0; j < obj.options.length; j++) {
				if (obj.options[j].value == cate) {
					obj.options[j].selected = "selected";
					break;
				}
			}
			if (i == 4) break;
			this.set(i, cate);
		}
	}
}

/**
	2011-01-28 by x-ta-c
	�����̼� �ֹ� ���� �� �̰����� ���� ��ƿ��Ƽ �Լ���.
	(�մܰ� ȥ���� ���ϱ� ���� namespace �� �ٲ�)
 */
var nsTodayshopControl = function() {

	/* ����ȣ��� �Լ� */
	function popup(url,w_width,w_height,scroll) {

			var x = (screen.availWidth - w_width) / 2;
			var y = (screen.availHeight - w_height) / 2;

			if (scroll == 1) {
				var sc = "scrollbars=yes";
			}
			else var sc = "scrollbars=no";

			return window.open(url,"","width="+w_width+",height="+w_height+",top="+y+",left="+x+","+sc);
	}

	// jquery �� post �䳻����.
	function post(url, param, cb) {

		if (cb == undefined) cb = function(){};

		return new Ajax.Request( url,
		{
			method: "post",
			parameters: param,
			onComplete: cb
		});
	}


	return {

		order : {

			notAvail : function () {
				alert('�Ǹ� ������� ��ǰ�� �ֹ� ����Ʈ�� Ȯ���� �� �����ϴ�.');
			}
			,
			view : function(n) {
				popup('./todayshop_buyer_list.php?goodsno='+n,700,700,1);
			}
			,
			cancel_all : function(n) {
				if (confirm('�Ǹ� ���� ��ǰ�� �ϰ� �ֹ���� ó�� �Ͻðڽ��ϱ�?')) {
					var f = document.frmTemp;
					f.action = './indb.todayshop_buyer_list.php';
					f.mode.value = 'cancel_all';
					f.goodsno.value = n;
					f.submit();
				}
			}
			,
			cancel : function(n) {
				if (confirm('�Ǹ� ���� ��ǰ�� �ֹ���� ó�� �Ͻðڽ��ϱ�?')) {
					var f = document.frmTemp;
					f.action = './indb.todayshop_buyer_list.php';
					f.mode.value = 'cancel';
					f.ordno.value = n;
					f.submit();
				}
			}
			,
			sms : function(n) {
				if (confirm('��ǰ ���� ��ȣ�� �߼��Ͻðڽ��ϱ�?')) {
					var o = post(
						'./indb.todayshop_buyer_list.php',
						'mode=sms&ordno='+n,
						function(){
							if (o.transport.responseText) {
								eval(o.transport.responseText);
							}
							else {
								alert('SMS �޽����� �߼��߽��ϴ�.');
							}
						}
					);
				}
			}
			,
			publish : function(n) {
				if (confirm('��ǰ ���� ��ȣ �ϰ� �߼��Ͻðڽ��ϱ�?')) {
					var o = post(
						'./indb.todayshop_buyer_list.php',
						'mode=publish&goodsno='+n,
						function(){
							if (o.transport.responseText) {
								eval(o.transport.responseText);
							}
							else {
								alert('���� ��ȣ�� �ϰ� �߼�ó�� �Ͽ����ϴ�.');
							}
						}
					);
				}
			}
			,
			download : function(n) {
				if (confirm('�ֹ�����Ʈ�� �ٿ�ε� �Ͻðڽ��ϱ�?')) {
					var f = document.frmDnXls;
					f.action = './dnXls_todayshop_b.php';
					f.target = 'ifrmHidden';
					f.submit();
				}
			}
			,
			downloadCoupon : function(n) {
				if (confirm('�ֹ�����Ʈ�� �ٿ�ε� �Ͻðڽ��ϱ�?')) {
					var f = document.frmDnXls;
					f.action = './dnXls_todayshop_b.php';
					f.target = 'ifrmHidden';
					f.submit();
				}
			}
			,
			delivery : function(n) {
				// common.js ���� popupLayer �Լ� ���
				popupLayer('./todayshop_buyer_delivery.php?ordno='+n,450,200);
			}
		}	// order
	} //
} ();