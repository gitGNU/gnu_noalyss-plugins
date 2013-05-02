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

/* !\file
 * \brief raw file for PDF ewa
 */
require_once 'include/class_formulaire_param.php';
require_once 'include/class_rapav_declaration.php';
extract($_REQUEST);
if ($act == 'rapav_form_export')
{
	Formulaire_Param::to_csv($d_id);
	exit();
}
if ($act == 'export_decla_csv')
{
	Rapav_Declaration::to_csv($d_id);
	exit();
}
if ($act == 'export_decla_document')
{
	$decl = new Rapav_Declaration();
	$decl->d_id = $id;
	$decl->load();

	$cn->start();
	if ($decl->d_filename == "")
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

	$cn->lo_export($decl->d_lob, $tmp);

	ini_set('zlib.output_compression', 'Off');
	header("Pragma: public");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: must-revalidate");
	header('Content-type: ' . $decl->d_mimetype);
	header('Content-Disposition: attachment;filename="' . $decl->d_filename . '"', FALSE);
	header("Accept-Ranges: bytes");
	$file = fopen($tmp, 'r');
	while (!feof($file))
		echo fread($file, 8192);

	fclose($file);

	unlink($tmp);

	$cn->commit();
}
if ($act == 'export_definition_modele')
{
	$decl = new RAPAV_Formulaire();
	$decl->f_id = $id;
	$decl->load();

	$cn->start();
	if ($decl->f_filename == "")
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

	$cn->lo_export($decl->f_lob, $tmp);

	ini_set('zlib.output_compression', 'Off');
	header("Pragma: public");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: must-revalidate");
	header('Content-type: ' . $decl->f_mimetype);
	header('Content-Disposition: attachment;filename="' . $decl->f_filename . '"', FALSE);
	header("Accept-Ranges: bytes");
	$file = fopen($tmp, 'r');
	while (!feof($file))
		echo fread($file, 8192);

	fclose($file);

	unlink($tmp);

	$cn->commit();
}
?>
