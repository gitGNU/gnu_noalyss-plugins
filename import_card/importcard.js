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
  save:function() {
      var p_name=$("format_name").value;
      if (String.trim(p_name)==0) {
          smoke.alert("Nom est vide");
          return false;
      }
      if ( ! $('format_save_id') )
      {
          smoke.alert("format_save_id not found");
          return false;
      }
      var request={
          ac:js_ac,
          gDossier:js_dossier,
          plugin_code:js_plugin_code,
          format_name:p_name,
          format_id:$('format_save_id').value,
          "format_save":1
      };
      new Ajax.Request("ajax.php",{
          method:"post",
          parameters:request,
          onSuccess:function(req) {
              $('format_save_div').update(req.responseText);
          }
      });
      return false;
  }  ,
  /** 
   * Apply the format and show the result
   * @returns {Boolean}
   */
  apply:function () {
      // Take the selected format_id
      //      // var fiche_def=$('rfichedef').options[$('rfichedef').selectedIndex].value;

      var format_id=$('template_id').options[$('template_id').selectedIndex].value;
      
      // create request
      var request={
          ac:js_ac,
          gDossier:js_dossier,
          plugin_code:js_plugin_code,
          format_id:format_id,
          getFormat:1
      };
      
      // Send it to ajax 
      new Ajax.Request("ajax.php",{
          method:"get",
          parameters:request,
          onSuccess:function(req) {
              // Parse the answer
              var json=req.responseText.evalJSON(true);
              console.log(json);
              $("rdelimiter").value=json.rdelimiter;
              $("encodage").checked=(json.encodage=="Y")?true:false;
              // Problème avec le double quote
              // $("rsurround").value=json.rsurround; 
              $("skip_row").value=json.skip_row;
              console.log("skip row"+json.skip_row);
              var i=0;var e=0;
              var header=document.getElementsByName("head_col[]");
              console.log(header);
              for (i=0;i<header.length;i++){
                  var t=header[i];
                  if ( json.f_position[i] <= header.length) 
                  {
                      
                    for (e=0;e<t.options.length;e++ ) {
                        if ( t.options[e].value==json.f_position[i]) {
                            t.selectedIndex=e;
                        }
                    }   
                  } else {
                      t.selectedIndex=-1;
                  }
                  console.log(json.f_position[i]);
              }
              
          }
      });
      return true;
      
  }
    
};