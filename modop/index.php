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
 * \brief modify a operation
 */
global $version_plugin;
$version_plugin=SVNINFO;
Extension::check_version(6910);
$str=new IConcerned("jr_id");
$str->value=(isset($_GET['jr_id']))?strtoupper($_GET['jr_id']):'';
$str->value=(isset($_GET['ext_jr_id']))?strtoupper($_GET['ext_jr_id']):$str->value;
echo '<div style="float:right"><a class="mtitle" style="font-size:140%" href="http://wiki.noalyss.eu/doku.php?id=modification_d_operation" target="_blank">Aide</a>'.
'<span style="font-size:0.8em;color:red;display:inline">vers:SVNINFO</span>'.
'</div>';



?>
<FORM METHOD="GET">
  <?php echo HtmlInput::extension().dossier::hidden();?>
  <?php echo HtmlInput::hidden('ac',$_REQUEST['ac']);?>
<?php echo _("Code interne de l'opération à modifier") ?>
  <?php echo $str->input()?>
	<?php 

	echo HtmlInput::submit('seek','retrouver')?>
</FORM>
<hr>
<?php
$action=(isset ($_REQUEST['action']))?$_REQUEST['action']:'end';

  if ( ! isset ($_REQUEST['action']) && isset($_GET['seek'])) {
    /* retrieve and show the accounting */
    if ( trim($_GET['jr_id'])=='') {
      alert(_('Aucune opération demandé')); exit;}
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

