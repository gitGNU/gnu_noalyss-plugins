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
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
require_once NOALYSS_INCLUDE.'/lib/class_pdf.php';

class Amortissement_Table_PDF extends PDFLand
{

    function header()
    {
        parent::header();
        $this->setFont('DejaVu', 'B', 14);
        $this->Cell(200, 10, _('Amortissement ').$this->year, 1, 2, 'C');
        $col_size=array('code'=>20, 'desc'=>80, 'date.purch'=>20, 'year.purch'=>20, 'amount.purch'=>30, '#amort'=>10, 'amount.amort'=>30, '%'=>20, 'amount.remain'=>20);
        $this->Ln();
        $this->SetFont('DejaVu', 'BI', 7);
        $this->Cell($col_size['code'], 8, _('Code'));
        $this->Cell($col_size['desc'], 8, _('Description'));
        $this->Cell($col_size['date.purch'], 8, _('Date achat'));
        $this->Cell($col_size['year.purch'], 8, _('Année achat'));
        $this->Cell($col_size['amount.purch'], 8, _('Montant'), 0, 0, 'R');
        $this->Cell($col_size['#amort'], 8, _('Nbre'), 0, 0, 'R');
        $this->Cell($col_size['amount.amort'], 8, _('A amortir'), 0, 0, 'R');
        $this->Cell($col_size['%'], 8, _('%'), 0, 0, 'R');
        $this->Cell($col_size['amount.remain'], 8, _('Dot'), 0, 0, 'R');
        $this->Cell($col_size['amount.remain'], 8, _('Reste'), 0, 0, 'R');
        $this->Ln();
        
    }

    function export()
    {
        $sql=" select * from amortissement.amortissement where a_id
        in (select a_id from amortissement.amortissement_detail where ad_year=$1)";
        $array=$this->cn->get_array($sql, array($this->year));
        $tot_amort=0;
        $tot_net=0;
        $col_size=array('code'=>20, 'desc'=>80, 'date.purch'=>20, 'year.purch'=>20, 'amount.purch'=>30, '#amort'=>10, 'amount.amort'=>30, '%'=>20, 'amount.remain'=>20);

        bcscale(2);

        
        $this->Ln();
        $this->SetFont('DejaVu', '', 7);
        for ($i=0; $i<count($array); $i++)
        {
            $fiche=new fiche($this->cn, $array[$i]['f_id']);
            $remain=$this->cn->get_value("select coalesce(sum(ad_amount), 0) from amortissement.amortissement_detail
        where a_id=$1 and ad_year >= $2", array($array[$i]['a_id'], $this->year));
            $amortize=$this->cn->get_value("select ad_amount from amortissement.amortissement_detail
        where a_id=$1 and ad_year=$2", array($array[$i]['a_id'], $this->year));
            $pct=$this->cn->get_value("select  ad_percentage from amortissement.amortissement_detail
			where a_id=$1 and ad_year = $2", array($array[$i]['a_id'], $this->year));
            $toamortize=bcsub($remain, $amortize);
            $tot_amort=bcadd($tot_amort, $amortize);
            $tot_net=bcadd($tot_net, $toamortize);
            if ($i%2==0)
            {
                $this->SetFillColor(220, 221, 255);
                $fill=1;
            }
            else
            {
                $this->SetFillColor(0, 0, 0);
                $fill=0;
            }
            $this->Cell($col_size['code'], 8, $fiche->strAttribut(ATTR_DEF_QUICKCODE), 0, 0, 'L', $fill);
            $this->Cell($col_size['desc'], 8, $fiche->strAttribut(ATTR_DEF_NAME), 0, 0, 'L', $fill);
            $this->Cell($col_size['date.purch'], 8, format_date($array[$i]['a_date']), 0, 0, 'L', $fill);
            $this->Cell($col_size['year.purch'], 8, $array[$i]['a_start'], 0, 0, 'C', $fill);
            $this->Cell($col_size['amount.purch'], 8, nb($array[$i]['a_amount']), 0, 0, 'R', $fill);
            $this->Cell($col_size['#amort'], 8, nb($array[$i]['a_nb_year']), 0, 0, 'R', $fill);
            $this->Cell($col_size['amount.amort'], 8, nb($remain), 0, 0, 'R', $fill);
            $this->Cell($col_size['%'], 8, nb($pct), 0, 0, 'R', $fill);
            $this->Cell($col_size['%'], 8, nb($amortize), 0, 0, 'R', $fill);
            $this->Cell($col_size['amount.remain'], 8, nb($toamortize), 0, 0, 'R', $fill);
            $this->Ln();
        }
        $this->Ln(10);
        $tot=$this->cn->get_value(" select coalesce(sum(a_amount),0) from amortissement.amortissement where a_start=$1", array($this->year));
        $this->setX(40);
        $this->Cell(60, 8, "Acquisition de l'année", 1, 0, 'R');
        $this->Cell(60, 8, nb($tot), 1, 0, 'R');
        $this->ln();

        $this->setX(40);
        $this->Cell(60, 8, "Amortissement", 1, 0, 'R');
        $this->Cell(60, 8, nb($tot_amort), 1, 0, 'R');
        $this->ln();

        $this->setX(40);
        $this->Cell(60, 8, "Valeur net", 1, 0, 'R');
        $this->Cell(60, 8, nb($tot_net), 1, 0, 'R');
        $this->ln();

        $this->Output('tab-amort.pdf', 'I');
    }

}

?>
