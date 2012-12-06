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

require_once(t3lib_extMgm::extPath('scriptmergerbless').'lib/cssmin.php');
require_once(t3lib_extMgm::extPath('scriptmergerbless').'lib/SplitCSS.php');

class ux_tx_scriptmerger extends tx_scriptmerger {
    // Overwriting method "processCSSfiles" to inject our code into scriptmerger
    protected function processCSSfiles() {
        // fetch all remaining css contents
        $this->getCSSfiles();

        // minify, compress and merging
        foreach ($this->css as $relation => $cssByRelation) {
            foreach ($cssByRelation as $media => $cssByMedia) {
                $mergedContent = '';
                $firstFreeIndex = -1;
                foreach ($cssByMedia as $index => $cssProperties) {
                    $newFile = '';

                    // file should be minified
                    if ($this->extConfig['css.']['minify.']['enable'] === '1' &&
                        !$cssProperties['minify-ignore']
                    ) {
                        $newFile = $this->minifyCSSfile($cssProperties);
                    }

                    // file should be merged
                    if ($this->extConfig['css.']['merge.']['enable'] === '1' &&
                        !$cssProperties['merge-ignore']
                    ) {
                        if ($firstFreeIndex < 0) {
                            $firstFreeIndex = $index;
                        }

                        // add content
                        $mergedContent .= $cssProperties['content'] . LF;

                        // remove file from array
                        unset($this->css[$relation][$media][$index]);

                        // we doesn't need to compress or add a new file to the array,
                        // because the last one will finally not be needed anymore
                        continue;
                    }

                    // file should be compressed instead?
                    if ($this->extConfig['css.']['compress.']['enable'] === '1' &&
                        function_exists('gzcompress') && !$cssProperties['compress-ignore']
                    ) {
                        $newFile = $this->compressCSSfile($cssProperties);
                    }

                    // minification or compression was used
                    if ($newFile !== '') {
                        $this->css[$relation][$media][$index]['file'] = $newFile;
                        $this->css[$relation][$media][$index]['content'] =
                            $cssProperties['content'];
                        $this->css[$relation][$media][$index]['basename'] =
                            $cssProperties['basename'];
                    }
                }

                // save merged content inside a new file
                if ($this->extConfig['css.']['merge.']['enable'] === '1' && $mergedContent !== '') {
                    if ($this->extConfig['css.']['uniqueCharset.']['enable'] === '1') {
                        $mergedContent = $this->uniqueCharset($mergedContent);
                    }

                    // Change from scriptmergerbless begin
                    $SplitCSS = new SplitCSS($this->extConfig);
                    $cssData = $SplitCSS->init($mergedContent);

                    foreach($cssData as $mergedContent) {
                    // Change from scriptmergerbless end
                        if ($this->extConfig['css.']['uniqueCharset.']['enable'] === '1') {
                            $mergedContent = $this->uniqueCharset($mergedContent);
                        }
                        // create property array
                        $properties = array(
                            'content' => $mergedContent,
                            'basename' => 'head-' . md5($mergedContent) . '.merged'
                        );

                        // write merged file in any case
                        $newFile = $this->tempDirectories['merged'] . $properties['basename'] . '.css';
                        if (!file_exists($newFile)) {
                            t3lib_div::writeFile($newFile, $properties['content']);
                        }

                        // file should be compressed
                        if ($this->extConfig['css.']['compress.']['enable'] === '1' &&
                            function_exists('gzcompress')
                        ) {
                            $newFile = $this->compressCSSfile($properties);
                        }

                        // add new entry
                        $this->css[$relation][$media][$firstFreeIndex]['file'] = $newFile;
                        $this->css[$relation][$media][$firstFreeIndex]['content'] =
                            $properties['content'];
                        $this->css[$relation][$media][$firstFreeIndex]['basename'] =
                            $properties['basename'];

                        $firstFreeIndex++;
                    // Change from scriptmergerbless begin
                    }
                    // Change from scriptmergerbless end

                }
            }
        }

        // write the conditional comments and possibly merged css files back to the document
        $this->writeCSStoDocument();
    }

}

?>
