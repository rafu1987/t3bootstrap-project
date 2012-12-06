<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 Stefan Galinski <stefan.galinski@gmail.com>
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

/** Minify: Import Processor */
$pathToScriptmerger = t3lib_extMgm::extPath('scriptmerger');
require_once($pathToScriptmerger . 'resources/minify/lib/Minify/ImportProcessor.php');
require_once($pathToScriptmerger . 'resources/minify/lib/Minify/CSS.php');
require_once($pathToScriptmerger . 'resources/minify/lib/Minify/CommentPreserver.php');
require_once($pathToScriptmerger . 'resources/minify/lib/Minify/CSS/Compressor.php');
require_once($pathToScriptmerger . 'resources/minify/lib/Minify/CSS/UriRewriter.php');

/**
 * This class contains the parsing and replacing functionality of css and javascript files.
 * Furthermore several wrapper methods to the project minify are available to minify, merge
 * and compress such files.
 *
 * @author Stefan Galinski <stefan.galinski@gmail.com>
 * @package scriptmerger
 */
class tx_scriptmerger {
	/**
	 * directories for minified, compressed and merged files
	 *
	 * @var array
	 */
	protected $tempDirectories = '';

	/**
	 * holds the extension configuration
	 *
	 * @var array
	 */
	protected $extConfig = array();

	/**
	 * holds the conditional comments
	 *
	 * @var array
	 */
	protected $conditionalComments = array();

	/**
	 * holds the javascript code
	 *
	 * Structure:
	 * - $relation (rel attribute)
	 *   - $media (media attribute)
	 *	 - $file
	 *	   |-content => string
	 *	   |-basename => string (base name of $file without file prefix)
	 *	   |-minify-ignore => bool
	 *	   |-merge-ignore => bool
	 *
	 * @var array
	 */
	protected $css = array();

	/**
	 * holds the javascript code
	 *
	 * Structure:
	 * - $file
	 *   |-content => string
	 *   |-basename => string (base name of $file without file prefix)
	 *   |-minify-ignore => bool
	 *   |-merge-ignore => bool
	 *
	 * @var array
	 */
	protected $javascript = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		// define temporary directories
		$this->tempDirectories = array(
			'main' => PATH_site . 'typo3temp/scriptmerger/',
			'temp' => PATH_site . 'typo3temp/scriptmerger/temp/',
			'minified' => PATH_site . 'typo3temp/scriptmerger/uncompressed/',
			'compressed' => PATH_site . 'typo3temp/scriptmerger/compressed/',
			'merged' => PATH_site . 'typo3temp/scriptmerger/uncompressed/'
		);

		// create missing directories
		foreach ($this->tempDirectories as $directory) {
			if (!is_dir($directory)) {
				t3lib_div::mkdir($directory);
			}
		}

