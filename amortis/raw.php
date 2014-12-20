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
 * \brief raw file for PDF
 */
require_once('amortis_constant.php');

extract ($_REQUEST);

/* export all cards in PDF */
if ( isset($_REQUEST['pdf_all']))
  {
    require_once('include/class_pdf_card.php');
    global $cn;
    $a=new Pdf_Card($cn);
    $a->setDossierInfo(dossier::id());
    $a->AliasNbPages('{nb}');
    $a->AddPage();
    $a->export();
    exit();
  }
/*
 * Export the list per year in CSV
 */
if ( isset ($_REQUEST['csv_list_year']))
{
  $name="amortis-export-".$_REQUEST['csv_list_year'];
  header('Pragma: public');
  header('Content-type: application/csv');
  header('Content-Disposition: attachment;filename="'.$name.'.csv"',FALSE);
  print "\"Code\";\"Description\";\"Date acquisition\";\"Année Achat\";\"Montant Achat\";\"Nombre annuités\";\"Montant à amortir\";\"Amortissement\";\"Reste\"\r\n";
  $year=$_REQUEST['csv_list_year'];
  $sql="select * from amortissement.amortissement where a_id
         in (select a_id from amortissement.amortissement_detail where ad_year=$1)";
  $array=$cn->get_array($sql,array($year));
  bcscale(2);
  for ($i=0;$i<count($array);$i++)
    {
      $fiche=new fiche($cn,$array[$i]['f_id']);
      $remain=$cn->get_value("select coalesce(sum(ad_amount),0) from amortissement.amortissement_detail
			where a_id=$1 and ad_year >= $2",
			     array($array[$i]['a_id'],$year));
      $amortize=$cn->get_value("select ad_amount from amortissement.amortissement_detail
			where a_id=$1 and ad_year = $2",
			       array($array[$i]['a_id'],$year));
      $toamortize=bcsub($remain,$amortize);

      printf("\"%s\";\"%s\";%s;%s;%s;%s;%s;%s;%s\r\n",
	     $fiche->strAttribut(ATTR_DEF_QUICKCODE),
	     $fiche->strAttribut(ATTR_DEF_NAME),
	     format_date($array[$i]['a_date']),
	     $array[$i]['a_start'],
	     nb($array[$i]['a_amount']),
	     nb($array[$i]['a_nb_year']),
	     nb($remain),
	     nb($amortize),
	     nb($toamortize)
	     );
	     
    }
}

/* export all cards in PDF */
if ( isset($_REQUEST['pdf_list_year']))
  {
    require_once('include/class_amortissement_table_pdf.php');
    global $cn;
    $year=$_REQUEST['pdf_list_year'];
    $a=new Amortissement_Table_PDF($cn);
    $a->SetTitle('Amortissement '.$year);
    $a->SetAuthor('NOALYSS');
    $a->SetCreator('NOALYSS');
    $a->year=$year;
    $a->setDossierInfo(dossier::id());
    $a->AliasNbPages('{nb}');
    $a->AddPage();
    $a->export();
    exit();
  }
/*
 * Export to CSV all the listing
 */
if ( isset($_GET['csv_material']))
  {
    $name="listing-material";
    header('Pragma: public');
    header('Content-type: application/csv');
    header('Content-Disposition: attachment;filename="'.$name.'.csv"',FALSE);
    
    $ret=$cn->get_array("select * from amortissement.amortissement order by a_start,a_date");
    printf ("\"Visible\";\"qcode\";\"Nom\";\"Date acquisition\";\"Année Achat\";\"Nbre annuité\";\"Poste Charge\";\"Poste amortis\";\"Montant achat\";\"Montant amorti\";\"Montant a amortir\"\r\n");
    for ($i=0;$i<count($ret);$i++)
      {
	printf('"%s";',$ret[$i]['a_visible']);
	$qcode=$cn->get_value('select ad_value from fiche_detail where ad_id=23 and f_id=$1',array($ret[$i]['f_id']));
	printf('"%s";',$qcode);
	$qcode=$cn->get_value('select ad_value from fiche_detail where ad_id=1 and f_id=$1',array($ret[$i]['f_id']));
	printf('"%s";',$qcode);
	printf('"%s";',format_date($ret[$i]['a_date']));
	printf('"%s";',$ret[$i]['a_start']);
	printf('%s;',nb($ret[$i]['a_nb_year']));
	printf('"%s";',$ret[$i]['account_deb']);
	printf('"%s";',$ret[$i]['account_cred']);
	printf('%s;',nb($ret[$i]['a_amount']));
	$tot_amorti=$cn->get_value("select coalesce(sum(h_amount),0) from amortissement.amortissement_histo where a_id=$1",
				   array($ret[$i]['a_id']));
	printf("%s;",nb($tot_amorti));
	$remain=bcsub($ret[$i]['a_amount'],$tot_amorti);
	printf("%s",nb($remain));
	printf("\r\n");
      }
  }
/*
 * Export to PDF all the listing
 */
if ( isset($_GET['pdf_material']))
  {
    require_once('include/class_amortissement_material_pdf.php');
    global $cn;
    $a=new Amortissement_Material_PDF($cn);
    $a->SetTitle('Amortissement ');
    $a->SetAuthor('NOALYSS');
    $a->SetCreator('NOALYSS');
    $a->setDossierInfo(dossier::id());
    $a->AliasNbPages('{nb}');
    $a->AddPage();
    $a->export();
    exit();
    
   
  }


?>