$(function(){
	$.fn.insgoWidgetDisplay = function(opts){
		var displayDivid = 'insgoWidget';

		if(!opts.queryString) {
			return;
		}

		$.post('./_ajax.getInsgoWidgetUser.php', { queryString : opts.queryString }, function(data){
			var responseData = new Array();
			responseData = eval("("+data+")");

			if(typeof responseData.thumbnails == "undefined"){
				return;
			}

			var widgetHtml = '';
			switch(responseData.data['displayType']){
				case 'grid':
					widgetHtml = _getGridHtml(responseData);

					$('#insgoWidgetLayout').html(widgetHtml);

					if(responseData.data['thumbnailType'] == 'auto'){
						_iframeResize(responseData.data['iframeID']);
					}

					if(responseData.data['thumbnailEffect'] == 'blurPoint'){
						$('#insgoWidget img').mouseover(function(){
							$(this).fadeTo('fast', 0.3 );
						});
						$('#insgoWidget img').mouseout(function(){
							$(this).fadeTo('fast', 1.0 );
						});
					}
					else if(responseData.data['thumbnailEffect'] == 'blurException'){
						$('#insgoWidget img').hover(function(){
							$('img').not(this).fadeTo(50, 0.3 );
						});
						$('#insgoWidget').mouseleave(function(){
							$('#insgoWidget img').fadeTo(50, 1 );
						});
						$('#insgoWidget img').mouseover(function(){
							$(this).fadeTo(50, 1 );
						});
						$('#insgoWidget img').mouseout(function(){
							$(this).fadeTo(50, 0.3 );
						});
					}
					else {

					}
				break;

				default :

				break;
			}
		});

		var _iframeResize = function(iframeID)
		{
			$('#' + iframeID, parent.document).height($('#' + displayDivid).height());
		}

		var _getGridHtml = function(responseData){
			var html, size, minusMargin, type, imageStyle, backgroundColor, clearBoth, marginLeft, marginTop;

			size = _imageSize(responseData.data);
			type = _imageType(size);

			//이미지 사이즈
			imageStyle += 'width: ' + size + 'px; height: ' + size + 'px;';
			//이미지 테두리
			imageStyle += (responseData.data['thumbnailBorder'] == 'y') ? 'border: 1px #ACACAC solid;' : 'border: 0px;';
			//배경색
			backgroundColor = (responseData.data['backgroundColor']) ? '#' + responseData.data['backgroundColor'] : '#FFFFFF';

			html = '<div style="width: 100%; overflow: hidden; background-color : ' + backgroundColor + ';" id="'+displayDivid+'">';
			$.each(responseData.thumbnails, function(index, thumbnailsArr){
				clearBoth = marginLeft = marginTop = '';
				if((index % responseData.data['widthCount']) == 0){
					clearBoth = 'clear: both;';
				}
				if(responseData.data['thumbnailMargin'] > 0){
					if((index % responseData.data['widthCount']) != 0){
						marginLeft = 'margin-left:' + responseData.data['thumbnailMargin'] + 'px;';
					}
					if(index >= responseData.data['widthCount']){
						marginTop = 'margin-top:' + responseData.data['thumbnailMargin'] + 'px;';
					}
				}

				html += '<div style="float: left; ' + clearBoth + '">';
				html += '<a href="'+thumbnailsArr.viewUrl+'" target="_blank"><img src="' + thumbnailsArr.image[type]['url'] + '" style="cursor: pointer;' + imageStyle + marginLeft + marginTop + '" /></a>';
				html += '</div>';
			});
			html += '</div>';

			return html;
		}

		var _imageSize = function(responseData)
		{
			var size;
			if(responseData['thumbnailType'] == 'hand'){
				size = responseData['thumbnailSize'];
			}
			else {
				size = Math.floor($('#' + responseData['iframeID'], parent.document).width() / responseData['widthCount']);
				size -= responseData['thumbnailMargin'];
				if(responseData['thumbnailBorder'] == 'y'){
					size -= 2;
				}
			}

			return size;
		}

		var _imageType = function(size)
		{
			var type;
			if(size <= 150){
				type = 'thumbnail';
			}
			else if(size <= 320){
				type = 'low_resolution';
			}
			else {
				type = 'standard_resolution';
			}

			return type;
		}
	}
});