DynamicTree.prototype.Shifting = function () {
	function shifting() {
		this.nodeUp = function (evt) {
			ele = getTargetElement(evt);
			if (ele.tagName == 'IMG') ele = ele.parentNode;
			delEvent(ele, "mousemove", self.shifting.nodeMove);
		};
		this.nodeDown = function (evt) {
			ele = getTargetElement(evt);
			if (ele.tagName == 'IMG') ele = ele.parentNode;
			addEvent(ele, "mousemove", self.shifting.nodeMove);
		};
		this.nodeMove = function (evt) {
			if (window.getSelection) { window.getSelection().removeAllRanges(); }
			else if (document.selection && document.selection.clear) { document.selection.clear(); }

			ele = getTargetElement(evt);
			if (ele.tagName == 'IMG') ele = ele.parentNode;
			if (document.getElementById('mvCopyDiv') != null) {
				delEvent(ele, "mousemove", self.shifting.nodeMove);
				return;
			}
			ele.innerHTML = ele.innerHTML;

			var oDiv = document.createElement('DIV');
			var cDiv = document.body.appendChild(oDiv);
			with (cDiv.style) {
				position = 'absolute';
				border = 'solid 1px red';
				cursor = 'pointer';
				padding = '5px 13px';
				fontWeight = 'bold';
				filter = "Alpha(Opacity=70)";
				opacity = "0.7";
				backgroundColor = '#eeeeee';
			}
			cDiv.innerHTML = ele.innerHTML;
			cDiv.style.left = evt.clientX + document.body.scrollLeft - 10;
			cDiv.style.top = evt.clientY + document.body.scrollTop - (cDiv.offsetHeight / 2);
			cDiv.setAttribute('id', 'mvCopyDiv' );
			cDiv.setAttribute('setShiftId', ele.parentNode.id );

			addEvent(document, "mouseover", self.shifting.mcdMvMove);
			addEvent(document, "mouseup", self.shifting.mcdMvUp);
			for (var n in self.allNodes) {
				var actATag = self.shifting.getNodeActATag(n);
				addEvent(actATag, "mouseenter", self.shifting.aTagOver);
				addEvent(actATag, "mouseover", self.shifting.aTagOver);
			}
			var actATag = document.getElementById('node_top');
			addEvent(actATag, "mouseenter", self.shifting.aTagOver);
			addEvent(actATag, "mouseover", self.shifting.aTagOver);
		};
		this.aTagOver = function (evt) {
			ele = getTargetElement(evt);
			if (ele.tagName == 'IMG') ele = ele.parentNode;
			if (self.overAct != null) {
				with (self.overAct.style) {
					backgroundColor = self.overAct.getAttribute('oldBackgroundColor');
					color = self.overAct.getAttribute('oldColor');
				}
			}
			self.overAct = ele;
			ele.setAttribute('oldBackgroundColor', ele.style.backgroundColor);
			ele.setAttribute('oldColor', ele.style.color);
			with (ele.style) {
				backgroundColor = '#000000';
				color = '#FFFFFF';
			}
		};
		this.mcdMvMove = function (evt) {
			ele = document.getElementById('mvCopyDiv');
			cltX = evt.clientX + document.body.scrollLeft;
			cltY = evt.clientY + document.body.scrollTop;
			ele.style.left = cltX - 10;
			ele.style.top = cltY - (ele.offsetHeight / 2);
		};
		this.mcdMvUp = function (evt) {
			delEvent(document, "mouseover", self.shifting.mcdMvMove);
			delEvent(document, "mouseup", self.shifting.mcdMvUp);
			for (var n in self.allNodes) {
				var actATag = self.shifting.getNodeActATag(n);
				delEvent(actATag, "mouseenter", self.shifting.aTagOver);
				delEvent(actATag, "mouseover", self.shifting.aTagOver);
			}
			var actATag = document.getElementById('node_top');
			delEvent(actATag, "mouseenter", self.shifting.aTagOver);
			delEvent(actATag, "mouseover", self.shifting.aTagOver);

			ele = document.getElementById('mvCopyDiv');
			var setShiftId = ele.getAttribute('setShiftId');
			ele.parentNode.removeChild( ele );

			if ((setTargetId = self.shifting.getTargetId(evt)) == null) return;
			if (setShiftId == setTargetId) {alert("다른 분류로의 이동만 가능합니다.\n자신의 분류로 이동할 수 없습니다."); return;}
			if (self.allNodes[setShiftId].parentNode.id == setTargetId) {alert("선택한 분류는 이미 해당분류에 있으므로 이동이 되지않습니다."); return;}
			if (setTargetId != 'tree') {
				if (self.allNodes[setTargetId].parentNode.id == setShiftId) {alert("자신의 하위분류로의 이동은 불가능합니다."); return;}
				if ((setTargetId.length / 3) == 4) {alert("최하위분류 밑으로는 이동이 불가능합니다."); return;}
			}

			var query = '';
			query += '&ShiftCategory=' + document.getElementById(setShiftId).getElementsByTagName('input')[0].value;
			query += '&targetCategory=' + (setTargetId != 'tree' ? document.getElementById(setTargetId).getElementsByTagName('input')[0].value : '');
			var urlStr = (window.location.pathname.indexOf('/todayshop/category.php') > -1) ? "../todayshop/indb.category.php" : "../goods/indb.php";
			urlStr += "?mode=chgCategoryShift" + query + "&dummy=" + new Date().getTime();

			/* 샵터치 카테고리 관련 */
			if(window.location.pathname.indexOf('/shoptouch/shopTouch_category.php') > -1) {
				urlStr = "../shoptouch/indb.php?mode=chgCategoryShift" + query + "&dummy=" + new Date().getTime();
			}

			var cDivLoad = self.loading(evt);
			var ajax = new Ajax.Request( urlStr,
			{
				method: "get",
				onComplete: function () {
					var req = ajax.transport;
					cDivLoad.parentNode.removeChild( cDivLoad );
					if ( req.status == 200 ) {
						if ((response = req.responseText) == '') return;
						var jsonData = eval('(' + response + ')');
						if (jsonData['old'] == null || jsonData['new'] == null) {
							alert(response);
							return;
						}

						if (self.sorting.preRow && self.category != self.sorting.preRow.id) self.category = self.sorting.preRow.id;
						for (idx = 0; idx < jsonData['old'].length; idx++) {
							if (self.category == jsonData['old'][idx]) self.category = jsonData['new'][idx];
						}
						self.sorting.preRow = undefined;

						self.shifting.crossStep1_shift(setShiftId);
						nId = self.shifting.crossStep2_node(setShiftId, jsonData);
						self.shifting.crossStep3_target(nId, setTargetId);
					} else if ( req.getResponseHeader("Status") == 'depth' ) {
						alert("해당분류로의 이동은 불가능합니다\n이동하게되면 4차분류를 넘어서기 때문입니다.");
					} else {
						var msg = req.getResponseHeader("Status");
						if ( msg == null || msg.length == null || msg.length <= 0 )
							alert( "Error! Request status is " + req.status );
						else
							alert( msg );
					}
				}
			} );
		};
		this.getNodeActATag = function (id) {
			var aTag = document.getElementById(id).getElementsByTagName('a');
			return self.allNodes[id].isFolder !== true ? aTag[0] : aTag[1];
		};
		this.getTargetId = function (evt) {
			if (self.overAct == null) return;
			with (self.overAct.style) {
				backgroundColor = self.overAct.getAttribute('oldBackgroundColor');
				color = self.overAct.getAttribute('oldColor');
			}
			cltX = evt.clientX + document.body.scrollLeft + document.getElementById('treeCategory').scrollLeft;
			cltY = evt.clientY + document.body.scrollTop + document.getElementById('treeCategory').scrollTop;
			offleft = eval(get_objectLeft(self.overAct));
			offtop = eval(get_objectTop(self.overAct));
			if ( !(offleft <= cltX && cltX <= offleft + eval(self.overAct.offsetWidth)) ) self.overAct = null;
			else if ( !(offtop <= cltY && cltY <= offtop + eval(self.overAct.offsetHeight)) ) self.overAct = null;
			if (self.overAct == null) return;
			if (self.overAct.id == 'node_top') return 'tree';
			return self.overAct.parentNode.id;
		};
		this.crossStep1_shift = function (sid) {
			var parent = self.allNodes[sid].parentNode;
			parent.childNodes.removeByValue(self.allNodes[sid]); // 이동분류의 상위.childnodes 에서 이동분류 제거
			document.getElementById(sid).parentNode.removeChild(document.getElementById(sid)); // 이동분류 HTML_Node 제거
			if (parent.childNodes.length == 0) {
				parent.childNodes = null;
				parent.isDoc = true;
				parent.isFolder = false;
				this.dropSection(parent.id);
				self.opened.removeByValue(parent.id);
			}
			else {
				this.relaySection(parent.childNodes.getLast().id, 'section last', self.img.nodeOpenEnd, self.img.nodeEnd, self.img.leafEnd);
			}
		};
		this.crossStep2_node = function (sid, jsonData) {
			var nid = '';
			for (idx = 0; idx < jsonData['old'].length; idx++) {
				var okey = jsonData['old'][idx];
				var nkey = jsonData['new'][idx];
				if (self.allNodes[okey]) {
					self.allNodes[okey].id = nkey;
					self.allNodes[nkey] = self.allNodes[okey];
					delete self.allNodes[okey];
					if (self.opened.contains(okey)) {
						self.opened.removeByValue(okey);
						self.opened.push(nkey);
					}
				}
				if (sid == okey) nid = nkey;
			}
			return nid;
		}
		this.crossStep3_target = function (nid, tid) {
			var tidNode = (tid != 'tree' ? self.allNodes[tid] : self.tree);
			self.allNodes[nid].parentNode = tidNode; // 이동분류의 상위를 이동목표분류로 변경
			if (tidNode.isDoc == true) tidNode.childNodes = new Array();
			tidNode.childNodes.push(self.allNodes[nid]); // 이동목표분류.childnodes 에 이동분류 추가

			if (tidNode.id == 'tree') var section = document.getElementById(tidNode.id);
			else var section = this.getSection(tidNode.id);

			if (tidNode.isDoc == true) {
				tidNode.isDoc = false;
				tidNode.isFolder = true;
				section.style.display = 'block';
				self.opened.push(tidNode.id);
				section.innerHTML = self.toHtml(new Array(self.allNodes[nid]));
			}
			else if (section.innerHTML == '') {
				self.nodeClick(tidNode.id);
			}
			else if (section.innerHTML != '') {
				if (section.style.display == 'none') {
					section.style.display = 'block';
					self.opened.push(tidNode.id);
				}
				this.relaySection(tidNode.childNodes[tidNode.childNodes.length - 2].id, 'section', self.img.nodeOpen, self.img.node, self.img.leaf);
				section.innerHTML += self.toHtml(new Array(self.allNodes[nid]));
			}
			if (self.category) loadHistory(self.category, true);
		};
		this.relaySection = function (id, classnm, imgNodeOpen, imgNode, imgLeaf) {
			if (document.getElementById(id + '-section') != null) {
				document.getElementById(id).getElementsByTagName('IMG')[0].src = (document.getElementById(id + '-section').style.display == 'block' ? imgNodeOpen : imgNode);
				document.getElementById(id + '-section').className = classnm;
			}
			else {
				document.getElementById(id).getElementsByTagName('IMG')[0].src = imgLeaf;
			}
		};
		this.getSection = function (id) {
			if (document.getElementById(id + '-section') != null) return document.getElementById(id + '-section');
			return this.createSection(id); // section 생성
		};
		this.createSection = function (id) {
			var node = document.getElementById(id);
			node.removeChild( node.getElementsByTagName('IMG')[0] ); // leaf icon 제거
			var oa = document.createElement('A'); // node a tag 생성
			var va = node.insertBefore(oa, node.childNodes[0]);
			va['onclick'] = function(e) { self.nodeClick(id); };
			var oimg = document.createElement('IMG'); // node icon 생성
			var vimg = va.appendChild(oimg);
			vimg.setAttribute('id', id + '-node');
			vimg.src = (self.allNodes[id].parentNode.childNodes.getLast().id == id ? self.img.nodeOpenEnd : self.img.nodeOpen);
			vimg.width = "18";
			vimg.height = "18";
			vimg.alt = "";

			var oDiv = document.createElement('DIV');
			var vDiv = document.getElementById(id).appendChild(oDiv);
			vDiv.className = (self.allNodes[id].parentNode.childNodes.getLast().id == id ? 'section last' : 'section');
			vDiv.setAttribute('id', id + '-section');
			vDiv.style.display = 'none';
			return vDiv;
		};
		this.dropSection = function (id) {
			var node = document.getElementById(id);
			node.removeChild(document.getElementById(id + '-section'));
			node.removeChild( node.getElementsByTagName('A')[0] );

			var oimg = document.createElement('IMG');
			var vimg = node.insertBefore(oimg, node.childNodes[0]);
			vimg.setAttribute('id', id + '-node');
			vimg.src = (self.allNodes[id].isLast() ? self.img.leafEnd : self.img.leaf);
			vimg.width = "18";
			vimg.height = "18";
			vimg.alt = "";
		};
	}

	var self = this;
	this.shifting = new shifting();
	this.overAct;
}