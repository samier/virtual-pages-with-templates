jQuery(document).ready(function () {
	Vpt.toggleUrlSettings(jQuery('#use_custom_permalink_structure').is(':checked'));
	jQuery('#use_custom_permalink_structure').change(function () {
		Vpt.toggleUrlSettings(jQuery(this).is(':checked'));
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
	}
}