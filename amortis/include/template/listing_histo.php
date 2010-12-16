<div class="content" style="width:80%;margin-left:10%">
<!-- <h2 class="info">Historique</h2> -->
<form method="POST">
<?
echo HtmlInput::hidden('sa',$_REQUEST['sa']);
echo HtmlInput::hidden('sb',$_REQUEST['sb']);
echo HtmlInput::hidden('plugin_code',$_REQUEST['plugin_code']);
echo dossier::hidden();
?>
<table class="result">
<tr>
<th>Quick Code</th>
<th>Nom</th>
<th>Montant</th>
<th>Année</th>
<th>N°</th>
<th>Opération</th>

</tr>
<?

for ($i=0;$i<count($array);$i++) :
	echo '<tr>';
	echo td($array[$i]['quick_code']);
	echo td($array[$i]['vw_name']);
	echo td($array[$i]['h_amount']);
	echo td($array[$i]['h_year']);
				 
	echo td($array[$i]['h_pj']);
	$msg='';
	if ( $array[$i]['jr_internal'] != '' )
	{
	    $jrid=$cn->get_value('select jr_id from jrn where jr_internal=$1',array($array[$i]['jr_internal']));
            $msg=HtmlInput::detail_op($jrid,$array[$i]['jr_internal']);
        }
	echo td($msg);
   $ic=new ICheckBox('p_sel[]');

   echo td($ic->input().HtmlInput::hidden('h[]',$array[$i]['ha_id']));
				 


        echo '</tr>';
endfor;
?>

</table>
<? echo HtmlInput::submit('remove','Effacer la sélection')?>
</form>
</div>
