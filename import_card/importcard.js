/* 
 * Copyright (C) 2016 Dany De Bontridder <dany@alchimerys.be>
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


Format = {
  save:function(p_name) {
      alert(p_name);
      var request=$(p_name).serialize();
      new Ajax.Request("ajax.php",{
          method:"post",
          parameters:request,
          onSuccess:function(req) {
              smoke.alert(req.responseText)
          }
      });
      return false;
  }  ,
  /** 
   * Apply the format and show the result
   * @returns {Boolean}
   */
  apply:function () {
      var delimiter=$('rdelimiter').value;
     // var fiche_def=$('rfichedef').options[$('rfichedef').selectedIndex].value;
      var encoding=($('encodage').checked)?"Y":"N";
      var surround=$("rsurround").value;
      var skiprow=($('skip_row').checked)?"1":"0";
      var a_head_col = document.getElementsByName("head_col[]");
      var s_head="";
      var sep="";
      var i=0;
      for (i=0;i<a_head_col.length;i++) {
            console.log(a_head_col[i].value);
            s_head += sep + a_head_col[i].value;
            sep=",";
      }
      return true;
  }
    
};