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
if ( ! isset ($version_phpcompta) || $version_phpcompta < 3428 ) {
	alert('Cette extension ne fonctionne pas sur cett version de PhpCompta'.
	' Veuillez mettre votre programme a jour');
	exit();
}

$str=new IText("jr_internal");
$str->value=(isset($_GET['jr_internal']))?strtoupper($_GET['jr_internal']):'';
$str->value=(isset($_GET['ext_jr_internal']))?strtoupper($_GET['ext_jr_internal']):$str->value;

$search=new IButton('getjr');
$js="openRecherche(".dossier::id().")";
$search->label='Chercher dans les journaux';
$search->javascript=sprintf('%s',$js);
?>
<FORM METHOD="GET">
  <? echo HtmlInput::extension().dossier::hidden();?>
<?=_("Code interne de l'opération à modifier") ?>
  <?=$str->input()?>
  <?=HtmlInput::submit('seek','retrouver')?>
</FORM>
  <?=$search->input()?>
<hr>
<?php
  if ( isset($_GET['seek'])) {
    /* retrieve and show the accounting */
    if ( trim($_GET['jr_internal'])=='') {
      alert('Aucune opération demandé'); exit;}
    /*  retrieve and display operation */
    require_once('modop_display.php'); 
    exit();
  }
$action=(isset ($_GET['action']))?$_GET['action']:'end';
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

