<?php
/**
 *@brief calcul appel de fond
 */
require_once 'class_copro_key.php';
require_once 'class_copro_budget.php';
class Coprop_Appel_Fond
{
	function load()
	{
		global $cn;
		$array=$cn->get_array("SELECT af_id, to_char(af_date,'DD.MM.YYYY') as af_date,
					af_confirmed, af_percent, af_amount, af_card,
					af_ledger, tech_per, jr_internal, b_id, cr_id
						FROM coprop.appel_fond where af_id=$1",
				array($this->id));

		$var=array('af_id','af_date','af_confirmed','af_percent','af_amount','af_card','af_ledger','tech_per','jr_internal','b_id','cr_id');
		for ($i=0;$i<count($array);$i++)
		{
			$idx=$var[$i];
			$this->$idx=$array[0][$idx];
		}

	}
    /**
     *@brief create a key to save the data in DB
     */
    function create_key()
    {
        global $cn;
        $this->id=$cn->get_next_seq('coprop.appel_fond_id');
        // clean old
        $cn->exec_sql("delete from coprop.appel_fond where af_confirmed='N' and tech_per < now() - interval '5 hours' ");

        // insert new unconfirmed
        $cn->exec_sql("insert into coprop.appel_fond(af_id,af_date,af_confirmed,af_percent,af_amount,af_card,af_ledger,tech_per,b_id,cr_id)
                values ($1,to_date($2,'DD.MM.YYYY'),'N',$3,$4,$5,$6,now(),$7,$8)",
                array($this->id,$this->af_date,$this->af_percent,$this->af_amount,$this->af_card,$this->af_ledger,$this->b_id,$this->cr_id));

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

                $fract=  bcdiv($a_lot[$i]['crd_amount'],$key->cr_tantieme);

                $afd->afd_amount=  bcmul($fract,$p_amount);

                if ( $afd->afd_amount != 0 )$afd->insert();

            }
			$cn->commit();
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
        bcscale(4);
        try
        {
            $this->type = "amount";
            $this->af_amount = $p_array['amount'];
            $this->cr_id = $p_array['key'];
            $this->af_percent = 1;
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
        bcscale(4);
        try
        {
            // req bud_pct <= 1 && bud_pct > 0
            if ($p_array['bud_pct'] > 1 || $p_array['bud_pct']<0 ) throw new Exception ('Pourcentage incorrect');
            // req date valide
            if ( isDate($p_array['p_date'])==null) throw new Exception('La date est invalide');

            $this->type="budget";
            $this->b_id=$p_array['b_id'];
            $this->af_percent=$p_array['bud_pct'];
            $this->af_ledger=$p_array['p_jrn'];
            $this->af_date=$p_array['p_date'];
            $fiche=new Fiche($cn);
            $fiche->get_by_qcode($p_array['w_categorie_appel']);
            //  req  $fiche->id > 0
            if ( $fiche->id < 1 ) throw new Exception("La fiche pour l'appel de fond n'existe pas");
            $this->af_card=$fiche->id;
			$this->cr_id=null;

            $tot_bud=$cn->get_value('select b_amount from coprop.budget where b_id=$1',array($this->b_id));
            if ($tot_bud <=0  ) throw new Exception ("Ce budget a un montant de 0 ");

            $this->af_amount=  bcmul($tot_bud,$this->af_percent);
            $this->create_key();

            // get all the key of this budget
            $budget=new Copro_Budget();
            $budget->b_id=$this->b_id;

            $a_detail=$budget->get_detail();
            bcscale(4);

            // foreach key,
            for ($i=0;$i<count($a_detail);$i++)
            {
                //  compute the amount to take
                $amount=  bcmul($a_detail[$i]['bt_amount'],$this->af_percent);
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
		global $cn;
		$ledger = new Acc_Ledger($cn,0);
		$this->load();
		$adetail = $cn->get_array("
			select sum(afd_amount) as amount,S.coprop_id
				from coprop.appel_fond_detail as A
				join coprop.summary as S on (S.lot_id::numeric=A.lot_id)
			where af_id=$1 group by S.coprop_id", array($this->id));
		$array[] = array();

		$array['e_date'] = $this->af_date;
		$array['p_jrn'] = $this->af_ledger;
		$array['desc']="Appel de fond";
		$fiche=new Fiche($cn,$this->af_card);
		$array['qc_0']=$fiche->get_quick_code();
		$array['amount0']=$this->af_amount;
		$array['nb_item']=count($adetail);

		for ($i=0;$i<count($adetail);$i++){
			$idx=$i+1;
			$fiche=new Fiche($cn,$adetail[$i]['coprop_id']);
			$array['qc_'.$idx]=$fiche->get_quick_code();
			$array['amount'.$idx]=round($adetail[$i]['amount'],2);
			$array['ck'.$idx]=1;
		}

		echo '<FORM METHOD="GET" class="print">';
		echo $ledger->input($array,0);
		echo HtmlInput::request_to_hidden(array('amount','key','w_categorie_appel','b_id','aft','bud_pct','p_date','ac', 'plugin_code','sa'));
		echo HtmlInput::extension() . dossier::hidden();
		echo HtmlInput::hidden('action', 'confirm');
		echo HtmlInput::hidden('af_id', $this->id);
		echo HtmlInput::submit('save', 'Sauve');
		echo HtmlInput::button('add', _('Ajout d\'une ligne'), 'onClick="quick_writing_add_row()"');
		echo '</form>';
		echo '<div class="info">' .
		_('Débit') . ' = <span id="totalDeb"></span>  ' .
		_('Crédit') . ' = <span id="totalCred"></span>  ' .
		_('Difference') . ' = <span id="totalDiff"></span></div> ';
		echo "<script>checkTotalDirect();</script>";
		echo '</div>';
	}
	function confirm()
	{
		global $cn;
		$cn->exec_sql("update coprop.appel_fond set af_confirmed='Y' where af_id=$1",array($this->id));
	}
}
?>
