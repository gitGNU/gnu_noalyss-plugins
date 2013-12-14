<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_rapav_listing_compute
 *
 * @author dany
 */
require_once 'class_rapav_listing.php';
require_once 'class_rapav_listing_formula.php';
require_once 'class_rapport_avance_sql.php';
require_once 'class_rapav_listing_compute_fiche.php';
class RAPAV_Listing_Compute
{

    /**
     * < Data point to listing_compute
     */
    var $data;
    var $listing;

    /**
     * Type of operation
     *    - 0 all operations
     *    - 1 only paid operations
     *    - 2 only UNpaid operations
     */
    private $type_operation;

    function __construct()
    {
        $this->data = new RAPAV_Listing_Compute_SQL();
        $this->listing=new RAPAV_Listing();
    }
    function load($p_id)
    {
        $this->data->lc_id=$p_id;
        $this->data->load();
        $this->load_listing($this->data->l_id);
    }
    private function load_listing($p_id)
    {
        $this->listing->data->l_id=$p_id;
        $this->listing->data->load();
    }
    /**
     * Compute all the values and save them in the table rapav_listing_compute
     * @param RAPAV_Listing $rapav_listing
     * @param type $p_date_start
     * @param type $p_date_end
     */
    function compute(RAPAV_Listing &$rapav_listing, $p_date_start, $p_date_end)
    {
        global $cn;
        try
        {
            // save an object Listing_Compute with the flag to_keep to N
            $this->data->l_name=$rapav_listing->data->l_name;
            $this->data->l_start = $p_date_start;
            $this->data->l_end = $p_date_end;
            $this->data->l_keep = 'N';
            $this->data->l_id = $rapav_listing->data->l_id;
            $this->data->insert();
            $this->listing=clone $rapav_listing;

            // retrieve all the code from $rapav_listing
            $rapav_listing->load_detail();
            $a_code = $rapav_listing->a_detail;

            // for each code, compute the value must end with the rapav_listing_compute objects
            $nb = count($a_code);

            // ------------------------------------------------------
            // For each card
            $fiche_def = new Fiche_Def($cn);
            $fiche_def->id = $rapav_listing->data->getp('fiche_def_id');
            $a_fiche = $fiche_def->get_by_type();
            $nb_fiche = count($a_fiche);
            for ($e = 0; $e < $nb_fiche; $e++)
            {
                /*
                 * save a listing_compute_fiche
                 */
                $fiche = new RAPAV_Listing_Compute_Fiche_SQL();
                $fiche->f_id = $a_fiche[$e]['f_id'];
                $this->lc_id = $this->data->lc_id;
                $fiche->insert();

                $a_later = array();
                for ($i = 0; $i < $nb; $i++)
                {
                    //Compute if an object either Rapav_Formula_Account, Rapav_Formula_Attribute,
                    // Rapav_Formula_Compute or Rapav_Formula_Formula,
                    unset($compute);
                    $compute = RAPAV_Listing_Formula::make_object($a_code[$i]->Param);
                    $compute->fiche = $fiche;

                    if ($compute->sig == 'COMP')
                    {
                        $a_later[] = clone $compute;
                    } else
                    {
                        $compute->set_listing_compute($this->data->lc_id);
                        $compute->filter_operation($this->type_operation);


                        //compute
                        $compute->compute($p_date_start, $p_date_end);
                        // save computed
                        $compute->save_computed();
                    }
                }
                $nb_later = count($a_later);

                /**
                 * for Listing_Formula_compute
                 */
                for ($i = 0; $i < $nb_later; $i++)
                {
                    $compute->set_listing_compute($this->data->lc_id);
                    $a_later[$i]->compute($p_date_start, $p_date_end);
                    $a_later[$i]->save_computed();
                }
            }
        } catch (Exception $e)
        {
            $cn->rollback();
            throw $e;
        }
        $cn->commit();
    }

