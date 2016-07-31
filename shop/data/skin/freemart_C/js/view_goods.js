
function setViewMoreFrom(title, lname1, loc1) {
	
	
	$j = jQuery.noConflict();
	
	if($j('#view-more-from-title').length > 0) {
		$j('#view-more-from-title').html(title);
	}
	
	if ($j('#'+lname1).length > 0) {
		$j('#'+lname1).html(loc1);
	}
}