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

require_once NOALYSS_INCLUDE."/lib/class_noalyss_csv.php";
/**
 * @file
 * @brief Export
 */
class Impacc_Export_CSV
{
    var $ledger;     //!< Ledger id (jrn_def.jrn_def_id
    var $date_start; //!< date start
    var $date_end;   //!< date_end
    function __construct()
    {
        global $g_user;
        $cn=Dossier::connect();
        $this->ledger=0;
        $exercice=$g_user->get_exercice();
        $per=new Periode($cn);
        $lim=$per->get_limit($exercice);
        $this->date_start=$lim[0]->first_day();
        $this->date_end=$lim[1]->last_day();
        
    }
    //--------------------------------------------------------------------
    /// Show a form to input the parameter , ledger , stard and end date 
    //--------------------------------------------------------------------
    function input_param()
    {
        $cn=Dossier::connect();
        $l=new Acc_Ledger($cn,$this->ledger);
        $select_ledger=$l->select_ledger();
        
        $date_start=new IDate("date_start", $this->date_start);
        $date_end=new IDate("date_end", $this->date_end);
                
        require DIR_IMPORT_ACCOUNT."/template/export_param.php";
    }
    
    //--------------------------------------------------------------------
    //--------------------------------------------------------------------
    function get_param()
    {
        $this->ledger=HtmlInput::default_value_request("p_jrn", 0);
        $this->date_start=HtmlInput::default_value_request("date_start", 0);
        $this->date_end=HtmlInput::default_value_request("date_end", 0);
    }
    //--------------------------------------------------------------------
    //--------------------------------------------------------------------
    function export_csv()
    {
        $cn=Dossier::connect();
        $ledger=new Acc_Ledger($cn,$this->ledger);
        $cvs=new Noalyss_Csv("ledger".$ledger->get_name());
        $type=$ledger->get_type();
        if ( $type == 'ACH' ) {
            $cvs->send_header();
            $this->export_purchase($cvs);
        } else
        if ( $type == 'VEN' ) {
            $cvs->send_header();
            $this->export_sale($cvs);
        } else
        if ($type=="ODS")
        {
            $cvs->send_header();
            $this->export_misc($cvs);
        } else
        if ( $type=="FIN") {
            $cvs->send_header();
            $this->export_fin($cvs);
        } else {
            throw new Exception (_("Journal invalide"));
        }
    }
    //--------------------------------------------------------------------
    /// Export a ledger of Sale 
    //--------------------------------------------------------------------
    function export_sale(Noalyss_Csv $p_csv)
    {
        $cn=Dossier::connect();
        $sql="
        select 
          to_char(jr_date,'DD.MM.YYYY') as sdate,
          jr_id,
          (select ad_value from fiche_detail where f_id=qs_client and ad_id=23) as qcode,
          jr_pj,
          jr_comment,
          (select ad_value from fiche_detail where f_id=qs_fiche and ad_id=23) as qcode_serv,
          qs_unit,
          qs_price,
          qs_vat_code,
          qs_price+qs_vat as price_tax,
          to_char(jr_ech,'DD.MM.YYYY') slimit,
          to_char(jr_date_paid,'DD.MM.YYYY') sdatepaid
      from jrn
      join jrnx on (j_grpt=jr_grpt_id)
      join public.quant_sold using (j_id)
      where
          jr_date <= to_date($1,'DD.MM.YYYY') and
          jr_date >= to_date($2,'DD.MM.YYYY') and
          jr_def_id=$3
      order by jr_date,j_id
            ";
        $ret=$cn->exec_sql($sql,array($this->date_end,$this->date_start,$this->ledger));
        $nb=Database::num_row($ret);
        for ($i=0;$i<$nb;$i++)
        {
            $row=Database::fetch_array($ret, $i);
            $p_csv->add($row["sdate"]);
            $p_csv->add($row["jr_id"]);
            $p_csv->add($row["qcode"]);
            $p_csv->add($row["jr_pj"]);
            $p_csv->add($row["jr_comment"]);
            $p_csv->add($row["qcode_serv"]);
            $p_csv->add($row["qs_unit"],"number");
            $p_csv->add($row["qs_price"],"number");
            $p_csv->add($row["qs_vat_code"]);
            $p_csv->add($row["price_tax"],"number");
            $p_csv->add($row["slimit"]);
            $p_csv->add($row["sdatepaid"]);
            $p_csv->write();
        }
        
    }
    //--------------------------------------------------------------------
    /// Export a ledger of Purchase 
    //--------------------------------------------------------------------
    function export_purchase(Noalyss_Csv $p_csv)
    {
         $cn=Dossier::connect();

        $sql="
        select 
          to_char(jr_date,'DD.MM.YYYY') as sdate,
          jr_id,
         (select ad_value from fiche_detail where f_id=qp_supplier and ad_id=23) as qcode,
          jr_pj,
          jr_comment,
          (select ad_value from fiche_detail where f_id=qp_fiche and ad_id=23) as qcode_serv,
          qp_unit,
          qp_price,
          qp_vat_code,
          qp_price+qp_vat  as price_tax,
          to_char(jr_ech,'DD.MM.YYYY') slimit,
          to_char(jr_date_paid,'DD.MM.YYYY') sdatepaid
        from jrn
            join jrnx on (j_grpt=jr_grpt_id)
            join public.quant_purchase using (j_id)
        where
          jr_date <= to_date($1,'DD.MM.YYYY') and
          jr_date >= to_date($2,'DD.MM.YYYY') and
          jr_def_id=$3
        order by jr_date,j_id
            ";
         $ret=$cn->exec_sql($sql,array($this->date_end,$this->date_start,$this->ledger));
        $nb=Database::num_row($ret);
        for ($i=0;$i<$nb;$i++)
        {
            $row=Database::fetch_array($ret, $i);
            $p_csv->add($row["sdate"]);
            $p_csv->add($row["jr_id"]);
            $p_csv->add($row["qcode"]);
            $p_csv->add($row["jr_pj"]);
            $p_csv->add($row["jr_comment"]);
            $p_csv->add($row["qcode_serv"]);
            $p_csv->add($row["qp_unit"],"number");
            $p_csv->add($row["qp_price"],"number");
            $p_csv->add($row["qp_vat_code"]);
            $p_csv->add($row["price_tax"],"number");
            $p_csv->add($row["slimit"]);
            $p_csv->add($row["sdatepaid"]);
            $p_csv->write();
        }
    }
    //--------------------------------------------------------------------
    /// Export a financial ledger
    //--------------------------------------------------------------------
    function export_fin(Noalyss_Csv $p_csv)
    {
        $cn=Dossier::connect();
        $sql= "
        select 
           to_char(jr_date,'DD.MM.YYYY') as sdate,
          (select ad_value from fiche_detail where f_id=qf_other and ad_id=23),
           jr_pj_number,
           jr_comment,
           qf_amount
        from 
           quant_fin 
           join jrn using (jr_id)
        where 
          jr_date <= to_date($1,'DD.MM.YYYY') and
          jr_date >= to_date($2,'DD.MM.YYYY') and
          jr_def_id=$3
";            

    }
    //--------------------------------------------------------------------
    /// Export ODS ledger
    //--------------------------------------------------------------------
    function export_misc(Noalyss_Csv $p_csv)
    {
        $cn=Dossier::connect();
        $sql= "
            select 
                to_char(jr_date,'DD.MM.YYYY') as sdate,
                jr_id,
                coalesce(j_qcode,j_poste),
                jr_pj_number,
                jr_comment,
                j_montant,
                case when j_debit = false then 'C'
                else  'D'
                end
            from 
                jrn 
                join jrnx on (jr_grpt_id=j_grpt)
            where
                jr_date <= to_date($1,'DD.MM.YYYY') and
                jr_date >= to_date($2,'DD.MM.YYYY') and
                jr_def_id=$3
";
    }
}
?>
