
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
global $cn;

ob_start();
  /**
   * Open the file and parse it
   */
$fcard=fopen($filename,'r');
if ( $fcard == FALSE) {
    printf(_("Erreur ouverture fichier %s "),$fcard);
    return;
}

$row_count=0;
$max=0;

while (($row=fgetcsv($fcard,0,$delimiter,$surround)) !== false)
  {
    $row_count++;
    echo '<tr style="border:solid 1px black">';
    if ( $skip_row >= $row_count) echo td(_('Supprimé')); else echo td($row_count); 
    $count_col=count($row);
    $max=($count_col>$max)?$count_col:$max;
    for ($i=0;$i<$count_col;$i++)
      {
	echo td($row[$i],'style="border:solid 1px black"');
      }
      echo '</tr>';
  }
$table=ob_get_contents();
ob_end_clean();

echo '<table style="border:solid 1px black;width:100%">
<tr>';

/**
 *create widget column header
 */


echo th('Numéro de ligne');
$nb_attribute = count($a_attribute);
$nb_header=count($a_header);
for ($i=0;$i<$max;$i++)
  {
    if ($i >= $nb_header) 
        $header->selected=-1;
    else
    {
        if ( in_array($a_header[$i],$a_valid_header) )
            $header->selected=$a_header[$i];
        else
            $header->selected=-1;
    }
    echo '<th>'.$header->input().'</th>';
  }
echo '</tr>';
echo $table;
echo '</table>';
?>
</form>
