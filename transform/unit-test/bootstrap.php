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

// Copyright Author Dany De Bontridder danydb@aevalys.eu

$path='/home/dany/developpement/phpcompta/noalyss/include:..:../include:/usr/share/php/PHPUnit';
$old_path=  get_include_path();
set_include_path($old_path.':'.$path);
require_once 'Framework/SelfDescribing.php';
require_once 'Framework/Test.php';
require_once 'Framework/Assert.php';
require_once 'Framework/TestCase.php';
require_once 'class_database.php';
require_once 'class_own.php';

