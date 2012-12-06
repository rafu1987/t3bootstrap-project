<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Maximo Cuadros (mcuadros@gmail.com)
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
/** 
 * Plugin 'Google Sitemap for Pages and Contents' for the 'mc_googlesitemapmod' extension.





 *
 * @author	Maximo Cuadros <mcuadros@gmail.com>
 * @others	Peter Russ <peter.russ@4many.net>, 4Many Services 
 * Thx for some lines of code and guidelines
 */



class tx_mcgooglesitemapmod_base  {
	
	function tx_mcgooglesitemapmod_base($cObj,$type=0)	{
		$this->cObj = &$cObj;



		ini_Set("max_execution_time",120);
		$GLOBALS["TSFE"]->set_no_cache();

		$this->act=array("1" => "Always", "2" => "Hourly", "3" => "Daily", "4" => "Weekly", "5" => "Monthly", "6" => "Yearly",  "7" => "Never");
		header('Content-type: text/xml');
		$head[]='<?xml version="1.0" encoding="UTF-8"?>';
		if ( $type != 2 ) { 
			#$head[]='<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'."\n";
			$head[]='<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		} else {
			#$head[]='<sitemapindex xmlns="http://www.google.com/schemas/sitemap/0.84">'."\n";
			$head[]='<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		}


		echo implode("\n",$head); unset($head);

        $tmp=explode("/",$GLOBALS['_SERVER']['PHP_SELF']);
        unset($tmp[0]); unset($tmp[count($tmp)]);
        $path=implode("/",$tmp);
        if ( strlen($path) != 0 )  { $path.="/"; }

		$host=$_SERVER['HTTP_X_FORWARDED_HOST'];
		if (!$host) { 
		    $host=$_SERVER['HTTP_HOST'];
        }
		$this->baseUrl='http://'.$host.'/'.$path;
		if ( $this->cObj->data['tx_mcgooglesitemapmod_lastmod'] == 1 ) {
			$this->dateFormat='Y-m-d\TH:i:s\Z';
		} else {
			$this->dateFormat="Y-m-d";
		}
		if (  $type == 0 ) {
			$this->sitemapContent();		
		} elseif ($type==2) {
			$this->sitemapIndex();	
		} else {
			$this->sitemapPage();
		}

		echo "</urlset>\n";
		exit();

	}
	function sitemapContent($array=array()) {
		if ( count($array) == 0 ) { $array=$this->cObj->data; }
		if ( $array['tx_mcgooglesitemapmod_objective'] == "tt_news" ) { return $this->sitemapTTNews($array); }

		if ( $array['tx_mcgooglesitemapmod_changefreq'] != 0 ) { $fix['changefreq']=strtolower($this->act[$array['tx_mcgooglesitemapmod_changefreq']]); }
		if ( $array['tx_mcgooglesitemapmod_priority'] <= 1 && $array['tx_mcgooglesitemapmod_priority'] > 0 ) {
			$fix['priority']=$array['tx_mcgooglesitemapmod_priority'];
			if ( strlen($fix['priority']) == 1 ) { $fix['priority'].=".0"; }
		}
		$sel=mysql_query("SELECT * FROM ".$array['tx_mcgooglesitemapmod_objective']." WHERE pid IN (".$array['pages'].") ".$this->cObj->enableFields($array['tx_mcgooglesitemapmod_objective']));

		while ($row=mysql_fetch_array($sel,MYSQL_ASSOC)) {
#if ( $i++ > 10 ) { exit(); }
			$tema=array_merge($fix,$tema);
			$tema['lastmod']=gmdate($this->dateFormat,$row['tstamp']);
			$tema['page']=$this->elcHash($array['tx_mcgooglesitemapmod_pageuid'],$this->replaceParams ($row,$array['tx_mcgooglesitemapmod_url']));
			if ($GLOBALS['TYPO3_CONF_VARS']['EXT']['mc_googlesitemapmod'] == 1 ) { $tema['page']=$this->changeTitle($tema['page'],$row['title']); }

			$tema['loc']=htmlspecialchars(utf8_encode($this->baseUrl.$tema['page']));
			$this->createElement($tema);
			unset($tema);
		}
	}
	function sitemapTTNews($array=array()) {
                if ( count($array) == 0 ) { $array=$this->cObj->data; }
		if ( count($GLOBALS['TYPO3_LOADED_EXT']['tt_news']) == 0 ) { return; }

                if ( $array['tx_mcgooglesitemapmod_changefreq'] != 0 ) { $fix['changefreq']=strtolower($this->act[$array['tx_mcgooglesitemapmod_changefreq']]); }
                if ( $array['tx_mcgooglesitemapmod_priority'] <= 1 && $array['tx_mcgooglesitemapmod_priority'] > 0 ) {
                        $fix['priority']=$array['tx_mcgooglesitemapmod_priority'];
                        if ( strlen($fix['priority']) == 1 ) { $fix['priority'].=".0"; }
                }

		$pages=explode(',',$array['pages'] ? $array['pages'] : $GLOBALS['TSFE']->id);
                foreach ($pages as $page) {
	$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, 'SELECT tt_news.uid,tt_news.tstamp, tt_news_cat.single_pid FROM tt_news LEFT OUTER  JOIN tt_news_cat_mm ON tt_news_cat_mm.uid_local = tt_news.uid LEFT OUTER JOIN tt_news_cat ON tt_news_cat_mm.uid_foreign = tt_news_cat.uid  WHERE tt_news.pid = '.$page.$this->cObj->enableFields("tt_news"));
		
//	$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, 'SELECT  tt_news.uid,tt_news.tstamp, tt_news_cat.single_pid FROM tt_news INNER JOIN tt_news_cat_mm ON tt_news_cat_mm.uid_local = tt_news.uid INNER JOIN tt_news_cat ON tt_news_cat_mm.uid_foreign = tt_news_cat.uid  WHERE tt_news.pid = '.$page.$this->cObj->enableFields("tt_news"));
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				if ( $row['single_pid'] == 0 ) { $row['single_pid']=$GLOBALS['TSFE']->tmpl->setup['plugin.']['tt_news.']['singlePid']; }
				$tema=array_merge($fix,$tema);
				$tema['lastmod']=gmdate($this->dateFormat,$row['tstamp']);
				$tema['page']=$this->elcHash($row['single_pid'], array("tx_ttnews[tt_news]" => $row['uid']));
	                        $tema['loc']=htmlspecialchars(utf8_encode($this->baseUrl.$tema['page']));

				if ( ((int)$row['single_pid']*1) == 0 ) {  $row['single_pid']=$array['tx_mcgooglesitemapmod_pageuid']; }
				if ( ((int)$row['single_pid']*1) != 0 ) {  $this->createElement($tema); }
         	        unset($tema);
			}
print_R($i);
		}

	}

	function sitemapIndex($array=array()) {
		if ($this->cObj->data['pages']) {
			$pages=explode(',',$this->cObj->data['pages'] ? $this->cObj->data['pages'] : $GLOBALS['TSFE']->id);
			foreach ($pages as $page) {
				$tree.=$this->cObj->getTreeList($page, 1000);
			}
			$tree=substr($tree, 0, strlen($tree)-1);
			$getTree = " AND pages.uid IN (".$tree.") ";
		} else {
			$getTree = "";
		}

                $this->dateFormat='Y-m-d\TH:i:s\Z';
		$res=mysql_query("SELECT tt_content.* FROM tt_content INNER JOIN pages ON pages.uid=tt_content.pid WHERE ( tt_content.menu_type='mc_googlesitemapmod_pi1' OR tt_content.menu_type='mc_googlesitemapmod_pi3' ) " . $getTree .$this->cObj->enableFields("pages")." ".$this->cObj->enableFields("tt_content"));
	    
    //$res=mysql_query("SELECT tt_content.* FROM tt_content INNER JOIN pages ON pages.uid=tt_content.pid WHERE ( tt_content.menu_type='mc_googlesitemapmod_pi1' OR  tt_content.menu_type='mc_googlesitemapmod_pi3' ) " .$this->cObj->enableFields("pages")." ".$this->cObj->enableFields("tt_content"));
        	while($row=mysql_fetch_array($res)) {
	            $url= $this->cObj->typolink("",array("no_cache" => 0,"returnLast" => "url","parameter" => $row['pid'], "useCacheHash" => 0));
        	    if ( $row['menu_type']=="mc_googlesitemapmod_pi1" ) {
	                $last=mysql_query("SELECT tstamp FROM ".$row['tx_mcgooglesitemapmod_objective']." WHERE pid IN (".$row['pages'].") ".$this->cObj->enableFields($row['tx_mcgooglesitemapmod_objective'])." ORDER BY tstamp DESC LIMIT 1");
	            } else {
		            $last=mysql_query("SELECT tstamp FROM pages  WHERE pid IN (".$row['pages'].") ".$this->cObj->enableFields("pages")." ORDER BY tstamp DESC LIMIT 1");
        	    }
	            $last=mysql_fetch_array($last);

        	    $linea[]= "\t<sitemap>";
	            $linea[]= "\t\t<loc>".$this->baseUrl.$url."</loc>";
	            $linea[]= "\t\t<lastmod>".gmdate('Y-m-d\TH:i:s\Z',$last[0])."</lastmod>";
	            $linea[]= "\t</sitemap>";
	            echo implodE("\n",$linea); unset($linea);
	        }
        	echo "</sitemapindex>\n";
                exit();



	}
        function sitemapPage($array=array()) {
                if ( count($array) == 0 ) { $array=$this->cObj->data; }

            $anormal=array();
            
	    	$pages=explode(',',$array['pages'] ? $array['pages'] : $GLOBALS['TSFE']->id);
    		foreach ($pages as $page) {
    			$tree.=$this->cObj->getTreeList($page, 1000);
    		}

    		$tree=substr($tree, 0, strlen($tree)-1);   	
    		$sel=mysql_query("SELECT uid,pid,doktype,tx_mcgooglesitemapmod_priority AS prio,tx_mcgooglesitemapmod_changefreq AS freq FROM pages WHERE uid IN (".$tree.") ".$this->cObj->enableFields("pages"));
            while ($row=mysql_fetch_array($sel,MYSQL_ASSOC)) {
		    	$pids[$row['uid']]=$row['pid'];
    			$prios[$row['uid']]=$row['prio'];
    			$freqs[$row['uid']]=$row['freq'];

                if ( ( !in_array($row['uid'],$pages) && ( $row['doktype'] == 254 ||  $row['doktype'] == 199 )) || @in_array($row['pid'],$anormal) )  {  
                    $anormal[]=$row['uid']; 
                }
            }
	    	$tree=implode(",",array_merge($pages,array_diff(explode(",",$tree),$anormal)));
            if ( count($anormal) != 0 ) {
                $anormal=implode(",",$anormal);
                $anormalSql=" pid NOT IN (".$anormal.") AND ";
            }
            
    		$sel=mysql_query("SELECT * FROM pages WHERE doktype IN(1,2) AND ".$anormalSql." uid IN (".$tree.") AND nav_hide=0 ".$this->cObj->enableFields("pages"));

	    	while ($row=mysql_fetch_array($sel,MYSQL_ASSOC)) {
    			$uid=$row['uid'];
    			$freq=$row['tx_mcgooglesitemapmod_changefreq'];
    			while (($freq == 0) && array_key_exists($uid,$pids)) {
    		        $uid=$pids[$uid];
    				$freq=$freqs[$uid];
	    		}
    			$uid=$row['uid'];
    			$prio=$row['tx_mcgooglesitemapmod_priority'];
	    		while (($prio == 0) && array_key_exists($uid,$pids)) {
		    		$uid=$pids[$uid];
    				$prio=$prios[$uid];
	    		}
    		    if ( $freq != 0 ) { $tema['changefreq']=strtolower($this->act[$freq]); }
            	if ( $prio <= 1 && $prio > 0 ) {
                    $tema['priority']=$prio;
	                if ( strlen($tema['priority']) == 1 ) { $tema['priority'].=".0"; }
    		    }
    			$time=($row['SYS_LASTCHANGED']>$row['tstamp'])?$row['SYS_LASTCHANGED']:$row['tstamp'];
            	$tema['lastmod']=gmdate($this->dateFormat,$time);
                $tema['page']=$this->elcHash($row['uid'],array(),0);
                if ( @strpos('http://',$tema['page'])===false ) {
                    $tema['loc']=htmlspecialchars(utf8_encode($this->baseUrl.$tema['page']));
                } else {
                    $tema['loc']=htmlspecialchars(utf8_encode($tema['page']));
                }
	            $this->createElement($tema);
            	unset($tema);
            }
    }
	function recoverTree($pids) {
		$i=0; $pids=explode(",",$pids); $max=count($pids);
		while ($i < $max) {
			$salida=array_merge($salida,$tree= $this->cObj->getTreeList($pids[$i], 100));
			$i++;
		}

		return $salida;


	}

	function replaceParams ($fields,$params) {
		$params=str_replace("?","",$params);

		$i=0; $max=count($fields); $keys=array_keys($fields);
		while ($i < $max) {
			$params=str_replace("###".$keys[$i]."###",$fields[$keys[$i]],$params);
			$i++;
		}
		$i=0; $ele=explode("&",$params);$max=count($ele);
		while ($i < $max) {
			$tmp=explodE("=",$ele[$i]);
			$salida[$tmp[0]]=$tmp[1];
			$i++;
		}
		return $salida;

	}
        function elcHash ($page,$array,$cHash=1) {
                $i=0; $max=count($array); $keys=array_keys($array);
                while ($i < $max) {
			if ( strlen($array[$keys[$i]]) != 0 ) {
	                        $salida.="&".$keys[$i]."=".$array[$keys[$i]];
			}
                        $i++;
                }

                $typolink_conf=array(
                        "no_cache" => 0,
                        "returnLast" => "url",
                        "parameter" => $page,
                        "additionalParams" => $salida,
                        "useCacheHash" => $cHash);

                        return $this->cObj->typolink("",$typolink_conf);
        }
	function createElement($array) {
		$linea[]="\t<url>";
		$linea[]="\t\t<loc>".$array['loc']."</loc>";
		$linea[]="\t\t<lastmod>".$array['lastmod']."</lastmod>";
		if ( strlen($array['changefreq']) != 0 ) { $linea[]="\t\t<changefreq>".$array['changefreq']."</changefreq>"; }
		if ( strlen($array['priority']) != 0 ) { $linea[]="\t\t<priority>".$array['priority']."</priority>"; }
		$linea[]="\t</url>\n";	
		echo implode("\n",$linea); unset($linea);			
	}
        function changeTitle($url,$str){
                $str=str_replace(chr(225),"a",$str);
                $str=str_replace(chr(233),"e",$str);
                $str=str_replace(chr(237),"i",$str);
                $str=str_replace(chr(243),"o",$str);
                $str=str_replace(chr(250),"u",$str);

                $str=str_replace(chr(193),"A",$str);
                $str=str_replace(chr(201),"E",$str);
                $str=str_replace(chr(205),"I",$str);
                $str=str_replace(chr(211),"O",$str);
                $str=str_replace(chr(218),"U",$str);

                $str=str_replace(chr(241),"n",$str);
                $str=str_replace(chr(209),"N",$str);

                $str=str_replace("+","",$str);
                $str=str_replace("%","",$str);
                $str=str_replace("&","",$str);
                $str=str_replace("(","",$str);
                $str=str_replace(")","",$str);
                $str=str_replace("$","",$str);
                $str=str_replace("@","",$str);
                $str=str_replace("#","",$str);
                $str=str_replace("!","",$str);
                $str=str_replace("","",$str);
                $str=str_replace("?","",$str);
                $str=str_replace("","",$str);
                $str=str_replace(":","",$str);
                $str=str_replace('.',"",$str);
                $str=str_replace("'","",$str);
                $str=str_replace("'","",$str);
                $str=str_replace("*","",$str);
                $str=str_replace(';',"",$str);
                $str=str_replace(',',"",$str);


                $str=str_replace(" ","_",$str);
	        	$str=str_replace("\n","",$str);
        		$str=str_replace("\r","",$str);
	        	$str=str_replace("\t","",$str);
                $tmp=explode('.',$url);
                $tmp[0]=$str;
                return implode('.',$tmp);

       } 

}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/mc_googlesitemapmod/class.tx_mcgooglesitemapmod_base.php"])
{
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/mc_googlesitemapmod/class.tx_mcgooglesitemapmod_base.php"]);
}
?>
