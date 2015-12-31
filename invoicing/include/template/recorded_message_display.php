<?php

/* 
 * Copyright (C) 2015 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

echo "<p>";
echo _("Envoyé par")." ".$this->message->getp("sender");
echo "</p>";
echo "<p>";
echo _("Sujet")." ".$this->message->getp("subject");
echo "</p>";
echo "<p>";
echo _("Message")." ".$this->message->getp("message");
echo "</p>";