<?php
/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/*!\file
 * \brief print for a specific year
 */

echo '<div class="content" style="width:80%;margin-left:10%">';
echo '<FORM METHOD="GET">';
echo HtmlInput::hidden('sa',$_REQUEST['sa']);
echo HtmlInput::hidden('sb',$_REQUEST['sb']);
echo HtmlInput::hidden('plugin_code',$_REQUEST['plugin_code']);
echo dossier::hidden();
$year=new INum('p_year');
$year->value=(isset($_GET['p_year']))?$_GET['p_year']:'';
echo "Année ".$year->input();
echo HtmlInput::submit('search','Accepter');
echo '</form>';
/* display for year */
if ( isset($_GET['search'])&& $_GET['p_year'] != '' &&isNumber($_GET['p_year'])==1)
  {
    $year=$_GET['p_year'];
    $sql="select * from amortissement.amortissement where a_id
         in (select a_id from amortissement.amortissement_detail where ad_year=$1)";
    $array=$cn->get_array($sql,array($_GET['p_year']));
    require_once('template/listing_year.php');
    $d=dossier::id();$plugin=$_REQUEST['plugin_code'];$y=$_GET['p_year'];

  }
echo '</div>';