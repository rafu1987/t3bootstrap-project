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

class user_t3bootstrap {

    public function imageWidth($content, $conf) {
        $width = $this->cObj->stdWrap($content, $conf['width.']);
        $height = $this->cObj->stdWrap($content, $conf['height.']);
        $crop = $this->cObj->stdWrap($content, $conf['crop.']);

        if ($width && !$crop) {
            return $width;
        } else if ($width && $height && $crop) {
            return $width . 'c';
        }
    }

    public function imageHeight($content, $conf) {
        $width = $this->cObj->stdWrap($content, $conf['width.']);
        $height = $this->cObj->stdWrap($content, $conf['height.']);
        $crop = $this->cObj->stdWrap($content, $conf['crop.']);

        if (!$width && $height) {
            return $height;
        } else if ($width && $height && $crop) {
            return $height . 'c';
        }
    }

    public function imageEnlarge($content, $conf) {
        $enlarge = $this->cObj->stdWrap($content, $conf['enlarge.']);
        $gallery = $this->cObj->stdWrap($content, $conf['gallery.']);
        $uid = $this->cObj->stdWrap($content, $conf['uid.']);
        $link = $this->cObj->stdWrap($content, $conf['link.']);

        if (!$link) {
            if ($enlarge && !$gallery) {
                return 'class="lightbox"';
            } else {
                return 'class="lightbox" rel="gallery[' . $uid . ']"';
            }
        }
    }

    public function imageLink($content, $conf) {
        $file = $this->cObj->stdWrap($content, $conf['file.']);
        $link = $this->cObj->stdWrap($content, $conf['link.']);

        if ($link) {
            return $link;
        } else {
            return $file;
        }
    }

    public function imageCols($content, $conf) {
        $cols = $this->cObj->stdWrap($content, $conf['cols.']);
        $colsPlus = $cols+1;
        $uid = $this->cObj->stdWrap($content, $conf['uid.']);

        if ($cols > 1) {
            $GLOBALS ['TSFE']->additionalHeaderData['t3bootstrap_' . $uid] = '
                <style type="text/css">
                    .t3bootstrap-text-image-inner-image-single.t3bootstrap-text-image-inner-image-single-' . $uid . ':nth-child(' . $colsPlus . 'n+'.$cols.') {
                        margin-right: 0;
                    }                      
                </style>
            ';
            $GLOBALS ['TSFE']->additionalFooterData['t3bootstrap_' . $uid] = '
                <script type="text/javascript">
                    (function($) {
                        $(document).ready(function() {
                            $(".t3bootstrap-text-image-inner-image-single.t3bootstrap-text-image-inner-image-single-' . $uid . ':nth-child('.$cols.'n)").after("<div class=\"t3bootstrap-spacer\"></div>");
                        });
                    })(jQuery);    
                </script>
            ';
        } else {
            $GLOBALS ['TSFE']->additionalHeaderData['t3bootstrap_' . $uid] = '
                <style type="text/css">
                    .t3bootstrap-text-image-inner-image-single.t3bootstrap-text-image-inner-image-single-' . $uid . ' {
                        display: block;
                        margin-right: 0;
                    }
                </style>
            ';
        }
    }

    public function imageEffect($content, $conf) {
        $effect = $this->cObj->stdWrap($content, $conf['effect.']);
        $blackwhite = $this->cObj->stdWrap($content, $conf['blackwhite.']);

        if ($blackwhite) {
            $blackwhiteFinal = "img-desaturate";
        }

        if ($effect == 1) {
            return 'class="img-rounded ' . $blackwhiteFinal . '"';
        } else if ($effect == 2) {
            return 'class="img-circle ' . $blackwhiteFinal . '"';
        } else if ($effect == 3) {
            return 'class="img-polaroid ' . $blackwhiteFinal . '"';
        } else {
            return false;
        }
    }

    public function imageClass($content, $conf) {
        $uid = $this->cObj->stdWrap($content, $conf['uid.']);
        
        return 't3bootstrap-text-image-inner-image-single t3bootstrap-text-image-inner-image-single-' . $uid;
    }

