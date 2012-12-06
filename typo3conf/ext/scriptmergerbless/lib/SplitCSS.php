<?php

class SplitCSS {

    protected $parsedCSS;
    protected $selCount = 0;
    protected $conf;
    protected $curCss = 0;
    protected $curLine = 0;
    protected $curparsedCSS = '';
    protected $cssFiles = array();
    protected $startToken = false;
    protected $plugins;
    protected $endTokenClassName = '';

    public function __construct($conf)
    {
        $this->conf = $conf;

        // define plugins to be used with CssMin
        $this->plugins = array(
            "Comment" => true,
            "String" => true,
            "Url" => true,
            "Expression" => true,
            "Ruleset" => true,
            "AtCharset" => true,
            "AtFontFace" => true,
            "AtImport" => true,
            "AtKeyframes" => true,
            "AtMedia" => true,
            "AtPage" => true,
            "AtVariables" => true
        );
    }

    /*
        Initualize object with CSS String to be checked against selector count threshold and return
        split up CSS files
    */
    public function init($cssData)
    {
        $this->parsedCSS = CssMin::parse($cssData, $plugins);

        $this->countSelectors();

        if($this->selCount > $this->conf['css.']['scriptmergerBless.']['threshold'] &&
           isset($this->conf['css.']['scriptmergerBless.']['activate']) &&
           $this->conf['css.']['scriptmergerBless.']['activate'] == 1) {
            $this->split();
        } else {
            $this->cssFiles[] = $cssData;
        }

        return array_reverse($this->cssFiles);
    }

    // Count the amount of selectors in initialized CSS string
    protected function countSelectors()
    {
        foreach ($this->parsedCSS as $css) {
            $objectToArr = (array) $css;
            $arrKeys = array_keys($objectToArr);
            foreach ($arrKeys as $s) {
                if ($s == "Selectors") {
                    $this->selCount++;
                }
            }
        }
    }

    // Split CSS string into chunks, taking care not to go over selector count threshold per chunk
    protected function split()
    {
        $i = 0;
        foreach($this->parsedCSS as $selector) {
            $selClass = get_class($selector);

            if($selClass == 'CssCommentToken') {
                continue;
            }

            if(strpos($selClass, 'At') !== false && strpos($selClass, 'StartToken') !== false && strpos($selClass, 'Ruleset') === false) {
                $this->startToken = $selector;
                $this->endTokenClassName = str_replace('StartToken', 'EndToken', $selClass);
            }

            $objectToArr = (array)$selector;
            $arrKeys = array_keys($objectToArr);
            foreach ($arrKeys as $s) {
                if ($s == "Selectors") {
                    $this->curLine++;
                }
            }

            if($this->curLine >= $this->conf['css.']['scriptmergerBless.']['threshold']) {
                if($this->startToken !== false && $selClass == $this->endTokenClassName) {
                    $this->curparsedCSS .= (string)$selector;
                    $this->startToken = false;
                    $this->finalizeCSS();
                } elseif($this->startToken === false && strpos($selClass, 'EndToken') !== false) {
                    $this->curparsedCSS .= (string)$selector;
                    $this->finalizeCSS();
                } elseif($this->startToken !== false  && strpos($selClass, 'EndToken') !== false) {
                    $this->curparsedCSS .= (string)$selector;
                    $this->curparsedCSS .= '}';
                    $this->finalizeCSS();
                    $j = $i + 1;
                    $nextClass = get_class($this->parsedCSS[$j]);
                    if(strpos($nextClass, 'EndToken') === false) {
                        $this->curparsedCSS .= (string)$this->startToken;
                    }
                } else {
                    $this->curparsedCSS .= (string)$selector;
                }
            } else {
                if($this->startToken !== false && $selClass == $this->endTokenClassName) {
                    $this->startToken = false;
                }

                if(!empty($this->curparsedCSS) || strpos($selClass, 'EndToken') === false)
                    $this->curparsedCSS .= (string)$selector;
            }

            $i++;
        }
        $this->finalizeCSS();
    }

    // Add chunk to list to be returned and clean up for next chunk
    protected function finalizeCSS()
    {
        if(!empty($this->curparsedCSS)) {
            $this->cssFiles[] = $this->curparsedCSS;
            $this->curparsedCSS = '';
            $this->curLine = 0;
            $this->curCss++;
        }
    }

}
