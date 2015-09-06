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

/**
 * @file
 * \brief generate writing of amortize
 */
require_once('class_amortissement_histo_sql.php');

class Am_Generate
{

    /**
     * Show the form to generate the writing
     * select ledger
     *        year
     *        date of operation
     *        list of material
     */
    function input($p_array)
    {
        global $cn, $g_user;
        /*
         * select ledger
         */

        $ledger=new Acc_Ledger($cn, 0);
        $sel_ledger=$ledger->select_ledger('ODS', 2);
        $sel_ledger->selected=(isset($p_array['p_jrn']))?$p_array['p_jrn']:'';
        /*
         * Operation Date
         */
        $p_date=new IDate('p_date');
        $p_date->value=(isset($p_array['p_date']))?$p_array['p_date']:date('d.m.Y');

        /*
         * select year
         */
        $year=new INum('p_year');
        $year->size=4;
        $year->value=(isset($p_array['p_year']))?$p_array['p_year']:date('Y');

        /*
         * PJ
         */
        $pj=new IText('p_pj');
        $pj->size=10;
        /*
         * If we use the periode
         */
        if ($ledger->check_periode()==true)
        {
            $l_user_per=$g_user->get_periode();
            $def=(isset($periode))?$periode:$l_user_per;
            $period=new IPeriod("period");
            $period->user=$g_user;
            $period->cn=$cn;
            $period->value=$def;
            $period->type=OPEN;
            try
            {
                $l_form_per=$period->input();
            }
            catch (Exception $e)
            {
                if ($e->getCode()==1)
                {
                    echo _("Aucune période ouverte");
                    exit();
                }
            }
            $label=HtmlInput::infobulle(3);
            $f_periode=_("Période comptable")." $label ";
        }
        /*
         * show all the visible material
         */
        require_once('template/util_generate.php');
    }

