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

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief handle the data: database level
 *
 */
require_once 'class_noalyss_sql.php';

class formulaire_param_sql extends Noalyss_SQL
{

    function __construct($p_id = -1)
    {


        $this->table = "rapport_advanced.formulaire_param";
        $this->primary_key = "p_id";

        $this->name = array(
            "p_id" => "p_id",
            "p_code" => "p_code",
            "p_libelle" => "p_libelle",
            "p_type" => "p_type",
            "p_order" => "p_order",
            "f_id" => "f_id",
            "t_id" => "t_id"
        );

        $this->type = array(
            "p_id" => "numeric",
            "p_code" => "text",
            "p_libelle" => "text",
            "p_type" => "numeric",
            "p_order" => "numeric",
            "f_id" => "numeric",
            "t_id" => "numeric"
        );

        $this->default = array(
            "p_id" => "auto"
        );
        global $cn;

        parent::__construct($cn, $p_id);
    }

}

class formulaire_sql extends Noalyss_SQL
{

    function __construct($p_id = -1)
    {


        $this->table = "rapport_advanced.formulaire";
        $this->primary_key = "f_id";

        $this->name = array(
            "f_id" => "f_id",
            "f_title" => "f_title",
            "f_description" => "f_description",
            "f_lob" => "f_lob",
            "f_mimetype" => "f_mimetype",
            "f_filename" => "f_filename",
            "f_size" => "f_size"
        );

        $this->type = array(
            "f_id" => "numeric",
            "f_title" => "text",
            "f_description" => "text",
            "f_lob" => "oid",
            "f_mimetype" => "text",
            "f_filename" => "text",
            "f_size" => "numeric"
        );

        $this->default = array(
            "f_id" => "auto"
        );
        global $cn;

        parent::__construct($cn, $p_id);
    }

}

class Formulaire_Param_Detail_SQL extends Noalyss_SQL
{

    function __construct($p_id = -1)
    {


        $this->table = "rapport_advanced.formulaire_param_detail";
        $this->primary_key = "fp_id";

        $this->name = array(
            "fp_id" => "fp_id",
            "p_id" => "p_id",
            "tmp_val" => "tmp_val",
            "tva_id" => "tva_id",
            "fp_formula" => "fp_formula",
            "fp_signed" => "fp_signed",
            "jrn_def_type" => "jrn_def_type",
            "tt_id" => "tt_id",
            "type_detail" => "type_detail",
            "with_tmp_val" => "with_tmp_val",
            "type_sum_account" => "type_sum_account",
            "operation_pcm_val" => "operation_pcm_val",
            "jrn_def_id" => "jrn_def_id",
            "date_paid" => "date_paid"
        );

        $this->type = array(
            "fp_id" => "numeric",
            "p_id" => "numeric",
            "tmp_val" => "text",
            "tva_id" => "numeric",
            "fp_formula" => "text",
            "fp_signed" => "numeric",
            "jrn_def_type" => "text",
            "tt_id" => "numeric",
            "type_detail" => "numeric",
            "with_tmp_val" => "text",
            "type_sum_account" => "numeric",
            "operation_pcm_val" => "text",
            "jrn_def_id" => "numeric",
            "date_paid" => "numeric"
        );

        $this->default = array(
            "fp_id" => "auto"
        );
        global $cn;

        parent::__construct($cn, $p_id);
    }

}

class RAPAV_Declaration_SQL extends Noalyss_SQL
{

    function __construct($p_id = -1)
    {


        $this->table = "rapport_advanced.declaration";
        $this->primary_key = "d_id";

        $this->name = array(
            "d_id" => "d_id",
            "d_title" => "d_title",
            "d_description" => 'd_description',
            "d_start" => "d_start",
            "d_end" => "d_end",
            "to_keep" => "to_keep",
            "d_generated" => "d_generated",
            "d_lob" => "d_lob",
            "d_filename" => "d_filename",
            "d_mimetype" => "d_mimetype",
            "d_size" => "d_size",
            "f_id" => "f_id",
            'd_step' => 'd_step'
        );

        $this->type = array(
            "d_id" => "numeric",
            "d_title" => "text",
            "d_description" => 'text',
            "d_start" => "date",
            "d_end" => "date",
            "to_keep" => "text",
            "d_generated" => "date",
            "f_id" => "numeric",
            "d_lob" => "oid",
            "d_filename" => "text",
            "d_mimetype" => "text",
            "d_size" => "numeric",
            'd_step' => 'numeric'
        );

        $this->default = array(
            "d_id" => "auto",
            "d_generated" => "auto"
        );
        global $cn;

        $this->date_format = "DD.MM.YYYY";
        parent::__construct($cn, $p_id);
    }

}

