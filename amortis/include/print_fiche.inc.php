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
require_once('class_am_card.php');
/*!\file
 * \brief print detail of a card
 */
echo '<div class="content" style="width:80%;margin-left:10%">';
echo '<FORM METHOD="GET">';
echo HtmlInput::hidden('sa',$_REQUEST['sa']);
echo HtmlInput::hidden('sb',$_REQUEST['sb']);
echo HtmlInput::hidden('plugin_code',$_REQUEST['plugin_code']);
echo dossier::hidden();
$list=$cn->make_list('select fd_id from fiche_def where frd_id=7');

$p_card=new ICard('p_card');
$p_card->size=25;
$p_card->set_attribute('typecard',$list);
$p_card->set_attribute('label','p_card_label');
$p_card->javascript=sprintf(' onchange="fill_data_onchange(\'%s\');" ',
            $p_card->name);
$p_card->set_function('fill_data');
$p_card->set_dblclick("fill_ipopcard(this);");
$msg="Fiche";
if ( isset($_GET['p_card']))
  {
    /* search the card */
    $fiche=new Fiche($cn);
    $fiche->get_by_qcode($_GET['p_card']);
    $msg=$fiche->strAttribut(ATTR_DEF_NAME);
    $p_card->value=$_GET['p_card'];
  }
echo '<span style="text-align:left;display:block;font-size:2em" id="p_card_label"  >'.$msg.'</span>';
echo "Fiche ".$p_card->input().$p_card->search();
echo HtmlInput::submit('search','Accepter');
echo '</form>';

echo '<FORM METHOD="GET" ACTION="extension.raw.php">';
echo HtmlInput::hidden('sa',$_REQUEST['sa']);
echo HtmlInput::hidden('sb',$_REQUEST['sb']);
echo HtmlInput::hidden('plugin_code',$_REQUEST['plugin_code']);
echo dossier::hidden();
echo HtmlInput::submit('pdf_all','Toutes les fiches en PDF');
echo '</form>';

if ( isset($_GET['search']))
  {
    $a=new Am_Card();
    echo $a->print_detail($_GET['p_card']);
  }
echo '</div>';