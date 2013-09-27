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
require_once PATH_t3lib . 'class.t3lib_scbase.php';

/**
 * TYPO3 Backend module base
 *
 * @author		TEQneers GmbH & Co. KG <info@teqneers.de>
 * @package		TYPO3
 * @subpackage	tx_seo
 */
class  tx_tqseo_module_base extends t3lib_SCbase {
	###########################################################################
	# Attributes
	###########################################################################

	/**
	 * Page info
	 *
	 * @var array
	 */
	public $pageinfo = null;

	/**
	 * Module arguments
	 *
	 * @var array
	 */
	protected $_moduleArgs = array();

	/**
	 * Menu back link
	 *
	 * @var	string
	 */
	protected $_menuBackLink = null;

	/**
	 * Module url
	 *
	 * @var	string
	 */
	protected $_moduleUrl = null;
	
	/**
	 * Backend Form Protection object
	 * 
	 * @var t3lib_formprotection_BackendFormProtection
	 */
	protected $_formProtection = null;

	###########################################################################
	# Public methods
	###########################################################################

	/**
	 * Initializes the Module
	 *
	 * @return    void
	 */
	public function init()    {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		global $MCONF;

		parent::init();

		// Fetch module args
		$this->_moduleArgs = array();
		if( !empty($_GET[ $MCONF['name'] ]) ) {
			$this->_moduleArgs = (array)$_GET[ $MCONF['name'] ];
		}
		
		// Init form protection instance
		$this->_formProtection = t3lib_div::makeInstance('t3lib_formprotection_BackendFormProtection');
	}


	/**
	 * Main function of the module
	 *
	 * Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 */
	public function main() {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		// If no access or if ID == zero
		$this->doc = t3lib_div::makeInstance('mediumDoc');
		$this->doc->backPath = $BACK_PATH;

		$this->content.=$this->doc->startPage($this->_moduleTitle());
		$this->content.=$this->doc->header($this->_moduleTitle());
		$this->content.=$this->doc->spacer(5);
		$this->content.=$this->doc->spacer(10);
	}

	/**
	 * Generates the module content
	 *
	 * @return    void
	 */
	public function moduleContent() {
		$function = '';
		if( !empty($_GET['SET']['function']) ) {
			// GET-param
			$function = (string)$_GET['SET']['function'];
		} else if( !empty($this->MOD_SETTINGS['function']) ) {
			// Selector
			$function = (string)$this->MOD_SETTINGS['function'];
		} else {
			// None
			$function = 'main';
		}

		// security
		$function = strtolower( trim($function) );
		$function = preg_replace('[^a-z]', '' , $function);

		if( empty($function) ) {
			$function = 'main';
		}

		$method = 'execute'.$function;
		$call	= array($this, $method);

		if( !is_callable($call) ) {
			$method		= $method = 'executeMain';
			$function	= 'main';
		}

		// set url
		$this->__moduleUrl = $this->_moduleLink($function);

		$ret = $this->$method();

		if( !empty($ret) ) {
			$this->content .= $ret;
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return    void
	 */
	public function printContent()    {
		$this->content.=$this->doc->endPage();
		echo $this->content;
	}


	/**
	 * Module help links
	 *
	 * @return	string
	 */
	protected function getModuleHelp() {

	}

	/**
	 * Gets the filled markers that are used in the HTML template
	 *
	 * @return	array		The filled marker array
	 */
	protected function getTemplateMarkers() {
		$markers = array(
			'CSH'		=> $this->getModuleHelp(),
			'FUNC_MENU' => $this->getFunctionMenu(),
			'CONTENT'   => $this->content,
			'TITLE'     => $this->_moduleTitle(),
		);

		return $markers;
	}

	/**
	 * Gets the function menu selector for this backend module
	 *
	 * @return	string		The HTML representation of the function menu selector
	 */
	protected function getFunctionMenu() {
		$functionMenu = t3lib_BEfunc::getFuncMenu(
			$this->id,
			'SET[function]',
			$this->MOD_SETTINGS['function'],
			$this->MOD_MENU['function']
		);

		return $functionMenu;
	}

	/**
	 * Gets the buttons that shall be rendered in the docHeader
	 *
	 * @return	array		Available buttons for the docHeader
	 */
	protected function getDocHeaderButtons() {
		$buttons = array(
			'reload'	=> '',
			'shortcut'	=> $this->getShortcutButton(),
			'back'		=> '',
		);

		if( $this->_menuBackLink) {
			$buttons['back'] = '<a href="'.htmlspecialchars($this->_menuBackLink).'" title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.goback', TRUE) . '">' .
			  t3lib_iconWorks::getSpriteIcon('actions-view-go-back') .
			'</a>';
		}

		return $buttons;
	}

	/**
	 * Gets the button to set a new shortcut in the backend (if current user is allowed to)
	 *
	 * @return	string		HTML representiation of the shortcut button
	 */
	protected function getShortcutButton() {
		$result = '';
		if ($GLOBALS['BE_USER']->mayMakeShortcut()) {
			$result = $this->doc->makeShortcutIcon('', 'function', $this->MCONF['name']);
		}

		return $result;
	}

	/**
	 * Generate link to module
	 *
	 *
	 * @param	string	$action	Action
	 * @param	array	$params	Params
	 * @return	string
	 */
	protected function _moduleLink($action, $params = null) {
		global $MCONF;

		$args = '';
		if( !empty($params) ) {
			$params = array(
				$MCONF['name'] => $params
			);

			$args = '&'.http_build_query($params);

		}

		$baseUrl = $MCONF['_'];

		return $baseUrl.'&SET[function]='.rawurlencode($action).$args;;
	}

	/**
	 * Generate link to module (for onclick handler)
	 *
	 *
	 * @param	string	$action	Action
	 * @param	array	$params	Params
	 * @return	string
	 */
	protected function _moduleLinkOnClick($action, $params = null) {
		$url = $this->_moduleLink($action, $params);
		return 'window.location.href=\''.$url.'\'; return false;';
	}

	protected function _moduleTitle() {
		return $GLOBALS['LANG']->getLL('title');
	}

	/**
	 * Create session token
	 * 
	 * @param	string	$formName	Form name/Session token name
	 * @return	string
	 */
	protected function _sessionToken($formName) {
		$token = $this->_formProtection->generateToken($formName);
		return $token;
	}
	
}

?>