class RAPAV_Declaration_Row_SQL extends Noalyss_SQL
{

    function __construct($p_id = -1)
    {


        $this->table = "rapport_advanced.declaration_row";
        $this->primary_key = "dr_id";

        $this->name = array(
            "dr_id" => "dr_id",
            "d_id" => "d_id",
            "dr_libelle" => "dr_libelle",
            "dr_order" => "dr_order",
            "dr_code" => "dr_code",
            "dr_amount" => "dr_amount",
            "dr_type" => "dr_type",
            "dr_start" => "dr_start",
            "dr_end" => "dr_end"
        );

        $this->type = array(
            "dr_id" => "numeric",
            "d_id" => "numeric",
            "dr_libelle" => "text",
            "dr_order" => "text",
            "dr_code" => "numeric",
            "dr_amount" => "numeric",
            "dr_type" => "numeric",
            "dr_start" => "date",
            "dr_end" => "date"
        );

        $this->default = array(
        );
        global $cn;
        $this->date_format = 'DD.MM.YYYY';
        parent::__construct($cn, $p_id);
    }

}

class RAPAV_Declaration_Row_Detail_SQL extends Noalyss_SQL
{

    function __construct($p_id = -1)
    {


        $this->table = "rapport_advanced.declaration_row_detail";
        $this->primary_key = "ddr_id";

        $this->name = array(
            "ddr_id" => "ddr_id",
            "ddr_amount" => "ddr_amount",
            "dr_id" => "dr_id"
        );

        $this->type = array(
            "ddr_id" => "numeric",
            "ddr_amount" => "numeric",
            "dr_id" => "numeric"
        );

        $this->default = array(
            "ddr_id" => "auto"
        );
        global $cn;

        parent::__construct($cn, $p_id);
    }

}

class RAPAV_Listing_SQL extends Noalyss_SQL
{
    var $l_id;
    var $l_description;
    var $l_name;
    var $l_lob;
    var $l_filename;
    var $l_mimetype;
    var $l_size;
    var $fd_id;
    
    function __construct($p_id = -1)
    {
        $this->table = "rapport_advanced.listing";
        $this->primary_key = "l_id";
        $this->name = array(
            "id" => "l_id",
            "description" => "l_description",
            "name" => 'l_name',
            "lob" => "l_lob",
            "filename" => "l_filename",
            "mimetype" => "l_mimetype",
            "size" => "l_size",
            "fiche_def_id" => "fd_id"
        );
        $this->type = array(
            "l_id" => "numeric",
            "l_name" => 'text',
            "l_description" => "text",
            "l_lob" => "oid",
            "l_filename" => "text",
            "l_mimetype" => "text",
            "l_size" => "numeric",
            "fd_id" => "numeric"
        );
        $this->default = array(
            "l_id" => "auto"
        );
        global $cn;

        parent::__construct($cn, $p_id);
    }

}

class RAPAV_Listing_Param_SQL extends Noalyss_SQL
{
    /*
     *  operation_pcm_val,with_tmp_val,tmp_val,date_paid,jrn_def_id,type_sum_account,type_detail,tt_id,jrn_def_type,fp_signed,fp_formula,tva_id,lp_with_card,lp_card_saldo,ad_id,l_order,l_card,lp_comment,lp_code,l_id,lp_id
     */

