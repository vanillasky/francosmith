var AnimationBanner = function(container, param)
{

	// Constant
	AnimationBanner.ANIMATE_PLAIN = 0;
	AnimationBanner.ANIMATE_SLIDE = 1;
	AnimationBanner.ANIMATE_SWIPE = 2;
	AnimationBanner.ANIMATE_BLIND = 3;
	AnimationBanner.ANIMATE_FADE = 4;

	AnimationBanner.DIRECTION_LEFT = 0;
	AnimationBanner.DIRECTION_RIGHT = 1;
	AnimationBanner.DIRECTION_TOP = 2;
	AnimationBanner.DIRECTION_BOTTOM = 3;

	AnimationBanner.DIRECTION_HORIZONTAL = 0;
	AnimationBanner.DIRECTION_VERTICAL = 1;

	AnimationBanner.POSITION_TOP_LEFT = 0;
	AnimationBanner.POSITION_TOP_CENTER = 1;
	AnimationBanner.POSITION_TOP_RIGHT = 2;
	AnimationBanner.POSITION_MIDDLE_LEFT = 3;
	AnimationBanner.POSITION_MIDDLE_RIGHT = 4;
	AnimationBanner.POSITION_BOTTOM_LEFT = 5;
	AnimationBanner.POSITION_BOTTOM_CENTER = 6;
	AnimationBanner.POSITION_BOTTOM_RIGHT = 7;
	AnimationBanner.POSITION_TOP = 8;
	AnimationBanner.POSITION_MIDDLE = 9;
	AnimationBanner.POSITION_BOTTOM = 10;
	
	AnimationBanner.ANCHOR_UNIFIED = 0;
	AnimationBanner.ANCHOR_INDIVIDUAL = 1;

	// jQuery member
	var $container = jQuery(container);
	var $visibleArea = jQuery(document.createElement("div"));
	var $unvisibleArea = jQuery(document.createElement("div"));
	var $nextButton = $container.children(param.nextButtonSelector);
	var $prevButton = $container.children(param.prevButtonSelector);
	var $anchorContainer = $container.children(param.anchorContainerSelector);
	var $anchorButton = $container.children(param.anchorButtonSelector);

	// Plain member
	var _self = this;
	var _length = 0;
	var _width = null;
	var _height = null;
	var _direction = AnimationBanner.DIRECTION_HORIZONTAL;
	var _animateType = null;
	var _index = 0;
	var _minIndex = 0;
	var _maxIndex = $container.children(param.bannerImageSelector).length - 1;
	var _anchorWidth = 20;
	var _anchorHeight = 20;
	var _property = new Object();
	var _duration = 400;
	var _interval = 3000;
	var _intervalResource = null;
	var _disableAutoShift = false;
	var _anchorMode = AnimationBanner.ANCHOR_UNIFIED;
	var _currentAnimation = null;
	var _animationQueue = new Array();
	var _reserveAnimation = 0;

	var __construct = function()
	{
		// 엘리먼트 구성
		if (param.bannerImageSelector) {
			$container.children(param.bannerImageSelector).each(function(index, element){
				var imageContainer = document.createElement("div");
				imageContainer.style.backgroundImage = "url('" + element.getAttribute("data-image") + "')";
				imageContainer.style.backgroundPosition = "center";
				imageContainer.style.backgroundRepeat = "no-repeat";

				var file_name = window.location.pathname.split('/');
				if(file_name[file_name.length-1] == "iframe.animation_banner.php") {
					_width = $container.attr("data-width");
					_height = $container.attr("data-height");
				}
				else {
					if(!_width) _width = document.body.clientWidth;
					if(!_height) {
						var check_attr = $container.attr("data-image-height");
						if(typeof check_attr === typeof undefined || check_attr === false) {
							_height = (screen.width * $container.attr("data-height")) / $container.attr("data-width");
						}
						else {
							_height = $container.attr("data-image-height") * (_width / $container.attr("data-image-width"));
						}
					}
				}

				imageContainer.style.backgroundSize = _width + "px " + _height + "px";
				if (_width) imageContainer.style.width = _width + "px";
				if (_height) imageContainer.style.height = _height + "px";
				if (element.getAttribute("data-link")) {
					var a = document.createElement("a");
					a.href = element.getAttribute("data-link");
					a.target = element.getAttribute("data-target");
					a.style.display = "block";
					a.style.width = "100%";
					a.style.height = "100%";
					imageContainer.appendChild(a);
				}

				element.innerHTML = "";
				element.style.overflow = "hidden";
				element.appendChild(imageContainer);
			});
			$container.children(param.bannerImageSelector).appendTo($unvisibleArea);
		}
		else {
			$container.children().appendTo($unvisibleArea);
		}
		$visibleArea.appendTo($container);
		_length = $unvisibleArea.children().length;
		$unvisibleArea.appendTo($container);
		$unvisibleArea.children().eq(_index).clone().appendTo($visibleArea);
		if ($anchorContainer.children().length > 0) {
			var anchorList = new Array();
			$anchorContainer.children().each(function(index, element){
				var anchorURL = new Object();
				anchorURL.on = element.getAttribute("data-on");
				anchorURL.off = element.getAttribute("data-off");
				anchorList.push(anchorURL);
			});
			_self.setIndividualAnchor(anchorList);
		}
		else {
			$anchorContainer.appendTo($container);
			$unvisibleArea.children().each(function(index){
				$anchorButton.clone().addClass(index.toString()).text(index + 1).appendTo($anchorContainer);
			});
		}

		// 이벤트 바인딩
		$nextButton.click(_moveNext);
		$prevButton.click(_movePrev);
		$anchorContainer.children().each(function(index, element){
			jQuery(element).unbind("click").click(function(){
				if (_currentAnimation) {
					_addAnimationQueue(function(){
						_self.change(index);
					});
					return false;
				}
				else {
					_self.stop();
					_self.change(index);
					_self.start();
				}
			});
		});

		// 스타일 초기화
		$container.css({
			"overflow" : "hidden",
			"position" : "relative"
		});
		$visibleArea.css({
			"width" : _width,
			"height" : _height
		});
		$unvisibleArea.css({
			"display" : "none"
		});
		$nextButton.css({
			"position" : "absolute",
			"top" : "50%",
			"right" : 0,
			"z-index" : 10
		});
		$prevButton.css({
			"position" : "absolute",
			"top" : "50%",
			"left" : 0,
			"z-index" : 10
		});
		$anchorContainer.css({
			"position" : "absolute",
			"top" : 0,
			"left" : 0,
			"z-index" : 10
		});
		$anchorContainer.children().css({
			"float" : "left"
		});
		$anchorButton.css({
			"display" : "none"
		});

		_anchorWidth = $anchorContainer.children().eq(0).outerWidth(true);
		_anchorHeight = $anchorContainer.children().eq(0).outerHeight(true);

		_self.setIndex(_index);

		// 리사이즈 및 오리엔테이션 처리
		$(window).bind("orientationchange resize", function() {
			window.setTimeout(function() {
				if( navigator.userAgent.match(/Android/i)
				|| navigator.userAgent.match(/webOS/i)
				|| navigator.userAgent.match(/iPhone/i)
				|| navigator.userAgent.match(/iPad/i)
				|| navigator.userAgent.match(/iPod/i)
				|| navigator.userAgent.match(/BlackBerry/i)
				|| navigator.userAgent.match(/Windows Phone/i)
				){
					_width = document.body.clientWidth;
					var check_attr = $container.attr("data-image-height");
					if(typeof check_attr === typeof undefined || check_attr === false) {
						_height = (screen.width * $container.attr("data-height")) / $container.attr("data-width");
					}
					else {
						_height = $container.attr("data-image-height") * (_width / $container.attr("data-image-width"));
					}

					$('.banner-image, .banner-image>div').css({width: _width + "px", height: _height + "px"});
					$('.banner-image>div').css({backgroundSize: _width + "px " + _height + "px"});
					$container.css({width: _width + "px", height: _height + "px"});
					$visibleArea.css({
						"width" : _width,
						"height" : _height
					});
					if(window.orientation == 90 || window.orientation == -90 || window.orientation == 270) {
						//landscape
					} else {
						//portrait
					}
				}
			}, 200);
		});

		// 애니메이션 배너내 swipe 기능 추가
		jQuery(function(){
			$container.bind( "swipeleft", swipeleftHandler );
			$container.bind( "swiperight", swiperightHandler );

			function swipeleftHandler( event ){ _self.next(); }
			function swiperightHandler( event ){ _self.prev(); }
		});
	};

	var _addAnimationQueue = function(callback)
	{
		_reserveAnimation++;
		_animationQueue.push(callback);
		_currentAnimation.duration = _currentAnimation.duration / (_reserveAnimation + 1);
	};

	var _shiftAnimationQueue = function()
	{
		if (_animationQueue.length > 0) {
			(_animationQueue.shift())();
			if (_animationQueue.length === 0) _reserveAnimation = 0;
		}
	};

	var _getDuration = function()
	{
		if (_reserveAnimation > 0) {
			return _duration / (_reserveAnimation + 1);
		}
		else {
			return _duration;
		}
	};

	var _moveNext = function()
	{
		if (_currentAnimation) {
			_addAnimationQueue(_moveNext);
			return false;
		}
		else {
			_self.stop();
			_self.next();
			_self.start();
		}
	};

	var _movePrev = function()
	{
		if (_currentAnimation) {
			_addAnimationQueue(_movePrev);
			return false;
		}
		else {
			_self.stop();
			_self.prev();
			_self.start();
		}
	};

	_self.setDuration = function(duration)
	{
		_duration = parseInt(duration) || 400;
	};

	_self.setInterval = function(interval)
	{
		_interval = parseInt(interval) || 3000;
		_self.stop();
		_self.start(_animateType);
	};

	_self.disableAutoShift = function()
	{
		_disableAutoShift = true;
		_self.stop();
	};

	_self.enableAutoShift = function()
	{
		_disableAutoShift = false;
		_self.start();
	};

	_self.setWidth = function(width)
	{
		_width = parseInt(width);
		$container.css("width", _width);
		$visibleArea.css("width", _width).children().css("width", _width).children().css("width", _width);
		$unvisibleArea.children().css("width", _width).children().css("width", _width);
	};

	_self.setHeight = function(height)
	{
		_height = parseInt(height);
		$container.css("height", _height);
		$visibleArea.css("height", _height).children().css("height", _height).children().css("height", _height);
		$unvisibleArea.children().css("height", _height).children().css("height", _height);
	};

	_self.setProperty = function(name, value)
	{
		_property[name] = value;
	}

	_self.setDirection = function(direction)
	{
		if (direction === AnimationBanner.DIRECTION_VERTICAL) {
			_direction = AnimationBanner.DIRECTION_VERTICAL;
			if (_anchorMode === AnimationBanner.ANCHOR_INDIVIDUAL) {
				// Nothing
			}
			else {
				$anchorContainer.css({
					"width" : _anchorWidth
				});
			}
			$nextButton.css({
				"top" : "",
				"bottom" : 0,
				"right" : "50%",
				"z-index" : 10,
				"margin-right" : -($nextButton.outerWidth() / 2)
			});
			$prevButton.css({
				"top" : "0",
				"right" : "50%",
				"left" : "",
				"z-index" : 10,
				"margin-right" : -($prevButton.outerWidth() / 2)
			});
		}
		else {
			_direction = AnimationBanner.DIRECTION_HORIZONTAL;
			if (_anchorMode === AnimationBanner.ANCHOR_INDIVIDUAL) {
				// Nothing
			}
			else {
				$anchorContainer.css({
					"width" : (_anchorWidth * _length)
				});
			}
			$nextButton.css({
				"top" : "50%",
				"right" : 0,
				"z-index" : 10,
				"margin-top" : -($nextButton.outerHeight() / 2)
			});
			$prevButton.css({
				"top" : "50%",
				"left" : 0,
				"z-index" : 10,
				"margin-top" : -($prevButton.outerHeight() / 2)
			});
		}
	};

	_self.setAnchorWidth = function(anchorWidth)
	{
		$anchorContainer.children().css({
			"width" : anchorWidth
		});
		_anchorWidth = $anchorContainer.children().eq(0).outerWidth(true);
		$anchorContainer.css({
			"width" : (_direction === AnimationBanner.DIRECTION_VERTICAL) ? _anchorWidth : (_anchorWidth * _length)
		});
	};

	_self.setAnchorHeight = function(anchorHeight)
	{
		_anchorHeight = anchorHeight;
		$anchorContainer.children().css({
			"height" : _anchorHeight
		});
	};

	_self.setAnchorPosition = function(position)
	{
		switch (position) {
			case AnimationBanner.POSITION_TOP_LEFT:
				$anchorContainer.css({
					"top" : 0,
					"right" : "",
					"bottom" : "",
					"left" : 0
				});
				break;
			case AnimationBanner.POSITION_TOP_CENTER:
				$anchorContainer.css({
					"top" : 0,
					"right" : "",
					"bottom" : "",
					"left" : "50%",
					"margin-left" : -($anchorContainer.outerWidth(true) / 2)
				});
				break;
			case AnimationBanner.POSITION_TOP_RIGHT:
				$anchorContainer.css({
					"top" : 0,
					"left" : "",
					"bottom" : "",
					"right" : 0
				});
				break;
			case AnimationBanner.POSITION_MIDDLE_LEFT:
				$anchorContainer.css({
					"top" : "50%",
					"right" : "",
					"bottom" : "",
					"left" : 0,
					"margin-top" : -($anchorContainer.outerHeight(true) / 2)
				});
				break;
			case AnimationBanner.POSITION_MIDDLE_RIGHT:
				$anchorContainer.css({
					"top" : "50%",
					"right" : 0,
					"bottom" : "",
					"left" : "",
					"margin-top" : -($anchorContainer.outerHeight(true) / 2)
				});
				break;
			case AnimationBanner.POSITION_BOTTOM_LEFT:
				$anchorContainer.css({
					"top" : "",
					"right" : "",
					"bottom" : 0,
					"left" : 0
				});
				break;
			case AnimationBanner.POSITION_BOTTOM_CENTER:
				$anchorContainer.css({
					"top" : "",
					"right" : "",
					"bottom" : 0,
					"left" : "50%",
					"margin-left" : -($anchorContainer.outerWidth(true) / 2)
				});
				break;
			case AnimationBanner.POSITION_BOTTOM_RIGHT:
				$anchorContainer.css({
					"top" : "",
					"right" : 0,
					"bottom" : 0,
					"left" : ""
				});
				break;
		}
	};

	_self.setUnifiedAnchor = function()
	{
		_anchorMode = AnimationBanner.ANCHOR_UNIFIED;
		$anchorContainer.empty();
		$unvisibleArea.children().each(function(index){
			$anchorButton.clone().css({
				"display" : "",
				"float" : "left"
			}).addClass(index.toString()).text(index + 1).appendTo($anchorContainer);
		});
		_self.setAnchorWidth($anchorContainer.children().eq(0).outerWidth());
		_self.setAnchorHeight(_anchorHeight = $anchorContainer.children().eq(0).outerHeight());
		$anchorContainer.children().each(function(index, element){
			jQuery(element).unbind("click").click(function(){
				if (_currentAnimation) {
					_addAnimationQueue(function(){
						_self.change(index);
					});
					return false;
				}
				else {
					_self.stop();
					_self.change(index);
					_self.start();
				}
			});
		});
		$anchorContainer.children().eq(_index).addClass("active");
	};

	_self.setIndividualAnchor = function(anchorList)
	{
		_anchorMode = AnimationBanner.ANCHOR_INDIVIDUAL;
		$anchorContainer.empty();
		_anchorWidth = null;
		_anchorHeight = null;
		for (var index = 0; index < anchorList.length; index++) {
			var img = document.createElement("img");
			img.src = anchorList[index].off;
			$anchorContainer.css({
				"width" : "",
				"height" : ""
			});
			img.setAttribute("data-off", anchorList[index].off);
			img.setAttribute("data-on", anchorList[index].on);
			$anchorContainer.append(img);
		}
		$anchorContainer.children().each(function(index, element){
			jQuery(element).unbind("click").click(function(){
				if (_currentAnimation) {
					_addAnimationQueue(function(){
						_self.change(index);
					});
					return false;
				}
				else {
					_self.stop();
					_self.change(index);
					_self.start();
				}
			});
		});
	};

	_self.setImage = function(index, image)
	{
		var element = $unvisibleArea.find(param.bannerImageSelector).eq(index).attr("data-image", image)[0];
		var imageContainer = document.createElement("div");
		imageContainer.style.backgroundImage = "url('" + element.getAttribute("data-image") + "')";
		imageContainer.style.backgroundPosition = "center";
		imageContainer.style.backgroundRepeat = "no-repeat";
		imageContainer.style.backgroundSize = "cover";
		imageContainer.style.width = _width + "px";
		imageContainer.style.height = _height + "px";
		if (element.getAttribute("data-link")) {
			var a = document.createElement("a");
			a.href = element.getAttribute("data-link");
			a.target = element.getAttribute("data-target");
			a.style.display = "block";
			a.style.width = "100%";
			a.style.height = "100%";
			imageContainer.appendChild(a);
		}
		element.style.overflow = "hidden";
		element.innerHTML = "";
		element.appendChild(imageContainer);
	};

	_self.appendBanner = function(element, option)
	{
		_length++;
		_maxIndex++;
		var index = _maxIndex;
		var imageContainer = document.createElement("div");
		imageContainer.appendChild(element);
		$unvisibleArea.append(imageContainer);
		if (_anchorMode === AnimationBanner.ANCHOR_UNIFIED) {
			$anchorButton.clone().css({
				"display" : "",
				"float" : "left"
			}).addClass(_maxIndex.toString()).unbind("click").click(function(){
				if (_currentAnimation) {
					_addAnimationQueue(function(){
						_self.change(index);
					});
					return false;
				}
				else {
					_self.stop();
					_self.change(index);
					_self.start();
				}
			}).text(_maxIndex + 1).appendTo($anchorContainer);
		}
		else {
			var img = document.createElement("img");
			img.src = option.off;
			$anchorContainer.css({
				"width" : "",
				"height" : ""
			});
			img.setAttribute("data-off", option.off);
			img.setAttribute("data-on", option.on);
			jQuery(img).unbind("click").click(function(){
				if (_currentAnimation) {
					_addAnimationQueue(function(){
						_self.change(index);
					});
					return false;
				}
				else {
					_self.stop();
					_self.change(index);
					_self.start();
				}
			});
			$anchorContainer.append(img);
		}
	};

	_self.removeBanner = function(index)
	{
		_length--;
		_maxIndex--;
		$unvisibleArea.children().eq(index).remove();
		$anchorContainer.children().eq(index).remove();
	};

	_self.start = function(animateType)
	{
		if (jQuery.inArray(animateType, [
			AnimationBanner.ANIMATE_PLAIN,
			AnimationBanner.ANIMATE_SLIDE,
			AnimationBanner.ANIMATE_BLIND,
			AnimationBanner.ANIMATE_FADE,
			AnimationBanner.ANIMATE_SWIPE
		]) > -1) _animateType = animateType;

		if (_intervalResource) {
			clearInterval(_intervalResource);
		}

		if (_disableAutoShift === false) {
			_intervalResource = setInterval(function(){
				_moveNext();
			}, _interval);
		}
	};

	_self.stop = function()
	{
		clearInterval(_intervalResource);
	};

	_self.change = function(index)
	{
		if (_index === index) {
			return false;
		}
		else {
			switch (_animateType) {
				case AnimationBanner.ANIMATE_PLAIN:
					_self.setIndex(index);
					break;
				case AnimationBanner.ANIMATE_SLIDE:
					if (_index < index) {
						if (_direction === AnimationBanner.DIRECTION_VERTICAL) {
							_self.slide(index, AnimationBanner.DIRECTION_TOP);
						}
						else {
							_self.slide(index, AnimationBanner.DIRECTION_LEFT);
						}
					}
					else {
						if (_direction === AnimationBanner.DIRECTION_VERTICAL) {
							_self.slide(index, AnimationBanner.DIRECTION_BOTTOM);
						}
						else {
							_self.slide(index, AnimationBanner.DIRECTION_RIGHT);
						}
					}
					break;
				case AnimationBanner.ANIMATE_FADE:
					_self.fade(index);
					break;
				case AnimationBanner.ANIMATE_SWIPE:
					if (_index < index) {
						if (_direction === AnimationBanner.DIRECTION_VERTICAL) {
							_self.swipe(index, AnimationBanner.DIRECTION_TOP);
						}
						else {
							_self.swipe(index, AnimationBanner.DIRECTION_LEFT);
						}
					}
					else {
						if (_direction === AnimationBanner.DIRECTION_VERTICAL) {
							_self.swipe(index, AnimationBanner.DIRECTION_BOTTOM);
						}
						else {
							_self.swipe(index, AnimationBanner.DIRECTION_RIGHT);
						}
					}
					break;
				case AnimationBanner.ANIMATE_BLIND:
					if (_index < index) {
						if (_direction === AnimationBanner.DIRECTION_VERTICAL) {
							_self.blind(index, AnimationBanner.DIRECTION_TOP);
						}
						else {
							_self.blind(index, AnimationBanner.DIRECTION_LEFT);
						}
					}
					else {
						if (_direction === AnimationBanner.DIRECTION_VERTICAL) {
							_self.blind(index, AnimationBanner.DIRECTION_BOTTOM);
						}
						else {
							_self.blind(index, AnimationBanner.DIRECTION_RIGHT);
						}
					}
					break;
			}
		}
	};

	_self.next = function()
	{
		var index = 0;

		if (_index < _maxIndex) {
			index = _index + 1;
		}
		else { 
			index = _minIndex;
		}

		switch (_animateType) {
			case AnimationBanner.ANIMATE_PLAIN:
				_self.setIndex(index);
				break;
			case AnimationBanner.ANIMATE_SLIDE:
				if (_direction === AnimationBanner.DIRECTION_VERTICAL) {
					_self.slide(index, AnimationBanner.DIRECTION_TOP);
				}
				else {
					_self.slide(index, AnimationBanner.DIRECTION_LEFT);
				}
				break;
			case AnimationBanner.ANIMATE_FADE:
				_self.fade(index);
				break;
			case AnimationBanner.ANIMATE_SWIPE:
				if (_direction === AnimationBanner.DIRECTION_VERTICAL) {
					_self.swipe(index, AnimationBanner.DIRECTION_TOP);
				}
				else {
					_self.swipe(index, AnimationBanner.DIRECTION_LEFT);
				}
				break;
			case AnimationBanner.ANIMATE_BLIND:
				if (_direction === AnimationBanner.DIRECTION_VERTICAL) {
					_self.blind(index, AnimationBanner.DIRECTION_TOP);
				}
				else {
					_self.blind(index, AnimationBanner.DIRECTION_LEFT);
				}
				break;
		}
	};

	_self.prev = function()
	{
		var index = 0;
		if (_index > _minIndex) {
			index = _index - 1;
		}
		else { 
			index = _maxIndex;
		}

		switch (_animateType) {
			case AnimationBanner.ANIMATE_PLAIN:
				_self.setIndex(index);
				break;
			case AnimationBanner.ANIMATE_SLIDE:
				if (_direction === AnimationBanner.DIRECTION_VERTICAL) {
					_self.slide(index, AnimationBanner.DIRECTION_BOTTOM);
				}
				else {
					_self.slide(index, AnimationBanner.DIRECTION_RIGHT);
				}
				break;
			case AnimationBanner.ANIMATE_FADE:
				_self.fade(index);
				break;
			case AnimationBanner.ANIMATE_SWIPE:
				if (_direction === AnimationBanner.DIRECTION_VERTICAL) {
					_self.swipe(index, AnimationBanner.DIRECTION_BOTTOM);
				}
				else {
					_self.swipe(index, AnimationBanner.DIRECTION_RIGHT);
				}
				break;
			case AnimationBanner.ANIMATE_BLIND:
				if (_direction === AnimationBanner.DIRECTION_VERTICAL) {
					_self.blind(index, AnimationBanner.DIRECTION_BOTTOM);
				}
				else {
					_self.blind(index, AnimationBanner.DIRECTION_RIGHT);
				}
				break;
		}
	};

	_self.setIndex = function(index)
	{
		_index = index;
		if (_anchorMode === AnimationBanner.ANCHOR_INDIVIDUAL) {
			$anchorContainer.children(".active").removeClass("active").each(function(index, element){
				element.src = element.getAttribute("data-off");
			});
			$anchorContainer.children().eq(_index).each(function(index, element){
				jQuery(element).addClass("active");
				element.src = element.getAttribute("data-on");
			});
		}
		else {
			$anchorContainer.children().removeClass("active").eq(_index).addClass("active");
		}
		$visibleArea.empty();
		$unvisibleArea.children().eq(_index).clone().appendTo($visibleArea);
		$visibleArea.css("width", _width).children().css("width", _width);
		$visibleArea.css("height", _height).children().css("height", _height);
		_shiftAnimationQueue();
	};

	_self.getIndex = function()
	{
		return _index;
	};

	_self.slide = function(indexTo, direction)
	{
		_animateType = AnimationBanner.ANIMATE_SLIDE;

		var $temporaryContainer = jQuery(document.createElement("div"));
		$temporaryContainer.css({
			"width" : (direction === AnimationBanner.DIRECTION_LEFT || direction === AnimationBanner.DIRECTION_RIGHT) ? _width * 2 : _width,
			"height" : (direction === AnimationBanner.DIRECTION_TOP || direction === AnimationBanner.DIRECTION_BOTTOM) ? _height * 2 : _height,
			"position" : "absolute",
			"left" : (direction !== AnimationBanner.DIRECTION_RIGHT) ? 0 : -_width,
			"top" : (direction !== AnimationBanner.DIRECTION_BOTTOM) ? 0 : -_height,
			"z-index" : 1
		});

		$unvisibleArea.children().eq(_index).clone().css({
			"position" : "absolute",
			"left" : (direction !== AnimationBanner.DIRECTION_RIGHT) ? 0 : _width,
			"top" : (direction !== AnimationBanner.DIRECTION_BOTTOM) ? 0 : _height,
			"z-index" : 1
		}).appendTo($temporaryContainer);

		$unvisibleArea.children().eq(indexTo).clone().css({
			"position" : "absolute",
			"left" : (direction !== AnimationBanner.DIRECTION_LEFT) ? 0 : _width,
			"top" : (direction !== AnimationBanner.DIRECTION_TOP) ? 0 : _height,
			"z-index" : 1
		}).appendTo($temporaryContainer);

		$visibleArea.empty().append($temporaryContainer);

		$temporaryContainer.animate({
			"left" : (direction !== AnimationBanner.DIRECTION_LEFT) ? 0 : -_width,
			"top" : (direction !== AnimationBanner.DIRECTION_TOP) ? 0 : -_height
		}, {
			"duration" : _getDuration(),
			"start" : function(animation)
			{
				_currentAnimation = animation;
			},
			"complete" : function()
			{
				_currentAnimation = null;
				_self.setIndex(indexTo);
			}
		});
	};

	_self.fade = function(indexTo)
	{
		_animateType = AnimationBanner.ANIMATE_FADE;

		$visibleArea.empty();

		$unvisibleArea.children().eq(_index).clone().css({
			"position" : "absolute",
			"top" : 0,
			"z-index" : 1
		}).appendTo($visibleArea);

		$unvisibleArea.children().eq(indexTo).clone().css({
			"position" : "absolute",
			"display" : "none",
			"top" : 0,
			"z-index" : 2
		}).appendTo($visibleArea);

		$visibleArea.children().eq(1).fadeIn({
			"duration" : _getDuration(),
			"start" : function(animation)
			{
				_currentAnimation = animation;
			},
			"complete" : function()
			{
				_currentAnimation = null;
				_self.setIndex(indexTo);
			}
		});

	};

	_self.swipe = function(indexTo, direction)
	{
		_animateType = AnimationBanner.ANIMATE_SWIPE;

		$visibleArea.empty();

		$unvisibleArea.children().eq(_index).clone().css({
			"position" : "absolute",
			"left" : 0,
			"top" : 0,
			"z-index" : (direction === AnimationBanner.DIRECTION_BOTTOM || direction === AnimationBanner.DIRECTION_RIGHT) ? 1 : 2
		}).appendTo($visibleArea);

		$unvisibleArea.children().eq(indexTo).clone().css({
			"position" : "absolute",
			"left" : (direction === AnimationBanner.DIRECTION_RIGHT) ? -_width : 0,
			"top" : (direction === AnimationBanner.DIRECTION_BOTTOM) ? -_height : 0,
			"z-index" : (direction === AnimationBanner.DIRECTION_BOTTOM || direction === AnimationBanner.DIRECTION_RIGHT) ? 2 : 1
		}).appendTo($visibleArea);

		$visibleArea.children().eq((direction === AnimationBanner.DIRECTION_BOTTOM || direction === AnimationBanner.DIRECTION_RIGHT) ? 1 : 0).animate({
			"top" : (direction !== AnimationBanner.DIRECTION_TOP) ? 0 : -_height,
			"left" : (direction !== AnimationBanner.DIRECTION_LEFT) ? 0 : -_width
		}, {
			"duration" : _getDuration(),
			"start" : function(animation)
			{
				_currentAnimation = animation;
			},
			"complete" : function()
			{
				_currentAnimation = null;
				_self.setIndex(indexTo);
			}
		});
	};

	_self.blind = function(indexTo, direction)
	{
		_animateType = AnimationBanner.ANIMATE_BLIND;

		$visibleArea.empty();

		$unvisibleArea.children().eq(_index).clone().css({
			"position" : "absolute",
			"left" : 0,
			"top" : 0,
			"z-index" : (direction === AnimationBanner.DIRECTION_BOTTOM || direction === AnimationBanner.DIRECTION_RIGHT) ? 1 : 2,
			"width" : _width,
			"height" : _height
		}).appendTo($visibleArea);

		$unvisibleArea.children().eq(indexTo).clone().css({
			"position" : "absolute",
			"left" : (_property.blindPoint === "center" && direction === AnimationBanner.DIRECTION_RIGHT) ? _width/2 : 0,
			"top" : (_property.blindPoint === "center" && direction === AnimationBanner.DIRECTION_BOTTOM) ? _height/2 : 0,
			"z-index" : (direction === AnimationBanner.DIRECTION_BOTTOM || direction === AnimationBanner.DIRECTION_RIGHT) ? 2 : 1,
			"width" : (direction !== AnimationBanner.DIRECTION_RIGHT) ? _width : 0,
			"height" : (direction !== AnimationBanner.DIRECTION_BOTTOM) ? _height : 0
		}).appendTo($visibleArea);

		if (_property.blindPoint === "center") {
			$visibleArea.children().css("backgroundPosition", "center");
		}
		else {
			$visibleArea.children().css("backgroundPosition", "left top");
		}

		$visibleArea.children().eq((direction === AnimationBanner.DIRECTION_BOTTOM || direction === AnimationBanner.DIRECTION_RIGHT) ? 1 : 0).animate({
			"left" : (_property.blindPoint !== "center" || direction !== AnimationBanner.DIRECTION_LEFT) ? 0 : _width/2,
			"top" : (_property.blindPoint !== "center" || direction !== AnimationBanner.DIRECTION_TOP) ? 0 : _height/2,
			"width" : (direction !== AnimationBanner.DIRECTION_LEFT) ? _width : 0,
			"height" : (direction !== AnimationBanner.DIRECTION_TOP) ? _height : 0
		}, {
			"duration" : _getDuration(),
			"start" : function(animation)
			{
				_currentAnimation = animation;
			},
			"complete" : function()
			{
				_currentAnimation = null;
				_self.setIndex(indexTo);
			}
		});
	};

	__construct();

};