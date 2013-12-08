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

class RAPAV_Listing_Compute
{

    /**
     * < Data point to listing_compute
     */
    var $data;

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
            $this->data->l_start = $p_date_start;
            $this->data->l_end = $p_date_end;
            $this->data->l_keep = 'N';
            $this->data->l_id = $rapav_listing->Data->l_id;
            $this->data->insert();

            // retrieve all the code from $rapav_listing
            $rapav_listing->load_detail();
            $a_code = $rapav_listing->a_detail;

            // for each code, compute the value must end with the rapav_listing_compute objects
            $nb = count($a_code);

            // ------------------------------------------------------
            // For each card
            $fiche_def = new Fiche_Def($cn);
            $fiche_def->id = $rapav_listing->Data->getp('fiche_def_id');
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

    function display()
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
                echo '<table style="min-width:100%" class="result">';
                echo '<tr>';
                for ($e=0;$e<$nb_detail;$e++)
                {
                    $detail=$odetail->next($r_detail,$e);
                    echo th( $detail->lc_code);
                }
                echo '</tr>';
            }
            /** for each detail */
            echo '<tr>';
            for ($e=0;$e <$nb_detail;$e++)
            {
                $detail=$odetail->next($r_detail,$e);
                
                echo (($detail->ld_value_numeric !== null)?td(nbm($detail->ld_value_numeric),'class="num"'):"");
                echo (($detail->ld_value_text !== null)?td($detail->ld_value_text):"");
                echo (($detail->ld_value_date!== null)?td($detail->ld_value_date):"");
            }
            echo '</tr>';
        }
        echo '</table>';
    }
    function keep($p_id)
    {
        $this->data->lc_id=$p_id;
        $this->data->load();
        $this->data->l_keep='Y';
        $this->data->save();
    }

}
