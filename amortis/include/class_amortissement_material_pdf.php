<?php

/* 
 * Copyright (C) 2014 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once NOALYSS_INCLUDE.'/class_pdf.php';

class Amortissement_Material_PDF extends PDFLand
{

    function header()
    {
        parent::header();
        $this->setFont('DejaVu', 'B', 14);
        $this->Cell(190, 10, _('Amortissement : Liste de biens'), 1, 2, 'C');
        $this->ln();
        $this->col_size=array('qcode'=>40, 'name'=>85,'desc'=>120, 'date.purch'=>20, 'year.purch'=>20,  '#amort'=>10,'amount.purch'=>30, 'amount.amort'=>30, '%'=>20, 'amount.remain'=>30,'amount.delta'=>30);

        $this->setFont('DejaVu', 'B', 7);
        $this->Cell($this->col_size['qcode'], 8, _('QCode'));
        $this->Cell($this->col_size['name'], 8, _('Nom'));
        $this->Cell($this->col_size['date.purch'], 8, _('Date achat'));
        $this->Cell($this->col_size['year.purch'], 8, _('Année achat'));
        $this->Cell($this->col_size['#amort'], 8, _('Nbre'), 0, 0, 'R');
        $this->Cell($this->col_size['amount.purch'], 8, _('Montant'), 0, 0, 'R');
        $this->Cell($this->col_size['amount.amort'], 8, _('A amortir'), 0, 0, 'R');
        $this->Cell($this->col_size['amount.delta'], 8, _('Val. Comptable Net'), 0, 0, 'R');
        $this->Ln();
    }

    function export()
    {
        global  $cn;
        $this->SetFont('DejaVu', '', 7);
        $ret=$cn->get_array("select * from amortissement.v_amortissement_summary where a_visible='Y' order by a_start,a_date");
        bcscale(2);
        $tot_purchase=0;$tot_amorti=0;$tot_remain=0;

        for ($i=0;$i<count($ret);$i++)
        {
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
            
            $this->Cell($this->col_size['qcode'], 8, $ret[$i]['quick_code'], 0, 0, 'L', $fill);
            $this->Cell($this->col_size['name'], 8, $ret[$i]['vw_name'], 0, 0, 'L', $fill);
            $this->Cell($this->col_size['date.purch'], 8, format_date($ret[$i]['a_date']), 0, 0, 'L', $fill);
            $this->Cell($this->col_size['year.purch'], 8, $ret[$i]['a_start'], 0, 0, 'C', $fill);
            $this->Cell($this->col_size['#amort'], 8, round($ret[$i]['a_nb_year']), 0, 0, 'R', $fill);
            $this->Cell($this->col_size['amount.purch'], 8, nb($ret[$i]['a_amount']), 0, 0, 'R', $fill);
            $this->Cell($this->col_size['amount.amort'], 8, nb($ret[$i]['amort_done']), 0, 0, 'R', $fill);
            $delta=bcsub($ret[$i]['a_amount'],$ret[$i]['amort_done']);
            $this->Cell($this->col_size['amount.delta'], 8, nb($delta), 0, 0, 'R', $fill);
            
            $tot_purchase=bcadd($tot_purchase,$ret[$i]['a_amount']);
            $tot_amorti=bcadd($tot_amorti,$ret[$i]['amort_done']);
            $tot_remain=bcadd($tot_remain,$delta);
	
        $this->Ln();
        }
        $deca=$this->col_size['qcode']+$this->col_size['name']+$this->col_size['date.purch']+$this->col_size['year.purch']+$this->col_size['#amort'];
        $this->Cell($deca+$this->col_size['amount.purch'],8,nb($tot_purchase) ,0,0,'R',0);
        $this->Cell($this->col_size['amount.amort'],8,nb($tot_amorti) ,0,0,'R',0);
        $this->Cell($this->col_size['amount.delta'],8,nb($tot_remain) ,0,0,'R',0);
        $this->ln();
        $this->Output('listing-amort.pdf', 'I');
    }

}
