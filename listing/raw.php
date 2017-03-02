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

/*!\file
 * \brief raw file for PDF ewa
 */
require_once 'include/class_rapav_listing_compute_fiche.php';
require_once 'include/class_rapav_listing_compute.php';

extract($_REQUEST, EXTR_SKIP);
if ($act == 'export_listing_csv')
{
   $decl = new RAPAV_Listing_Compute();
   $decl->load($lc_id);
   $decl->to_csv();
   exit();
}
/**
 * Show generated file
 */
if ($act=="show_file")
{
        $decl = new RAPAV_Listing_Compute_Fiche();
	$decl->lf_id = $lf_id;
	$decl->load();

	$cn->start();
	if ($decl->lf_filename == "")
	{
		ini_set('zlib.output_compression', 'Off');
		header("Pragma: public");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: must-revalidate");
		header('Content-type: ' . 'text/plain');
		header('Content-Disposition: attachment;filename=vide.txt', FALSE);
		header("Accept-Ranges: bytes");
		echo "******************";
		echo _("Fichier effacé");
		echo "******************";
		exit();
	}
	$tmp = tempnam($_ENV['TMP'], 'document_');

	$cn->lo_export($decl->lf_lob, $tmp);

	ini_set('zlib.output_compression', 'Off');
	header("Pragma: public");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: must-revalidate");
	header('Content-type: ' . $decl->lf_mimetype);
	header('Content-Disposition: attachment;filename="' . $decl->lf_filename . '"', FALSE);
	header("Accept-Ranges: bytes");
	$file = fopen($tmp, 'r');
	while (!feof($file))
		echo fread($file, 8192);

	fclose($file);

	unlink($tmp);

	$cn->commit();
}
/**
 * Show generated file
 */
if ($act=="show_pdf")
{
        $decl = new RAPAV_Listing_Compute_Fiche();
	$decl->lf_id = $lf_id;
	$decl->load();

	$cn->start();
	if ($decl->lf_pdf == "")
	{
		ini_set('zlib.output_compression', 'Off');
		header("Pragma: public");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: must-revalidate");
		header('Content-type: ' . 'text/plain');
		header('Content-Disposition: attachment;filename=vide.txt', FALSE);
		header("Accept-Ranges: bytes");
		echo "******************";
		echo _("Fichier effacé");
		echo "******************";
		exit();
	}
	$tmp = tempnam($_ENV['TMP'], 'document_');

	$cn->lo_export($decl->lf_pdf, $tmp);

	ini_set('zlib.output_compression', 'Off');
	header("Pragma: public");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: must-revalidate");
	header('Content-type: application/pdf');
	header('Content-Disposition: attachment;filename="' . $decl->lf_filename . '.pdf"', FALSE);
	header("Accept-Ranges: bytes");
	$file = fopen($tmp, 'r');
	while (!feof($file))
		echo fread($file, 8192);

	fclose($file);

	unlink($tmp);

	$cn->commit();
}
if ($act == 'export_download_all')
{
    $compute = new RAPAV_Listing_compute();
    $compute->load($_GET['lc_id']);
    $filename = $compute->create_zip();
    if ($filename == "") exit();
    ini_set('zlib.output_compression', 'Off');
    header("Pragma: public");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: must-revalidate");
    header('Content-type: application/zip');
    header('Content-Disposition: attachment;filename="' . $filename.'"' , FALSE);
    header("Accept-Ranges: bytes");
    $file = fopen($filename, 'r');
    while (!feof($file))
        echo fread($file, 8192);

    fclose($file);

    unlink($filename);
}
if ( $act=='downloadTemplateListing')
{
        require_once 'include/class_rapav_listing.php';
        $id=HtmlInput::default_value_get('id',0);
        
            
        $obj=new Rapav_Listing($id);
	$cn->start();
	if ($id==0 || $obj->data->l_filename == "")
	{
		ini_set('zlib.output_compression', 'Off');
		header("Pragma: public");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: must-revalidate");
		header('Content-type: ' . 'text/plain');
		header('Content-Disposition: attachment;filename=vide.txt', FALSE);
		header("Accept-Ranges: bytes");
		echo "******************";
		echo _("Fichier effacé");
		echo "******************";
		exit();
	}
	$tmp = tempnam($_ENV['TMP'], 'document_');

	$cn->lo_export($obj->data->l_lob, $tmp);

	ini_set('zlib.output_compression', 'Off');
	header("Pragma: public");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: must-revalidate");
	header('Content-type: ' . $obj->data->l_mimetype);
	header('Content-Disposition: attachment;filename="' . $obj->data->l_filename . '"', FALSE);
	header("Accept-Ranges: bytes");
	$file = fopen($tmp, 'r');
	while (!feof($file))
		echo fread($file, 8192);

	fclose($file);

	unlink($tmp);

	$cn->commit();    
}
?>
