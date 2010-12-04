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

/*!\file
 * \brief this class manages the installation and the patch of the plugin
 */

class Install_Plugin 
{

  function __construct(& $p_cn) {
    $this->cn=$p_cn;
  }

  /**
   *@brief install the plugin, create all the needed schema, tables, proc
   * in the database
   *@param $p_dossier is the dossier id
   */
  function install() 
  {
    $this->cn->start();
    // create the schema
    $this->create_schema();
 
    $this->cn->commit();
  }
  function create_schema() 
  {
    $this->cn->exec_sql('create schema amortissement');
  }
  function create_table_amortissement()
  {
 
  }
}