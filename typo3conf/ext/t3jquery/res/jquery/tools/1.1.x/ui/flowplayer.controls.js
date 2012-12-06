/**
 * flowplayer.controls 3.0.2. Flowplayer JavaScript plugin.
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
 * Revision: 1424 
 */ 
$f.addPlugin("controls", function(wrap, options) {
	
		
//{{{ private functions
		
	function fixE(e) {
		if (typeof e == 'undefined') { e = window.event; }
		if (typeof e.layerX == 'undefined') { e.layerX = e.offsetX; }
		if (typeof e.layerY == 'undefined') { e.layerY = e.offsetY; }
		return e;
	}
	
	function w(e) {
		return e.clientWidth;	
	}
	
	function offset(e) {
		return e.offsetLeft;	
	}
	
	/* a generic dragger utility for hoirzontal dragging */
	function Draggable(o, min, max, offset) { 
		
		var dragging = false;
		
		function foo() { }
		
		o.onDragStart	= o.onDragStart || foo;
		o.onDragEnd		= o.onDragEnd 	 || foo;
		o.onDrag			= o.onDrag 		 || foo;	
		
		function move(x) {
			// must be withing [min, max]
			if (x > max) { return false; }
			if (x < min) { return false; } 
			o.style.left = x + "px";	
			return true;
		}
		
		function end() {
			document.onmousemove = null;
			document.onmouseup   = null;
			o.onDragEnd(parseInt(o.style.left, 10));
			dragging = false;
		}

		function drag(e) {
			e = fixE(e); 
			var x = e.clientX - offset; 
			if (move(x)) {
				dragging = true;
				o.onDrag(x);		
			} 
			return false;
		}
		
		o.onmousedown = function(e)  {
			e = fixE(e);
			o.onDragStart(parseInt(o.style.left, 10)); 
			document.onmousemove		= drag;
			document.onmouseup		= end;
			return false;	
		};
		
		this.dragTo = function(x) {
			if (move(x)) {
				o.onDragEnd(x);		
			}	
		};
		
		this.setMax = function(val) {
			max = val;	
		};
		
		this.isDragging = function() {
			return dragging;	
		};

		return this; 
	}

	function extend(to, from) {
		if (from) {
			for (key in from) {
				if (key) {
					to[key] = from[key];		
				} 
			}
		}
	}
	
	function byClass(name) {
		var els = wrap.getElementsByTagName("*");		
		var re = new RegExp("(^|\\s)" + name + "(\\s|$)");
		for (var i = 0; i < els.length; i++) {
			if (re.test(els[i].className)) {
				return els[i];
			}
		}
	}
	
	// prefix integer with zero when nessessary 
	function pad(val) {
		val = parseInt(val, 10);
		return val >= 10 ? val : "0" + val;
	}
	
	// display seconds in hh:mm:ss format
	function toTime(sec) {
		
		var h = Math.floor(sec / 3600);
		var min = Math.floor(sec / 60);
		sec = sec - (min * 60);
		
		if (h >= 1) {
			min -= h * 60;
			return pad(h) + ":" + pad(min) + ":" + pad(sec);
		}
		
		return pad(min) + ":" + pad(sec);
	}
	
	function getTime(time, duration) {
		return "<span>" + toTime(time) + "</span> <strong>" + toTime(duration) + "</strong>";	
	}
	
//}}}
	
	
	var self = this;
	
	var opts = {
		playHeadClass: 'playhead',
		trackClass: 'track',
		playClass: 'play',
		pauseClass: 'pause',
		bufferClass: 'buffer',
		progressClass: 'progress',
		
		timeClass: 'time',
		muteClass: 'mute',
		unmuteClass: 'unmute',
		duration: 0,		
		
		template: '<a class="play">play</a>' + 
					 '<div class="track">' +
					 	'<div class="buffer"></div>' +
					 	'<div class="progress"></div>' +
					 	'<div class="playhead"></div>' +
					 '</div>' + 
					 '<div class="time"></div>' +
					 '<a class="mute">mute</a>'				 
	};
	
	extend(opts, options);
	
	if (typeof wrap == 'string') {
		wrap = document.getElementById(wrap);
	}
	
	if (!wrap) { return;	}
	
	// inner HTML
	if (!wrap.innerHTML.replace(/\s/g, '')) {
		wrap.innerHTML = opts.template;		
	}	 
	
	// get elements 
	var ball = byClass(opts.playHeadClass);
	var bufferBar = byClass(opts.bufferClass);
	var progressBar = byClass(opts.progressClass);
	var track = byClass(opts.trackClass);
	var time = byClass(opts.timeClass);
	var mute = byClass(opts.muteClass);
	
	
	// initial time
	time.innerHTML = getTime(0, opts.duration);
	
	// get dimensions 
	var trackWidth = w(track);
	var ballWidth = w(ball); 
	
	// initialize draggable playhead
	var head = new Draggable(ball, 0, 0, offset(wrap) + offset(track) + (ballWidth / 2)); 
	
	// track click moves playHead	
	track.onclick = function(e) {
		e = fixE(e);
		if (e.target == ball) { return false; }
		head.dragTo(e.layerX - ballWidth / 2);
	};
	
	// play/pause button
	var play = byClass(opts.playClass);
	
	play.onclick = function() {
		if (self.isLoaded()) {
			self.toggle();		
		} else {
			self.play();	
		}						
	};
	
	// mute/unmute button
	mute.onclick = function() {
		if (self.getStatus().muted)  {
			self.unmute();	
		} else {
			self.mute();	
		}
	};

	// setup timer
	var timer = null;
	
	function getMax(len, total) {
		var x = parseInt(Math.min(len / total * trackWidth, trackWidth - ballWidth / 2), 10);
		return isNaN(x) ? 0 : x; 	
	}
	
	self.onStart(function(clip) {
		
		var duration = clip.duration || 0;

		// clear previous timer		
		clearInterval(timer);
		 
		// begin timer		
		timer = setInterval(function()  {			
			
			var status = self.getStatus();			

			// time display
			if (status.time)  {				
				time.innerHTML = getTime(status.time, clip.duration);	
			} 
			
			if (status.time === undefined) {
				clearInterval(timer);
				return;
			}
			
			// buffer width
			var x = getMax(status.bufferEnd, duration);
			bufferBar.style.width = x + "px";
			head.setMax(x);	
		
			
			
			// progress width
			if (!self.isPaused() && !head.isDragging()) {
				x = getMax(status.time, duration);
				progressBar.style.width = x + "px";
				ball.style.left = (x -ballWidth / 2) + "px";
			}
			
		}, 500);
	});
	
	self.onBegin(function() {
		play.className = opts.pauseClass;		
	});

	
	// pause / resume states	
	self.onPause(function() {
		play.className = opts.playClass;
	});

	self.onResume(function() {
		play.className = opts.pauseClass;
	});
	
	
	// mute / unmute states	
	self.onMute(function() {
		mute.className = opts.unmuteClass;
	});

	self.onUnmute(function() {
		mute.className = opts.muteClass;
	});
	
	
	// clear timer when clip ends	
	self.onFinish(function(clip) {		
		clearInterval(timer);	
	}); 
	
	self.onUnload(function() {
		time.innerHTML = getTime(0, opts.duration);
	});
	
	
	ball.onDragEnd = function(x) {
		var to = parseInt(x / trackWidth  * 100, 10) + "%";
		progressBar.style.width = x + "px";
		if (self.isLoaded()) {
			self.seek(to);		
		} 
	};
	
	ball.onDrag = function(x) {
		progressBar.style.width = x + "px";	
	};

	
	// return player instance to enable plugin chaining
	return self;
	
});
		
			
			

