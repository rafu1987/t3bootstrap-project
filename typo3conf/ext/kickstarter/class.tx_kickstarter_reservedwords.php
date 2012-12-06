<?php
/***************************************************************
*  Copyright notice
*
*  (c)  2006-2008 Ingo Renner (ingo@typo3.org)  All rights reserved
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
***************************************************************/

/**
 * Reserved TYPO3 and MySQL words
 *
 * @author Ingo Renner <ingo@typo3.org>
 * @author Peter Foerger
 */
class tx_kickstarter_reservedWords {

	var $TYPO3ReservedFields = array(
		'uid',
		'pid',
		'endtime',
		'starttime',
		'sorting',
		'fe_group',
		'hidden',
		'deleted',
		'cruser_id',
		'crdate',
		'tstamp',
		'type'
	);

	 var $mysqlReservedWords = array(
		'accessible',
		'action',
		'add',
		'all',
		'alter',
		'analyze',
		'and',
		'as',
		'asc',
		'asensitive',
		'before',
		'between',
		'bigint',
		'binary',
		'bit',
		'blob',
		'both',
		'by',
		'call',
		'cascade',
		'case',
		'change',
		'char',
		'character',
		'check',
		'collate',
		'column',
		'condition',
		'constraint',
		'continue',
		'convert',
		'create',
		'cross',
		'current_date',
		'current_time',
		'current_timestamp',
		'current_user',
		'cursor',
		'database',
		'databases',
		'date',
		'day_hour',
		'day_microsecond',
		'day_minute',
		'day_second',
		'dec',
		'decimal',
		'declare',
		'default',
		'delayed',
		'delete',
		'desc',
		'describe',
		'deterministic',
		'distinct',
		'distinctrow',
		'div',
		'double',
		'drop',
		'dual',
		'each',
		'else',
		'elseif',
		'enclosed',
		'enum',
		'escaped',
		'exists',
		'exit',
		'explain',
		'false',
		'fetch',
		'float',
		'float4',
		'float8',
		'for',
		'force',
		'foreign',
		'from',
		'fulltext',
		'grant',
		'group',
		'having',
		'high_priority',
		'hour_microsecond',
		'hour_minute',
		'hour_second',
		'if',
		'ignore',
		'in',
		'index',
		'infile',
		'inner',
		'inout',
		'insensitive',
		'insert',
		'int',
		'int1',
		'int2',
		'int3',
		'int4',
		'int8',
		'integer',
		'interval',
		'into',
		'is',
		'iterate',
		'join',
		'key',
		'keys',
		'kill',
		'leading',
		'leave',
		'left',
		'like',
		'limit',
		'linear',
		'linear',
		'lines',
		'load',
		'localtime',
		'localtimestamp',
		'lock',
		'long',
		'longblob',
		'longtext',
		'loop',
		'low_priority',
		'master_ssl_verify_server_cert',
		'master_ssl_verify_server_cert',
		'match',
		'mediumblob',
		'mediumint',
		'mediumtext',
		'middleint',
		'minute_microsecond',
		'minute_second',
		'mod',
		'modifies',
		'natural',
		'no',
		'not',
		'no_write_to_binlog',
		'null',
		'numeric',
		'on',
		'optimize',
		'option',
		'optionally',
		'or',
		'order',
		'out',
		'outer',
		'outfile',
		'precision',
		'primary',
		'procedure',
		'purge',
		'range',
		'range',
		'read',
		'reads',
		'read_only',
		'read_write',
		'read_write',
		'real',
		'references',
		'regexp',
		'release',
		'rename',
		'repeat',
		'replace',
		'require',
		'restrict',
		'return',
		'revoke',
		'right',
		'rlike',
		'schema',
		'schemas',
		'second_microsecond',
		'select',
		'sensitive',
		'separator',
		'set',
		'show',
		'smallint',
		'spatial',
		'specific',
		'sql',
		'sqlexception',
		'sqlstate',
		'sqlwarning',
		'sql_big_result',
		'sql_calc_found_rows',
		'sql_small_result',
		'ssl',
		'starting',
		'straight_join',
		'table',
		'terminated',
		'text',
		'then',
		'time',
		'timestamp',
		'tinyblob',
		'tinyint',
		'tinytext',
		'to',
		'trailing',
		'trigger',
		'true',
		'undo',
		'union',
		'unique',
		'unlock',
		'unsigned',
		'update',
		'usage',
		'use',
		'using',
		'utc_date',
		'utc_time',
		'utc_timestamp',
		'values',
		'varbinary',
		'varchar',
		'varcharacter',
		'varying',
		'when',
		'where',
		'while',
		'with',
		'write',
		'xor',
		'year_month',
		'zerofill',
	);

	/**
	 * merges the lists of reserved words and returns them in an unique array
	 *
	 * @return array array of reserved words
	 */
	function getReservedWords() {
		$reservedWords = array_unique(
			array_merge (
				$this->TYPO3ReservedFields,
				$this->mysqlReservedWords
			)
		);

		return $reservedWords;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/class.tx_kickstarter_reservedwords.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/class.tx_kickstarter_reservedwords.php']);
}

?>