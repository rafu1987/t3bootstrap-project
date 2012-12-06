# scriptmergerbless

XCLASS of the [scriptmerger](http://typo3.org/extensions/repository/view/scriptmerger) Extension, to do something similar to [Bless CSS](http://blesscss.com/). IE versions 6, 7, 8 & 9 all have a limit on the number of selectors allowed in a single CSS file. Once the limit is reached, IE silently fails and just ignores any further CSS in the file leaving parts of your site totally unstyled.

When a lot of CSS files are minified and merged by the scriptmerger extension, the limit is quickly reached. This extensions solves this problem by splitting the CSS files with help of a threshold TypoScript value. You also have the possibility to de/activate the splitting via TypoScript; the extension then does practically nothing, but you don't have to uninstall it every time.

**Powered by [medialis.net UG (haftungsbeschränkt)](http://www.medialis.net)**