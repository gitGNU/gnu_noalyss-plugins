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
// Copyright (c) 2002 Author Dany De Bontridder dany@alchimerys.be

/**
 * @file
 * @brief take all the invoices, create a large zip file and propose to download
 * it
 * @param sel_sale array of choosen jr_id
 * 
 */
//- create a tmp folder 
$dirname = tempnam($_ENV['TMP'], 'invoice');
unlink($dirname);
mkdir($dirname);
$cn->start();
$feedback = array();
//--- take all the invoice
foreach ($_GET['sel_sale'] as $key => $value)
{
    $a_invoice = $cn->get_array("select jr_pj_name,jr_pj from jrn where jr_id = $1", array($value));
    $invoice = $a_invoice[0];
    if ($invoice['jr_pj_name'] != "" && $invoice['jr_pj'] != "")
    {
        $filename= $invoice['jr_pj_name'];
        $file = $dirname . '/' .$filename;
        /*
         * Avoid that the file is overwritten by another one with
         * the same name
         */
        $i=1;
        while (file_exists($file))
        {
            $filename=sprintf("%s-%s",$i,$filename);
            $file = $dirname . '/' .$filename;
            $i++;
        }
        $cn->lo_export($invoice['jr_pj'], $file);
        $feedback[] = _('Ajout facture ') . $filename;
    }
}
// -- zip file
$date = date('ymd.Hi');
$zip_file = $_ENV['TMP'] . "/" . "invoice-" . $date . ".zip";

// --- create the zip
$zip = new Zip_Extended();
$res = $zip->open($zip_file, ZipArchive::CREATE);
if ($res != true)
{
    die("Cannot create zip file");
}
$zip->add_recurse_folder($dirname . "/");
$zip->close();
//-- send the zip
$link = http_build_query(array('gDossier' => Dossier::id(), 'ac' => $_REQUEST['ac'], 'plugin_code' => $_REQUEST['plugin_code'], 'file' => basename($zip_file)));
?>
<p>
<h2>
    <?php echo _('Facture'); ?>
</h2>
<ol>
    <?php foreach ($feedback as $row): ?>

        <li>
            <?php echo $row ?>
        </li>
    <?php endforeach; ?>
</ol>
</p>
<p>
<a class="button" style="display:inline;" href="extension.raw.php?<?php echo $link; ?>"> <?php echo _('Télécharger le fichier') ?></a>
</p>