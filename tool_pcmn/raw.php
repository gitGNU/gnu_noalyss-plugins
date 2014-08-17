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

/*!\file
 * \brief export the pcmn
 */
$cn=new Database(dossier::id());

$sql="SELECT pcm_val, pcm_lib, pcm_val_parent, pcm_type
  FROM tmp_pcmn order by pcm_val::text";

$array=$cn->get_array($sql);

header('Pragma: public');
header('Content-type: application/csv');
header('Content-Disposition: attachment;filename="pcmn.csv"',FALSE);

for ($i=0;$i<count($array);$i++)
  {
    printf('"%s";"%s";"%s";"%s"'."\n\r",
	   $array[$i]['pcm_val'],
	   $array[$i]['pcm_lib'],
	   $array[$i]['pcm_val_parent'],
	   $array[$i]['pcm_type']);
  }
?>