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

// Copyright Author Dany De Bontridder ddebontridder@yahoo.fr

/**\file
 * \brief raw file for backup
 */
$cmd = escapeshellcmd(PG_DUMP);
putenv("PGPASSWORD=" . phpcompta_password);
putenv("PGUSER=" . phpcompta_user);
putenv("PGHOST=" . phpcompta_psql_host);
putenv("PGPORT=" . phpcompta_psql_port);
$database = domaine . "dossier" . Dossier::id();
$args = " -Fc -Z9 --no-owner -p " . phpcompta_psql_port . " " . $database;
header('Content-type: application/octet');
header('Content-Disposition:attachment;filename="' . $database . '.bin"', FALSE);

passthru($cmd . $args, $a);