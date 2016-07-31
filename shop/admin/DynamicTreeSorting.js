DynamicTree.prototype.Sorting = function () {
	function sorting() {
		this.init = function () {
			addEvent(document, "keydown", this.keydnTree);
		};
		this.ready = function (obj) {
			this.iciRow = obj.parentNode;
			this.iciHighlight();
		};
		/*** ���� �з��� ���̶���Ʈ ***/
		this.iciHighlight = function () {
			if (this.preRow){
				with (this.preRow.getElementsByTagName('input')[0].parentNode.style){
					fontWeight = "";
					color = "";
				}
			}

			with (this.iciRow.getElementsByTagName('input')[0].parentNode.style){
				fontWeight = "bold";
				color = "red";
			}
			this.preRow = this.iciRow;
		};
		/*** �з���ġ �����̵� ***/
		this.moveTree = function (idx) {
			var objTop = this.iciRow.parentNode;
			var nextPos = this.nodeIndex(this.iciRow) + idx;
			if (nextPos==objTop.childNodes.length) nextPos = 0;
			else if (nextPos==-1) nextPos = objTop.childNodes.length - 1;

			this.moveNode(objTop,this.nodeIndex(this.iciRow),nextPos);

			return false;
		};
		this.moveNode = function (node,a,b) {
			var nodes = node.childNodes;
			var clna = nodes[a].cloneNode(true);
			var clnb = nodes[b].cloneNode(true);

			if (a==0 && b==nodes.length-1){
				ele = node.appendChild(clna);
				node.removeChild(node.firstChild);
			} else if (b==0 && a==nodes.length-1){
				ele = node.insertBefore(clna,node.firstChild);
				node.removeChild(node.lastChild);
			} else {
				ele1 = node.replaceChild(clnb, nodes[a]);
				ele2 = node.replaceChild(clna, nodes[b]);
			}

			var nodes = node.childNodes;
			if (nodes.length > 1) {
				nodes[0].getElementsByTagName('img')[0].src = nodes[0].getElementsByTagName('img')[0].src.replace("-end.gif",".gif");
				if (document.getElementById(nodes[0].id + '-section') != null) document.getElementById(nodes[0].id + '-section').className = 'section';

				nodes[nodes.length-2].getElementsByTagName('img')[0].src = nodes[nodes.length-2].getElementsByTagName('img')[0].src.replace("-end.gif",".gif");
				if (document.getElementById(nodes[nodes.length-2].id + '-section') != null)  document.getElementById(nodes[nodes.length-2].id + '-section').className = 'section';
			}
			if (!node.lastChild.getElementsByTagName('img')[0].src.match(/end/i)) node.lastChild.getElementsByTagName('img')[0].src = node.lastChild.getElementsByTagName('img')[0].src.replace(".gif","-end.gif");
			if (document.getElementById(node.lastChild.id + '-section') != null) document.getElementById(node.lastChild.id + '-section').className = 'section last';

			this.iciRow = this.preRow = nodes[b];
		};
		this.nodeIndex = function (node) {
			var idx2 = 0;
			var nodes = node.parentNode.childNodes;

			for (var i=0;i<nodes.length;i++){
				if (nodes[i].getElementsByTagName('input')[0].previousSibling.nodeValue==node.getElementsByTagName('input')[0].previousSibling.nodeValue){
					idx2 = i;
				}
			}
			return idx2;
		};
		/*** ����Ű ��Ʈ�� ***/
		this.keydnTree = function (e) {
			if (self.sorting.iciRow==null) return;
			e = e ? e : event;
			switch (e.keyCode){
				case 38: return self.sorting.moveTree(-1); break;
				case 40: return self.sorting.moveTree(1); break;
			}
			return;
		};
	}

	var self = this;
	this.sorting = new sorting();
	this.sorting.init();
	this.iciRow;
	this.preRow;
}