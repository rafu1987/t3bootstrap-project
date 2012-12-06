ad_rtepasteplain = {
	
	editorIds: 	[],
	
	init: function() {	
		
		setTimeout(function() {
			try {			
				
				if (window.RTEarea) {
					for (var editorId in window.RTEarea) {					  
						if (
						  editorId.indexOf("data") != -1						  					  
						) {
							ad_rtepasteplain.editorIds.push("editorWrap" + editorId);
							continue;
						}
					};
					var numEditors = ad_rtepasteplain.editorIds.length;					
					for (var i = 0; i < numEditors; i++) {
						
						var _editorId 	= ad_rtepasteplain.editorIds[i];
																		
						var container 	= document.getElementById(_editorId);						
						var iframeEl 	= container.getElementsByTagName("iframe")[0];						
						
						
						var doc;
						if (iframeEl.contentDocument) { // DOM
							doc = iframeEl.contentDocument;
						} else if (iframeEl.contentWindow) { // IE
							doc = iframeEl.contentWindow.document;
						}
						doc._editorId = _editorId;						
						
						if (!ad_rtepasteplain.addEvent(doc, "paste", ad_rtepasteplain.handleEvent, false)) {
							ad_rtepasteplain.addEvent(doc, "keydown", ad_rtepasteplain.handleEvent, false);
						}						
					}					
				}
						
			} catch(e) { }
			
		}, 1000);
	
	},
	
	addEvent: function(el, eType, fn, uC) {
		if (el.addEventListener) {
			el.addEventListener(eType, fn, uC);
			return true;
		} else if(el.attachEvent) {
			el.attachEvent('on' + eType, fn);			
		} else {
			el['on' + eType] = fn;			 
		}
		var ret = el['on' + eType];
		if(ret == undefined)
			ret = false;
		
		return ret;
	},

	handleEvent: function(e) {
		
		e = e || window.event;				
		if(
			(e.type == "paste") ||
			((e.ctrlKey || e.metaKey) && e.keyCode == 86)
		) {	
				
			var doc = this.document || this;
				
			var text = null;
			
			if(text = ad_rtepasteplain.getClipboardText()) {	
				//replace newlines with br			
				text = ad_rtepasteplain.convertNewlines(text);
				ad_rtepasteplain.doInsertText(doc, text);					
				return false;
			} else {						
				// Clipboard access failed		
				ad_rtepasteplain.helperLayer.create(doc);						
			}
			
			if(e && e.preventDefault) {
		        e.preventDefault();
		    } else if(e.returnValue) {
		        e.returnValue = false;
		    }
			return false;							
		}		
	},
			
	getClipboardText: function() {
		
		var clipboardText = null;
		
		try {
			clipboardText = window.clipboardData.getData('Text');			
		} catch(e) { 
		
		}
				
		if(!clipboardText) {
	
			try {
			
				window.netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
				
				var clip = window.Components.classes["@mozilla.org/widget/clipboard;1"].getService(window.Components.interfaces.nsIClipboard);
				var trans = window.Components.classes["@mozilla.org/widget/transferable;1"].createInstance(window.Components.interfaces.nsITransferable);
				trans.addDataFlavor("text/unicode");
				clip.getData(trans, clip.kGlobalClipboard);
				
				var str = {}, strLength = {}, clipboardText;
				trans.getTransferData("text/unicode", str, strLength);
				str = str.value.QueryInterface(window.Components.interfaces.nsISupportsString);
				clipboardText = str.data.substring(0, strLength.value / 2);					
			} catch(e) { 
			
			}			
		}	
		
		return clipboardText;
	
	},
		
	convertNewlines: function(text){
		text = escape(text);
		if(text.indexOf('%0D%0A') > -1){
			re_nlchar = /%0D%0A/g ;
		} else if(text.indexOf('%0A') > -1){
			re_nlchar = /%0A/g ;
		} else if(text.indexOf('%0D') > -1){
			re_nlchar = /%0D/g ;
		} else {
			return unescape(text);
		}
		return unescape( text.replace(re_nlchar, '<br />') );
	},
	
	doInsertText: function(doc, text) {		
		try	{
			var selection = doc.selection;
			if ( selection.type == 'Control' )
				selection.clear();
			selection.createRange().pasteHTML( text );
		} catch(e) {
			doc.execCommand( "inserthtml", false, text );
		}					
	},
		
	helperLayer: {
				
		create: function(doc) {
		
			var editorId	= doc._editorId || null;
			var layerId 	= "ad_rtepasteplain_helperLayer"+ editorId;
			if(editorId && !document.getElementById(layerId)) {
				
        var labels      = ad_rtepasteplain.labels.layer;
							
        var layer       = document.createElement("div");
				layer.id 				= layerId;
				layer.className			= "ad_rtepasteplain_helperLayer";				
								
					var layerForm 			= document.createElement("form");
					layerForm.onsubmit 		= function() { 
						return false; 
					};
																
						var layerInfo 				= document.createElement("p");
						layerInfo.className			= "pasteInfo";
						layerInfo.innerHTML			= labels.info;
						
						var layerTextarea 			= document.createElement("textarea");
												
						var layerInsertBtn 			= document.createElement("button");
						layerInsertBtn.className	= "insert";
						layerInsertBtn.innerHTML	= labels.insert;
						layerInsertBtn.onclick		= function() {
							ad_rtepasteplain.helperLayer.insert(layer, layerTextarea.value, doc);
						};
						
						var layerCancelBtn 			= document.createElement("button");
						layerCancelBtn.className	= "cancel";
						layerCancelBtn.innerHTML	= labels.cancel;
						layerCancelBtn.onclick		= function() {
							ad_rtepasteplain.helperLayer.cancel(layer);
						};
				
					layerForm.appendChild(layerInfo);
					layerForm.appendChild(layerTextarea);
					layerForm.appendChild(layerInsertBtn);
					layerForm.appendChild(layerCancelBtn);
					
						
				layer.appendChild(layerForm);
				
				var container   = document.getElementById(editorId).getElementsByTagName("div")[0];		
				
				if(ad_rtepasteplain.getElByClassName('htmlarea-tbar', container)) {
				  var toolbar   = ad_rtepasteplain.getElByClassName('htmlarea-tbar', container)[0];
				}	else {	
				  var toolbar 	= container.getElementsByTagName("div")[0].getElementsByTagName('div')[0];
				}
				var toolbarHeight 	= parseInt(ad_rtepasteplain.getStyle(toolbar, "height")) || null;
				if(toolbarHeight)
					layer.style.top = (toolbarHeight+20) + "px";
					
				var toolbarWidth 	= parseInt(ad_rtepasteplain.getStyle(toolbar, "width")) || null;
				if(toolbarWidth)
					layer.style.width = (toolbarWidth-50) + "px";	
					
				container.appendChild(layer);
			}
			
			return;
		},
		
		insert: function(layer, value, doc){		
			if(value) {
				value = value.replace(/\n/g, "<br\/>");
				ad_rtepasteplain.doInsertText(doc, value);				
				this.remove(layer);
			}			
		},
		
		cancel : function(layer) {
			this.remove(layer);
		},
		
		remove: function(layer) {
			layer.parentNode.removeChild(layer);
		}
		
	},
		
	getStyle: function(el, prop){
		if (el.currentStyle) //IE
			return el.currentStyle[prop];
		else if (document.defaultView && document.defaultView.getComputedStyle) //Firefox
			return document.defaultView.getComputedStyle(el, "")[prop];
		else //try and get inline style
			return el.style[prop];
	},
	
	getElByClassName: function(classname, node) {
    if(!node) 
      node = document.getElementsByTagName("body")[0];
    var a = [];
    var re = new RegExp('\\b' + classname + '\\b');
    var els = node.getElementsByTagName("*");
    for(var i=0,j=els.length; i<j; i++)
        if(re.test(els[i].className))
          a.push(els[i]);
    return (a.length > 0) ? a : false;   
  }
	
};