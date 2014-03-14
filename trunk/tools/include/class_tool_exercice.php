<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_tool_exercice
 *
 * @author dany
 */
class Tool_Exercice
{

	function __construct($cn)
	{
		$this->cn = $cn;
	}

	/**
	 * Show form to input data for new exercice
	 */
	function input()
	{
		$exercice = new INum('p_exercice');
		$exercice->prec = 0;
		$exercice->value = HtmlInput::default_value_post('p_exercice', '');

		$year = new INum('year');
		$year->prec = 0;
		$year->value = HtmlInput::default_value_post('year', '');

		$nb_month = new INum('nb_month');
		$nb_month->prec = 0;
		$nb_month->value = HtmlInput::default_value_post('nb_month', '');

		$from = new ISelect('from_month');
		$from->selected= HtmlInput::default_value_post('from_month', '');
		$amonth = array();
		for ($i = 1; $i < 13; $i++)
		{
			$amonth[] = array("value" => $i, "label" => $i);
		}
		$from->value = $amonth;

		require_once 'template/tool_exercice_input.php';
	}

	function fromPost()
	{
		$this->exercice = $_POST['p_exercice'];
		$this->nb_month = $_POST['nb_month'];
		$this->from = $_POST['from_month'];
		$this->year= $_POST['year'];
	}

	function verify()
	{
		if (isNumber($this->exercice) == 0)
			throw new Exception("Exercice n'est pas un nombre");

		if ($this->exercice > COMPTA_MAX_YEAR|| $this->exercice < COMPTA_MIN_YEAR)
			throw new Exception("Exercice doit être entre ".COMPTA_MAX_YEAR."& ".COMPTA_MIN_YEAR);
		if (isNumber($this->year) == 0)
			throw new Exception("Année n'est pas un nombre");

		if ($this->year > COMPTA_MAX_YEAR|| $this->year < COMPTA_MIN_YEAR)
			throw new Exception("Année doit être entre ".COMPTA_MAX_YEAR."& ".COMPTA_MIN_YEAR);

		if (isNumber($this->nb_month) == 0)
			throw new Exception("Nombre de mois n'est pas un nombre");
		if ($this->nb_month < 1 || $this->nb_month > 60)
			throw new Exception("Nombre de mois doit être compris entre 1 & 60 ");
		if (isNumber($this->from) == 0)
			throw new Exception("Mois de début n'existe pas ");
		if ($this->from > 13 || $this->from < 1)
			throw new Exception("Mois de début n'existe pas ");
	}

	function save()
	{
		try
		{
			$this->verify();
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());
		}
		$this->cn->start();
		try
		{
			$periode = new Periode($this->cn);
			$exercice=$this->exercice;
			$year=$this->year;
			$month=$this->from;
			for ($i = 1; $i <= $this->nb_month; $i++)
			{


				$date_start = sprintf('01.%02d.%d', $month, $year);
				$date_end = $this->cn->get_value("select to_char(to_date('$date_start','DD.MM.YYYY')+interval '1 month'-interval '1 day','DD.MM.YYYY')");
				if ($periode->insert($date_start, $date_end, $this->exercice) != 0)
				{
					throw new Exception('Erreur insertion période');
				}
				if ($month>11 )
				{
					$year++;
					$month=1;
				}
				else
				{
					$month++;
				}
			}
		}
		catch (Exception $e)
		{
			var_dump($e->getTraceAsString());
			return -1;
		}
		return 0;
	}

}

?>
