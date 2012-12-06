<?php
$tmp=explode("/",$GLOBALS['_SERVER']['PHP_SELF']);
unset($tmp[0]); unset($tmp[count($tmp)]);
echo $path=implode("/",$tmp)."/";
