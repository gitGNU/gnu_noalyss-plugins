<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
/* $Revision$ */

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/*!\file
 * \brief ajax handling for AMORTIZING
 *@parameter $t is the target
 *@parameter $op is the request action
 *@return xml <ctl> the destination object <code> the HTML to display <extra> various usage
 */
extract ($_REQUEST, EXTR_SKIP);

$ctl=$t;
$html='opération non trouvée';
$extra='';
$close=HtmlInput::anchor_close($t);
$html=$close.$html;
switch($op)
  {
    /*
     * Show a form to add new material
     */
  case 'add_mat':
    ob_start();
    echo $close;
    require_once('include/material_add.inc.php');
    $html=ob_get_contents();
    ob_end_clean();
    break;
    /*
     * save the new material
     */
  case 'save_new_material':
    ob_start();
    echo $close;
    require_once('include/material_new.inc.php');
    $html=ob_get_contents();
    ob_end_clean();
    break;
  case 'display_modify':
    ob_start();
    require_once('include/material_modify.inc.php');
    $html=ob_get_contents();
    ob_end_clean();
    break;

  case 'save_modify':
    ob_start();
    echo '<span id="result" style="float:left;background:red;color:white">Sauvé</span>';

    require_once('include/material_save.inc.php');
    $f=$cn->get_value("select f_id from fiche join fiche_detail using (f_id) where ad_id=23 and ad_value=$1",array($_POST['p_card']));
    require_once('include/material_modify.inc.php');
    $html=ob_get_contents();
    ob_end_clean();
    break;
  case 'rm':
    ob_start();
    require_once('include/material_delete.inc.php');
    $html=ob_get_contents();
    ob_end_clean();
    break;

  }
$xml=escape_xml($html);
if (headers_sent()) {
    echo $html;
} else 
{
    header('Content-type: text/xml; charset=UTF-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<data>';
    echo '<ctl>'.$ctl.'</ctl>';
    echo '<code>'.$xml.'</code>';
    echo '<extra>'.$extra.'</extra>';
    echo '</data>';
}