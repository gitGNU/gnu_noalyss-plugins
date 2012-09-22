

content[200]="Le code doit être unique pour ce formulaire";
content[201]="Formula TODO";


function rapav_form_def(plugin_code,ac,dossier,f_id)
{
	try
	{
		$('form_list_div').hide();
		$('form_mod_div').innerHTML="";
		$('form_mod_div').show();
		waiting_box();
		var querystring='plugin_code='+plugin_code+'&ac='+ac+'&gDossier='+dossier+'&act=mod_form'+"&f_id="+f_id;
		var action=new Ajax.Request(
			"ajax.php",
			{
				method:'get',
				parameters:querystring,
				onFailure:error_get_predef,
				onSuccess:function(req){
					remove_waiting_box();
					var answer=req.responseText;
					$('form_mod_div').innerHTML=answer;
					answer.evalScripts()
				}
			}
			);
	}
	catch(e)
	{
		alert(e.message);
	}
}
/**
 * @brief Add a row to the table in formulaire_definition
 * table id = table_formulaire_definition_id
 */
function add_row_definition(plugin_code,ac,dossier)
{
	try
	{
		var querystring='plugin_code='+plugin_code+'&ac='+ac+'&gDossier='+dossier+'&act=add_row_definition';
		waiting_box();
		var action=new Ajax.Request(
			"ajax.php",
			{
				method:'get',
				parameters:querystring,
				onFailure:error_get_predef,
				onSuccess:function(req){
					remove_waiting_box();
					var answer=req.responseText;
					var mytable=g("table_formulaire_definition_id").tBodies[0];
					var nNumberRow=mytable.rows.length;
					var oRow=mytable.insertRow(nNumberRow);
					oRow.innerHTML=answer;
				}
			}
			);

	}catch (e)
	{
		alert(e.message);
	}
}

/**
 * @brief Add a row to the table in formulaire_parametre
 * table id = table p_id
 */
function add_param_detail(plugin_code,ac,dossier,p_id)
{
	try
	{
		var querystring='plugin_code='+plugin_code+'&ac='+ac+'&gDossier='+dossier+'&act=add_param_detail'+"&p_id="+p_id;
		waiting_box();
		var action=new Ajax.Request(
			"ajax.php",
			{
				method:'get',
				parameters:querystring,
				onFailure:error_get_predef,
				onSuccess:function(req){
					remove_waiting_box();
					removeDiv('param_detail_div');
					var nTop=calcy(100);
					var nLeft="200px";
					var str_style="top:"+nTop+"px;left:"+nLeft+";width:50em;height:auto";
					add_div({
						id:'param_detail_div',
						style:str_style,
						cssclass:'inner_box',
						drag:1
					});
					$('param_detail_div').innerHTML=req.responseText;
					req.responseText.evalScripts();
				}
			}
			);

	}catch (e)
	{
		alert("add_param_detail"+e.message);
	}
}
function rapav_search_code(ac,plugin_code,dossier,f_id)
{
	try
	{
		var querystring='plugin_code='+plugin_code+'&ac='+ac+'&gDossier='+dossier+'&act=rapav_search_code'+"&f_id="+f_id;
		waiting_box();
		var action=new Ajax.Request(
			"ajax.php",
			{
				method:'get',
				parameters:querystring,
				onFailure:error_get_predef,
				onSuccess:function(req){
					remove_waiting_box();
					removeDiv('search_code_div');
					var nTop=calcy(80);
					var nLeft="400px";
					var str_style="top:"+nTop+"px;left:"+nLeft+";width:50em;height:auto;z-index:4";
					add_div({
						id:'search_code_div',
						style:str_style,
						cssclass:'inner_box',
						drag:1
					});
					$('search_code_div').innerHTML=req.responseText;
				//req.responseText.evalScripts();
				}
			}
			);

	}catch (e)
	{
		alert("add_param_detail"+e.message);
	}
}
function delete_param_detail(plugin_code,ac,dossier,fp_id)
{
	try
	{
		if ( ! confirm("Confirmez-vous l'effacement ?")) { return false;}
		waiting_box();

		var querystring='plugin_code='+plugin_code+'&ac='+ac+'&gDossier='+dossier+'&act=delete_param_detail'+"&fp_id="+fp_id;
		var action=new Ajax.Request(
			"ajax.php",
			{
				method:'get',
				parameters:querystring,
				onFailure:error_get_predef,
				onSuccess:function(req){
					remove_waiting_box();
					$('tr_'+fp_id).style.textDecoration="line-through";
					$('tr_'+fp_id).style.color="red";
					$('del_'+fp_id).innerHTML="";
				}
			}
			);
	}catch (e)
	{
		alert(e);
	}
}
/**
 *@brief Sauve les données
 */
/*function  show_poste(answer) {
	try{
		var answer=req.responseXML;
		var a=answer.getElementsByTagName('ctl');
		var html=answer.getElementsByTagName('code');
		if ( a.length == 0 ) {
			var rec=req.responseText;
			alert ('erreur :'+rec);
		}
		var name_ctl=a[0].firstChild.nodeValue;
		var code_html=html[0].firstChild.nodeValue;
		// ou mieux
		var code_html=getNodeText(html[0]); // Firefox ne prend que les 4096 car.
		code_html=unescape_xml(code_html);
		$(name_ctl).innerHTML=code_html;
	}
	catch (e) {
		alert(e.message);
	}
	try{
		code_html.evalScripts();
	}
	catch(e){
		alert("Impossible executer script de la reponse\n"+e.message);
	}

}*/
/**
 * @brief Add a row to the table in formulaire_parametre
 * table id = table p_id
 */
