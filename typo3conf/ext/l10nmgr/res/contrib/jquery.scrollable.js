/**
*
*  Scrollable HTML table plugin for jQuery
*  http://www.webtoolkit.info/
*
**/

jQuery.fn.Scrollable = function(tableHeight, tableWidth) {
	this.each(function(){
		if (jQuery.browser.msie || jQuery.browser.mozilla) {
			var table = new ScrollableTable(this, tableHeight, tableWidth);
		}
	});
};
