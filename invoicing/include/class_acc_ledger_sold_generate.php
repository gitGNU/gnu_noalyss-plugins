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

// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be
require_once NOALYSS_INCLUDE.'/class/class_acc_ledger_sold.php';
class Acc_Ledger_Sold_Generate extends Acc_Ledger_Sold
{

    /**
     * @brief Show all the operation of ledger Sold 
     * @param$sql is the sql stmt, normally created by build_search_sql
     * @param$offset the offset
     * @param$p_paid if we want to see info about payment
      \code
      // Example
      // Build the sql
      list($sql,$where)=$Ledger->build_search_sql($_GET);
      // Count nb of line
      $max_line=$cn->count_sql($sql);

      $step=$_SESSION['g_pagesize'];
      $page=(isset($_GET['offset']))?$_GET['page']:1;
      $offset=(isset($_GET['offset']))?$_GET['offset']:0;
      // create the nav. bar
      $bar=navigation_bar($offset,$max_line,$step,$page);
      // show a part
      list($count,$html)= $Ledger->list_operation($sql,$offset,0);
      echo $html;
      // show nav bar
      echo $bar;

      \endcode
     * \see build_search_sql
     * \see display_search_form
     * \see search_form

     * \return HTML string
     */
    public function list_operation($sql, $offset, $p_paid = 0)
    {
        global $g_parameter, $g_user;
        bcscale(2);
        $table = new Sort_Table();
        $gDossier = dossier::id();
        $amount_paid = 0.0;
        $amount_unpaid = 0.0;
        $limit = ($_SESSION['g_pagesize'] != -1) ? " LIMIT " . $_SESSION['g_pagesize'] : "";
        $offset = ($_SESSION['g_pagesize'] != -1) ? " OFFSET " . Database::escape_string($offset) : "";
        $order = "  order by jr_date_order asc,jr_internal asc";
        // Sort
        $url = "?" . CleanUrl();
        $str_dossier = dossier::get();
        $table->add("Date", $url, 'order by jr_date asc,substring(jr_pj_number,\'[0-9]+$\')::numeric asc', 'order by  jr_date desc,substring(jr_pj_number,\'[0-9]+$\')::numeric desc', "da", "dd");
        $table->add('Echeance', $url, " order by  jr_ech asc", " order by  jr_ech desc", 'ea', 'ed');
        $table->add('Paiement', $url, " order by  jr_date_paid asc", " order by  jr_date_paid desc", 'eap', 'edp');
        $table->add('PJ', $url, ' order by  substring(jr_pj_number,\'[0-9]+$\')::numeric asc ', ' order by  substring(jr_pj_number,\'[0-9]+$\')::numeric desc ', "pja", "pjd");
        $table->add('Tiers', $url, " order by  name asc", " order by  name desc", 'na', 'nd');
        $table->add('Montant', $url, " order by jr_montant asc", " order by jr_montant desc", "ma", "md");
        $table->add("Description", $url, "order by jr_comment asc", "order by jr_comment desc", "ca", "cd");

        $ord = (!isset($_GET['ord'])) ? 'da' : $_GET['ord'];
        $order = $table->get_sql_order($ord);

        // Count
        $count = $this->db->count_sql($sql);
        // Add the limit
        $sql.=$order . $limit . $offset;
        // Execute SQL stmt
        $Res = $this->db->exec_sql($sql);

        //starting from here we can refactor, so that instead of returning the generated HTML,
        //this function returns a tree structure.

        $r = "";


        $Max = Database::num_row($Res);

        if ($Max == 0)
            return array(0, _("Aucun enregistrement trouvé"));

        $r.='<table class="result">';


        $r.="<tr >";
        $r.='<th>';
        $r.=ICheckbox::toggle_checkbox('sellall',"sel_sale_frm");
        $r.='</th>';
        $r.="<th>Internal</th>";
        if ($this->type == 'ALL')
        {
            $r.=th('Journal');
        }
        $r.='<th>' . $table->get_header(0) . '</th>';
        $r.='<th>' . $table->get_header(1) . '</td>';
        $r.='<th>' . $table->get_header(2) . '</th>';
        $r.='<th>' . $table->get_header(3) . '</th>';
        $r.='<th>' . $table->get_header(4) . '</th>';
        $r.='<th>' . $table->get_header(6) . '</th>';
        $r.='<th>' . $table->get_header(5) . '</th>';
        $r.="<th>" . _('D') . "</th>";
        $r.=th('Notes', ' style="width:15%"');
        // if $p_paid is not equal to 0 then we have a paid column
        if ($p_paid != 0)
        {
            $r.="<th> " . _('Payé') . "</th>";
        }
        $r.="<th>" . _('Concerne') . "</th>";
        
        $r.="</tr>";
        // Total Amount
        $tot = 0.0;
        $gDossier = dossier::id();
        for ($i = 0; $i < $Max; $i++)
        {


            $row = Database::fetch_array($Res, $i);

            if ($i % 2 == 0)
                $tr = '<TR class="odd">';
            else
                $tr = '<TR class="even">';
            $r.=$tr;
            $checkbox=new ICheckBox('sel_sale[]');
            $checkbox->value=$row['jr_id'];
            $r.=td($checkbox->input());
            //internal code
            // button  modify
            $r.="<TD>";
            // If url contains
            //

            $href = basename($_SERVER['PHP_SELF']);


            $r.=sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:modifyOperation(\'%s\',\'%s\')" >%s </A>', $row['jr_id'], $gDossier, $row['jr_internal']);
            $r.="</TD>";
            if ($this->type == 'ALL')
                $r.=td($row['jrn_def_name']);
            // date
            $r.="<TD>";
            $r.=$row['str_jr_date'];
            $r.="</TD>";
            // echeance
            $r.="<TD>";
            $r.=$row['str_jr_ech'];
            $r.="</TD>";
            $r.="<TD>";
            $r.=$row['str_jr_date_paid'];
            $r.="</TD>";

            // pj
            $r.="<TD>";
            $r.=$row['jr_pj_number'];
            $r.="</TD>";

            // Tiers
            $other = ($row['quick_code'] != '') ? '[' . $row['quick_code'] . '] ' . $row['name'] . ' ' . $row['first_name'] : '';
            $r.=td($other);
            // comment
            $r.="<TD>";
            $tmp_jr_comment = h($row['jr_comment']);
            $r.=$tmp_jr_comment;
            $r.="</TD>";
            
            // Amount
            // If the ledger is financial :
            // the credit must be negative and written in red
            $positive = 0;

            // Check ledger type :
            if ($row['jrn_def_type'] == 'FIN')
            {
                $positive = $this->db->get_value("select qf_amount from quant_fin where jr_id=$1", array($row['jr_id']));
                if ($this->db->count() != 0)
                    $positive = ($positive < 0) ? 1 : 0;
            }
            $r.="<TD align=\"right\">";
            $t_amount = $row['jr_montant'];
            if ($row['total_invoice'] != null && $row['total_invoice'] != $row['jr_montant'])
                $t_amount = $row['total_invoice'];
            $tot = ($positive != 0) ? bcsub($tot, $t_amount) : bcadd($tot, $t_amount);
            //STAN $positive always == 0
            if ($row ['jrn_def_type'] == 'FIN')
            {
                $r.=( $positive != 0 ) ? "<font color=\"red\">  - " . nbm($t_amount) . "</font>" : nbm($t_amount);
            } else
            {
                $r.=( $t_amount < 0 ) ? "<font color=\"red\">  " . nbm($t_amount) . "</font>" : nbm($t_amount);
            }
            $r.="</TD>";


            // Show the paid column if p_paid is not null
            if ($p_paid != 0)
            {
                $w = new ICheckBox();
                $w->name = "rd_paid" . $row['jr_id'];
                $w->selected = ($row['jr_rapt'] == 'paid') ? true : false;
                // if p_paid == 2 then readonly
                $w->readonly = ( $p_paid == 2) ? true : false;
                $h = new IHidden();
                $h->name = "set_jr_id" . $row['jr_id'];
                $r.='<TD>' . $w->input() . $h->input() . '</TD>';
                if ($row['jr_rapt'] == 'paid')
                    $amount_paid = bcadd($amount_paid, $t_amount);
                else
                    $amount_unpaid = bcadd($amount_unpaid, $t_amount);
            }
            //document
            if ($row['jr_pj_name'] != "")
            {
                $r.='<td>'.HtmlInput::show_receipt_document($row['jr_id']).'</td>';
            } else
            {
                $r.="<TD></TD>";
            }
            
            // NOTE
            $r.=td(h($row['n_text']), ' style="font-size:0.87em%"');
            
            
            // Rapprochement
            $rec = new Acc_Reconciliation($this->db);
            $rec->set_jr_id($row['jr_id']);
            $a = $rec->get();
            $r.="<TD>";
            if ($a != null)
            {

                foreach ($a as $key => $element)
                {
                    $operation = new Acc_Operation($this->db);
                    $operation->jr_id = $element;
                    $l_amount = $this->db->get_value("select jr_montant from jrn " .
                            " where jr_id=$element");
                    $r.= "<A class=\"detail\" HREF=\"javascript:modifyOperation('" . $element . "'," . $gDossier . ")\" > " . $operation->get_internal() . "[" . nbm($l_amount) . "]</A>";
                }//for
            }// if ( $a != null ) {
            $r.="</TD>";

            if ($row['jr_valid'] == 'f')
            {
                $r.="<TD> Op&eacute;ration annul&eacute;e</TD>";
            } else
            {
                
            } // else
            
            

            // end row
            $r.="</tr>";
        }
        $amount_paid = round($amount_paid, 4);
        $amount_unpaid = round($amount_unpaid, 4);
        $tot = round($tot, 4);
        $r.="<TR>";
        $r.="<tfoot>";
        $r.='<TD >Total</TD>';
        $r.=td("").td("").td("").td("").td("").td("").td("");
        $r.='<TD ALIGN="RIGHT">' . nbm($tot) . "</TD>";
        $r.="<tfoot>";
        $r.="</tr>";
        if ($p_paid != 0)
        {
            $r.="<TR>";
            $r.="<tfoot>";
            $r.='<TD COLSPAN="5">Pay&eacute;</TD>';
            $r.='<TD ALIGN="RIGHT">' . nbm($amount_paid) . "</TD>";
            $r.="</tr>";
            $r.="<TR>";
            $r.='<TD COLSPAN="5">Non pay&eacute;</TD>';
            $r.='<TD ALIGN="RIGHT">' . nbm($amount_unpaid) . "</TD>";
            $r.="<tfoot>";
            $r.="</tr>";
        }
        $r.="</table>";

        return array($count, $r);
    }
    /**
     * @brief
     */
    function convert_to_array(Acc_Sold $sold)
    {
        $item=count($sold->det->array);
        if ( $item ==0)
        {
            throw new Exception('No Data in Quant_Purchase');
        }
        $array=array();
        
        $array['e_ech']=$sold->det->jrn_ech;
        $array['e_comm']=$sold->det->jr_comment;
        $array['p_jrn']=$sold->det->jr_def_id;
        $array['nb_item']=$item;
        $array['ledger_type']=$sold->signature;
        $array['e_date']=  format_date($sold->det->jr_date);
        $array['e_pj']=  $sold->det->jr_pj_number;
        
        $this->pj=$array['e_pj'];
        $array['internal']=$sold->det->jr_internal;
                
        $client=new Fiche($sold->db,$sold->det->array[0]['qs_client']);
        $array['e_client']=$client->get_quick_code();
        bcscale(2);
        for ($i=0;$i < $item ; $i++)
        {
            $idx='e_march';
            $serv=new Fiche($sold->db,$sold->det->array[$i]['qs_fiche']);
            $array[$idx.$i]=$serv->get_quick_code();
            $array[$idx.$i.'_label']=$sold->det->array[$i]['j_text'];
            $array[$idx.$i.'_price']=bcdiv($sold->det->array[$i]['qs_price'],$sold->det->array[$i]['qs_quantite']);
            $array[$idx.$i.'_tva_id']=$sold->det->array[$i]['qs_vat_code'];
            $array[$idx.$i.'_tva_amount']=bcsub($sold->det->array[$i]['qs_vat'],$sold->det->array[$i]['qs_vat_sided']);
            $array['e_quant'.$i]=$sold->det->array[$i]['qs_quantite'];
        }
        return $array;
       
        
    }
    /**
     * 
     * @param type $p_array
     * @param type $p_doc
     * @return type
     */
    function create_document($p_array,$p_doc)
    {
      $internal=$p_array['internal'];  
      $p_array['gen_doc']=$p_doc;
      return parent::create_document($internal,$p_array);
    }
    static function test_me($p_string = '')
    {
         $_SESSION['g_user']='phpcompta';
        $_SESSION['g_pass']='phpcompta';
        global $g_user;
        $cn=Dossier::connect();
        $g_user=new User($cn);
        $a=new Acc_Operation($cn);
        $a->jr_id=2889;
        $b=$a->get_quant();
        var_dump($b);
        $c=new Acc_Ledger_Sold_Generate($cn,$b->det->jr_def_id);
        $array=$c->convert_to_array($b);
        var_dump($array);
        echo $c->create_document($array, 8);
    }
}
