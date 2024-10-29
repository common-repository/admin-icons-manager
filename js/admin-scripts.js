jQuery(document).ready(

	function($) {
		

		$('#adminmenuicons_section').show();
		$('#buttonsubmit').show();
		//$('#navigationmenuicons_section').hide();
		$('#about_section').hide();
		
		
		$('#sections-menu a').on('click', function(){
			$('a.active').removeClass('active');
			$(this).addClass('active');
		});
		
		
		$('#sections-menu #adminmenuicons_link').on('click', function() {
		  //$('#navigationmenuicons_section').hide();
		  $('#about_section').hide();
		  $('#adminmenuicons_section').show();
		  $('#buttonsubmit').show();
		});
		
		/*
		$('#sections-menu #navigationmenuicons_link').on('click', function() {
		  $('#adminmenuicons_section').hide();	
		  $('#navigationmenuicons_section').show();		
		});
		*/

		$('#sections-menu #about_link').on('click', function() {
		  $('#adminmenuicons_section').hide();	
		  $('#buttonsubmit').hide();
		  $('#about_section').show();		
		});		
		

		/**
		 * WordPress Color Picker
		 */		
		
		jQuery(document).ready(function($){
			$('.color-field').wpColorPicker();
		});
		
});




