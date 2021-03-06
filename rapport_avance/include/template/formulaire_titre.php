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
 * @brief input for title, description
 *
 */
?>
<?php echo HtmlInput::hidden('f_id',$this->f_id)?>
<table>
	<tr>
		<td>
			Nom
		</td>
		<td>
			<?php 
			$title=new IText("f_title",$this->f_title);
			$title->size=50;
			echo $title->input();
			?>
		</td>
	</tr>
	<tr>
		<td>
			Description
		</td>
		<td>
			<?php 
			$description=new IText("f_description",$this->f_description);
			$description->size=100;
			echo $description->input();
			?>
		</td>
	</tr>
</table>
