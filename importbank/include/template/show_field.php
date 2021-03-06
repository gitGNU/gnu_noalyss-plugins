<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
$f=HtmlInput::default_value_request("format", 0);
?>

<h2><?php echo _('Etape 2 / 4: introduire le fichier')?></h2>
<?php
    if ( $f != 0 ) :
        echo _("Nom du format");
        echo " ";
        echo h($format->value);
        echo '<br>';
        echo HtmlInput::button_action(_('Changer'), "$('bank_format_div').show()","x","smallbutton");
    endif;

?>

<form method="POST"  id="validate_show_field_frm" enctype="multipart/form-data"  onsubmit="return validate_show_field('validate_show_field_frm')">
<table id="bank_format_div">
<tr>
	<td>
	<?php echo _('Nom du format')?>
	</td>
	<td>
	<?php echo $format->input()?>
	</td>
</tr>
<tr>
	<td>
	<?php echo _('A importer dans le journal de banque')?>
	</td>
	<td>
	<?php echo $jrn_def->input()?>
	</td>
</tr>
<tr>
	<td>
        <?php echo _('Format de date')?>
	</td>
	<td>
	<?php echo $format_date->input()?>
	</td>
</tr>

<tr>
	<td>
	<?php echo _('Séparateur de champs')?>
	</td>
	<td>
	<?php echo $sep_field->input()?>
	</td>
</tr>

<tr>
	<td>
	<?php echo _('Séparateur de millier')?>
	</td>
	<td>
	<?php echo $sep_thousand->input()?>
	</td>
</tr>

<tr>
	<td>
	<?php echo _('Séparateur décimal')?>
	</td>
	<td>
	<?php echo $sep_decimal->input()?>
	</td>
</tr>
<tr>
	<td>
	<?php echo _('Ligne d\'en-tête à ne pas prendre en considération')?>
	</td>
	<td>
	<?php echo $skip->input()?>
	</td>
</tr>

<tr>
	<td>
	<?php echo _('Les lignes ayant ce nombre de colonnes sont valides, laissez à vide si vous ne savez pas')?>
	</td>
	<td>
	<?php echo $nb_col->input()?>
	</td>
</tr>



</table>

<?php echo _('Fichier à importer')?> <?php echo $file->input()?>

<?php 
echo HtmlInput::get_to_hidden(array('format','gDossier','sa','plugin_code','format'));
echo HtmlInput::hidden('sb',$sb);

?>
<p>
</p>
<ol class="menuv">
    <li class="menuv">
        <?php echo HtmlInput::submit('input_file',_('Valider'));?>
    </li>
    <li class="menuv">
        <?php echo HtmlInput::button_action(_('Effacer'),"confirm_remove()");?>
    </li>

</ol>
    
    
</form>
<div id="confirm_remove_format" style="display:none">
    <form id="confirm_remove_format_frm" method="post">
        <?php
        echo HtmlInput::get_to_hidden(array('format','gDossier','sa','plugin_code','format'));
        echo HtmlInput::hidden('sb',$sb);
        echo HtmlInput::hidden('remove_format','1');
        ?>
    </form>
</div>
<script>
    /**
     * Validate the form, the name of the bank format can not be empty
     * @returns {undefined}
     */
    function validate_show_field() {
        if ($('format_name').value=="") {
            alert_box('<?php echo _('Nom manquant') ?>');
            $('format_name').parentNode.style.border="2px solid red";
            return false
        }
        return true;
    }
    function confirm_remove(){
        smoke.confirm('Confirmez',function (e) {
                    if (e ) {
                        $('confirm_remove_format_frm').submit();
                }
            }
            );
    }
    $('confirm_remove_format').hide();
    <?php 
    if ($f != 0 ):
    ?>
        $('bank_format_div').hide();
    <?php
    endif;
    ?>
</script>