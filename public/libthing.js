/** for Libthing Slider **/
jQuery(document).ready(function(){
	jQuery('.lt-row:first').addClass('visible');
	toggleSlide = function() {
	    var $active = jQuery('.visible');
	    if($active.length == 0) {
	        $active = jQuery('.lt-row:first');
	    }
	    $active.removeClass('visible');
	    if($active.next('.lt-row').length > 0) {
	        $active.next('.lt-row').addClass('visible');
	    } else {
	        jQuery('.lt-row:first').addClass('visible');
	    }
	}
	setInterval(toggleSlide, 5000)
});