    public function buttonClasses($content, $conf) {
        $size = $this->cObj->stdWrap($content, $conf['size.']);
        $style = $this->cObj->stdWrap($content, $conf['style.']);
        $block = $this->cObj->stdWrap($content, $conf['block.']);
        $deactivate = $this->cObj->stdWrap($content, $conf['deactivate.']);
        $customClass = $this->cObj->stdWrap($content, $conf['custom.']);
        $toTop = $this->cObj->stdWrap($content, $conf['totop.']);

        // To top
        if ($toTop)
            $toTopFinal = "btn-totop";

        // Size
        switch ($size) {
            case 1:
                $sizeFinal = '';
                break;
            case 2:
                $sizeFinal = "btn-large";
                break;
            case 3:
                $sizeFinal = "btn-small";
                break;
            case 4:
                $sizeFinal = "btn-mini";
                break;
        }

        // Block element
        if ($block) {
            $blockFinal = "btn-block";
        }

        // Deactivate
        if ($deactivate) {
            $deactivateFinal = "disabled";
        }

        // Custom
        if ($customClass) {
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('description', 'tx_medbootstraptools_buttonstyles', 'hidden=0 AND deleted=0 AND uid=' . $customClass, '', '', '');
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

            $customClassFinal = $row['description'];
        }

        // Create button
        switch ($style) {
            case 1:
                $btn = "";
                break;
            case 2:
                $btn = "btn-primary";
                break;
            case 3:
                $btn = "btn-info";
                break;
            case 4:
                $btn = "btn-success";
                break;
            case 5:
                $btn = "btn-warning";
                break;
            case 6:
                $btn = "btn-danger";
                break;
            case 7:
                $btn = "btn-inverse";
                break;
        }

        // Output button
        $btnFinal = 'btn ' . $btn . ' ' . $sizeFinal . ' ' . $blockFinal . ' ' . $deactivateFinal . ' ' . $customClassFinal . ' ' . $toTopFinal;

        return 'class="' . trim($btnFinal) . '"';
    }

    public function buttonParameter($content, $conf) {
        $link = $this->cObj->stdWrap($content, $conf['parameter.']);
        $toTop = $this->cObj->stdWrap($content, $conf['totop.']);

        if ($link && !$toTop) {
            return $link;
        } else {
            return 'javascript:void(0);';
        }
    }

    public function buttonIcon($content, $conf) {
        $icon = $this->cObj->stdWrap($content, $conf['icon.']);
        $position = $this->cObj->stdWrap($content, $conf['position.']);
        $white = $this->cObj->stdWrap($content, $conf['white.']);

        // White
        if ($white)
            $whiteFinal = 'icon-white';

        // Position
        if ($icon) {
            switch ($position) {
                case 1:
                    return '<i class="' . $icon . ' ' . $whiteFinal . '"></i>&nbsp;|';
                    break;
                case 2:
                    return '|&nbsp;<i class="' . $icon . ' ' . $whiteFinal . '"></i>';
                    break;
            }
        }
    }

    public function buttonAlign($content, $conf) {
        $align = $this->cObj->stdWrap($content, $conf['align.']);

        return '<p class="align-' . $align . '">|</p>';
    }

    public function colSize($content, $conf) {
        $size = $this->cObj->stdWrap($content, $conf['size.']);
        $offset = $this->cObj->stdWrap($content, $conf['offset.']);
        
        if (!$offset) {
            return 'span'.$size;
        } else {
            return 'span'.$size . ' ' . 'offset'.$offset;
        }
    }

    public function carouselItem($content, $conf) {
        $sectionPos = $this->cObj->stdWrap($content, $conf['sectionPos.']);

        if ($sectionPos == 1) {
            return "item active";
        } else {
            return "item";
        }
    }

    public function carouselHeadline($content, $conf) {
        $headline = $this->cObj->stdWrap($content, $conf['headline.']);
        $text = $this->cObj->stdWrap($content, $conf['text.']);

        if ($headline && !$text) {
            return '<div class="carousel-caption"><h4>' . $headline . '</h4></div>';
        } else if (!$headline && $text) {
            return '<div class="carousel-caption">';
        } else if ($headline && $text) {
            return '<div class="carousel-caption"><h4>' . $headline . '</h4>';
        }
    }

    public function carouselText($content, $conf) {
        $headline = $this->cObj->stdWrap($content, $conf['headline.']);
        $text = $this->cObj->stdWrap($content, $conf['text.']);

        if ($headline && !$text) {
            return '';
        } else if (!$headline && $text) {
            return $text . '</div>';
        } else if ($headline && $text) {
            return $text . '</div>';
        }
    }

