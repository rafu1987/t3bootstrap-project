<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<TITLE>Multi Content JS-Template</TITLE>
</head>
<body>

<h1>Multi Content JS-Template</h1>

<br />
<br />
<br />

<em>Common markers:</em>
KEY - Unique key of the multicontent
OPTIONS - Set the JS-options defined in FlexForm

<em>Tabs markers:</em>
OPEN_EXTERNAL_LINK - Subpart for the JS to open the url in the rel-attribute (only if openExternalLink is set in EXT-Config)
PREG_QUOTE_KEY - Unique key of the multicontent preg_quoted
ROTATE - Set the JS for autoplay
TAB_SELECTOR - Set the selection of a panel by hash

<em>Accordion markers:</em>
OPEN_EXTERNAL_LINK - Subpart for the JS to open the url in the rel-attribute (only if openExternalLink is set in EXT-Config)
CONTENT_COUNT - Count of the contents
EASING_ANIMATION - Subpart for the easing definition
EASING - Used easing transition
TRANS_DURATION - Duration of the transition
SETTIMEOUT - Subpart for timer of autoplay
CONTINUING - Subpart to stop the autoplay in case of user action

<em>Slidedeck markers:</em>
HEIGHT - Set the defined height of the easyaccordion

<em>Easyaccordion markers:</em>
WIDTH - Set the defined width of the easyaccordion





<!-- ###TEMPLATE_TAB_JS### begin -->
jQuery(document).ready(function(){
	jQuery('####KEY###').tabs({
		###OPTIONS###
	});
	<!-- ###TAB_SELECT_BY_HASH### -->
	if (location.hash.toLowerCase()) {
		jQuery('####KEY###').tabs('option', 'active', location.hash.toLowerCase());
		jQuery('####KEY###').tabs('option', 'activate', function(e, ui) {
			location.hash = ui.newPanel.selector;
		});
	}
	<!-- ###TAB_SELECT_BY_HASH### -->
	<!-- ###OPEN_EXTERNAL_LINK### -->
	jQuery('####KEY###').tabs('option', 'beforeActivate', function(e, ui) {
		var rel = jQuery(ui.newTab.context).attr('rel');
		if (typeof(rel) != 'undefined' && rel.length > 0) {
			document.location.href = rel;
		}
	});
	<!-- ###OPEN_EXTERNAL_LINK### -->
});
<!-- ###TEMPLATE_TAB_JS### end -->





<!-- ###TEMPLATE_ACCORDION_JS### begin -->
jQuery(document).ready(function(){
	jQuery('####KEY###').accordion({
		###OPTIONS###
	});
	<!-- ###OPEN_EXTERNAL_LINK### -->
	jQuery('####KEY###').bind('accordionchangestart', function(e, ui) {
		var rel = jQuery(ui.newHeader).find('a').attr('rel');
		if (typeof(rel) != 'undefined' && rel.length > 0) {
			document.location.href = rel;
		}
	});
	<!-- ###OPEN_EXTERNAL_LINK### -->
	<!-- ###TAB_SELECT_BY_HASH### -->
	var hash = location.hash.toLowerCase();
	if (hash) {
		var split = hash.split('-');
		if (split[0] == '####KEY###' && parseInt(split[1]) > 0) {
			jQuery('####KEY###').accordion('option', 'active', (parseInt(split[1]) - 1));
		}
	}
	jQuery('####KEY###').bind("accordionchange", function(event, ui) {
		location.hash = '####KEY###-' + (jQuery('####KEY###').accordion('option', 'active') + 1);
	});
	<!-- ###TAB_SELECT_BY_HASH### -->
});
<!-- ###TEMPLATE_ACCORDION_JS### end -->





<h2>TEMPLATE_SLIDER_JS:</h2>

<!-- ###TEMPLATE_SLIDER_JS### begin -->
jQuery(document).ready(function(){
	jQuery('####KEY###').anythingSlider({
		###OPTIONS###
	});
});
<!-- ###TEMPLATE_SLIDER_JS### end -->





<h2>TEMPLATE_SLIDEDECK_JS:</h2>

<!-- ###TEMPLATE_SLIDEDECK_JS### begin -->
jQuery(document).ready(function(){
	jQuery('####KEY###').css({height: '###HEIGHT###px'});
	jQuery('####KEY###').slidedeck({
		###OPTIONS###
	});
});
<!-- ###TEMPLATE_SLIDEDECK_JS### end -->





<h2>TEMPLATE_EASYACCORDION_JS:</h2>

<!-- ###TEMPLATE_EASYACCORDION_JS### begin -->
jQuery(document).ready(function(){
	jQuery('####KEY###, ####KEY### dl').css({width: '###WIDTH###px'});
	jQuery('####KEY###').easyAccordion({
		###OPTIONS###
	});
});
<!-- ###TEMPLATE_EASYACCORDION_JS### end -->





<h2>TEMPLATE_BOOKLET_JS:</h2>

<!-- ###TEMPLATE_BOOKLET_JS### begin -->
jQuery(document).ready(function(){
	jQuery('####KEY###').booklet({
		###OPTIONS###
	});
});
<!-- ###TEMPLATE_BOOKLET_JS### end -->





</pre>


</body>
</html>
