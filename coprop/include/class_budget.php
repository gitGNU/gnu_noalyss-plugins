<?php


class Budget
{
    function to_list()
    {
        global $cn;

        $array=$cn->get_array("select b_id, b_name,
                    to_char(b_start,'DD.MM.YYYY') as str_start,
                    to_char(b_end,'DD.MM.YYYY') as str_end,
                    b_amount
                    from coprop.budget
                    order by b_name
                    ");

        require_once 'template/budget_list.php';

    }
	function load()
	{
		global $cn;
		try
		{
			if ($this->b_id == '') throw new Exception("Aucun budget demandé");
			$array=$cn->get_array("select b_id,b_name,b_start,b_end,b_amount,
				to_char(b_start,'DD.MM.YYYY') as str_b_start,
				to_char(b_end,'DD.MM.YYYY') as str_b_end
				from coprop.budget where b_id=$1",array($this->b_id));
			if ($cn->count() == 1)
			{
				$this->b_name=$array[0]['b_name'];
				$this->b_start=$array[0]['b_start'];
				$this->str_b_start=$array[0]['str_b_start'];
				$this->b_end=$array[0]['b_end'];
				$this->str_b_end=$array[0]['str_b_end'];
				$this->b_amount=$array[0]['b_amount'];
			}
			else
				throw new Exception ('Aucun budget trouvé');
		} catch (Exception $e)
		{
			echo $e->getTraceAsString();
			throw $e;
		}
	}
	/**
	 *Detail d'un budget avec les détails, pour mettre à jour
	 * @global type $cn
	 * @throws Exception
	 */
    function detail()
    {
        global $cn;
        try
        {

            if ( ! isset ($this->b_id)|| trim($this->b_id)=='')
                    throw new Exception ("Aucun budget demandé");
			$this->load();
			$name=new IText('b_name');
			if ($this->b_id <> 0)
			{
				$name->value=$this->b_name;
				$start=new IDate('b_start',$this->str_b_start);
				$end=new IDate('b_end',$this->str_b_end);
				$amount=new INum('b_amount',round($this->b_amount,2));

			}	else {
				$start=new IDate('b_start');
				$end=new IDate('b_end');
				$amount=new INum('b_amount',0);

			}
			$amount->javascript='onchange="format_number(this,2);compute_budget();"';
			$bud_amount=$amount->value;

			echo HtmlInput::hidden("b_id",$this->b_id);
			echo HtmlInput::request_to_hidden(array('gDossier','ac','plugin_code','sa'));
			require_once 'template/budget.php';

            $array=$cn->get_array("select bt_id,bt_amount,f_id,vw_name,quick_code,cr_name,cr_id
                from coprop.budget_detail
                join coprop.clef_repartition using (cr_id)
                join vw_fiche_attr using (f_id)
                where b_id=$1",array($this->b_id));
            $a_input=array();
            $fiche_dep=$cn->make_list("select fd_id from fiche_def where frd_id=2");

			$a_key=$cn->make_array(" select cr_id,cr_name from coprop.clef_repartition order by cr_name");
			$max=count($array);
            for ($i=0;$i<MAXROWBUD;$i++)
            {
                $card=new ICard('f_id'.$i);
                $card->value=($i>=$max)?"":$array[$i]['quick_code'];
                $card->table=0;

                 // name of the field to update with the name of the card
                $card->set_attribute('label','w_card_label'.$i);

                // Type of card : deb, cred,
                $card->set_attribute('typecard',$fiche_dep);

                $card->extra=$fiche_dep;

                // Add the callback function to filter the card on the jrn
                $card->set_callback('filter_card');
                $card->set_attribute('ipopup','ipopcard');
                // when value selected in the autcomplete
                  $card->set_function('fill_data');

                // when the data change

                  $card->javascript=sprintf(' onchange="fill_data_onchange(\'%s\');" ',
                            $card->name);
                  $card->set_dblclick("fill_ipopcard(this);");

                  $card_label=new ISpan();
                  $card_label->table=0;
                  $f_card_label=$card_label->input("w_card_label".$i,"");

                // Search button for card
                $f_card_bt=$card->search();

                $amount=new INum("bt_amount[]");
                $amount->value=($i>=$max)?"":round($array[$i]['bt_amount'],2);
				$amount->javascript='onchange="format_number(this,2);compute_budget();"';
                $hidden=($i>=$max)?0:HtmlInput::hidden("bt_id[]",$array[$i]["bt_id"]);

                $ikey=new ISelect("key[]");
                $ikey->value=$a_key;
                $ikey->selected=($i>=$max)?0:$array[$i]['cr_id'];

                $a_input[$i]["amount"]=$amount->input();
                $a_input[$i]["hidden"]=$hidden;
                $a_input[$i]["card"]=$card->input().$f_card_bt;
                $a_input[$i]["card_label"]=$f_card_label;

                $a_input[$i]['key']=$ikey->input();

            }
            require_once 'template/bud_detail.php';
        }
        catch (Exception $e)
        {
            $e->getTraceAsString();
        }
    }
}



?>
