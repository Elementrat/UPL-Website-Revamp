<?php if (!defined('PmWiki')) exit();
/*  Copyright 2004-2011 Patrick R. Michaud (pmichaud@pobox.com)
    This file is part of PmWiki; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.  See pmwiki.php for full details.

    This file is used to enable the iso-8859-2 character set in PmWiki.
    The first part converts the charset to iso-8859-2 and removes
    conflicts for newline and keep tokens; the second part
    handles the conversion of pagenames from utf-8 (sent by browsers)
    into iso-8859-2 if needed.  
*/
  global $HTTPHeaders, $pagename, $KeepToken, $Charset, $DefaultPageCharset;

  $HTTPHeaders[] = "Content-Type: text/html; charset=iso-8859-2;";
  $Charset = "ISO-8859-2";
  SDVA($DefaultPageCharset, array('ISO-8859-1'=>$Charset));

  $KeepToken = "\263\263\263";

  $pagename = $_REQUEST['n'];
  if (!$pagename) $pagename = @$_GET['pagename'];
  if ($pagename=='' && $EnablePathInfo)
    $pagename = @substr($_SERVER['PATH_INFO'],1);
  if (!$pagename &&
      preg_match('!^'.preg_quote($_SERVER['SCRIPT_NAME'],'!').'/?([^?]*)!',
          $_SERVER['REQUEST_URI'],$match))
      $pagename = urldecode($match[1]);
  $pagename = preg_replace('!/+$!','',$pagename);

  if (!preg_match('/[\\x80-\\x9f]/', $pagename)) return;

  if (function_exists('iconv')) 
    $pagename = iconv('UTF-8','ISO-8859-2',$pagename);
  else {
    $conv = array(
      ' '=>'�', 'Ą'=>'�', '˘'=>'�', 'Ł'=>'�', 
      '¤'=>'�', 'Ľ'=>'�', 'Ś'=>'�', '§'=>'�', 
      '¨'=>'�', 'Š'=>'�', 'Ş'=>'�', 'Ť'=>'�', 
      'Ź'=>'�', '­'=>'�', 'Ž'=>'�', 'Ż'=>'�', 
      '°'=>'�', 'ą'=>'�', '˛'=>'�', 'ł'=>'�', 
      '´'=>'�', 'ľ'=>'�', 'ś'=>'�', 'ˇ'=>'�', 
      '¸'=>'�', 'š'=>'�', 'ş'=>'�', 'ť'=>'�', 
      'ź'=>'�', '˝'=>'�', 'ž'=>'�', 'ż'=>'�', 
      'Ŕ'=>'�', 'Á'=>'�', 'Â'=>'�', 'Ă'=>'�', 
      'Ä'=>'�', 'Ĺ'=>'�', 'Ć'=>'�', 'Ç'=>'�', 
      'Č'=>'�', 'É'=>'�', 'Ę'=>'�', 'Ë'=>'�', 
      'Ě'=>'�', 'Í'=>'�', 'Î'=>'�', 'Ď'=>'�', 
      'Đ'=>'�', 'Ń'=>'�', 'Ň'=>'�', 'Ó'=>'�', 
      'Ô'=>'�', 'Ő'=>'�', 'Ö'=>'�', '×'=>'�', 
      'Ř'=>'�', 'Ů'=>'�', 'Ú'=>'�', 'Ű'=>'�', 
      'Ü'=>'�', 'Ý'=>'�', 'Ţ'=>'�', 'ß'=>'�', 
      'ŕ'=>'�', 'á'=>'�', 'â'=>'�', 'ă'=>'�', 
      'ä'=>'�', 'ĺ'=>'�', 'ć'=>'�', 'ç'=>'�', 
      'č'=>'�', 'é'=>'�', 'ę'=>'�', 'ë'=>'�', 
      'ě'=>'�', 'í'=>'�', 'î'=>'�', 'ď'=>'�', 
      'đ'=>'�', 'ń'=>'�', 'ň'=>'�', 'ó'=>'�', 
      'ô'=>'�', 'ő'=>'�', 'ö'=>'�', '÷'=>'�', 
      'ř'=>'�', 'ů'=>'�', 'ú'=>'�', 'ű'=>'�', 
      'ü'=>'�', 'ý'=>'�', 'ţ'=>'�', '˙'=>'�', 
    );
    $pagename = str_replace(array_keys($conv),array_values($conv),$pagename);
  }

