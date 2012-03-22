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
 * \brief modify a operation
 */
Extension::check_version(4400);
$str=new IConcerned("jr_id");
$str->value=(isset($_GET['jr_id']))?strtoupper($_GET['jr_id']):'';
$str->value=(isset($_GET['ext_jr_id']))?strtoupper($_GET['ext_jr_id']):$str->value;
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.phpcompta.eu/doku.php?id=modification_d_operation" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';



?>
<FORM METHOD="GET">
  <? echo HtmlInput::extension().dossier::hidden();?>
  <? echo HtmlInput::hidden('ac',$_REQUEST['ac']);?>
<?=_("Code interne de l'opération à modifier") ?>
  <?=$str->input()?>
	<?

	echo HtmlInput::submit('seek','retrouver')?>
</FORM>
<hr>
<?php
$action=(isset ($_REQUEST['action']))?$_REQUEST['action']:'end';

  if ( ! isset ($_REQUEST['action']) && isset($_GET['seek'])) {
    /* retrieve and show the accounting */
    if ( trim($_GET['jr_id'])=='') {
      alert('Aucune opération demandé'); exit;}
    /*  retrieve and display operation */
    require_once('modop_display.php');
    exit();
  }


/* we need to confirm it */
if ( $action=='confirm' ) {
  require_once('modop_confirm.php');
  exit();
}
/*  we can now save it */
if ($action=='save') {
  require_once ('modop_save.php');
  exit();
}
?>

