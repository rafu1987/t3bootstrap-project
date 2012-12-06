<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Raphael Zschorsch <zschorsch@medialis.net>
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
 * ************************************************************* */

class tx_medmarkdown_text {  
    
    function user_text($PA, $fobj) {
    	$content = '
    		<style type="text/css">
    .editor{
      max-width: 700px;
      margin: 0;
      background: none;
      padding: 0;
      border: none;
    }

    /* clearfix */
    .clear-block:after{
      content:".";
      display:block;
      clear:both;
      visibility:hidden;
      line-height:0;
      height:0;
    }


    .editor .control{
      background-color: #ddd;
      background-image: -khtml-gradient(linear, left top, left bottom, from(#eee), to(#ddd));
      background-image: -moz-linear-gradient(top, #eee, #ddd);
      background-image: -ms-linear-gradient(top, #eee, #ddd);
      background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #eee), color-stop(100%, #ddd));
      background-image: -webkit-linear-gradient(top, #eee, #ddd);
      background-image: -o-linear-gradient(top, #eee, #ddd);
      background-image: linear-gradient(top, #eee, #ddd);
      color: #333;
      text-decoration: none;
      text-shadow:#fff 0 1px 0;
      border: 1px solid #ddd;
      -webkit-box-shadow: inset 0 1px 0 #f9f9f9;
      -moz-box-shadow: inset 0 1px 0 #f9f9f9;
      box-shadow: inset 0 1px 0 #f9f9f9;
    }

    .editor .control{
      font-size: 11px;
      font-family: Helvetica, Arial;
      font-weight: bold;
      float: left;
      padding: 3px 10px;
      margin: 0 4px 4px 0;
      -moz-border-radius:5px;
      -webkit-border-radius:5px;
      border-radius:5px;
    }

    .editor .control span{
      text-decoration: underline;
    }

    .editor .control.c-bold{font-weight:900;}
    .editor .control.c-italic{font-style: italic;}
    .editor .control.c-link{color: #0066CC;}


    .editor .control:hover{
      cursor: pointer;
      background: #fff;
    }
    		</style>
    	';
    	$content .= '<script type="text/javascript" src="../typo3conf/ext/medmarkdown/res/js/jquery-1.8.3.min.js"></script>';
    	$content .= '<script type="text/javascript" src="../typo3conf/ext/medmarkdown/res/js/jquery.markdown-editor.js"></script>';
    	$content .= '<script type="text/javascript" src="../typo3conf/ext/medmarkdown/res/js/functions.js"></script>';
        
        $content .= '
        <div class="editor">
    <div class="controls clear-block hide-if-no-js">
      <a class="control c-bold" accesskey="b"><strong>B</strong></a>
      <a class="control c-italic" accesskey="i"><em>I</em></a>
      <a class="control c-link" accesskey="a">LINK</a>
      <a class="control c-image" accesskey="m">I<span>m</span>age</a>
      <a class="control c-quote" accesskey="q"><span>Q</span>uote</a>
      <a class="control c-code" accesskey="c"><span>C</span>ode</a>
    </div>        
        ';
        
        $content .= '<textarea class="formField2" style="width: 460px; position: relative;" cols="48" rows="10" wrap="virtual" name="' . $PA['itemFormElName'] . '">'.htmlspecialchars($PA['itemFormElValue']).'</textarea>';
        
        $content .= '</div>';
        
        return $content;
    }

}

?>