    public function carouselID($content, $conf) {
        $uid = $this->cObj->stdWrap($content, $conf['uid.']);
        $control = $conf['control'];

        if ($control == 1) {
            return '#t3bootstrap-carousel-' . $uid;
        } else {
            return 't3bootstrap-carousel-' . $uid;
        }
    }

    public function carouselWidth($content, $conf) {
        $uid = $this->cObj->stdWrap($content, $conf['uid.']);

        // Get flexform data
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_templavoila_flex', 'tt_content', 'uid=' . $uid, '', '', '');
        $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

        // Convert xml to array
        $rowArr = t3lib_div::xml2array($row['tx_templavoila_flex']);

        $width = $rowArr['data']['sDEF']['lDEF']['field_width']['vDEF'];

        return $width;
    }

    public function carouselJS($content, $conf) {
        $uid = $this->cObj->stdWrap($content, $conf['uid.']);
        $interval = intval($this->cObj->stdWrap($content, $conf['interval.']));
        $pause = intval($this->cObj->stdWrap($content, $conf['pause.']));

        if ($pause == 1)
            $pauseFinal = "hover";
        else
            $pauseFinal = "";

        if ($interval) {
            $GLOBALS ['TSFE']->additionalFooterData['t3bootstrap_' . $uid] = '
                <script type="text/javascript">
                    (function($) {
                        $(document).ready(function() {
                            $("#t3bootstrap-carousel-' . $uid . '").carousel({
                                interval: ' . $interval . ',
                                pause: "' . $pauseFinal . '"
                            });                        
                        });
                    })(jQuery);
                </script>
            ';
        }
    }

    public function spacerStyle($content, $conf) {
        $top = $this->cObj->stdWrap($content, $conf['top.']);
        $bottom = $this->cObj->stdWrap($content, $conf['bottom.']);
        $custom = $this->cObj->stdWrap($content, $conf['custom.']);

        if ($custom) {
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('hex', 'tx_medbootstraptools_colors', 'hidden=0 AND deleted=0 AND uid=' . $custom, '', '', '');
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

            $customStyleFinal = $row['hex'];
            
            $customFinal = 'border-top: 1px solid ' . $customStyleFinal . ';';
        }

        if ($top && !$bottom) {
            return 'margin: ' . $top . 'px 0 0 0;' . $customFinal;
        } else if (!$top && $bottom) {
            return 'margin: 0 0 ' . $bottom . 'px 0;' . $customFinal;
        } else if ($top && $bottom) {
            return 'margin: ' . $top . 'px 0 ' . $bottom . 'px 0;' . $customFinal;
        } else {
            return '';
        }
    }

    public function spacerClass($content, $conf) {
        $color = $this->cObj->stdWrap($content, $conf['color.']);

        switch ($color) {
            case 1:
                return 't3bootstrap-spacer-white';
                break;
            case 2:
                return 't3bootstrap-spacer-grey';
                break;
        }
    }

    public function containerClass($content, $conf) {
        $background = $this->cObj->stdWrap($content, $conf['background.']);
        $custom = $this->cObj->stdWrap($content, $conf['custom.']);
        
        if($custom) $textColor = $this->getContrastYIQ($custom);        

        switch ($background) {
            case 1:
                return 'container-wrap '.$textColor;
                break;
            case 2:
                return 'container-wrap container-wrap-grey '.$textColor;
                break;
        }
    }

    public function containerStyle($content, $conf) {
        $custom = $this->cObj->stdWrap($content, $conf['custom.']);

        // Custom
        if ($custom) {
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('hex', 'tx_medbootstraptools_colors', 'hidden=0 AND deleted=0 AND uid=' . $custom, '', '', '');
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

            $customStyleFinal = $row['hex'];
        }        
        
        if ($custom) {
            return 'background:' . $customStyleFinal . ';';
        }

    }

    /* http://24ways.org/2010/calculating-color-contrast */
    private function getContrastYIQ($hexcolor) {
        $r = hexdec(substr($hexcolor, 0, 2));
        $g = hexdec(substr($hexcolor, 2, 2));
        $b = hexdec(substr($hexcolor, 4, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        
        if($yiq >= 128) {
            $yigFinal = '';
        }
        else {
            $yigFinal = 'text-white';
        }
        
        return $yigFinal;
    }

    private function debug($var) {
        print_r("<pre>") . print_r($var) . print_r("</pre>");
    }

}

?>