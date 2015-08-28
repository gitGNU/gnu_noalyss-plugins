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
function modify_intervat_assujetti(p_dossier, p_ac, p_plugin_code, c_id)
{
    try
    {
        var querystring = 'plugin_code=' + p_plugin_code + '&ac=' + p_ac + '&gDossier=' + p_dossier + '&act=modify_intervat_assujetti' + '&c_id=' + c_id;
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_get_predef,
                    onSuccess: function(req) {
                        remove_waiting_box();
                        var answer = req.responseText;
                        var position = fixed_position(451, 217);
                        add_div({'id': 'modify_intervat_assujetti_div',
                            'cssclass': 'inner_box', 'drag': 1, 'style': 'min-width:25%;'+position});

                        $('modify_intervat_assujetti_div').innerHTML = answer;
                        answer.evalScripts();
                    }
                }
        );

    } catch (e)
    {
        alert_box(e.message);
    }
}
function save_intervat_assujetti()
{
    try
    {
        var qs = $("save_intervat_assujetti_frm").serialize() + '&act=save_intervat_assujetti';
        waiting_box();
        var action = new Ajax.Request(
                "ajax.php",
                {
                    method: 'get',
                    parameters: qs,
                    onFailure: error_get_predef,
                    onSuccess: function(req, json) {
                        try {
                            remove_waiting_box();
                            var answer = req.responseXML;
                            var acode = answer.getElementsByTagName('code');
                            var action = answer.getElementsByTagName('action');
                            var html = answer.getElementsByTagName('html');
                            var rowid = answer.getElementsByTagName('rowid');

                            if (acode.length == 0) {
                                var rec = req.responseText;
                                alert_box('erreur :' + rec);
                            }
                            var code = acode[0].firstChild.nodeValue;
                            var code_xml = getNodeText(html[0]);
                            var code_html = unescape_xml(code_xml);
                            var str_action = action[0].firstChild.nodeValue;
                            var str_rowid = rowid[0].firstChild.nodeValue;

                            if (code == 'ok')
                            {
                                removeDiv('modify_intervat_assujetti_div');
                                var row = $('tr_' + str_rowid);
                                if (str_action == 'EFF') {
                                    row.cells[row.cells.length - 1].innerHTML = "";
                                    row.style.textDecoration = 'line-through';
                                } else {
                                    row.innerHTML = code_html;
                                }

                            }
                            if (code == 'nok')
                            {
                                // montre erreur
                                $('modify_intervat_assujetti_div_comment').innerHTML = code_html;
                            }
                        }
                        catch (e) {
                            alert_box("modify_intervat_assujetti_div " + e.message);
                            return false;
                        }

                    }
                }
        );
    }
    catch (e)
    {
        alert_box(e.message);
        return false;
    }

    return false;
}

