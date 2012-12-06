<?php
class ux_tx_rtehtmlarea_browse_links extends tx_rtehtmlarea_browse_links {
	function addRelField(){
		$rels=array(
			''=>'None',
			'nofollow'=>'Don\'t follow that link',
			'alternate'=>'An alternate version of the document',
			'stylesheet'=>'An external style sheet for the document',
			'start'=>'The first document in a selection',
			'next'=>'The next document in a selection',
			'prev'=>'The previous document in a selection',
			'contents'=>'A table of contents for the document',
			'index'=>'An index for the document',
			'glossary'=>'A glossary of words used in the document',
			'copyright'=>'A document containing copyright information',
			'chapter'=>'A chapter of the document',
			'section'=>'A section of the document',
			'subsection'=>'A subsection of the document',
			'appendix'=>'An appendix for the document',
			'help'=>'A help document',
			'bookmark'=>'A related document',
			'licence'=>'licence',
			'tag'=>'tag',
			'friend'=>'friend',
		);

		$content='';

//<input type="checkbox" name="lrel" value="nofollow"'.($this->additionalAttributes['rel']=='nofollow' ? ' checked="checked"' : '').' /> nofollow
		if($this->act == 'page' || $this->act == 'url' || $this->act == 'file'){
			$content.='
				<tr>
					<td>'.$GLOBALS['LANG']->getLL('linkRelationship',1).':</td>
					<td colspan="3">
						<select name="lrel">';
			foreach($rels as $key=>$rel){
				$content.='<option value="'.htmlspecialchars($key).'"'.($this->additionalAttributes['rel']==$key ? ' selected="selected"' : '').'>'.htmlspecialchars($rel).'</option>';
			}
			$content.='
						</select>
					</td>
				</tr>';
		}

		return $content;
	}
}
?>