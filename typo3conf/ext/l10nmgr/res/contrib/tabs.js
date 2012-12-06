//Set tab to intially be selected when page loads:
//[which tab (1=first tab), ID of tab content to display (or "" if no corresponding tab content)]:
var initialtab=[1, "sc1"]

				//Turn menu into single level image tabs (completely hides 2nd level)?
				var turntosingle=0 //0 for no (default), 1 for yes

				//Disable hyperlinks in 1st level tab images?
				var disabletablinks=0 //0 for no (default), 1 for yes

				var previoustab=""

				if (turntosingle==1)
				document.write('<style type="text/css">\n#tabcontentcontainer{display: none;}\n</style>')

				function expandcontent(cid, aobject){
				if (disabletablinks==1)
				aobject.onclick=new Function('return false')
				if (document.getElementById && turntosingle==0){
				highlighttab(aobject)
				if (previoustab!='')
				document.getElementById(previoustab).style.display="none"
				if (cid!=''){
				document.getElementById(cid).style.display="block"
				previoustab=cid
				}
				}
				}
				
				/**
				 * [Describe function...]
				 *
				 * @param	[type]		$aobject: ...
				 * @return	[type]		...
				 */
				function highlighttab(aobject){
				if (typeof tabobjlinks=='undefined')
				collectddtabs()
				for (i=0; i<tabobjlinks.length; i++)
				tabobjlinks[i].className=""
				aobject.className="current"
				}
				
				/**
				 * [Describe function...]
				 *
				 * @return	[type]		...
				 */
				function collectddtabs(){
				var tabobj=document.getElementById('ddtabs')
				tabobjlinks=tabobj.getElementsByTagName('A')
				}
				
				/**
				 * [Describe function...]
				 *
				 * @return	[type]		...
				 */
				function do_onload(){
				collectddtabs()
				expandcontent(initialtab[1], tabobjlinks[initialtab[0]-1])
				}
				
				if (window.addEventListener)
				window.addEventListener('load', do_onload, false)
				else if (window.attachEvent)
				window.attachEvent('onload', do_onload)
				else if (document.getElementById)
				window.onload=do_onload
				
				function popup(url,name,w,h,scroll) {
					LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
					TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
					settings = 
					'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
					window.open(url,name,settings)
				}

