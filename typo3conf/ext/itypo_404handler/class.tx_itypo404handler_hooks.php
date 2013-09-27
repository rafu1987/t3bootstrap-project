<?php

class tx_itypo404handler_hooks {
	/**
	 * Initialization of FE-user, setting the user-group list if applicable.
	 *
	 * @param	array		Parameters from frontend
	 * @param	object		TSFE object
	 * @return	void
	 */
	function fe_feuserInit(&$params, $ref) {
		$origcHash = $params['pObj']->cHash;
		$get = t3lib_div::_GET('tx_itypo404handler');
		if ($params['pObj']->cHash && $get['ses_id'])	{
			// check chash match
			$urlParams = '&tx_itypo404handler[ses_id]='.$get['ses_id'];
			$cHash = t3lib_div::generateCHash($urlParams);
			if ($cHash == $origcHash) {
				// fetch userinfo belonging to this session
				$query = "SELECT u.* FROM fe_sessions AS s
INNER JOIN fe_users AS u
ON s.ses_userid = u.uid
WHERE s.ses_id = '".mysql_real_escape_string($get['ses_id'])."'
LIMIT 1";
				$res = mysql_query($query);

				if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res) == 1) {
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					
					// log in as this user
					if (!is_array($params['pObj']->fe_user->user))	$params['pObj']->fe_user->user = array();
					$params['pObj']->fe_user->user = $row;
				}
			}
		}
	}
}

?>