		// prepare the extension configuration
		$this->prepareExtensionConfiguration();
	}

	/**
	 * Just a wrapper for the main function! It's used for the contentPostProc-output hook.
	 *
	 * This hook is executed if the page contains *_INT objects! It's called always at the
	 * last hook before the final output. This isn't the case if you are using a
	 * static file cache like nc_staticfilecache.
	 *
	 * @return bool
	 */
	public function contentPostProcOutput() {
		// only enter this hook if the page contains COA_INT or USER_INT objects
		if (!$GLOBALS['TSFE']->isINTincScript()) {
			return TRUE;
		}

		return $this->main();
	}

	/**
	 * Just a wrapper for the main function!  It's used for the contentPostProc-all hook.
	 *
	 * The hook is only executed if the page doesn't contains any *_INT objects. It's called
	 * always if the page wasn't cached or for the first hit!
	 *
	 * @return bool
	 */
	public function contentPostProcAll() {
		// only enter this hook if the page doesn't contains any COA_INT or USER_INT objects
		if ($GLOBALS['TSFE']->isINTincScript()) {
			return TRUE;
		}

		return $this->main();
	}

	/**
	 * This method fetches and prepares the extension configuration.
	 *
	 * @return void
	 */
	protected function prepareExtensionConfiguration() {
		// global extension configuration
		$this->extConfig = unserialize(
			$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['scriptmerger']
		);

		// typoscript extension configuration
		$tsSetup = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_scriptmerger.'];
		if (is_array($tsSetup)) {
			foreach ($tsSetup as $key => $value) {
				$this->extConfig[$key] = $value;
			}
		}

		// no compression allowed if content should be added inside the document
		if ($this->extConfig['css.']['addContentInDocument'] === '1') {
			$this->extConfig['css.']['compress.']['enable'] = '0';
		}

		if ($this->extConfig['javascript.']['addContentInDocument'] === '1') {
			$this->extConfig['javascript.']['compress.']['enable'] = '0';
		}

		// prepare ignore expressions
		if ($this->extConfig['css.']['minify.']['ignore'] !== '') {
			$this->extConfig['css.']['minify.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extConfig['css.']['minify.']['ignore']) .
				').*/isU';
		}

		if ($this->extConfig['css.']['compress.']['ignore'] !== '') {
			$this->extConfig['css.']['compress.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extConfig['css.']['compress.']['ignore']) .
				').*/isU';
		}

		if ($this->extConfig['css.']['merge.']['ignore'] !== '') {
			$this->extConfig['css.']['merge.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extConfig['css.']['merge.']['ignore']) .
				').*/isU';
		}

		if ($this->extConfig['javascript.']['minify.']['ignore'] !== '') {
			$this->extConfig['javascript.']['minify.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extConfig['javascript.']['minify.']['ignore']) .
				').*/isU';
		}

		if ($this->extConfig['javascript.']['compress.']['ignore'] !== '') {
			$this->extConfig['javascript.']['compress.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extConfig['javascript.']['compress.']['ignore']) .
				').*/isU';
		}

		if ($this->extConfig['javascript.']['merge.']['ignore'] !== '') {
			$this->extConfig['javascript.']['merge.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extConfig['javascript.']['merge.']['ignore']) .
				').*/isU';
		}
	}

	/**
	 * Contains the process logic of the whole plugin!
	 *
	 * @return void
	 */
	protected function main() {
		if ($this->extConfig['css.']['enable'] === '1' || $this->extConfig['javascript.']['enable'] === '1') {
			$this->getConditionalComments();

			if ($this->extConfig['javascript.']['enable'] === '1') {
				$this->processJavascriptFiles();
			}

			if ($this->extConfig['css.']['enable'] === '1') {
				$this->processCSSfiles();
			}

			$this->writeConditionalCommentsToDocument();
		}
	}

	/**
	 * Controller for the css parsing and replacement
	 *
	 * @return void
	 */
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
				}
			}
		}

		// write the conditional comments and possibly merged css files back to the document
		$this->writeCSStoDocument();
	}

	/**
	 * Some browser fail on parsing merged CSS files if multiple charset definitions are found.
	 * Therefor we replace all charset definition's with an empty string and add a single charset
	 * definition to the beginning of the content. At least Webkit engines fail badly.
	 *
	 * @param string $content
	 * @return string
	 */
	protected function uniqueCharset($content){
		if (!empty($this->extConfig['css.']['uniqueCharset.']['value'])) {
			$content = preg_replace('/@charset[^;]+;/', '', $content);
			$content = $this->extConfig['css.']['uniqueCharset.']['value'] . $content;
		}
		return $content;
	}

	/**
	 * Controller for the processing of the javascript files.
	 *
	 * @return void
	 */
	protected function processJavascriptFiles() {
		// fetch all javascript content
		$this->getJavascriptFiles();

		// minify, compress and merging
		foreach ($this->javascript as $section => $javascriptBySection) {
			$mergedContent = '';
			$firstFreeIndex = -1;
			foreach ($javascriptBySection as $index => $javascriptProperties) {
				$newFile = '';

				// file should be minified
				if ($this->extConfig['javascript.']['minify.']['enable'] === '1' &&
					!$javascriptProperties['minify-ignore']
				) {
					$newFile = $this->minifyJavascriptFile($javascriptProperties);
				}

				// file should be merged
				if ($this->extConfig['javascript.']['merge.']['enable'] === '1' &&
					!$javascriptProperties['merge-ignore']
				) {
					if ($firstFreeIndex < 0) {
						$firstFreeIndex = $index;
					}

					// add content
					$mergedContent .= $javascriptProperties['content'] . LF;

					// remove file from array
					unset($this->javascript[$section][$index]);

					// we doesn't need to compress or add a new file to the array,
					// because the last one will finally not be needed anymore
					continue;
				}

				// file should be compressed instead?
				if ($this->extConfig['javascript.']['compress.']['enable'] === '1' &&
					function_exists('gzcompress') && !$javascriptProperties['compress-ignore']
				) {
					$newFile = $this->compressJavascriptFile($javascriptProperties);
				}

				// minification or compression was used
				if ($newFile !== '') {
					$this->javascript[$section][$index]['file'] = $newFile;
					$this->javascript[$section][$index]['content'] =
						$javascriptProperties['content'];
					$this->javascript[$section][$index]['basename'] =
						$javascriptProperties['basename'];
				}
			}

			// save merged content inside a new file
			if ($this->extConfig['javascript.']['merge.']['enable'] === '1' && $mergedContent !== '') {
				// create property array
				$properties = array(
					'content' => $mergedContent,
					'basename' => $section . '-' . md5($mergedContent) . '.merged'
				);

				// write merged file in any case
				$newFile = $this->tempDirectories['merged'] . $properties['basename'] . '.js';
				if (!file_exists($newFile)) {
					t3lib_div::writeFile($newFile, $properties['content']);
				}

				// file should be compressed
				if ($this->extConfig['javascript.']['compress.']['enable'] === '1' &&
					function_exists('gzcompress')
				) {
					$newFile = $this->compressJavascriptFile($properties);
				}

				// add new entry
				$this->javascript[$section][$firstFreeIndex]['file'] = $newFile;
				$this->javascript[$section][$firstFreeIndex]['content'] =
					$properties['content'];
				$this->javascript[$section][$firstFreeIndex]['basename'] =
					$properties['basename'];
			}
		}

		// write javascript content back to the document
		$this->writeJavascriptToDocument();
	}

	/**
	 * Callback function to replace conditional comments with placeholders
	 *
	 * @param array $hits
	 * @return string
	 */
	protected function conditionalCommentsSave($hits) {
		$this->conditionalComments[] = $hits[0];
		return '###conditionalComment' . (count($this->conditionalComments) - 1) . '###';
	}

	/**
	 * Callback function to restore placeholders for conditional comments
	 *
	 * @param array $hits
	 * @return string
	 */
	protected function conditionalCommentsRestore($hits) {
		$results = array();
		preg_match('/\d+/is', $hits[0], $results);
		$result = '';
		if (count($results) > 0) {
			$result = $this->conditionalComments[$results[0]];
		}
		return $result;
	}

	/**
	 * This method parses the output content and saves any found conditional comments
	 * into the "conditionalComments" class property. The output content is cleaned
	 * up of the found results.
	 *
	 * @return void
	 */
	protected function getConditionalComments() {
		$pattern = '/<!--\[if.+?<!\[endif\]-->/is';
		$GLOBALS['TSFE']->content = preg_replace_callback(
			$pattern,
			array($this, 'conditionalCommentsSave'),
			$GLOBALS['TSFE']->content
		);
	}

	/**
	 * This method writes the conditional comments back into the final output content.
	 *
	 * @return void
	 */
	protected function writeConditionalCommentsToDocument() {
		$pattern = '/###conditionalComment\d+###/is';
		$GLOBALS['TSFE']->content = preg_replace_callback(
			$pattern,
			array($this, 'conditionalCommentsRestore'),
			$GLOBALS['TSFE']->content
		);
	}

	/**
	 * This method parses the output content and saves any found css files or inline code
	 * into the "css" class property. The output content is cleaned up of the found results.
	 *
	 * @return void
	 */
	protected function getCSSfiles() {
		// filter pattern for the inDoc styles (fetches the content)
		$filterInDocumentPattern = '/' .
			'<style.*?>' .					// This expression removes the opening style tag
			'(?:.*?\/\*<!\[CDATA\[\*\/)?' .	// and the optionally prefixed CDATA string.
			'\s*(.*?)' .					// We save the pure css content,
			'(?:\s*\/\*\]\]>\*\/)?' .		// remove the possible closing CDATA string
			'\s*<\/style>' .				// and closing style tag
			'/is';

		// parse all available css code inside link and style tags
		$cssTags = array();
		$pattern = '/' .
			'<(link|sty)' .							// Parse any link and style tags.
				'(?=.+?(?:media="(.*?)"|>))' .		// Fetch the media attribute
				'(?=.+?(?:href="(.*?)"|>))' .		// and the href attribute
				'(?=.+?(?:rel="(.*?)"|>))' .		// and the rel attribute
				'(?=.+?(?:title="(.*?)"|>))' .		// and the title attribute of the tag.
			'(?:[^>]+?\.css[^>]+?\/?>' .			// Continue parsing from \1 to the closing tag.
				'|le[^>]*?>[^>]+?<\/style>)\s*' .
			'/is';

		preg_match_all($pattern, $GLOBALS['TSFE']->content, $cssTags);
		if (!count($cssTags[0])) {
			return;
		}

		// remove any css code inside the output content
		$GLOBALS['TSFE']->content = preg_replace($pattern, '', $GLOBALS['TSFE']->content, count($cssTags[0]));

		// parse matches
		$amountOfResults = count($cssTags[0]);
		for ($i = 0; $i < $amountOfResults; ++$i) {
			$content = '';

			// get media attribute (all as default if it's empty)
			$media = (trim($cssTags[2][$i]) === '') ? 'all' : $cssTags[2][$i];
			$media = implode(',', array_map('trim', explode(',', $media)));

			// get rel attribute (stylesheet as default if it's empty)
			$relation = (trim($cssTags[4][$i]) === '') ? 'stylesheet' : $cssTags[4][$i];

			// get source attribute
			$source = $cssTags[3][$i];

			// get title attribute
			$title = trim($cssTags[5][$i]);
			
			// add basic entry
			$this->css[$relation][$media][$i]['minify-ignore'] = FALSE;
			$this->css[$relation][$media][$i]['compress-ignore'] = FALSE;
			$this->css[$relation][$media][$i]['merge-ignore'] = FALSE;
			$this->css[$relation][$media][$i]['file'] = $source;
			$this->css[$relation][$media][$i]['content'] = '';
			$this->css[$relation][$media][$i]['basename'] = '';
			$this->css[$relation][$media][$i]['title'] = $title;

			// styles which are added inside the document must be parsed again
			// to fetch the pure css code
			$cssTags[1][$i] = ($cssTags[1][$i] === 'sty' ? 'style' : $cssTags[1][$i]);
			if ($cssTags[1][$i] === 'style') {
				$cssContent = array();
				preg_match_all($filterInDocumentPattern, $cssTags[0][$i], $cssContent);

				// we doesn't need to continue if it was an empty style tag
				if ($cssContent[1][0] === '') {
					unset($this->css[$relation][$media][$i]);
					continue;
				}

				// save the content into a temporary file
				$hash = md5($cssContent[1][0]);
				$source = $this->tempDirectories['temp'] . 'inDocument-' . $hash;
				$tempFile = $source . '.css';
				if (!file_exists($source . '.css')) {
					t3lib_div::writeFile($tempFile, $cssContent[1][0]);
				}

				// try to resolve any @import occurrences
				/** @noinspection PhpUndefinedClassInspection */
				$content = Minify_ImportProcessor::process($tempFile);
				$this->css[$relation][$media][$i]['file'] = $tempFile;
				$this->css[$relation][$media][$i]['content'] = $content;
				$this->css[$relation][$media][$i]['basename'] = basename($source);
			} elseif ($source !== '') {
				// try to fetch the content of the css file
				$file = ($source{0} === '/' ? substr($source, 1) : $source);
				if ($GLOBALS['TSFE']->absRefPrefix !== '' && strpos($file, $GLOBALS['TSFE']->absRefPrefix) === 0) {
					$file = substr($file, strlen($GLOBALS['TSFE']->absRefPrefix) - 1);
				}
				if (file_exists(PATH_site . $file)) {
					$content = Minify_ImportProcessor::process(PATH_site . $file);
				} else {
					$tempFile = $this->getExternalFile($source);
					$content = Minify_ImportProcessor::process($tempFile);
				}

				// ignore this file if the content could not be fetched
				if ($content == '') {
					$this->css[$relation][$media][$i]['minify-ignore'] = TRUE;
					$this->css[$relation][$media][$i]['compress-ignore'] = TRUE;
					$this->css[$relation][$media][$i]['merge-ignore'] = TRUE;
					continue;
				}

				// check if the file should be ignored for some processes
				if ($this->extConfig['css.']['minify.']['ignore'] !== '') {
					if (preg_match($this->extConfig['css.']['minify.']['ignore'], $source)) {
						$this->css[$relation][$media][$i]['minify-ignore'] = TRUE;
					}
				}

				if ($this->extConfig['css.']['compress.']['ignore'] !== '') {
					if (preg_match($this->extConfig['css.']['compress.']['ignore'], $source)) {
						$this->css[$relation][$media][$i]['compress-ignore'] = TRUE;
					}
				}

				if ($this->extConfig['css.']['merge.']['ignore'] !== '') {
					if (preg_match($this->extConfig['css.']['merge.']['ignore'], $source)) {
						$this->css[$relation][$media][$i]['merge-ignore'] = TRUE;
					}
				}

				// set the css file with it's content
				$this->css[$relation][$media][$i]['content'] = $content;
			}

			// get base name for later usage
			// base name without file prefix and prefixed hash of the content
			$filename = basename($source);
			$hash = md5($content);
			$this->css[$relation][$media][$i]['basename'] =
				substr($filename, 0, strrpos($filename, '.')) . '-' . $hash;
		}
	}

	/**
	 * This method parses the output content and saves any found javascript files or inline code
	 * into the "javascript" class property. The output content is cleaned up of the found results.
	 *
	 * @return array js files
	 */
	protected function getJavascriptFiles() {
		// init
		$javascriptTags = array(
			'head' => array(),
			'body' => array()
		);

		// create search pattern
		$searchScriptsPattern = '/' .
			'<script' .							// This expression includes any script nodes.
				'(?=.+?(?:src="(.*?)"|>))' .	// It fetches the src attribute.
			'[^>]*?>' .							// Finally we finish the parsing of the opening tag
			'.*?<\/script>\s*' .				// until the closing tag.
			'/is';

		// filter pattern for the inDoc scripts (fetches the content)
		$filterInDocumentPattern = '/' .
			'<script.*?>' .					// The expression removes the opening script tag
			'(?:.*?\/\*<!\[CDATA\[\*\/)?' .	// and the optionally prefixed CDATA string.
			'(?:.*?<!--)?' .				// senseless <!-- construct
			'\s*(.*?)' .					// We save the pure js content,
			'(?:\s*\/\/\s*-->)?' .			// senseless <!-- construct
			'(?:\s*\/\*\]\]>\*\/)?' .		// remove the possible closing CDATA string
			'\s*<\/script>' .				// and closing script tag
			'/is';

		// fetch the head content
		$head = array();
		$pattern = '/<head>.+?<\/head>/is';
		preg_match($pattern, $GLOBALS['TSFE']->content, $head);
		$head = $head[0];

		// parse all available js code inside script tags
		preg_match_all($searchScriptsPattern, $head, $javascriptTags['head']);

		// remove any js code inside the output content
		if (count($javascriptTags['head'][0])) {
			$head = preg_replace($searchScriptsPattern, '', $head, count($javascriptTags['head'][0]));

			// replace head with new one
			$pattern = '/<head>.+?<\/head>/is';
			$GLOBALS['TSFE']->content = preg_replace($pattern, $head, $GLOBALS['TSFE']->content);
		}

		// fetch the body content
		if ($this->extConfig['javascript.']['parseBody'] === '1') {
			$body = array();
			$pattern = '/<body.*>.+?<\/body>/is';
			preg_match($pattern, $GLOBALS['TSFE']->content, $body);
			$body = $body[0];

			// parse all available js code inside script tags
			preg_match_all($searchScriptsPattern, $body, $javascriptTags['body']);

			// remove any js code inside the output content
			// we leave markers in the form ###100### at the original places to write them
			// back here; it's started by 100
			if (count($javascriptTags['body'][0])) {
				$function = create_function('', 'static $i = 0; return \'###MERGER\' . $i++ . \'MERGER###\';');

				$body = preg_replace_callback(
					$searchScriptsPattern,
					$function,
					$body,
					count($javascriptTags['body'][0])
				);

				// replace body with new one
				$pattern = '/<body.*>.+?<\/body>/is';
				$GLOBALS['TSFE']->content = preg_replace($pattern, $body, $GLOBALS['TSFE']->content);
			}
		}

		// parse matches
		foreach ($javascriptTags as $section => $results) {
			$amountOfResults = count($results[0]);
			for ($i = 0; $i < $amountOfResults; ++$i) {
				// get source attribute
				$source = trim($results[1][$i]);
				$isSourceFromMainAttribute = FALSE;
				if ($source !== '') {
					preg_match('/^<script([^>]*)>/', trim($results[0][$i]), $scriptAttribute);
					$isSourceFromMainAttribute = (strpos($scriptAttribute[1], $source) !== FALSE);
				}

				// add basic entry
				$this->javascript[$section][$i]['minify-ignore'] = FALSE;
				$this->javascript[$section][$i]['compress-ignore'] = FALSE;
				$this->javascript[$section][$i]['merge-ignore'] = FALSE;
				$this->javascript[$section][$i]['file'] = $source;
				$this->javascript[$section][$i]['content'] = '';
				$this->javascript[$section][$i]['basename'] = '';
				$this->javascript[$section][$i]['addInDocument'] = FALSE;

				if ($isSourceFromMainAttribute) {
					// try to fetch the content of the css file
					$file = ($source{0} === '/' ? substr($source, 1) : $source);
					if ($GLOBALS['TSFE']->absRefPrefix !== '' && strpos($file, $GLOBALS['TSFE']->absRefPrefix) === 0) {
						$file = substr($file, strlen($GLOBALS['TSFE']->absRefPrefix) - 1);
					}
					$file = PATH_site . $file;

					if (file_exists($file)) {
						$content = file_get_contents($file);
					} else {
						$content = $this->getExternalFile($source, TRUE);
					}

					// ignore this file if the content could not be fetched
					if (trim($content) === '') {
						$this->javascript[$section][$i]['minify-ignore'] = TRUE;
						$this->javascript[$section][$i]['compress-ignore'] = TRUE;
						$this->javascript[$section][$i]['merge-ignore'] = TRUE;
						continue;
					}

					// check if the file should be ignored for some processes
					if ($this->extConfig['javascript.']['minify.']['ignore'] !== '' &&
						preg_match($this->extConfig['javascript.']['minify.']['ignore'], $source)
					) {
						$this->javascript[$section][$i]['minify-ignore'] = TRUE;
					}

					if ($this->extConfig['javascript.']['compress.']['ignore'] !== '' &&
						preg_match($this->extConfig['javascript.']['compress.']['ignore'], $source)
					) {
						$this->javascript[$section][$i]['compress-ignore'] = TRUE;
					}

					if ($this->extConfig['javascript.']['merge.']['ignore'] !== '' &&
						preg_match($this->extConfig['javascript.']['merge.']['ignore'], $source)
					) {
						$this->javascript[$section][$i]['merge-ignore'] = TRUE;
					}

					// set the javascript file with it's content
					$this->javascript[$section][$i]['file'] = $source;
					$this->javascript[$section][$i]['content'] = $content;

					// get base name for later usage
					// base name without file prefix and prefixed hash of the content
					$filename = basename($source);
					$hash = md5($content);
					$this->javascript[$section][$i]['basename'] =
						substr($filename, 0, strrpos($filename, '.')) . '-' . $hash;

				} else {
					// styles which are added inside the document must be parsed again
					// to fetch the pure js code
					$javascriptContent = array();
					preg_match_all($filterInDocumentPattern, $results[0][$i], $javascriptContent);

					// we doesn't need to continue if it was an empty style tag
					if ($javascriptContent[1][0] === '') {
						unset($this->javascript[$section][$i]);
						continue;
					}

					// save the content into a temporary file
					$hash = md5($javascriptContent[1][0]);
					$source = $this->tempDirectories['temp'] . 'inDocument-' . $hash;

					if (!file_exists($source . '.js')) {
						t3lib_div::writeFile($source . '.js', $javascriptContent[1][0]);
					}

					// try to resolve any @import occurrences
					$this->javascript[$section][$i]['file'] = $source . '.js';
					$this->javascript[$section][$i]['content'] = $javascriptContent[1][0];
					$this->javascript[$section][$i]['basename'] = basename($source);

					// inDocument styles of the body shouldn't be removed from their position
					if ($this->extConfig['javascript.']['doNotRemoveInDocInBody'] === '1' && $section === 'body') {
						$this->javascript[$section][$i]['minify-ignore'] = FALSE;
						$this->javascript[$section][$i]['compress-ignore'] = TRUE;
						$this->javascript[$section][$i]['merge-ignore'] = TRUE;
						$this->javascript[$section][$i]['addInDocument'] = TRUE;
					}
				}
			}
		}
	}

	/**
	 * Gets a file from an external resource (e.g. http://) and caches them
	 *
	 * @param string $source Source address
	 * @param boolean $returnContent
	 * @return string cache file or content (depends on the parameter)
	 */
	protected function getExternalFile($source, $returnContent = FALSE) {
		$filename = basename($source);
		$hash = md5($source);
		$cacheFile = $this->tempDirectories['temp'] . $filename . '-' . $hash;
		$externalFileCacheLifetime = intval($this->extConfig['externalFileCacheLifetime']);
		$cacheLifetime = ($externalFileCacheLifetime > 0) ? $externalFileCacheLifetime : 3600;

			// check the age of the cache file (also fails with non-existent file)
		$content = '';
		if ((int) @filemtime($cacheFile) <= ($GLOBALS['EXEC_TIME'] - $cacheLifetime)) {
			$content = t3lib_div::getURL($source);
			if ($content !== FALSE) {
				t3lib_div::writeFile($cacheFile, $content);
			} else {
				$cacheFile = '';
			}
		} elseif ($returnContent) {
			$content = file_get_contents($cacheFile);
		}

		$returnValue = $cacheFile;
		if ($returnContent) {
			$returnValue = $content;
		}

		return $returnValue;
	}

	/**
	 * This method minifies a css file. It's based upon the Minify_CSS class
	 * of the project minify.
	 *
	 * @param array $properties properties of an entry (copy-by-reference is used!)
	 * @return string new filename
	 */
	protected function minifyCSSfile(&$properties) {
		// get new filename
		$newFile = $this->tempDirectories['minified'] .
			$properties['basename'] . '.min.css';

		// stop further processing if the file already exists
		if (file_exists($newFile)) {
			$properties['basename'] .= '.min';
			$properties['content'] = file_get_contents($newFile);
			return $newFile;
		}

		// minify content
		/** @noinspection PhpUndefinedClassInspection */
		$properties['content'] = Minify_CSS::minify($properties['content']);

		// save content inside the new file
		t3lib_div::writeFile($newFile, $properties['content']);

		// save new part of the base name
		$properties['basename'] .= '.min';

		return $newFile;
	}

	/**
	 * This method minifies a javascript file. It's based upon the JSMin+ class
	 * of the project minify. Alternatively the old JSMin class can be used, but it's
	 * definitely not the preferred solution!
	 *
	 * @param array $properties properties of an entry (copy-by-reference is used!)
	 * @return string new filename
	 */
	protected function minifyJavascriptFile(&$properties) {
			// stop further processing if the file already exists
		$newFile = $this->tempDirectories['minified'] . $properties['basename'] . '.min.js';
		if (file_exists($newFile)) {
			$properties['basename'] .= '.min';
			$properties['content'] = file_get_contents($newFile);
			return $newFile;
		}

			// check for conditional compilation code to fix an issue with jsmin+
		$hasConditionalCompilation = FALSE;
		if ($this->extConfig['javascript.']['minify.']['useJSMinPlus'] === '1') {
			$hasConditionalCompilation = preg_match('/\/\*@cc_on/is', $properties['content']);
		}

			// minify content (the ending semicolon must be added to prevent minimisation bugs)
		if (!$hasConditionalCompilation && $this->extConfig['javascript.']['minify.']['useJSMinPlus'] === '1') {
			if (!class_exists('JSMinPlus', FALSE)) {
				require_once(t3lib_extMgm::extPath('scriptmerger') . 'resources/jsminplus.php');
			}

			$minifiedContent = JSMinPlus::minify($properties['content']);

		} else {
			if (!class_exists('JSMin', FALSE)) {
				require_once(t3lib_extMgm::extPath('scriptmerger') . 'resources/jsmin.php');
			}

			$minifiedContent = JSMin::minify($properties['content']);
		}

			// check result length
		if (strlen($minifiedContent) > 2 || count(explode(LF, $minifiedContent)) > 50) {
			$properties['content'] = $minifiedContent . ';';

		} else {
			$message = 'This javascript file could not be minified: "' . $properties['file'] . '"! ' .
				'You should exclude it from the minification process!';
			t3lib_div::sysLog($message, 'scriptmerger', t3lib_div::SYSLOG_SEVERITY_ERROR);
		}

		t3lib_div::writeFile($newFile, $properties['content']);
		$properties['basename'] .= '.min';

		return $newFile;
	}

	/**
	 * This method compresses a css file.
	 *
	 * @param array $properties properties of an entry (copy-by-reference is used!)
	 * @return string new filename
	 */
	protected function compressCSSfile(&$properties) {
		$newFile = $this->tempDirectories['compressed'] . $properties['basename'] . '.gz.css';
		if (file_exists($newFile)) {
			return $newFile;
		}

		t3lib_div::writeFile($newFile, gzencode($properties['content'], 5));

		return $newFile;
	}

	/**
	 * This method compresses a javascript file.
	 *
	 * @param array $properties properties of an entry (copy-by-reference is used!)
	 * @return string new filename
	 */
	protected function compressJavascriptFile(&$properties) {
		$newFile = $this->tempDirectories['compressed'] . $properties['basename'] . '.gz.js';
		if (file_exists($newFile)) {
			return $newFile;
		}

		t3lib_div::writeFile($newFile, gzencode($properties['content'], 5));

		return $newFile;
	}

	/**
	 * This method writes the css back to the document.
	 *
	 * @return void
	 */
	protected function writeCSStoDocument() {
		// write all files back to the document
		foreach ($this->css as $relation => $cssByRelation) {
			$cssByRelation = array_reverse($cssByRelation);
			foreach ($cssByRelation as $media => $cssByMedia) {
				foreach ($cssByMedia as $cssProperties) {
					$file = $cssProperties['file'];

					// normal file or http link?
					if (file_exists($file)) {
						$file = $GLOBALS['TSFE']->absRefPrefix .
							(PATH_site === '/' ? $file : str_replace(PATH_site, '', $file));
					}

					// build css script link or add the content directly into the document
					if ($this->extConfig['css.']['addContentInDocument'] === '1') {
						$content = LF . "\t" .
							'<style media="' . $media . '" type="text/css">' . LF .
							"\t" . '/* <![CDATA[ */' . LF .
							"\t" . $cssProperties['content'] . LF .
							"\t" . '/* ]]> */' . LF .
							"\t" . '</style>' . LF;
					} else {
						$title = (trim($cssProperties['title']) !== '' ?
							'title="' . $cssProperties['title']  . '"' : '');
						$content = LF . "\t" . '<link rel="' . $relation . '" type="text/css" ' .
							'media="' . $media . '" ' . $title . ' href="' . $file . '" />' . LF;
					}

					// add content right after the opening head tag
					$GLOBALS['TSFE']->content = preg_replace(
						'/<(?:\/base|base|meta name="generator"|link|\/title|\/head).*?>/is',
						'\0' . $content,
						$GLOBALS['TSFE']->content,
						1
					);
				}
			}
		}
	}

	/**
	 * This method writes the javascript back to the document.
	 *
	 * @return void
	 */
	protected function writeJavascriptToDocument() {
		// write all files back to the document
		foreach ($this->javascript as $section => $javascriptBySection) {
			ksort($javascriptBySection);
			if (!is_array($javascriptBySection)) {
				continue;
			}

			// prepare pattern
			$addToBody = ($section === 'body' || $this->extConfig['javascript.']['addBeforeBody'] === '1');
			if ($addToBody) {
				$pattern = '/<\/body>/i';
			} else {
				$pattern = '/<(?:\/base|base|meta name="generator"|link|\/title).*?>/is';
				$javascriptBySection = array_reverse($javascriptBySection);
			}

			foreach ($javascriptBySection as $index => $javascriptProperties) {
				$file = $javascriptProperties['file'];

				// normal file or http link?
				if (file_exists($file)) {
					$file = $GLOBALS['TSFE']->absRefPrefix .
						(PATH_site === '/' ? $file : str_replace(PATH_site, '', $file));
				}

				// build javascript script link or add the content directly into the document
				if ($javascriptProperties['addInDocument'] ||
					$this->extConfig['javascript.']['addContentInDocument'] === '1'
				) {
					$content = "\t" .
						'<script type="text/javascript">' . LF .
						"\t" . '/* <![CDATA[ */' . LF .
						"\t" . $javascriptProperties['content'] . LF .
						"\t" . '/* ]]> */' . LF .
						"\t" . '</script>' . LF;
				} else {
					$content = "\t" .
						'<script type="text/javascript" src="' . $file . '"></script>' . LF;
				}

				// add body scripts back to their original place if they were ignored
				if ($section === 'body' && $javascriptProperties['merge-ignore']) {
					$GLOBALS['TSFE']->content = str_replace(
						'###MERGER' . $index . 'MERGER###',
						$content,
						$GLOBALS['TSFE']->content
					);
					continue;
				}

				// add content right after the opening head tag or inside the body
				$replacement = ($addToBody ? $content . '\0' : '\0' . $content);
				$GLOBALS['TSFE']->content = preg_replace($pattern, $replacement, $GLOBALS['TSFE']->content, 1);
			}
		}

		// remove all empty body markers
		if ($this->extConfig['javascript.']['parseBody'] === '1') {
			$pattern = '/###MERGER[0-9]*?MERGER###/is';
			$GLOBALS['TSFE']->content = preg_replace($pattern, '', $GLOBALS['TSFE']->content);
		}
	}
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/scriptmerger/class.tx_scriptmerger.php']) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/scriptmerger/class.tx_scriptmerger.php']);
}

?>