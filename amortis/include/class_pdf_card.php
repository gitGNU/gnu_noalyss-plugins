<?php 
/*
 *   This file is a part of NOALYSS.
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
 * Copyright 2010 De Bontridder Dany <dany@alchimerys.be>

 * 
*/
require_once NOALYSS_INCLUDE.'/lib/class_pdf.php';


class Pdf_Card extends PDF
{
    public function __construct (&$p_cn = null, $orientation = 'P', $unit = 'mm', $format = 'A4')
    {

        if($p_cn == null) die("No database connection. Abort.");

        parent::__construct($p_cn,'P', 'mm', 'A4');
        date_default_timezone_set ('Europe/Paris');
	$this->total_page=0;

    }

    function setDossierInfo($dossier = "n/a")
    {
        $this->dossier = dossier::name()." ".$dossier;
    }

    function Header()
    {
        //Arial bold 12
        $this->SetFont('DejaVu', 'B', 12);
        //Title
        $this->write_cell(0,10,$this->dossier, 'B', 0, 'C');
        //Line break
        $this->line_new(20);
    }
    function Footer()
    {
        //Position at 2 cm from bottom
        $this->SetY(-20);
        //Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        //Page number
        $this->write_cell(0,8,'Date '.$this->date." - Page ".$this->PageNo().'/{nb}',0,0,'C');
        $this->line_new(3);
        // Created by NOALYSS
        $this->write_cell(0,8,'Created by NOALYSS, a professional opensource accounting software http://www.noalyss.eu',0,0,'C',false,'http://www.noalyss.eu');
    }
    

    function export()
    {
        // take all material from 
      $array=$this->cn->get_array("select a_id , f_id , vw_name,vw_description,
				account_deb , account_cred , a_amount ,
				a_nb_year , a_start
				from  amortissement.amortissement
			       left join vw_fiche_attr using(f_id)
			       where a_visible='Y'
			       order by vw_name	");

      $this->total_page=count($array);
      $this->SetFont('DejaVu','BI',7);
      bcscale(2);
        // print all operation
        for ($i=0;$i< count($array);$i++)
        {
            $this->SetFont('DejaVuCond','B',15);
            $row=$array[$i];
	    $this->write_cell(0,0,$row['vw_name'],0,0,'C');

            $this->line_new(12);

            $this->SetFont('DejaVu','',7);
            $this->write_cell(50,7,'Montant');
            $this->write_cell(50,7,nbm($row['a_amount']));
	    $this->line_new(4);

            $this->write_cell(50,7,"Année d'achat");
            $this->write_cell(50,7,$row['a_start']);
	    $this->line_new(4);

            $this->write_cell(50,7,"Poste charge");
            $this->write_cell(50,7,$row['account_deb']);
	    $deb=$this->cn->get_value("select pcm_lib from tmp_pcmn where pcm_val=$1",
				      array($row['account_deb']));
            $this->write_cell(120,7,$deb);
	    $this->line_new(4);

            $this->write_cell(50,7,"Poste contrepartie");
            $this->write_cell(50,7,$row['account_cred']);
	    $cred=$this->cn->get_value("select pcm_lib from tmp_pcmn where pcm_val=$1",
				      array($row['account_cred']));
            $this->write_cell(120,7,$cred);
	    $this->line_new(4);

            $this->write_cell(50,7,"Nbre annuités");
            $this->write_cell(50,7,$row['a_nb_year']);
	    $this->line_new(12);

	    /*
	     * Now we print for each year 
	     */
	    $col=array('Année','Montant','Am. actés','Pièce','n° interne','%');
	    foreach ($col as $scol)
	      {
		$this->write_cell(25,7,$scol,1);
	      }
	    $this->line_new();
			
	    $array_year=$this->cn->get_array("select   ad_id , ad_amount , a_id , ad_year , ad_percentage ".
					     " from amortissement.amortissement_detail ".
					     " where a_id=$1 order by ad_year",
					     array($row['a_id']));
	    for ($e=0;$e<count($array_year);$e++)
	      {
		$this->write_cell(25,7,$array_year[$e]['ad_year'],1,0);
		$this->write_cell(25,7,nbm($array_year[$e]['ad_amount']),1,0,'R');
		$ret=$this->cn->exec_sql("select h_amount,h_pj,jr_internal from amortissement.amortissement_histo where a_id=$1 and h_year=$2",
					 array($row['a_id'],$array_year[$e]['ad_year']));
		$value=$this->cn->fetch_array($ret,0);
		$this->write_cell(25,7,nbm($value['h_amount']),1,0,'R');
		$this->write_cell(25,7,$value['h_pj'],1,0);
		$this->write_cell(25,7,$value['jr_internal'],1,0);
		$pct=bcdiv($array_year[$e]['ad_amount'],$row['a_amount']);
		$pct=bcmul($pct,100);
		$this->write_cell(25,7,$pct,1,0,'R');

		$this->line_new();

	      }// for all year
	    if ($i < count($array)) $this->AddPage();
        } // for ... all card
	$this->Output('toutes_les_fiches.pdf','I');
    }
}
?>