    /**
     * Filter the operations
     *    - 0 all operations
     *    - 1 only paid operations - only VEN & ACH
     *    - 2 only UNpaid operations - only VEN & ACH
     * @param $p_type
     */
    function filter_operation($p_type)
    {
        $this->type_operation = $p_type;
    }
    /**
     * Display the result of the computing, no card are deleted for the moment
     * @param $with_sel true display the checkbox, false don't
     * @param $form_name Name of the form (to compute it id)
     * @global type $cn
     */
    function display($with_sel,$form_name="")
    {
        global $cn;
        $ofiche=new RAPAV_Listing_Compute_Fiche_SQL();
        $r_fiche=$ofiche->seek (" where lc_id = $1",array($this->data->lc_id));
        $nb_fiche=Database::num_row($r_fiche);

        /* For each card */
        for ($i = 0;$i < $nb_fiche;$i++)
        {
            $fiche=$ofiche->next($r_fiche,$i);
            
            $odetail=new RAPAV_Listing_Compute_Detail_SQL();
            $r_detail=$odetail->seek(" where lf_id=$1 order by lc_order",array($fiche->lf_id));
            $nb_detail=Database::num_row($r_detail);
            // table header
            if ($nb_detail > 0 && $i == 0)
            {
                $col=array();
                for ($e=0;$e<$nb_detail;$e++)
                {
                    $col[]=$e;
                }
                $col_range=implode(",",$col);
                echo "Filtre ".HtmlInput::filter_table($form_name."_tb", $col_range, 1);
                echo '<table id="'.$form_name.'_tb" style="min-width:100%" class="result">';
                echo '<tr>';
                if ( $with_sel ) {
                    echo '<TH><INPUT TYPE="CHECKBOX" onclick="toggle_checkbox(\''.$form_name.'\')"></TH>';
                }
                for ($e=0;$e<$nb_detail;$e++)
                {
                    $detail=$odetail->next($r_detail,$e);
                    echo th( $detail->lc_code);
                }
                echo th("Fichier");
                if ( GENERATE_PDF == 'YES')
                {
                    echo th('PDF');
                }
                echo '</tr>';
            }
            /** for each detail */
            $class=($i%2==0)?' class="even" ':'class="odd" ';
            echo '<tr '.$class.'>';
            for ($e=0;$e <$nb_detail;$e++)
            {
                $detail=$odetail->next($r_detail,$e);
                if ($e==0 && $with_sel)
                {
                     $check_box=new ICheckBox("selected_card[]", $fiche->lf_id);
                     echo td($check_box->input());
                }
                echo (($detail->ld_value_numeric !== null)?td(nbm($detail->ld_value_numeric),'class="num"'):"");
                echo (($detail->ld_value_text !== null)?td($detail->ld_value_text):"");
                echo (($detail->ld_value_date!== null)?td($detail->ld_value_date):"");
            }
            if ( $fiche->lf_filename != "")
            {
                $arg=array("gDossier"=>$_REQUEST['gDossier'],
                    "plugin_code"=>$_REQUEST['plugin_code'],
                    "ac"=>$_REQUEST['ac'],
                    'act'=>'show_file',
                    'lf_id'=>$fiche->lf_id);
                $href=  "extension.raw.php?".http_build_query($arg);
                echo td('<a href="'.$href.'">'.$fiche->lf_filename);
            }
            if ( $fiche->lf_pdf != "")
            {
                $arg=array("gDossier"=>$_REQUEST['gDossier'],
                    "plugin_code"=>$_REQUEST['plugin_code'],
                    "ac"=>$_REQUEST['ac'],
                    'act'=>'show_pdf',
                    'lf_id'=>$fiche->lf_id);
                $href=  "extension.raw.php?".http_build_query($arg);
                echo td('<a href="'.$href.'">'.$fiche->lf_pdf_filename);
            }
            echo '</tr>';
        }
        echo '</table>';
    }
    /**
     * Set the flag to keep to Y
     * @param $p_id lc_id of the RAPAV_Listing_Compute to keep
     */
    function keep($p_id)
    {
        $this->data->lc_id=$p_id;
        $this->data->load();
        $this->data->l_keep='Y';
        $this->data->save();
    }
    /**
     * Return true or false if the corresponding RAPAV_Listing has a document
     * @return true if RAPAV_Listing has a template otherwise false
     */
    function has_template()
    {
        if ($this->listing->data->l_filename != "") 
            {
                return true;
            }
            else 
            {
                return false;
            }
    }
    /**
     * Save the card selected, they are in an array with the idx selected_fiche
     * @param $p_array array 
     */
    function save_selected($p_array)
    {
        global $cn;
        if (count($p_array)==0)
                return;
        $to_keep=implode(',',$p_array);
        $to_keep=sql_string($to_keep);
        $cn->exec_sql(" delete from rapport_advanced.listing_compute_fiche
            where lf_id not in ($to_keep) and lc_id=$1",array($this->data->lc_id));
    }
    function generate($p_id)
    {
        global $cn;
        $ofiche=new RAPAV_Listing_Compute_Fiche();
        $r_fiche=$ofiche->seek (" where lc_id = $1",array($this->data->lc_id));
        $nb_fiche=Database::num_row($r_fiche);
        if (isNumber($p_id)==0) {$p_id=0;}
        /* For each card */
        for ($i = 0;$i < $nb_fiche;$i++)
        {
            $fiche=$ofiche->next($r_fiche,$i);
            $fiche->set_listing_compute($this);
            $fiche->set_number($p_id);
            $p_id++;
            $fiche->generate_document();
        }
    }
    function create_pdf()
    {
        global $cn;
        $ofiche=new RAPAV_Listing_Compute_Fiche();
        $r_fiche=$ofiche->seek (" where lc_id = $1",array($this->data->lc_id));
        $nb_fiche=Database::num_row($r_fiche);
        if (isNumber($p_id)==0) {$p_id=0;}
        /* For each card */
        for ($i = 0;$i < $nb_fiche;$i++)
        {
            $fiche=$ofiche->next($r_fiche,$i);
            $file=$fiche->create_pdf();
        }
    }
    /**
     * @brief display a form to generate CSV
     */
    function propose_CSV()
    {
        echo '<form method="GET" action="extension.raw.php" class="noprint" style="display:inline">';
        echo HtmlInput::array_to_hidden(array('ac','gDossier','plugin_code','sa'), $_REQUEST);
        echo HtmlInput::hidden('lc_id',$this->data->lc_id);
        echo HtmlInput::hidden('act','export_listing_csv');
        echo HtmlInput::submit("export_listing", "Export CSV","","smallbutton");
        echo '</form>';
    }
    /**
     * @brief display a form to generate document
     */

    function propose_generate()
    {
        echo '<form method="GET" action="do.php" class="noprint" style="display:inline">';
        $num=new IText('numerotation');
        echo "Numéro de document ".$num->input();
        echo HtmlInput::array_to_hidden(array('ac','gDossier','plugin_code','sa'), $_REQUEST);
        echo HtmlInput::hidden('lc_id',$this->data->lc_id);
        echo HtmlInput::submit("generate_document", "Génération des documents","","smallbutton");
        echo '</form>';
        
    }
    /**
     * @brief export to CSV
     */
    function to_csv()
    {
        $title = mb_strtolower($this->listing->data->l_name, 'UTF-8');
        $title = str_replace(array('/', '*', '<', '>', '*', '.', '+', ':', '?', '!', " ", ";"), "_", $title);

        $out = fopen("php://output", "w");
        header('Pragma: public');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment;filename="' . $title . '.csv"', FALSE);
        $ofiche=new RAPAV_Listing_Compute_Fiche_SQL();
        $r_fiche=$ofiche->seek (" where lc_id = $1",array($this->data->lc_id));
        $nb_fiche=Database::num_row($r_fiche);

        /* For each card */
        for ($i = 0;$i < $nb_fiche;$i++)
        {
            $fiche=$ofiche->next($r_fiche,$i);
            
            $odetail=new RAPAV_Listing_Compute_Detail_SQL();
            $r_detail=$odetail->seek(" where lf_id=$1 order by lc_order",array($fiche->lf_id));
            $nb_detail=Database::num_row($r_detail);
            // table header
            if ($nb_detail > 0 && $i == 0)
            {
                $col=array();
                for ($e=0;$e<$nb_detail;$e++)
                {
                    $detail=$odetail->next($r_detail,$e);
                    $col[]=$detail->lc_code;
                }
                fputcsv($out, $col, ";", '"');
            }
            /** for each detail */
            $det_csv=array();
            for ($e=0;$e <$nb_detail;$e++)
            {
                $detail=$odetail->next($r_detail,$e);
                $value= (($detail->ld_value_numeric !== null)?nb($detail->ld_value_numeric):"");
                $value.=(($detail->ld_value_text !== null)?$detail->ld_value_text:"");
                $value.=(($detail->ld_value_date!== null)?$detail->ld_value_date:"");
                $det_csv[]=$value;
            }
            fputcsv($out, $det_csv, ';', '"');
        }
        
    }
    function propose_send_mail()
    {
        
        echo '<form method="GET" action="extension.raw.php" class="noprint" style="display:inline">';
        echo HtmlInput::array_to_hidden(array('ac','gDossier','plugin_code','sa'), $_REQUEST);
        echo HtmlInput::hidden('lc_id',$this->data->lc_id);
        echo HtmlInput::hidden('act','export_send_mail');
        echo HtmlInput::submit("export_send_mail", "Envoi par email","","smallbutton");
        echo '</form>';
        return 0;
    }
    function propose_include_follow()
    {
        
        echo '<form method="GET" action="extension.raw.php" class="noprint" style="display:inline">';
        echo HtmlInput::array_to_hidden(array('ac','gDossier','plugin_code','sa'), $_REQUEST);
        echo HtmlInput::hidden('lc_id',$this->data->lc_id);
        echo HtmlInput::hidden('act','include_follow_up');
        echo HtmlInput::submit("include_follow_up", "Inclure dans les actions","","smallbutton");
        echo '</form>';
        return 0;
    }
    function propose_download_all()
    {
        
        echo '<form method="GET" action="extension.raw.php" class="noprint" style="display:inline">';
        echo HtmlInput::array_to_hidden(array('ac','gDossier','plugin_code','sa'), $_REQUEST);
        echo HtmlInput::hidden('lc_id',$this->data->lc_id);
        echo HtmlInput::hidden('act','export_download_all');
        echo HtmlInput::submit("export_download_all", "Télécharger tous les documents","","smallbutton");
        echo '</form>';
        return 0;
    }

}
