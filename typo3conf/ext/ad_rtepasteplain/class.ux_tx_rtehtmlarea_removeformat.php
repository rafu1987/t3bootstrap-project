<?php
	class ux_tx_rtehtmlarea_removeformat extends tx_rtehtmlarea_removeformat {
		
		public function buildJavascriptConfiguration($RTEcounter) {
			global $TSFE, $LANG;
	
			$registerRTEinJavascriptString = "";
			
			$this->insertJS();
			
			return $registerRTEinJavascriptString;
		}
		
		function insertJS() {			
			
			global $LANG;
			$LANG->includeLLFile("EXT:ad_rtepasteplain/locallang.xml");
			
			$extKey 	= "ad_rtepasteplain";
			$extPath 	= t3lib_extMgm::extRelPath($extKey);
			
			$this->htmlAreaRTE->TCEform->additionalCode_pre["ux_tx_rtehtmlarea_removeformat"] = '
				<link rel="stylesheet" type="text/css" href="'. $extPath .'res/helpers.css" />
				<script type="text/javascript" src="'. $extPath .'res/pasteplain.js"></script>
				<script type="text/javascript">					
					'. $extKey . '.labels               = {};
					'. $extKey . '.labels.layer         = {};				
					'. $extKey . '.labels.layer.info    = \''. addcslashes($LANG->getLL($extKey . ".layer.info"), 	"'/") .'\';
					'. $extKey . '.labels.layer.insert  = \''. addcslashes($LANG->getLL($extKey . ".layer.insert"),	"'/") .'\';
					'. $extKey . '.labels.layer.cancel  = \''. addcslashes($LANG->getLL($extKey . ".layer.cancel"), "'/") .'\';
					'. $extKey . '.addEvent(window, "load", ad_rtepasteplain.init, false);
				</script>
			';
			
			return;		
		}		
	}
?>