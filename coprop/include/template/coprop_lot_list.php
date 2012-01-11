<?php

/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/* $Revision$ */

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief show list building, lot and copropriétaire
 *
 */
$gDossier=Dossier::id();
?>
<h1>Lots affectés</h1>
<? for ( $i=0;$i<count($a_immeuble);$i++):?>
<h2><?=HtmlInput::card_detail($a_immeuble[$i]['quick_code'],$a_immeuble[$i]['vw_name'],' style="display:inline;text-decoration:underline"')?></h2>

<?
$ret_coprop=$cn->execute("coprop",array($a_immeuble[$i]['f_id']));
$max_coprop=Database::num_row($ret_coprop);
if ($max_coprop==0)
{
    echo "Pas de copropriétaires pour cet immeuble";
    continue;
}
?>
<ul style="list-style-type: none;margin-top:10px;margin-bottom:10px;">
<?for ($e=0;$e<$max_coprop;$e++):
    $r=Database::fetch_array($ret_coprop,$e);
    ?>
<li style="margin-top:4px;margin-bottom:4px;">
<?=HtmlInput::card_detail($r['copro_qcode'],'',' style="display:inline;text-decoration:underline"').' '.h($r['copro_name']." ".$r['copro_first_name'])?>
</li>
    <?
    $ret_lot=$cn->execute("lot",array($a_immeuble[$i]['f_id'],$r['coprop_id']));
   $max_lot=Database::num_row($ret_lot);
    if ($max_lot==0)
    {
        echo "Pas de lot pour ce copropriétaires ";
        continue;
    }
    ?>
    <ul>
    <?for ($l=0;$l<$max_lot;$l++):
    $s=Database::fetch_array($ret_lot,$l);
    ?>
       <li style="list-style-type: square;margin-top:2px;margin-bottom:2px;"><?=HtmlInput::card_detail($s['lot_qcode'],'',' style="display:inline;text-decoration:underline"')." ".h($s['lot_name']." ".$s['lot_desc'])?></li>
    <? endfor;?>
    </ul>
    <? endfor;?>
</ul>

<? endfor; ?>

<h1>Lot sans immeuble ou sans copropriétaires</h1>
<ul>
<? for($e=0;$e<count($a_undef_lot);$e++):?>
      <li><?=HtmlInput::card_detail($a_undef_lot[$e]['lot_qcode'],'',' style="display:inline;text-decoration:underline"')." ".h($a_undef_lot[$e]['lot_name']." ".$a_undef_lot[$e]['lot_desc'])?></li>
<? endfor; ?>
</ul>
