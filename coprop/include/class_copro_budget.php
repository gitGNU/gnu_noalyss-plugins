<?php


class Copro_Budget
{
    function to_list()
    {
        global $cn;

        $array=$cn->get_array("select b_id, b_name,
                   b_exercice,
				   b_type,
				    case when b_type = 'OPER' then 'Opérationnel'
					 when b_type = 'PREV' then 'Prévisionnel' else 'inconnu' end as str_type,
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
			$array=$cn->get_array("select b_id,b_name,b_amount,
				b_type,b_exercice
				from coprop.budget where b_id=$1",array($this->b_id));
			if ($cn->count() == 1)
			{
				$this->b_name=$array[0]['b_name'];
				$this->b_exercice=$array[0]['b_exercice'];
				$this->b_type=$array[0]['b_type'];
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
			$name=new IText('b_name');
			$name->size=50;
			if ($this->b_id <> 0)
			{
				$this->load();
				$name->value=$this->b_name;
				$exercice=new ISelect('b_exercice');
				$exercice->value=$cn->make_array("select distinct p_exercice,p_exercice from parm_periode
					order by 1");
				$exercice->selected=$this->b_exercice;

				$type=new ISelect('b_type');
				$type->value=array(
					array("value"=>"OPER","label"=>"Opérationnel"),
					array("value"=>"PREV","label"=>"Prévisionnel")
				);
				$type->selected=$this->b_type;

				$amount=new INum('b_amount',round($this->b_amount,2));

			}	else {
				$exercice=new ISelect('b_exercice');
				$exercice->value=$cn->make_array("select distinct p_exercice,p_exercice from parm_periode
					order by 1");

				$type=new ISelect('b_type');
				$type->value=array(
					array("value"=>"OPER","label"=>"Opérationnel"),
					array("value"=>"PREV","label"=>"Prévisionnel")
				);
				$amount=new INum('b_amount',0);

			}
			$amount->javascript='onchange="format_number(this,2);compute_budget();"';
			$bud_amount=$amount->value;

			echo HtmlInput::hidden("b_id",$this->b_id);
			echo HtmlInput::request_to_hidden(array('gDossier','ac','plugin_code','sa'));
			require_once 'template/budget.php';

            $array=$cn->get_array("select bt_label,
							bt_id,bt_amount,f_id,vw_name,quick_code,cr_name,cr_id
                from coprop.budget_detail
                join coprop.clef_repartition using (cr_id)
                join vw_fiche_attr using (f_id)
                where b_id=$1",array($this->b_id));
            $a_input=array();
            $fiche_dep=$cn->make_list("select fd_id from fiche_def where frd_id=2");

			$a_key=$cn->make_array(" select cr_id,cr_name from coprop.clef_repartition order by cr_name");
			$max=count($array);

			// Ajout bouton ajout charge
			$f_add_button=new IButton('add_card');
			$f_add_button->label=_('Créer une nouvelle fiche');
			$f_add_button->set_attribute('ipopup','ipop_newcard');
			$f_add_button->set_attribute('jrn',-1);
			$filter=$cn->make_list("select fd_id from fiche_def where frd_id=2");
			$f_add_button->javascript=" this.filter='$filter';this.jrn=-1;select_card_type(this);";
			echo $f_add_button->input();
            for ($i=0;$i<MAXROWBUD;$i++)
            {
				$label=new IText('bt_label[]');
				$label->value=($i>=$max)?"":$array[$i]['bt_label'];

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
                $hidden=($i>=$max)?HtmlInput::hidden("bt_id[]",0):HtmlInput::hidden("bt_id[]",$array[$i]["bt_id"]);
				echo $hidden;

                $ikey=new ISelect("key[]");
                $ikey->value=$a_key;
                $ikey->selected=($i>=$max)?0:$array[$i]['cr_id'];

                $a_input[$i]["amount"]=$amount->input();
                $a_input[$i]["hidden"]=$hidden;
                $a_input[$i]["card"]=$card->input().$f_card_bt;
                $a_input[$i]["card_label"]=$label->input();

                $a_input[$i]['key']=$ikey->input();

            }
            require_once 'template/bud_detail.php';
			echo create_script("compute_budget()");
        }
        catch (Exception $e)
        {
            $e->getTraceAsString();
			throw $e;
        }
    }
	/**
	 *@brief insert or update a new budget
	 * @param $p_array
	 *   - b_id
	 *   - b_name
	 *   - b_start
	 *   - b_end
	 *   - b_amount
	 *   - f_idX  -> qcode
	 *   - key[X]
	 *   - bt_amount[X]
	 *   - p_jrn
	 *   - bt_id[X]
	 */
	function save($p_array)
	{
		try{
			$this->b_id=$p_array['b_id'];
			if ( $p_array['b_id'] == 0 )
			{
				$this->insert($p_array);
				$this->save_detail($p_array);
			} else {
				$this->update($p_array);
				$this->save_detail($p_array);
			}
		}
		catch( Exception $e){
			throw $e;
		}
	}
	/**
	 *@brief update budget
	 */
	function update($p_array)
	{
		global $cn;
		try {
			extract ($p_array);
			// update coprop.budget
			$cn->exec_sql("update coprop.budget set b_name=$1,
					b_exercice=$2,
					b_type=$3,
					b_amount=$4
					where b_id=$5
					",array(
						strip_tags($b_name),
						$b_exercice,
						$b_type,
						$b_amount,
						$b_id
					));


		}
		catch (Exception $exc) {
			echo $exc->getTraceAsString();
			throw $exc;
		}

	}
	/**
	 *@brief insert budget
	 */
	function insert($p_array)
	{
		global $cn;
		try {
			extract ($p_array);
			// update coprop.budget
			$this->b_id=$cn->get_value("insert into coprop.budget (b_name,b_exercice,b_type,b_amount)
				values ($1,
					$2,
					$3,
					$4) returning b_id
					",array(
						strip_tags($b_name),
						$b_exercice,
						$b_type,
						$b_amount
					));


		}
		catch (Exception $exc) {
			echo $exc->getTraceAsString();
			throw $exc;
		}

	}
	function save_detail($p_array)
	{
		extract($p_array);
		global $cn;
		try
		{
			$max=count($bt_id);
			for ($i=0;$i<MAXROWBUD;$i++)
			{

				if ( $bt_id[$i]== 0)
				{
					if ( strlen(trim(${'f_id'.$i})) != 0)
					{
						$f_id=$cn->get_value("select f_id from vw_fiche_attr where quick_code=upper(trim($1))",
								array(${'f_id'.$i}));

						// insert into coprop.budget_detail
							$cn->exec_sql("insert into coprop.budget_detail (bt_label,f_id,b_id,bt_amount,cr_id) ".
								" values ($1,$2,$3,$4,$5)",
								array(
									strip_tags($bt_label[$i]),
									$f_id,
									$this->b_id,
									$bt_amount[$i],
									$key[$i]
									)

							);
					}
				}
				else
				{
					// update into coprop.budget_detail
					if ( strlen(trim(${'f_id'.$i})) != 0)
					{
						$f_id=$cn->get_value("select f_id from vw_fiche_attr where quick_code=upper(trim($1))",
								array(${'f_id'.$i}));

						$cn->exec_sql("update coprop.budget_detail set bt_label=$1,f_id=$2,bt_amount=$3,cr_id=$4 ".
								" where bt_id=$5",
								array(
									strip_tags($bt_label[$i]),
									$f_id,
									$bt_amount[$i],
									$key[$i],
									$bt_id[$i]
									)

							);

					} else {
						$cn->exec_sql("delete from coprop.budget_detail where bt_id=$1",array($bt_id[$i]));
					}

				}
			}


		}
		catch (Exception $exc)
		{
			echo $exc->getTraceAsString();
			throw $exc;
		}

	}
       function get_detail()
       {
           global $cn;
           $array=$cn->get_array("select * from coprop.budget_detail where b_id=$1",
                   array($this->b_id));
           return $array;
       }
}
?>
