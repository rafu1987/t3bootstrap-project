<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 TEQneers GmbH & Co. KG <info@teqneers.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

/**
 * TYPO3 Backend module tree
 *
 * @author		TEQneers GmbH & Co. KG <info@teqneers.de>
 * @package		TYPO3
 * @subpackage	tx_seo
 */
class  tx_tqseo_module_tree extends tx_tqseo_module_base {
	###########################################################################
	# Attributes
	###########################################################################

	/**
	 * Doc template
	 *
	 * @var string
	 */
	protected $_docTemplate = 'tree';

	###########################################################################
	# Public methods
	###########################################################################

	/**
	 * Main function of the module
	 *
	 * Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 */
	public function main() {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))    {
			// Page template
			$this->doc = t3lib_div::makeInstance('template');
			$this->doc->setModuleTemplate(t3lib_extMgm::extPath('tq_seo') . 'res/backend/template/'.$this->_docTemplate.'.html');
			$this->doc->backPath = $BACK_PATH;
			$this->pageRenderer = $this->doc->getPageRenderer();

			// ExtJS
			$this->pageRenderer->loadExtJS();
			$this->pageRenderer->enableExtJSQuickTips();

			// ExtJS Debug
			//$this->pageRenderer->enableExtJsDebug();

			// Std javascript
			$this->pageRenderer->addJsFile($BACK_PATH . t3lib_extMgm::extRelPath('tq_seo') . 'res/backend/js/TQSeo.js');

			// CSS
			$this->pageRenderer->addCssFile( $BACK_PATH . t3lib_extMgm::extRelPath('tq_seo') .  'res/backend/css/tqseo_backend.css' );


			// Set the form
			$this->doc->form = '<form name="tx_seo_form" id="tx_seo_form" method="post" action="">';

			// JavaScript for main function menu
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL) {
						document.location = URL;
					}
				</script>
			';


			$this->moduleContent();

			$content = $this->doc->moduleBody(
				array(),
				$this->getDocHeaderButtons(),
				$this->getTemplateMarkers()
			);

				// Renders the module page
			$this->content = $this->doc->render(
				$this->_moduleTitle(),
				$content
			);
		} else {
				// If no access or if ID == zero
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($this->_moduleTitle());
			$this->content.=$this->doc->header($this->_moduleTitle());
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

}

?>