<?php
/**
 *@brief calcul appel de fond 
 */
require_once 'class_copro_key.php';
require_once 'class_copro_budget.php';
class Coprop_Appel_Fond
{
    /**
     *@brief create a key to save the data in DB 
     */
    function create_key()
    {
        global $cn;
        $this->id=$cn->get_next_seq('appel_fond_id');
        // clean old
        $this->exec_sql("delete from coprop.appel_fond where af_confirmed='N' and tech_per < tech_per - interval '5 hours' )");
        
        // insert new unconfirmed
        $cn->exec_sql("insert into coprop.appel_fond(af_id,af_date,af_confirmed,af_percent,af_amount,af_card,af_ledger,tech_per,b_id,cr_id)
                values ($1,to_date($2,'DD.MM.YYYY','N',$3,$4,$5,$6,$7,now()),$8",
                array($this->id,$this->af_date,$this->percent,$this->amount,$this->af_card,$this->af_ledger,$this->b_id,$this->cr_id));
        
    }
    function compute_key($p_key_id,$p_amount)
    {
        global $cn;
        try
        {
            $cn->start();
            // prend tantieme global
            $key=new Copro_Key();
            $key->cr_id=$p_key_id;
            $key->load();
            
            bcscale(4);
            $a_lot=$key->get_detail();
            for ($i=0;$i<count($a_lot);$i++)
            {
                $afd=new Copro_Appel_Fond_Detail();
                $afd->af_id=$this->id;
                $afd->lot_id=$a_lot[$i]['lot_fk'];
                $afd->key_id=$p_key_id;
                $afd->key_tantieme=$key->cr_tantieme;
                $afd->lot_tantieme=$a_lot[$i]['crd_amount'];
                
                $fract=bcsub($key->cr_tantieme,$a_lot[$i]['crd_amount']);
                $afd->afd_amount=$fract;
                
                $afd->insert();
                
            }
            
        }
        catch (Exception $exc)
        {
            echo $exc->getTraceAsString();
            $cn->rollback();
            throw $exc;
        }
    }
    function compute_amount($p_array)
    {
        global $cn;
        bcscale(2);
        try
        {
            $this->type = "amount";
            $this->af_amount = $p_array['amount'];
            $this->cr_id = $p_array['cr_id'];
            $this->percent = 1;
            $this->b_id = null;
            // req date valide
            if (isDate($p_array['p_date']) == null)
                throw new Exception('La date est invalide');
            $this->af_ledger = $p_array['p_jrn'];
            $this->af_date = $p_array['p_date'];
            $fiche = new Fiche($cn);
            $fiche->get_by_qcode($p_array['w_categorie_appel']);
            //  req  $fiche->id > 0
            if ($fiche->id < 1)
                throw new Exception("La fiche pour l'appel de fond n'existe pas");
            $this->af_card = $fiche->id;
            $this->create_key();
            $this->compute_key($this->cr_id,$this->af_amount);
        }
        catch (Exception $e)
        {
            throw ($e);
        }
    }
    function compute_budget($p_array)
    {
        global $cn;
        bcscale(2);
        try 
        {
            // req bud_pct <= 1 && bud_pct > 0
            if ($p_array['bud_pct'] > 1 || $p_array['bud_pct']<0 ) throw new Exception ('Pourcentage incorrect');
            // req date valide
            if ( isDate($p_array['p_date'])==null) throw new Exception('La date est invalide');
            
            $this->type="budget";
            $this->b_id=$p_array['b_id'];
            $this->percent=$p_array['bud_pct'];
            $this->af_ledger=$p_array['p_jrn'];
            $this->af_date=$p_array['p_date'];
            $fiche=new Fiche($cn);
            $fiche->get_by_qcode($p_array['w_categorie_appel']);
            //  req  $fiche->id > 0
            if ( $fiche->id < 1 ) throw new Exception("La fiche pour l'appel de fond n'existe pas");
            $this->af_card=$fiche->id;

            $tot_bud=$cn->get_value('select b_amount from coprop.budget where b_id=$1',array($this->b_id));
            if ($tot_bud <=0  ) throw new Exception ("Ce budget a un montant de 0 ");

            $this->af_amount=  bcmul($tot_bud,$this->percent);
            $this->create_key();
            
            // get all the key of this budget
            $budget=new Budget();
            $budget->b_id=$this->b_id;
            
            $a_detail=$budget->get_detail();
            bcscale(4);
            
            // foreach key, 
            for ($i=0;$i<count($a_detail);$i++)
            {
                //  compute the amount to take 
                $amount=$a_detail[$i]['bt_amount'];
                $key=$a_detail[$i]['cr_id'];

                // call compute_key
                $this->compute_key($key, $amount);
                
            }
        } catch (Exception $e)
        {
            throw ($e);
        }
    }
    function display_ledger()
    {
        
    }
}
?>
