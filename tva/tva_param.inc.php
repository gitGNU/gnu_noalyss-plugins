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
 * \brief set up the parameters
 */
require_once('class_tva_parameter.php');
$cn=Dossier::connect();

// save all the parameters
if ( isset ($_POST['save_misc']))  {
	extract($_POST);

	foreach ( array('CRTVA','ATVA','DTTVA') as $i){
		$value=${$i};
		if ( trim(${$i})=='') $value=null;
		if ( $cn->get_value("select count(*) from tva_belge.parameter_chld where pcode=$1",array($i))==0)
		{
			$cn->exec_sql("insert into tva_belge.parameter_chld(pcode,pcm_val) values($1,$2) ",
					array($i,$value));
		}else {

		$cn->exec_sql("update tva_belge.parameter_chld set pcm_val=$1::account_type where pcode=$2",
				array($value,$i));
		}
	}

	unset($_POST['save_misc']);
}

if ( isset ($_POST['save_addparam'])){
	extract ($_POST);
	try {
		if ( trim($tva_id)=="")			throw new Exception("TVA n'existe pas");
		if ( trim($paccount)=="")			throw new Exception("Poste comptable vide");
		if ( $cn->get_value("select count(tva_id) from tva_rate where tva_id=$1",array($tva_id))==0) throw new Exception("TVA $tva_id n'existe pas");
		$cn->exec_sql("insert into tva_belge.parameter_chld(pcode,tva_id,pcm_val) values ($1,$2,$3::account_type)",
		array($pcode,$tva_id,$paccount));
	} catch(Exception $e) {
		alert("Ne peut sauver : ".$e->getMessage());
	}
}
if ( isset ($_POST['pi_id'])){
	$cn->exec_sql("delete from tva_belge.parameter_chld where pi_id=$1",array($_POST['pi_id']));
}
/* show all the possible parameters */
$tvap=new Tva_Parameter($cn);
require_once NOALYSS_INCLUDE.'/lib/class_itva_popup.php';
$a=new IPopup('popup_tva');
$a->set_title('Choisissez la tva qui convient');
echo $a->input();
require_once NOALYSS_INCLUDE.'/lib/class_iposte.php';
echo IPoste::ipopup('ipop_account');

echo dossier::hidden();
echo HtmlInput::extension();
echo $tvap->display();

