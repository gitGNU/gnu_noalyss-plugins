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

// Copyright Author Dany De Bontridder danydb@aevalys.eu
/**
 * extends class_anc_grandlivre
 *
 * @author dany
 */
class Anc_Grandlivre_Ext extends Anc_GrandLivre
{
    /*!
     * \brief Show the button to export in PDF or CSV
     * \param $url_csv url of the csv
     * \param $url_pdf url of the pdf
     * \param $p_string hidden data to include in the form
     *
     *
     * \return string with the button
     */

    function show_button($p_string = "")
    {
        $r = "";
        $r.= '<form method="GET" action="export.php"  style="display:inline">';
        $r.= HtmlInput::hidden("act", "CSV:AncGrandLivre");
        $r.= HtmlInput::hidden("to", $this->to);
        $r.= HtmlInput::hidden("from", $this->from);
        $r.= HtmlInput::hidden("pa_id", $this->pa_id);
        $r.= HtmlInput::hidden("from_poste", $this->from_poste);
        $r.= HtmlInput::hidden("to_poste", $this->to_poste);
        $r.= $p_string;
        $r.= dossier::hidden();
        $r.=HtmlInput::submit('bt_csv', "Export en CSV");
        $r.= '</form>';
        return $r;
    }

    /*!
     * \brief Show the button to export in PDF all the receipt
     * 
     * \param $p_string extra hidden value
     * \return string with the button
     */

    function button_export_pdf($p_string = "")
    {
        $r = "";
        $r.= HtmlInput::hidden("to", $this->to);
        $r.= HtmlInput::hidden("from", $this->from);
        $r.= HtmlInput::hidden("pa_id", $this->pa_id);
        $r.= HtmlInput::hidden("from_poste", $this->from_poste);
        $r.= HtmlInput::hidden("to_poste", $this->to_poste);
        $r.= $p_string;
        $r.= dossier::hidden();
        $r.=HtmlInput::submit('bt_receipt_anal_pdf', _("Export des pièces en PDF"));
        return $r;
    }

    /*!
     * \brief compute the html display
     *
     *
     * \return string
     */

    function display_html()
    {
        $r = "";
        //---Html
        $array = $this->load();
        if (is_array($array) == false)
        {
            return $array;
        }

        if (empty($array))
        {
            $r.= _("aucune donnée");
            return $r;
        }
        $r.= '<table class="result" style="width=100%">';
        $ix = 0;
        $prev = 'xx';
        $idx = 0;
        $tot_deb = $tot_cred = 0;

        foreach ($array as $row)
        {
            if ($prev != $row['po_name'])
            {
                if ($ix > 0)
                {
                    $r.='<tr>';
                    $tot_solde = bcsub($tot_cred, $tot_deb);
                    $sign = ($tot_solde > 0) ? 'C' : 'D';
                    $r.=td('') . td('') . td('') . td('') . td('') . td(nbm($tot_deb), ' class="num"') . td(nbm($tot_cred), ' class="num"') . td(nbm($tot_solde) . $sign, ' class="num"');
                }
                $r.='<tr>' . '<td colspan="7" style="width:auto">' . '<h2>' . h($row['po_name'] . ' ' . $row['po_description']) . '</td></tr>';
                $r.= '<tr>' .
                        '<th>' . '</th>' .
                        '<th>' . _('Date') . '</th>' .
                        '<th>' . _('Poste') . '</th>' .
                        '<th>' . _('Quick_code') . '</th>' .
                        '<th>' . _('Libellé') . '</th>' .
                        '<th>' . _('Document') . '</th>' .
                        '<th>' . _('Pièce') . '</th>' .
                        '<th>' . _('Num.interne') . '</th>' .
                        '<th style="text-align:right">' . _('Debit') . '</th>' .
                        '<th style="text-align:right">' . _('Credit') . '</th>' .
                        '</tr>';

                $tot_deb = $tot_cred = 0;
                $prev = $row['po_name'];
                $ix++;
            }
            $class = ($idx % 2 == 0) ? 'even' : 'odd';
            $idx++;
            $r.='<tr class="' . $class . '">';
            $detail = ($row['jr_id'] != null) ? HtmlInput::detail_op($row['jr_id'], $row['jr_internal']) : '';
            $post_detail = ($row['j_poste'] != null) ? HtmlInput::history_account($row['j_poste'], $row['j_poste']) : '';
            $card_detail = ($row['f_id'] != null) ? HtmlInput::history_card($row['f_id'], $row['qcode']) : '';
            $amount_deb = ($row['oa_debit'] == 't') ? $row['oa_amount'] : 0;
            $amount_cred = ($row['oa_debit'] == 'f') ? $row['oa_amount'] : 0;
            $tot_deb = bcadd($tot_deb, $amount_deb);
            $tot_cred = bcadd($tot_cred, $amount_cred);

            /*
             * Checked button
             */
            $str_ck = "";
            $str_document = "";
            if ($row['jr_id'] != null)
            {
                /*
                 * Get receipt info  
                 */
                $str_document = HtmlInput::show_receipt_document($row['jr_id']);
                if ($str_document != "")
                {
                    $ck = new ICheckBox('ck[]', $row['jr_id']);
                    $str_ck = $ck->input();
                }
            }

            $r.=
                    '<td>' . $str_ck . '</td>' .
                    '<td>' . $row['oa_date'] . '</td>' .
                    td($post_detail) .
                    td($card_detail) .
                    td($row['jr_comment']) .
                    '<td>' . $str_document . '</td>' .
                    td($row['jr_pj_number']) .
                    '<td>' . $detail . '</td>' .
                    '<td class="num">' . nbm($amount_deb) . '</td>' .
                    '<td class="num">' . nbm($amount_cred)
                    . '</td>';
            $r.= '</tr>';
        }
        $r.='<tr>';
        $tot_solde = bcsub($tot_cred, $tot_deb);
        $sign = ($tot_solde > 0) ? 'C' : 'D';
        $r.=td('') . td('') . td('') . td('') . td('') . td(nbm($tot_deb), ' class="num"') . td(nbm($tot_cred), ' class="num"') . td(nbm($tot_solde) . $sign, ' class="num"');

        $r.= '</table>';
        return $r;
    }

   

}