/*function row_add_code_tva(plugin_code,ac,dossier,p_id)
{
try
	{
		var max=parseFloat($('count_'+p_id).value);
		var querystring='plugin_code='+plugin_code+'&ac='+ac+'&gDossier='+dossier+'&act=row_add_code_tva'+"&p_id="+p_id+"&max="+max;
		waiting_box();
		var action=new Ajax.Request(
			"ajax.php",
			{
				method:'get',
				parameters:querystring,
				onFailure:error_get_predef,
				onSuccess:function(req){
					remove_waiting_box();
					var answer=req.responseText;
					var mytable=g("table_"+p_id).tBodies[0];
					var nNumberRow=mytable.rows.length;
					var oRow=mytable.insertRow(nNumberRow);
					oRow.innerHTML=answer;
					var max2=parseFloat($('count_'+p_id).value)+1;
					$('count_'+p_id).value=max2;
					answer.evalScripts();
				}
			}
			);

	}catch (e)
	{
		alert(e.message);
	}
}*/
/**
 * @brief Add a row to the table in formulaire_parametre, with total
 * table id = table p_id
 */
/*function row_add_compute(plugin_code,ac,dossier,p_id)
{
try
	{
		var max=parseFloat($('count_'+p_id).value);
		var querystring='plugin_code='+plugin_code+'&ac='+ac+'&gDossier='+dossier+'&act=row_add_compute'+"&p_id="+p_id+"&max="+max;
		waiting_box();
		var action=new Ajax.Request(
			"ajax.php",
			{
				method:'get',
				parameters:querystring,
				onFailure:error_get_predef,
				onSuccess:function(req){
					remove_waiting_box();
					var answer=req.responseText;
					var mytable=g("table_"+p_id).tBodies[0];
					var nNumberRow=mytable.rows.length;
					var oRow=mytable.insertRow(nNumberRow);
					oRow.innerHTML=answer;
					var max2=parseFloat($('count_'+p_id).value)+1;
					$('count_'+p_id).value=max2;
					answer.evalScripts();
				}
			}
			);

	}catch (e)
	{
		alert(e.message);
	}
}*/
/**
 * @brief  montre les détails d'un formulaire
 */
function rapav_form_param(plugin_code,ac,dossier,f_id)
{
	try
	{
		$('form_list_div').hide();
		$('form_mod_div').innerHTML="";
		$('form_mod_div').show();
		waiting_box();
		var querystring='plugin_code='+plugin_code+'&ac='+ac+'&gDossier='+dossier+'&act=mod_param'+"&f_id="+f_id;
		var action=new Ajax.Request(
			"ajax.php",
			{
				method:'get',
				parameters:querystring,
				onFailure:error_get_predef,
				onSuccess:function(req){
					remove_waiting_box();
					var answer=req.responseText;
					$('form_mod_div').innerHTML=answer;
					answer.evalScripts()
				}
			}
			);
	}
	catch(e)
	{
		alert(e.message);
	}
}
/**
 * @brief montre le div contenant le type de formule
 */
function show_type_formula(p_toshow)
{
	try
	{
		var div=['new_account_tva_id','new_formula_id','new_compute_id'];
		for (var r =0;r<div.length;r++ ) {
			$(div[r]).hide();
			$(div[r]+'_bt').style.backgroundColor="inherit";

		}
		$(p_toshow).show();
		$(p_toshow+'_bt').style.backgroundColor="red";
	} catch (e)
{
		alert (e.message);
	}

}
/**
 * @brief sauve les données pour nouvelle formule, code,...
 */
function save_param_detail(p_form_id)
{
	try
	{
		var qs=$(p_form_id).serialize()+'&act=save_param_detail';
		waiting_box();
		var action=new Ajax.Request(
			"ajax.php",
			{
				method:'get',
				parameters:qs,
				onFailure:error_get_predef,
				onSuccess:function infodiv(req,json) {
					try{
						remove_waiting_box();
						var answer=req.responseXML;
						var acode=answer.getElementsByTagName('code');
						var ap_id=answer.getElementsByTagName('p_id');
						var html=answer.getElementsByTagName('html');

						if ( acode.length == 0 ) {
							var rec=req.responseText;
							alert ('erreur :'+rec);
						}
						var code=acode[0].firstChild.nodeValue;
						var code_xml=getNodeText(html[0]);
						var code_html=unescape_xml(code_xml);
						if ( code == 'ok')
							{
								var afpid=answer.getElementsByTagName('fp_id');
								var fp_id=afpid[0].firstChild.nodeValue;
								var p_id=ap_id[0].firstChild.nodeValue;
								// Ajoute une ligne avec résultat
								var mytable=g("table_"+p_id).tBodies[0];
								var nNumberRow=mytable.rows.length;
								var oRow=mytable.insertRow(nNumberRow);
								oRow.id="tr_"+fp_id;
								oRow.innerHTML=code_html;

							}
						if (code == 'nok')
							{
								// montre erreur
								$('param_detail_info_div').innerHTML=code_html;
							}
					}
					catch (e) {
						alert("save_param_detail "+e.message);
					}
					try{
						code_html.evalScripts();
					}
					catch(e){
						alert("save_param_detail Impossible executer script de la reponse\n"+e.message);
					}

				}
			}
			);
	}
	catch(e)
	{
		alert(e.message);
	}

	return false;
}