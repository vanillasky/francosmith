jQuery(document).ready(function(){

	jQuery(".animation-banner").each(function(index, bannerElement){
		var $this = jQuery(bannerElement);
		var animationBanner = new AnimationBanner(bannerElement, {
			"bannerImageSelector" : "." + (bannerElement.getAttribute("data-banner-image-class") || "banner-image"),
			"nextButtonSelector" : "." + (bannerElement.getAttribute("data-next-button-class") || "next-button"),
			"prevButtonSelector" : "." + (bannerElement.getAttribute("data-prev-button-class") || "prev-button"),
			"anchorContainerSelector" : "." + (bannerElement.getAttribute("data-anchor-container-class") || "anchor-container"),
			"anchorButtonSelector" : "." + (bannerElement.getAttribute("data-anchor-button-class") || "anchor-button")
		});
		$this.data("wrapperClass", animationBanner);

		jQuery(bannerElement).find(".prev-button, .next-button").css({
			"opacity" : "0.5"
		});

		// 필수값 체크
		if (!$this.attr("data-height")) {
			return;
		}
		else {
			if( navigator.userAgent.match(/Android/i)
			|| navigator.userAgent.match(/webOS/i)
			|| navigator.userAgent.match(/iPhone/i)
			|| navigator.userAgent.match(/iPad/i)
			|| navigator.userAgent.match(/iPod/i)
			|| navigator.userAgent.match(/BlackBerry/i)
			|| navigator.userAgent.match(/Windows Phone/i)
			){

				animationBanner.setWidth(document.body.clientWidth);
				var check_attr = $this.attr("data-image-height");
				if(typeof check_attr === typeof undefined || check_attr === false) {
					animationBanner.setHeight($this.attr("data-height"));
				}
				else {
					animationBanner.setHeight($this.attr("data-image-height") * (document.body.clientWidth / $this.attr("data-image-width")));
				}
			} else {
				animationBanner.setWidth($this.attr("data-width"));
				var check_attr = $this.attr("data-image-height");
				if(typeof check_attr === typeof undefined || check_attr === false) {
					animationBanner.setHeight($this.attr("data-height"));
				}
				else {
					animationBanner.setHeight($this.attr("data-image-height") * ($this.attr("data-width") / $this.attr("data-image-width")));
				}
			}
		}

		animationBanner.setDuration(bannerElement.getAttribute("data-duration"));
		animationBanner.setInterval(bannerElement.getAttribute("data-interval"));

		if ($this.attr("data-direction") === "vertical") {
			animationBanner.setDirection(AnimationBanner.DIRECTION_VERTICAL);
		}
		else {
			animationBanner.setDirection(AnimationBanner.DIRECTION_HORIZONTAL);
		}

		animationBanner.setProperty("blindPoint", $this.attr("data-blind-point"));

		switch ($this.attr("data-anchor-position")) {
			case "top-left":
				animationBanner.setAnchorPosition(AnimationBanner.POSITION_TOP_LEFT);
				break;
			case "top-center":
				animationBanner.setAnchorPosition(AnimationBanner.POSITION_TOP_CENTER);
				break;
			case "top-right":
				animationBanner.setAnchorPosition(AnimationBanner.POSITION_TOP_RIGHT);
				break;
			case "middle-left":
				animationBanner.setAnchorPosition(AnimationBanner.POSITION_MIDDLE_LEFT);
				break;
			case "middle-right":
				animationBanner.setAnchorPosition(AnimationBanner.POSITION_MIDDLE_RIGHT);
				break;
			case "bottom-left":
				animationBanner.setAnchorPosition(AnimationBanner.POSITION_BOTTOM_LEFT);
				break;
			case "bottom-center":
				animationBanner.setAnchorPosition(AnimationBanner.POSITION_BOTTOM_CENTER);
				break;
			case "bottom-right":
				animationBanner.setAnchorPosition(AnimationBanner.POSITION_BOTTOM_RIGHT);
				break;
		}

		if ($this.attr("data-shift-type") === "manual") {
			animationBanner.disableAutoShift();
		}
		else {
			animationBanner.enableAutoShift();
		}

		switch ($this.attr("data-method")) {
			case "slide":
				animationBanner.start(AnimationBanner.ANIMATE_SLIDE);
				break;
			case "fade":
				animationBanner.start(AnimationBanner.ANIMATE_FADE);
				break;
			case "swipe":
				animationBanner.start(AnimationBanner.ANIMATE_SWIPE);
				break;
			case "blind":
				animationBanner.start(AnimationBanner.ANIMATE_BLIND);
				break;
			default:
				animationBanner.start(AnimationBanner.ANIMATE_PLAIN);
				break;
		}
	});
});