    function __construct($p_id = -1)
    {


        $this->table = "rapport_advanced.listing_param";
        $this->primary_key = "lp_id";

        $this->name = array(
            "lp_id" => "lp_id",
            "listing_id" => "l_id",
            "code" => "lp_code",
            "comment" => "lp_comment",
            "order" => "l_order",
            "tmp_val" => "tmp_val",
            "tva_id" => "tva_id",
            "formula" => "fp_formula",
            "signed" => "fp_signed",
            "jrn_def_type" => "jrn_def_type",
            "acc_vat_sum" => "tt_id",
            "formula_type" => "type_detail",
            "with_tmp_val" => "with_tmp_val",
            "sum_signed" => "type_sum_account",
            "operation_pcm_val" => "operation_pcm_val",
            "jrn_def_id" => "jrn_def_id",
            "date_paid" => "date_paid",
            "attribut_card" => "ad_id",
            "card_saldo" => "lp_card_saldo",
            "with_card" => "lp_with_card",
            "lp_histo"=>"lp_histo"
        );

        $this->type = array(
            "lp_id" => "numeric",
            "l_id" => "numeric",
            "lp_code" => "text",
            "lp_comment" => "text",
            "l_order" => "numeric",
            "tmp_val" => "text",
            "tva_id" => "numeric",
            "fp_formula" => "text",
            "fp_signed" => "numeric",
            "jrn_def_type" => "text",
            "tt_id" => "numeric",
            "type_detail" => "numeric",
            "with_tmp_val" => "text",
            "type_sum_account" => "numeric",
            "operation_pcm_val" => "text",
            "jrn_def_id" => "numeric",
            "date_paid" => "numeric",
            "ad_id" => "numeric",
            "lp_card_saldo" => "text",
            "lp_with_card" => "text",
            "lp_histo" => "numeric",
        );

        $this->default = array(
            "lp_id" => "auto"
        );
        global $cn;

        parent::__construct($cn, $p_id);
    }

}

/**
 * @brief Manage the table rapport_advanced.listing_compute
 */
class RAPAV_Listing_Compute_SQL extends Noalyss_SQL
{

    //------ Attributes-----
    var $lc_id;
    var $l_id;
    var $l_description;
    var $l_start;
    var $l_end;
    var $l_keep;
    var $l_timestamp;
    var $l_name;

    /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

    function __construct($p_id = -1)
    {
        $this->table = "rapport_advanced.listing_compute";
        $this->primary_key = "lc_id";

        $this->name = array(
            "lc_id" => "lc_id"
            , "l_id" => "l_id"
            , "description" => "l_description"
            , "l_start" => "l_start"
            , "l_end" => "l_end"
            , "l_keep" => "l_keep"
            , "l_timestamp" => "l_timestamp"
            ,"l_name"=>"l_name"
        );

        $this->type = array(
            "lc_id" => "lc_id",
            "l_id" => "numeric"
            , "l_description" => "text"
            , "l_start" => "date"
            , "l_end" => "date"
            , "l_keep" => "text"
            , "l_timestamp" => "date"
            , "l_name" => "text"
        );

        $this->default = array(
            "lc_id" => "auto"
            , "l_timestamp" => "auto"
        );
        global $cn;
        $this->date_format = "DD.MM.YYYY";
        parent::__construct($cn, $p_id);
    }

    /**
     * @brief Add here your own code: verify is always call BEFORE insert or update
     */
    public function verify()
    {
        parent::verify();
    }

}

/**
 * @brief Manage the table rapport_advanced.listing_compute_fiche
 */
class RAPAV_Listing_Compute_Fiche_SQL extends Noalyss_SQL
{

    //------ Attributes-----
    var $lf_id;
    var $f_id;
    var $lf_lob;
    var $lf_filename;
    var $lf_mimetype;
    var $lc_id;
    var $lf_pdf;
    var $lf_pdf_filename;
    var $lf_action_included;
    var $lf_email_send_date;
    var $lf_email_send_result;
    

    /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