    /**
     * Propose to save the writing in the selected ledger
      @code
      array
      'plugin_code' => string 'AMORT' (length=5)
      'sa' => string 'util' (length=4)
      'gDossier' => string '48' (length=2)
      'p_year' => string '' (length=0)
      'p_jrn' => string '4' (length=1)
      'p_date' => string '' (length=0)
      'a_id' =>
      array
      0 => string '86' (length=2)
      1 => string '85' (length=2)
      'p_ck1' => string '' (length=0)

      @endcode
     */
    function propose_writing($p_array)
    {
        global $cn;
        $msg='';
        if (isNumber($p_array['p_year'])==0||$p_array['p_year']<1900||$p_array['p_year']>2100)
            $msg="Année invalide\n";
        if (isDate($p_array['p_date'])==null)
            $msg.="Date invalide ";
        if ($msg!='')
        {
            echo alert($msg);
            return false;
        }

        $array=array(
            'p_jrn'=>$p_array['p_jrn'],
            'e_date'=>$p_array['p_date'],
            'periode'=>0,
            'desc'=>'Amortissement '.$p_array['p_year'],
            'e_pj'=>$p_array['p_pj'],
            'e_pj_suggest'=>$p_array['p_pj'],
        );
        if (isset($p_array['period']))
        {
            $array['period']=$p_array['period'];
        }
        $idx=0;
        $a_material=array();
        for ($i=0; $i<count($p_array['a_id']); $i++)
        {
            if (isset($p_array['p_ck'.$i]))
            {

                /*
                 * If selected the add it to array
                 */
                $n=new Amortissement_Sql($cn, $p_array['a_id'][$i]);
                $fiche=new Fiche($cn, $n->f_id);
                $val=$cn->get_value("select ad_amount from amortissement.amortissement_detail ".
                        " where a_id = $1 and ad_year=$2", array($n->a_id, $p_array['p_year']));
                $val=($val=='')?0:$val;
                // retrieve quick code card deb
                $card_deb="";
                if ($n->card_deb!="")
                {
                    $fiche_card_deb=new Fiche($cn, $n->card_deb);
                    $card_deb=$fiche_card_deb->get_quick_code();
                }

                $card_cred="";
                if ($n->card_cred!="")
                {
                    // retrieve quick code card cred
                    $fiche_card_cred=new Fiche($cn, $n->card_cred);
                    $card_cred=$fiche_card_cred->get_quick_code();
                }
                $mat=array('poste'.$idx=>$n->account_deb,
                    'amount'.$idx=>$val,
                    'ld'.$idx=>'Dotation à '.$fiche->strAttribut(ATTR_DEF_QUICKCODE),
                    'ck'.$idx=>1,
                    'qc_'.$idx=>$card_deb);
                $a_material+=array('request_a'.$i=>$idx);
                $array+=$mat;
                $idx++;
                $mat=array('poste'.$idx=>$n->account_cred,
                    'amount'.$idx=>$val,
                    'ld'.$idx=>'Amortissement '.$fiche->strAttribut(ATTR_DEF_QUICKCODE),
                    'qc_'.$idx=>$card_cred);
                $array+=$mat;
                $idx++;
            }
        }
        $array+=array('nb_item'=>$idx);
        $ledger=new Acc_Ledger($cn, $p_array['p_jrn']);
        $ledger->with_concerned=false;
        $list=new ISelect('grouped');
        $list->value=array(
            array('label'=>_('En une opération'), 'value'=>1),
            array('label'=>_('En plusieurs opérations'), 'value'=>0),
            array('label'=>_('-- choix --'), 'value'=>-1)
        );
        $list->selected=-1;
        echo '<div style="width:80%;margin-left:10%">';
        echo '<form method="POST" style="display:inline">';
        try
        {
            echo $ledger->input($array, true);
            foreach ($a_material as $key=> $value)
                echo HtmlInput::hidden($key, $value);
        }
        catch (Exception $e)
        {
            echo alert($e->getMessage());
            return false;
        }
        echo $list->input();
        echo HtmlInput::submit('save',_('Sauver'));
        echo HtmlInput::hidden('sa', $p_array['sa']);
        echo HtmlInput::hidden('sb', $p_array['sb']);
        echo HtmlInput::hidden('p_year', $p_array['p_year']);
        echo HtmlInput::hidden('p_date', $p_array['p_date']);
        echo HtmlInput::hidden('p_jrn', $p_array['p_jrn']);
        echo HtmlInput::hidden('plugin_code', $p_array['plugin_code']);
        for ($i=0; $i<count($p_array['a_id']); $i++)
        {
            echo HtmlInput::hidden('a_id[]', $p_array['a_id'][$i]);
            if (isset($p_array['p_ck'.$i]))
                echo HtmlInput::hidden('p_ck'.$i, '1');
        }


        echo '</form>';
        /*
         * correct
         */
        echo '<form method="POST" style="display:inline">';
        echo dossier::hidden();
        echo HtmlInput::hidden('sa', $p_array['sa']);
        echo HtmlInput::hidden('sb', $p_array['sb']);
        echo HtmlInput::hidden('p_year', $p_array['p_year']);
        echo HtmlInput::hidden('p_date', $p_array['p_date']);
        echo HtmlInput::hidden('p_jrn', $p_array['p_jrn']);
        echo HtmlInput::hidden('plugin_code', $p_array['plugin_code']);
        for ($i=0; $i<count($p_array['a_id']); $i++)
        {
            echo HtmlInput::hidden('a_id[]', $p_array['a_id'][$i]);
            if (isset($p_array['p_ck'.$i]))
                echo HtmlInput::hidden('p_ck'.$i, '1');
        }
        echo HtmlInput::submit('correct', 'Corriger');
        echo '</form>';
        echo '</div>';
        return true;
    }

