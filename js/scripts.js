jQuery(document).ready(function () {
	Vpt.toggleUrlSettings(jQuery('#use_custom_permalink_structure').is(':checked'));
	jQuery('#use_custom_permalink_structure').change(function () {
		Vpt.toggleUrlSettings(jQuery(this).is(':checked'));
	});

	jQuery( "#vpt_form" ).submit(function( event ) {
		
		if (Vpt.validate_form())
			return
		else
			event.preventDefault();
	});
});


var Vpt = {
	toggleUrlSettings : function ( use_custom_permalink ){
		if (use_custom_permalink == true){
			jQuery('#use_custom_pageurl').show();
			jQuery('#use_permalink_label').hide();
		}else{
			jQuery('#use_custom_pageurl').hide();
			jQuery('#use_permalink_label').show();
		}
	},
	validate_form : function (){
		if (!jQuery('#page_template').val()){
			jQuery('#message:not(.no-template-message)').remove();
			jQuery('.error:not(.no-template-message)').remove();
			jQuery('.no-template-message ').show();
			return false;
		}else{
			jQuery('.no-template-message ').hide();	
			return true;
	}
}


}