<?php
/**
 *@brief calcul appel de fond 
 */

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
        $cn->exec_sql("insert into coprop.appel_fond(af_id,af_date,af_confirmed,af_percent,af_amount,af_card,af_ledger,tech_per)
                values ($1,to_date($2,'DD.MM.YYYY','N',$3,$4,$5,$6,$7,now())",array($this->id,$this->af_date,$this->percent,$this->amount,$this->bud_id,$this->af_card,$this->af_ledger));
        
    }
    function compute_amount($p_array)
    {
        $this->type="amount";
        $this->amount=0;
        $this->percent=1;
        $this->bud_id=null;
        
        $this->create_key();
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
            $this->bud_id=$p_array['b_id'];
            $this->percent=$p_array['bud_pct'];
            $this->af_ledger=$p_array['p_jrn'];
            $this->af_date=$p_array['p_date'];
            $fiche=new Fiche($cn);
            $fiche->get_by_qcode($p_array['w_categorie_appel']);
            //  req  $fiche->id > 0
            if ( $fiche->id < 1 ) throw new Exception("La fiche pour l'appel de fond n'existe pas");
            $this->af_card=$fiche->id;

            $tot_bud=$cn->get_value('select b_amount from coprop.budget where b_id=$1',array($this->bud_id));
            if ($tot_bud <=0  ) throw new Exception ("Ce budget a un montant de 0 ");

            $this->amount=  bcmul($tot_bud,$this->percent);
            $this->create_key();
        } catch (Exception $e)
        {
            throw ($e);
        }
    }
}
?>
