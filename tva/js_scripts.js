function show_declaration(p_type,p_id) {
    try {
	$('detail').innerHTML='<image src="image/loading.gif" border="0" alt="Chargement...">';
	$('detail').show();
	$('main').hide();
	var gDossier=$('gDossier').value; var code=$('plugin_code').value;
	var queryString='?act=dsp_decl&gDossier='+gDossier+'&plugin_code='+code;
	queryString+='&type='+p_type+'&id='+p_id;
	var action=new Ajax.Request ( 'ajax.php',
				  {
				 method:'get',
				 parameters:queryString,
				 onFailure:error_show_declaration,
				 onSuccess:success_show_declaration
			       }
			       );
    } catch(e) { alert('show_declaration '+e.message);}
}
function success_show_declaration(answer) {
    try {
	var xml=answer.responseXML;
	var html=xml.getElementsByTagName('html');
	if ( html.length == 0 ) { var rec=answer.responseText;alert ('erreur :'+rec);}
	var code_html=getNodeText(html[0]);
	code_html=unescape_xml(code_html);
	$('detail').innerHTML=code_html;
	code_html.evalScripts();

    } catch(e) { alert('success_show_declaration '+e.message);}
}
function error_show_declaration() {
    alert('error_show_declaration : ajax not supported');
}