    /**
     * save into amortissement_histo
     * @param $p_array contains the data to insert
     * @param $p_group boolean Save in one operation if TRUE otherwise in
     * several ones
      @code
      array
      'plugin_code' => string 'AMORT' (length=5)
      'sa' => string 'util' (length=4)
      'gDossier' => string '48' (length=2)
      'p_year' => string '' (length=0)
      'p_jrn' => string '4' (length=1)
      'p_date' => string '' (length=0)
      'a_id' =>
      array
      0 => string '86' (length=2)
      1 => string '85' (length=2)
      'p_ck1' => string '' (length=0)

      @endcode
     */
    function save($p_array, $p_group)
    {
        if ($p_group == true)
        {
            return $this->save_grouped($p_array);
        }
        else if ($p_group == false)
        {
            return $this->save_separated($p_array);
        }
    }

    /**
     * @brief save the amortized material into only one writing
     * @param $p_array contains the data
     * @see save
     */

    private function save_separated($p_array)
    {
        global $cn;
        $ledger=new Acc_Ledger($cn, $p_array['p_jrn']);
        $this->saved_operation=array();
        $msg='';
        for ($i=0; $i<count($p_array['a_id']); $i++)
        {
            if (isset($p_array['p_ck'.$i]))
            {
                /*                 * *
                 * corresponding 
                 * if  'p_ck5' => '1',
                 * then 
                 * if a_request5 = 4 operation 4 = deb 5 = cred
                 */
                $idx=$p_array['request_a'.$i];
                $cred=$idx+1;

                /**
                 * Save into the ledger
                 */
                $p_post=array();
                $p_post['p_jrn']=$p_array['p_jrn'];
                $p_post['jrn_type']=$p_array['jrn_type'];
                $p_post['e_date']=$p_array['e_date'];
                $p_post['e_pj']=$p_array['e_pj'];
                $p_post['e_pj_suggest']=$p_array['e_pj_suggest'];
                $msg_operation=$cn->get_value('select vw_name from vw_fiche_attr join amortissement.amortissement using (f_id) where a_id=$1', array($p_array['a_id'][$i]));
                $p_post['desc']=$p_array['e_comm']."-".$msg_operation;
                $this->saved_operation['desc'][]=$p_post['desc'];
                $p_post['mt']=microtime(false);
                $p_post['nb_item']=$cred*2;

                // Debit
                if (isset($p_array['ck'.$idx]))
                    $p_post['ck'.$idx]=1;
                if (isset($p_array['poste'.$idx]))
                    $p_post['poste'.$idx]=$p_array['poste'.$idx];
                if (isset($p_array['qc_'.$idx]))
                    $p_post['qc_'.$idx]=$p_array['qc_'.$idx];
                if (isset($p_array['ld'.$idx]))
                    $p_post['ld'.$idx]=$p_array['ld'.$idx];
                $p_post['amount'.$idx]=$p_array['amount'.$idx];
                // crédit
                if (isset($p_array['poste'.$cred]))
                    $p_post['poste'.$cred]=$p_array['poste'.$cred];
                if (isset($p_array['qc_'.$cred]))
                    $p_post['qc_'.$cred]=$p_array['qc_'.$cred];
                if (isset($p_array['ld'.$cred]))
                    $p_post['ld'.$cred]=$p_array['ld'.$cred];
                $p_post['amount'.$cred]=$p_array['amount'.$cred];
                $ledger->save($p_post);
                $this->saved_operation["internal"][]=$ledger->internal;
                $this->saved_operation["jr_id"][]=$ledger->jr_id;
                /*
                 * Do not exist we insert into amortissement.amortissement_histo
                 */
                $n=new Amortissement_Histo_Sql($cn);
                $val=$cn->get_value("select ad_amount from amortissement.amortissement_detail ".
                        " where a_id = $1 and ad_year=$2", array($p_array['a_id'][$i], $p_array['p_year']));
                $val=($val=='')?0:$val;
                $h=$cn->get_value('select ha_id from amortissement.amortissement_histo where a_id=$1 and h_year=$2', array($p_array['a_id'][$i], $p_array['p_year']));
                if ($cn->count()==0)
                    continue;
                $n->ha_id=$h;
                $n->load();
                $n->h_amount=$val;
                $n->h_year=$p_array['p_year'];
                $n->jr_internal=$ledger->internal;
                $n->update();
            }
        }
        return $msg;
    }

