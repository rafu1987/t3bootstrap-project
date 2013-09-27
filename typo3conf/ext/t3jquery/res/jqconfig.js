var Site = {

	startJqForm: function() {
		if (jQuery('#download')) Site.download();
		if (jQuery("#process")) Site.process();
	},

	process: function() {
		jQuery('#select_all').click(function(e) {
			jQuery("#process input.extkey").each(function(id, input) {
				input.checked = true;
			});
		});
		jQuery('#select_none').click(function(e) {
			jQuery("#process input.extkey").each(function(id, input) {
				input.checked = false;
			});
		});
	},

	download: function() {
		jQuery('input[deps]').each(function(id, input) {
			jQuery(input).click(function(event) {
				Site.toggleDeps(this);
			});
		});
		jQuery('#select_all').click(function() {
			jQuery('input[deps]').each(function(id, input) {
				Site.check(input);
			});
		});
		jQuery('#select_none').click(function(){
			jQuery('input[deps]').each(function(id, input) {
				Site.uncheck(input);
			});
		});
	},

	toggleDeps: function(input) {
		if (input.checked) {
			Site.check(input);
		} else {
			Site.uncheck(input);
		}
	},

	uncheck: function(input) {
		jQuery(input).prop('checked', false);
		var deps = jQuery(input).attr('deps');
		if (deps) {
			Site.uncheckDepending(jQuery(input).attr('id'));
		}
	},

	check: function(input) {
		jQuery(input).prop('checked', true);
		var deps = jQuery(input).attr('deps');
		if (deps) {
			Site.checkDependants(deps.split(','));
		}
		var dist = jQuery(input).attr('dist');
		if (dist) {
			Site.uncheckDisturbing(dist.split(','));
		}
	},

	checkDependants: function(deps) {
		jQuery(deps).each(function(id, input) {
			if (jQuery("#"+input).length && jQuery("#"+input).is(':checked') == false) {
				Site.check(jQuery("#"+input));
			}
		});
	},

	uncheckDepending: function(component) {
		jQuery('input[deps]:checked').each(function(id, input) {
			if (jQuery.inArray(component, jQuery(input).attr('deps').split(',')) != -1) {
				Site.uncheck(input);
			}
		});
	},

	uncheckDisturbing: function(component) {
		jQuery('input[deps]:checked').each(function(id, input) {
			if (jQuery.inArray(input.id, component) != -1) {
				Site.uncheck(input);
			}
		});
	},


	checkLoaded: function() {
		jQuery('input[deps]:checked').each(function(id, input) {
			var deps = jQuery(input).attr('deps').split(',');
			jQuery(deps).each(function(id2, dep){
				Site.check(jQuery("#"+dep));
			});
		});
	}
};

jQuery(document).ready(function() {
	Site.startJqForm();
	Site.checkLoaded();
	jQuery('#compression-tog').click(function(){
		jQuery('#download-options').slideToggle('fast');
		return false;
	});
});
