var nsGodoFormHelper = function() {
	return {

		_expire_date : null,
		_loc : null,
		_btn_id : null,
		_frm_id : null,

		init : function() {

			var self = this;
			var today = new Date();

			self._btn_id = 'el-godo-form-helper-toggle-btn';
			self._frm_id = 'frmSearch';

			self._expire_date = new Date(today.getTime() + 31536000);
			self._loc = window.location.pathname.split('/');

			//self._loc.pop();
			self._loc = self._loc.join('_');

			Event.observe(document, 'dom:loaded', function(){

				if (!$(self._btn_id) || !$(self._frm_id)) return;

				var ajax = new Ajax.Request('../proc/ax.indb.form_helper.php', {
					method: "post",
					parameters: 'mode=get&name=searchForm&key=' + nsGodoFormHelper._loc,
					onComplete: function(response) { if (response.status == 200) {

						if (response.responseText == 'block') {
							$$('tr.blindable').each(function(el){
								el.setStyle({'display':'block'});
							});

							$(self._btn_id).src = "../img/btn_search_form_toggle_close.gif";

						}
					}}
				});

			});
		},
		// css(blindable) 셀렉터를 이용해 검색폼을 숨김/보임
		toggle : function(btn_id) {

			var self = this;

			if (typeof btn_id == 'string') self._btn_id = btn_id;

			var dp = '';

			$$('tr.blindable').each(function(el){

				if (dp == '') dp = el.getStyle('display');
				if (dp == 'none') {
					el.setStyle({'display':'block'});
				}
				else {
					el.setStyle({'display':'none'});
				}
			});

			if (dp == 'block') {
				$(self._btn_id).src = "../img/btn_search_form_toggle_open.gif";
				dp = 'none';
			}
			else {
				$(self._btn_id).src = "../img/btn_search_form_toggle_close.gif";
				dp = 'block';
			}

			self._set('searchForm',dp);

		},
		// 검색 조건을 저장함
		save : function(frm_id) {
			var self = this;
			if (typeof frm_id == 'string') self._frm_id = fid;
			var condition = encodeURIComponent($(self._frm_id).serialize());
			self._set('searchCondition', condition,function(){alert('검색조건이 저장되었습니다.')});
		},
		// 저장된 검색 조건을 초기화함
		reset : function(frm_id) {
			var self = this;
			if (typeof frm_id == 'string') self._frm_id = fid;
			var el;

			for (var i=0, m=$(self._frm_id).elements.length;i<m ;i++) {
				el = $(self._frm_id).elements[i];

				try {
					if (el.tagName == 'SELECT') el.selectedIndex = 0;
					else if (el.tagName == 'INPUT'){
						switch (el.type) {
							case 'checkbox':
							case 'radio':

								el.checked = false;
								break;
							case 'text':
							case 'password':
								if (el.name == 'regdt[]') continue;
								el.value = '';
								break;
						}
					}
				}
				catch (e) {}

			}

			self._set('searchCondition','', function(){alert('검색조건이 초기화되었습니다.')});
		},
		_set : function(k, v, cb) {

			var ajax = new Ajax.Request('../proc/ax.indb.form_helper.php', {
				method: "post",
				parameters: 'mode=set&name=' + k + '&key='+ nsGodoFormHelper._loc +'&value=' + v,
				onComplete: function(response) { if (response.status == 200) {

					if (response.responseText != '' && typeof cb == 'function') cb();

				}}
			});
		},
		// name 속성이 배열인 input 태그의 checked 값을 토글 (체크박스여부는 체크하지 않지만, 체크박스에만 사용할것)
		magic_check : function(obj,all_key) {

			if (typeof all_key == 'undefined') all_key = 'all';

			var pattern = new RegExp('([a-zA-Z0-9_]+)','g');
			var matches = obj.name.match(pattern);

			if (matches) {

				if (obj.checked == true && matches[1] == all_key) {
					$$('input[name^="'+matches[0]+'["]:checked').each(function(el){
						if (el.name != obj.name) el.checked = false;
					});
				}
				else if (obj.checked == true && matches[1] != all_key) {
					try
					{
						$$('input[name="'+matches[0]+'['+all_key+']"]:checked')[0].checked = false;
					}
					catch (e) { }
				}
			}

		}
	}
}();

nsGodoFormHelper.init();