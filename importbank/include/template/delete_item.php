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
?>
<div id="<?php echo $ctl?>">
<?php echo HtmlInput::anchor_close($ctl)?>

<?php
echo h2info('Détail opération');
	if ($bi->id=='')
	{
		echo h2('Opération effacée','class="notice"');
		return;
	}
echo 		h2('Opération effacée','class="notice"');

?>

<table>
<TR>
<TD>Date</TD><td><?php echo $bi->tp_date?></td></tr>
<tr><td>n° opération </td><td><?php echo h($bi->ref_operation)?></td></tr>
<tr><TD>
	Tiers
    </TD>
    <td>
	<?php echo h($bi->tp_third)?>
    </td>
</tr>
<tr>
	<TD>Montant
	</TD>
	<td><?php echo nbm($bi->amount)?>
	</td>
</tr>
<tr>
	<TD>Libelle
	</TD>
	<td><?php echo h($bi->libelle)?>
	</td>
</tr>
<tr>
	<TD>Autre information
	</TD>
	<td><?php echo h($bi->tp_extra)?>
	</td>
</tr>
<tr>
	<TD>Journal
	</TD>
	<td><?php echo $jrn?>
	</td>
</tr>
<!--
<tr>
	<TD>Contrepartie
	</TD>
<?php $w->readOnly=true;$wConcerned->readOnly=true;?>
	<td><?php /* $w->input() */ ?><?php /* $w->search() */?><span id="e_third"></span>
	</td>
</tr>
<tr>
	<TD>reconciliation
	</TD>
	<td><?php /* $wConcerned->input();*/ ?>
	</td>
</tr>
-->
</table>

</form>
</div>