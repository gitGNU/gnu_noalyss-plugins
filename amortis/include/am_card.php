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
 * \brief display a list of card to be paid off, let modify, remove or add
 * included from index.php
 */
require_once('class_am_card.php');

$good=new Am_Card();
/* show simply the listing, in the top, there is a button to add
 * a card, if we click on a card, we get the details and the table of
 * report
 */
$but= $good->add_card();
echo '<div class="content" style="width:80%;margin-left:10%">';
echo '<form method="GET">';
echo dossier::hidden();
echo HtmlInput::hidden('plugin_code',$_REQUEST['plugin_code']);
echo HtmlInput::hidden('sa',$_REQUEST['sa']);
echo HtmlInput::hidden('ac',$_REQUEST['ac']);
$ck=new ICheckBox('all');
$ck->selected=(isset ($_GET['all']))?true:false;
echo '<p>'._('Tous les biens y compris ceux qui sont complétement amortis').' '.$ck->input();
echo HtmlInput::submit('look',_('Recherche')).'</p>';
echo '</form>';
echo $but->input();
echo $good->listing($ck->selected);

echo $but->input();
echo '</div>';