    /**
     * @brief save the amortized material into several writings
     * @param $p_array contains the data
     * @see save
     */
    private function save_grouped($p_array)
    {
        global $cn;
        $ledger=new Acc_Ledger($cn, $p_array['p_jrn']);
        $this->saved_operation=array();
        /**
         * Save into the ledger
         */
        $p_post=array();
        $p_post['p_jrn']=$p_array['p_jrn'];
        $p_post['jrn_type']=$p_array['jrn_type'];
        $p_post['e_date']=$p_array['e_date'];
        $p_post['e_pj']=$p_array['e_pj'];
        $p_post['e_pj_suggest']=$p_array['e_pj_suggest'];
        $p_post['desc']='Amortissement ';
        $this->saved_operation['desc'][0]=$p_post['desc'];
        $p_post['mt']=microtime(false);
        $msg='';
        for ($i=0; $i<count($p_array['a_id']); $i++)
        {
            if (isset($p_array['p_ck'.$i]))
            {
                /*                 * *
                 * corresponding 
                 * if  'p_ck5' => '1',
                 * then 
                 * if a_request5 = 4 operation 4 = deb 5 = cred
                 */
                $idx=$p_array['request_a'.$i];
                $cred=$idx+1;


                $p_post['nb_item']=$cred*2;

                // Debit
                if (isset($p_array['ck'.$idx]))
                    $p_post['ck'.$idx]=1;
                if (isset($p_array['poste'.$idx]))
                    $p_post['poste'.$idx]=$p_array['poste'.$idx];
                if (isset($p_array['qc_'.$idx]))
                    $p_post['qc_'.$idx]=$p_array['qc_'.$idx];
                if (isset($p_array['ld'.$idx]))
                    $p_post['ld'.$idx]=$p_array['ld'.$idx];
                $p_post['amount'.$idx]=$p_array['amount'.$idx];
                // crédit
                if (isset($p_array['poste'.$cred]))
                    $p_post['poste'.$cred]=$p_array['poste'.$cred];
                if (isset($p_array['qc_'.$cred]))
                    $p_post['qc_'.$cred]=$p_array['qc_'.$cred];
                if (isset($p_array['ld'.$cred]))
                    $p_post['ld'.$cred]=$p_array['ld'.$cred];
                $p_post['amount'.$cred]=$p_array['amount'.$cred];
            }
        }
        $ledger->save($p_post);
        $this->saved_operation["internal"][0]=$ledger->internal;
        $this->saved_operation["jr_id"][0]=$ledger->jr_id;
         for ($i=0; $i<count($p_array['a_id']); $i++)
        {
            if (isset($p_array['p_ck'.$i]))
            {
                /*
                 * Do not exist we insert into amortissement.amortissement_histo
                 */
                $n=new Amortissement_Histo_Sql($cn);
                $val=$cn->get_value("select ad_amount from amortissement.amortissement_detail ".
                        " where a_id = $1 and ad_year=$2", array($p_array['a_id'][$i], $p_array['p_year']));
                $val=($val=='')?0:$val;
                $h=$cn->get_value('select ha_id from amortissement.amortissement_histo where a_id=$1 and h_year=$2', array($p_array['a_id'][$i], $p_array['p_year']));
                if ($cn->count()==0)
                    continue;
                $n->ha_id=$h;
                $n->load();
                $n->h_amount=$val;
                $n->h_year=$p_array['p_year'];
                $n->jr_internal=$ledger->internal;
                $n->update();
            }
        }
        return $msg;
    }

}
