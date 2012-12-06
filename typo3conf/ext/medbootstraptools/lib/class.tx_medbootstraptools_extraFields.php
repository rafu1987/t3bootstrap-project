<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Raphael Zschorsch (zschorsch@medialis.net)
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

class tx_medbootstraptools_extraFields {

    public function renderBootstrapIcons() {
        $content = '
            <script type="text/javascript">
                function t3bootstrapPopup (url) {
                    fenster = window.open(url, "Popupfenster", "width=850,height=1085,resizable=no,scrollbars=no");
                    fenster.focus();
                    return false;
                }
            </script>
        ';

        $content .= '<p style="margin:12px 0 15px 0;"><b><a href="../typo3conf/ext/medbootstraptools/res/images/icons-twitter-bootstrap.jpg" target="_blank" onclick="return t3bootstrapPopup(this.href);">Iconliste anzeigen &raquo;</a></b></p>';

        return $content;
    }

    // Copied and modified from jftcaforms extension
    public function getExtSlider($PA, &$fObj) {
        $checkboxCode = NULL;

        // Define the unique vars
        $id_slider = uniqid('tceforms-slider-');
        $id_checkbox = uniqid('tceforms-check-');
        $var = uniqid('slider_');

        // The config from TCA-Field
        $conf = $PA['fieldConf']['config'];

        // Label
        $label = $conf['label'];
        
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

        $option[] = "value: " . $value;

        $emptyValue = ($conf['emptyValue'] ? $conf['emptyValue'] : '0');
        if (!is_numeric($PA['itemFormElValue']) && $emptyValue) {
            $disabled = TRUE;
        } else {
            $disabled = FALSE;
        }

        $option[] = "disabled: " . ($disabled ? 'true' : 'false');
        $option[] = "renderTo: '{$id_slider}-outer'";

        // Language vars
        $from_ts = $GLOBALS['LANG']->sL('LLL:EXT:jftcaforms/locallang_db.xml:tt_content.pi_flexform.from_ts');
        if (!$from_ts) {
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
			" . implode(",\n	", $option) . "
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
					Ext.get('{$id_slider}-num').update('" . t3lib_div::slashJS($from_ts) . "');
					{$var}.disable();
				} else {
					{$var}.enable();
					var newValue = {$var}.getValue();
					Ext.get('{$id_slider}').set({value: newValue});
					Ext.get('{$id_slider}-num').update(newValue ? newValue : '{$lower}');
				}
			});");
            $checkboxCode = '<input type="checkbox" class="checkbox" id="' . $id_checkbox . '" name="' . $PA['itemFormElName'] . '_cb" style="float:left;"' . ($disabled ? ' checked="checked"' : '') . '>';
        }

        return '' .
                '<input type="hidden" name="' . $PA['itemFormElName'] . '" value="' . ($disabled ? '' : $value) . '" id="' . $id_slider . '"/>' .
                $checkboxCode .
                '<div id="' . $id_slider . '-num">' . ($disabled ? $from_ts : $value) . '</div>' .
                '<div id="' . $id_slider . '-outer"></div>';
    }

    private function debug($var) {
        t3lib_utility_Debug::debug($var);
    }

}

?>