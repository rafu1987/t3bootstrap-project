<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Juergen Furrer <juergen.furrer@gmail.com>
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
 * 'tceFunc' for the 'jftcaforms' extension.
 *
 * @author     Juergen Furrer <juergen.furrer@gmail.com>
 * @package    TYPO3
 * @subpackage tx_jftcaforms
 */
class tx_jftcaforms_tceFunc
{
	/**
	 * This will render a slider to choose one value from a defined range.
	 * 
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	string		The HTML code for the TCEform field
	 */
	public function getExtSlider($PA, &$fObj)
	{
		$checkboxCode = NULL;

		// Define the unique vars
		$id_slider = uniqid('tceforms-slider-');
		$id_checkbox = uniqid('tceforms-check-');
		$var = uniqid('slider_');

		// The config from TCA-Field
		$conf = $PA['fieldConf']['config'];

		// define the options
		if (is_numeric($conf['width'])) {
			$option[] = "width: {$conf['width']}";
		}
		$lower = 0;
		if (is_numeric($conf['range']['lower'])) {
			$lower = $conf['range']['lower'];
			$option[] = "minValue: {$lower}";
		}
		if (is_numeric($conf['range']['upper'])) {
			$option[] = "maxValue: {$conf['range']['upper']}";
		}
		if (is_numeric($conf['decimalPrecision'])) {
			$option[] = "decimalPrecision: {$conf['decimalPrecision']}";
		}

		// 
		$default = (is_numeric($conf['default']) ? $conf['default'] : $lower);
		$value = (is_numeric($PA['itemFormElValue']) ? $PA['itemFormElValue'] : $default);

		$option[] = "value: ".$value;

		$emptyValue = ($conf['emptyValue'] ? $conf['emptyValue'] : '0');
		if (! is_numeric($PA['itemFormElValue']) && $emptyValue) {
			$disabled = TRUE;
		} else {
			$disabled = FALSE;
		}

		$option[] = "disabled: ".($disabled ? 'true' : 'false');
		$option[] = "renderTo: '{$id_slider}-outer'";

		// Language vars
		$from_ts = $GLOBALS['LANG']->sL('LLL:EXT:jftcaforms/locallang_db.xml:tt_content.pi_flexform.from_ts');
		if (! $from_ts) {
			$from_ts = 'From TS';
		}

		// get the pagerenderer
		$pagerender = $GLOBALS['TBE_TEMPLATE']->getPageRenderer();
		// Fix slider in hidden tabPanel

		$pagerender->addExtOnReadyCode("
		Ext.override(Ext.Slider, {
			getRatio: function() {
				var w = this.innerEl.getComputedWidth();
				var v = this.maxValue - this.minValue;
				return (v == 0 ? w : (w/v));
			}
		});", TRUE);

		// Add the slider
		$pagerender->addExtOnReadyCode("
		var {$var} = new Ext.Slider({
			".implode(",\n	", $option)."
		});
		{$var}.on('change', function(slider, newValue) {
			Ext.get('{$id_slider}').set({value: newValue});
			Ext.get('{$id_slider}-num').update(newValue ? newValue : '0');
		});");

		// The code for the checkbox will only be rendered, when emptyValue is set
		if ($emptyValue) {
			$pagerender->addExtOnReadyCode("
			Ext.get('{$id_checkbox}').on('click', function(obj1, obj2) {
				if (obj2.checked) {
					Ext.get('{$id_slider}').set({value: ''});
					Ext.get('{$id_slider}-num').update('".t3lib_div::slashJS($from_ts)."');
					{$var}.disable();
				} else {
					{$var}.enable();
					var newValue = {$var}.getValue();
					Ext.get('{$id_slider}').set({value: newValue});
					Ext.get('{$id_slider}-num').update(newValue ? newValue : '{$lower}');
				}
			});");
			$checkboxCode = '<input type="checkbox" class="checkbox" id="'.$id_checkbox.'" name="'.$PA['itemFormElName'].'_cb" style="float:left;"'.($disabled ? ' checked="checked"' : '').'>';
		}

		return '' .
			'<div class="t3-form-field t3-form-field-flex">' .
				'<input type="hidden" name="'.$PA['itemFormElName'].'" value="'.($disabled ? '' : $value).'" id="'.$id_slider.'"/>' .
				$checkboxCode .
				'<div id="'.$id_slider.'-num">'.($disabled ? $from_ts : $value).'</div>' .
			'</div>' .
			'<div id="'.$id_slider.'-outer"></div>';
	}

	/**
	 * This will render a spinner to choose one value from a defined range.
	 * 
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	string		The HTML code for the TCEform field
	 */
	public function getExtSpinner($PA, &$fObj)
	{
		$conf = $PA['fieldConf']['config'];
		if (class_exists(t3lib_utility_VersionNumber) && t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) < 4005000) {
			// Fallback if the used Typo3 Version is older then 4.5
			t3lib_div::devLog("ExtSpinner available in Typo3 4.5.x", 'jftcaform', 3);
			$upper = (is_numeric($conf['range']['upper']) ? $conf['range']['upper'] : '100');
			$PA['fieldConf']['config'] = array(
				'type' => 'input',
				'max' => strlen($upper),
				'size' => strlen($upper),
				'range' => array(
					'lower' => (is_numeric($conf['range']['lower']) ? $conf['range']['lower'] : '0'),
					'upper' => $upper
				),
				'eval' => 'int',
			);
			if (isset($conf['default'])) {
				$PA['fieldConf']['config']['default'] = intval($conf['default']);
			}
			if (isset($conf['emptyValue'])) {
				$PA['fieldConf']['config']['checkbox'] = $conf['emptyValue'];
			}
			$tceforms = &$PA['pObj'];
			return $tceforms->getSingleField_SW($PA['table'], $PA['field'], $PA['row'], $PA);
		} else {
			// Define the unique vars
			$id_spinner = uniqid('tceforms-spinner-');
			$id_checkbox = uniqid('tceforms-check-');
			$var = uniqid('spinner_');

			// define the options
			if (is_numeric($conf['width'])) {
				$option[] = "width: {$conf['width']}";
			}
			$lower = 0;
			if (is_numeric($conf['range']['lower'])) {
				$lower = $conf['range']['lower'];
				$option[] = "minValue: {$lower}";
			}
			if (is_numeric($conf['range']['upper'])) {
				$option[] = "maxValue: {$conf['range']['upper']}";
			}

			// 
			$default = (is_numeric($conf['default']) ? $conf['default'] : $lower);
			$value = (is_numeric($PA['itemFormElValue']) ? $PA['itemFormElValue'] : $default);

			$emptyValue = ($conf['emptyValue'] ? $conf['emptyValue'] : '0');
			if (! is_numeric($PA['itemFormElValue']) && $emptyValue) {
				$disabled = TRUE;
			} else {
				$disabled = FALSE;
			}

			$option[] = "disabled: ".($disabled ? 'true' : 'false');

			// get the pagerenderer
			$pagerender = $GLOBALS['TBE_TEMPLATE']->getPageRenderer();

			if ($emptyValue) {
				$pagerender->addExtOnReadyCode("
				Ext.get('{$id_checkbox}').on('click', function(obj1, obj2) {
					if (obj2.checked) {
						Ext.get('{$id_spinner}').set({value: ''});
						{$var}.disable();
					} else {
						{$var}.enable();
						Ext.get('{$id_spinner}').set({value: '{$default}'});
					}
				});");
				$checkboxCode = '<input type="checkbox" class="checkbox" id="'.$id_checkbox.'" name="'.$PA['itemFormElName'].'_cb"'.($disabled ? ' checked="checked"' : '').'>';
			}

			// Add the Spinner Script
			$pagerender->addCssFile(t3lib_extMgm::extRelPath('jftcaforms') . 'res/extjs/ux/css/Spinner.css');
			$pagerender->addJsFile(t3lib_extMgm::extRelPath('jftcaforms')  . 'res/extjs/ux/Spinner.js');
			$pagerender->addJsFile(t3lib_extMgm::extRelPath('jftcaforms')  . 'res/extjs/ux/SpinnerField.js');

			// Add the spinner
			$pagerender->addExtOnReadyCode("
			var {$var} = new Ext.ux.form.SpinnerField({
				".implode(",\n	", $option)."
			});
			{$var}.applyToMarkup('{$id_spinner}');");

			return '' .
			'<div class="t3-form-field t3-form-field-flex">' .
				'<table><tr><td>' .
					$checkboxCode .
				'</td><td>' .
					'<input type="text" name="'.$PA['itemFormElName'].'" value="'.($disabled ? '' : $value).'" id="'.$id_spinner.'"/>' .
				'</td></tr></table>' .
			'</div>';
		}
	}


	/**
	 * This will render a color-picker tt the tca.
	 * 
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	string		The HTML code for the TCEform field
	 */
	public function getColorPicker($PA, &$fObj)
	{
		$conf = $PA['fieldConf']['config'];

		$id_picker = uniqid('tceforms-colorpicker-');
		$id_checkbox = uniqid('tceforms-check-');

		$value = ($PA['itemFormElValue'] ? $PA['itemFormElValue'] : '');
		$value = str_replace('#', '', $value);
		if (strlen($value) > 0 && ! preg_match("/[0-9a-f]{6}/i", $value)) {
			if ($value != 'on') {
				t3lib_div::devLog('Value "'.$value.'" is no valid HEX-value', 'jftcaforms', 1);
			}
			$value = '';
		}

		$emptyValue = ($conf['emptyValue'] ? $conf['emptyValue'] : '0');
		if (! $value && $emptyValue) {
			$disabled = TRUE;
		} else {
			$disabled = FALSE;
		}

		$checkObserve = NULL;
		if ($emptyValue) {
			$checkboxCode = '<input type="checkbox" class="checkbox" id="'.$id_checkbox.'" name="'.$PA['itemFormElName'].'_cb"'.($disabled ? ' checked="checked"' : '').' />';
			$checkObserve .= "
Event.observe('{$id_checkbox}', 'change', function(event){
	if (this.checked) {
		$('{$id_picker}').value = '';
	}
	$('{$id_picker}').disabled = this.checked;
});";
		}
		$checkObserve .= "
Event.observe('{$id_picker}', 'change', function(event){
	var reg = /[0-9a-f]{6}/i;
	if (! reg.test($('{$id_picker}').value)) {
		$('{$id_picker}').value = '';
	}
	$('{$id_picker}').value = $('{$id_picker}').value.toLowerCase();
});";

		// get the pagerenderer
		$pagerender = $GLOBALS['TBE_TEMPLATE']->getPageRenderer();

		// Add the colorpicker scripts
		$pagerender->addCssFile(t3lib_extMgm::extRelPath('jftcaforms') . 'res/colorpicker/css/colorpicker.css');
		$pagerender->addJsFile(t3lib_extMgm::extRelPath('jftcaforms') . 'res/colorpicker/js/colorpicker.js', 'text/javascript', FALSE);

		// Add the colorpicker
		$pagerender->addExtOnReadyCode("
var cp".md5($id_picker)." = new colorPicker('{$id_picker}',{
	color:'#".($value ? $value : '000000')."'
});{$checkObserve}");

		return '' .
		'<div class="t3-form-field t3-form-field-flex">' .
			'<table><tr><td>' .
				$checkboxCode .
			'</td><td>' .
				'#<input type="text" name="'.$PA['itemFormElName'].'" id="'.$id_picker.'" value="'.$value.'" size="6"'.($disabled ? ' disabled="disabled"' : '').' onfocus="blur()" />' .
			'</td></tr></table>' .
		'</div>';
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jftcaforms/lib/class.tx_jftcaforms_tceFunc.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jftcaforms/lib/class.tx_jftcaforms_tceFunc.php']);
}
?>