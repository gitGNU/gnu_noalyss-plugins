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
 * @brief upload operation
 * @see Impacc_Operation::input_format
 */
?>
<h2>
<h2> 
<?php
echo _("Etape"),' 1/3',' ';
echo _('Importation de données');
?>
</h2>

<p>
    
</p>
<p>
   <?php echo _("Chargement d'un fichier CSV , crée depuis Calc (OpenOffice.org ou libreoffice)");?>
<a class="line" href="http://download.noalyss.eu/contrib/import_operation/" target="_blank">Fichiers exemples</a>
</p>
<form method="POST" enctype="multipart/form-data">
<?php echo HtmlInput::array_to_hidden(array('gDossier','ac','plugin_code','sa'), $_REQUEST)?>
<table>    
<tr>
<td>
   <?php echo _("Journal");?>
</td>
<td>
    <?php echo $in_ledger->input()?>
</td>    
    
</tr>
<tr>
<td><?php echo _("Délimiteur");?> </td>
<TD> <?php echo $in_delimiter->input()?></td>
</tr>

<tr>
<td><?php echo _("Encodage");?></td>
<TD> <?php echo $in_encoding->input()?></td>
</tr>
<tr>
<td>  <?php echo _("Texte entouré par");?></td>
<TD>
    <?php echo $in_surround->input()?></td>
</td>
</tr>
<tr>
<td>  <?php echo _("Décimale");?></td>
<TD>
    <?php echo $in_decimal->input()?>
</td>
</tr>
<tr>
    <td>
        <?php echo _("Séparateur de millier");?>
    </td>
    <td>
        <?php echo $in_thousand->input()?></td>
    </td>
</tr>
<tr>
<td><?php echo _("Fichier à charger");?></td>
<TD> <?php echo $in_filename->input()?></td>
</tr>
</table>
<?php echo HtmlInput::submit('test_import',_('Charger'));?>

</form>