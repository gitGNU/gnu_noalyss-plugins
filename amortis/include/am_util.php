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
 * \brief let you generate the accounting for the paid off for a selected
 *  year
 */
require_once('class_am_generate.php');
require_once('class_amortissement_sql.php');
global $cn;

$am=new Am_Generate();

if (isset( $_POST['generate'] ))
  {
    /*
     * propose writing
     */
    if ( $am->propose_writing($_POST) == false )
      {
	echo '<div class="content" style="width:80%;margin-left:10%">';
	echo $am->input($_POST );
	echo '</div>';
      }
    exit();
  }

if (isset($_POST['save']))
  {
    $ledger=new Acc_Ledger($cn,$_POST['p_jrn']);
    try 
      {
	$ledger->save($_POST);

	$jr_id=$cn->get_value("select jr_id from jrn where jr_internal=$1",array($ledger->internal));
	echo '<div class="content" style="width:80%;margin-left:10%">';

	echo HtmlInput::detail_op($jr_id,"Opération sauvée : ".$ledger->internal);	
	echo '</div>';

	exit();
      }
    catch (Exception $e)
      {
	echo alert($e->getMessage());
      }
  }

echo '<div class="content" style="width:80%;margin-left:10%">';
echo $am->input($_POST );


echo '</div>';