    function __construct($p_id = -1)
    {


        $this->table = "rapport_advanced.listing_compute_fiche";
        $this->primary_key = "lf_id";

        $this->name = array(
            "lf_id" => "lf_id", 
            "f_id" => "f_id",
            "lc_id" => "lc_id"
            , "lf_lob" => "lf_lob"
            , "lf_filename" => "lf_filename"
            , "lf_mimetype" => "lf_mimetype"
            , "lf_pdf" => "lf_pdf"
            , "lf_pdf_filename" => "lf_pdf_filename"
            ,'action_included'=>'lf_action_included'
            , 'email_send_date'=>'lf_email_send_date'
            , 'email_send_result'=>'lf_email_send_result'
        );

        $this->type = array(
            "lf_id" => "numeric",
            "lc_id" => "numeric",
            "f_id" => "numeric"
            , "lf_lob" => "oid"
            , "lf_filename" => "text"
            , "lf_mimetype" => "text"
            , "lf_pdf" => "oid"
            , "lf_pdf_filename" => "text"
            ,'lf_action_included'=>'text'
            , 'lf_email_send_date'=>'text'
            , 'lf_email_send_result'=>'text'
        );

        $this->default = array(
            "lf_id" => "auto"
        );
        global $cn;
        $this->date_format = "DD.MM.YYYY";
        parent::__construct($cn, $p_id);
    }

    /**
     * @brief Add here your own code: verify is always call BEFORE insert or update
     */
    public function verify()
    {
        parent::verify();
    }

}

/**
 * @file
 * @brief Manage the table rapport_advanced.listing_compute_detail
 *
 *
  Example
  @code

  @endcode
 */
require_once('class_noalyss_sql.php');

/**
 * @brief Manage the table rapport_advanced.listing_compute_detail
 */
class RAPAV_Listing_Compute_Detail_SQL extends Noalyss_SQL
{

    //------ Attributes-----
    var $lc_id;
    var $ld_value_date;
    var $ld_value_numeric;
    var $ld_value_text;
    var $lp_id;
    var $lf_id;
    var $lc_code;
    var $lc_comment;
    var $lc_order;
    var $lc_histo;

    /* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

    function __construct($p_id = -1)
    {


        $this->table = "rapport_advanced.listing_compute_detail";
        $this->primary_key = "ld_id";

        $this->name = array(
            "ld_id" => "ld_id", "lc_id" => "lc_id"
            , "ld_value_date" => "ld_value_date"
            , "ld_value_numeric" => "ld_value_numeric"
            , "ld_value_text" => "ld_value_text"
            , "lp_id" => "lp_id"
            , "lf_id" => "lf_id"
            ,'code'=>'lc_code'
            ,'comment'=>'lc_comment'
            ,'order'=>'lc_order'
            ,'history'=>'lc_histo'
        );

        $this->type = array(
            "ld_id" => "numeric",
            "lc_id" => "numeric"
            , "ld_value_date" => "date"
            , "ld_value_numeric" => "numeric"
            , "ld_value_text" => "text"
            , "lp_id" => "numeric"
            , "lf_id" => "numeric"
            , 'lc_code'=>"text"
            , 'lc_comment'=>"text"
            , 'lc_order'=>"numeric"
            , 'lc_histo'=>"numeric"
        );

        $this->default = array(
            "ld_id" => "auto"
        );
        global $cn;
        $this->date_format = "DD.MM.YYYY";
        parent::__construct($cn, $p_id);
    }

    /**
     * @brief Add here your own code: verify is always call BEFORE insert or update
     */
    public function verify()
    {
        parent::verify();
    }

}
/**
 * @brief Manage the table rapport_advanced.listing_compute_historique
 */
class RAPAV_Listing_Compute_Historique_SQL extends Noalyss_SQL
{

    //------ Attributes-----
    var $lh_id;
    var $ld_id;
    var $jr_id;


    function __construct($p_id = -1)
    {


        $this->table = "rapport_advanced.listing_compute_historique";
        $this->primary_key = "lh_id";

        $this->name = array(
            "lh_id" => "lh_id", "lh_id" => "lh_id"
            , "ld_id" => "ld_id"
            , "jr_id" => "jr_id"
        );

        $this->type = array(
            "lh_id" => "numeric"
            , "ld_id" => "numeric"
            , "jr_id" => "numeric"
        );

        $this->default = array(
            "lh_id" => "auto"
        );
        global $cn;
        $this->date_format = "DD.MM.YYYY";
        parent::__construct($cn, $p_id);
    }

    /**
     * @brief Add here your own code: verify is always call BEFORE insert or update
     */
    public function verify()
    {
        parent::verify();
    }

}

?>
