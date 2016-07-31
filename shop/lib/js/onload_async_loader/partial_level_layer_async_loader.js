// 등급변경알림 레이어 박스
(function(updateData){
	if (updateData.myLevelLayer && updateData.myLevelLayer.previousLevel && updateData.myLevelLayer.currentLevel
		&& !getCookie("cache_lnoti." + updateData.myLevelLayer.previousLevel + "." + updateData.myLevelLayer.currentLevel)) {
		_ID("MyLevelLayerBox-name").innerHTML = updateData.myLevelLayer.name;
		_ID("MyLevelLayerBox-previous-grpnm").innerHTML = updateData.myLevelLayer.previousGroupName;
		_ID("MyLevelLayerBox-current-grpnm").innerHTML = updateData.myLevelLayer.currentGroupName;
		_ID("MyLevelLayerBox").style.display = "block";
		var expireDate = new Date((new Date()).getTime() + (updateData.cacheExpireInterval * 1000));
		setCookie("cache_lnoti." + updateData.myLevelLayer.previousLevel + "." + updateData.myLevelLayer.currentLevel, "true", expireDate);
	}
})(updateData);