/** for Libthing Slider **/
jQuery(document).ready(function(){
   /* all rows are visible by default for progressive javascript 
      so turn off the ones we don't need, but turn 1st row on */
   jQuery('.lt-row').removeClass('visible');
   jQuery('.lt-row').css('visibility','hidden');
   jQuery('.lt-row:first').css('visibility','visible');
	jQuery('.lt-row:first').addClass('visible');
	toggleSlide = function() {
      jQuery('.lt-row').css('visibility', 'visible');
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