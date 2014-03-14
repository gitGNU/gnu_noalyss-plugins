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
 * \brief print all the card
 */
require_once('class_am_card.php');
$a=new Am_Card();
echo $a->listing();
echo '<div class="content" style="width:80%;margin-left:10%">';
echo '<hr>';
echo date('d.m.Y');
echo '<br>';
echo HtmlInput::print_window();
?>
<form method="GET" action="extension.raw.php" style="display:inline">
<?php echo dossier::hidden()?>
<?php echo HtmlInput::hidden('material','1');?>
<?php echo HtmlInput::hidden('ac',$_REQUEST['ac']);?>
<?php echo HtmlInput::extension()?>
<?php echo HtmlInput::submit('csv','Export CSV');?>
</form>
</div>