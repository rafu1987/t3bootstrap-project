/**
 * flowplayer.embed 3.0.2. Flowplayer JavaScript plugin.
 * 
 * This file is part of Flowplayer, http://flowplayer.org
 *
 * Author: Tero Piirainen, <info@flowplayer.org>
 * Copyright (c) 2008 Flowplayer Ltd
 *
 * Dual licensed under MIT and GPL 2+ licenses
 * SEE: http://www.opensource.org/licenses
 * 
 * Date: 2008-11-25 11:29:03 -0500 (Tue, 25 Nov 2008)
 * Revision: 1316 
 */ 
(function() {

	// converts paths to absolute URL's as required in external sites 
	function toAbsolute(url, base) {		
		
		// http://some.com/path
		if (url.substring(0, 4) == "http") { return url; }
		
		if (base) {			
			return base + (base.substring(base.length -1) != "/" ? "/" : "") + url; 
		}
		
		// /some/path
		base = location.protocol + "//" + location.host;		
		if (url.substring(0, 1) == "/") { return base + url; }
		
		// yet/another/path		
		var path = location.pathname;		
		path = path.substring(0, path.lastIndexOf("/"));
		return base + path + "/" + url;
	}
	
	
	// Flowplayer plugin implementation
	$f.addPlugin("embed", function(options) {
	
		var self = this;
		var conf = self.getConfig(true);

		// default configuration for flashembed
		var opts = {
			width: self.getParent().clientWidth || '100%',
			height: self.getParent().clientHeight || '100%',
			url: toAbsolute(self.getFlashParams().src), 
			index: -1,
			id: "_" + ("" + Math.random()).substring(2, 10),
			allowfullscreen: true,
			allowscriptaccess: 'always'
		};		 
		
		// override defaults
		$f.extend(opts, options);
		opts.src = opts.url;
		opts.w3c = true;
		
		
		// not needed for external objects
		delete conf.playerId;		
		delete opts.url;
		delete opts.index;
		
		
		// construct HTML code for the configuration
		this.getEmbedCode = function(runnable, index) {			
			
			// selected clip only: given in argument or in configuration
			index = typeof index == 'number' ? index : opts.index;
			if (index >= 0) {
				conf.playlist = [ self.getPlaylist()[index] ];				
			}
			
			// setup absolute path for each clip
			index = 0;
			$f.each(conf.playlist, function() {
				conf.playlist[index++].url = toAbsolute(this.url, this.baseUrl);
			});			
			
			var html = flashembed.getHTML(opts, {config: conf});
			
			if (!runnable)  {
				html = html.replace(/\</g, "&lt;").replace(/\>/g, "&gt;"); 	
			}				
				
			return html;			
		};
		
		return self;
		
	});
	
})();		
