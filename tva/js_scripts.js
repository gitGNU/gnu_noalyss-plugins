function show_declaration(p_type,p_id) {
    try {
	$('detail').innerHTML='<image src="image/loading.gif" border="0" alt="Chargement...">';
	$('detail').show();
	$('main').hide();
	var gDossier=$('gDossier').value; var code=$('plugin_code').value;
	var queryString='act=dsp_decl&gDossier='+gDossier+'&plugin_code='+code;
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
	var html=xml.getElementsByTagName('code');
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
function record_writing(plugin,dossier,p_id) {
    // call ajax to fill with form
    query='gDossier='+dossier+'&plugin_code='+plugin+'&act=rw&p_id='+p_id;

    // add a section
    show_box({id:'record_write',html:loading(),cssclass:'inner_box',style:'position:absolute;top:0;left:0%;margin-top:10%;height:80%;margin-left:10%;width:80%;',js_error:null,js_success:success_record_writing,qs:query,fixed:1,callback:'ajax.php'});
}
function remove_form(plugin,dossier,p_id,type) {
    // call ajax to fill with form
    query='gDossier='+dossier+'&plugin_code='+plugin+'&act=rm_form&p_id='+p_id+"&type="+type;

    // add a section
    show_box({id:'remove_form',html:loading(),cssclass:'inner_box',style:'position:absolute;top:0;left:20%;margin-top:10%;',js_error:null,js_success:success_box,qs:query,callback:'ajax.php'});
}

function success_record_writing(req) {
    try{
	var answer=req.responseXML;
	var a=answer.getElementsByTagName('ctl');
	var html=answer.getElementsByTagName('code');
	if ( a.length == 0 ) { var rec=req.responseText;alert ('erreur :'+rec);}
	var name_ctl=a[0].firstChild.nodeValue;
	var code_html=getNodeText(html[0]);

	code_html=unescape_xml(code_html);
	g(name_ctl).innerHTML=code_html;
    }
    catch (e) {
	alert("success_box"+e.message);}
    try{
	code_html.evalScripts();}
    catch(e){
	alert("answer_box Impossible executer script de la reponse\n"+e.message);}
}
function save_write(obj) {
    var query="act=sw&"+$(obj).serialize();
    var action=new Ajax.Request ( 'ajax.php',
				  {
				      method:'get',
				      parameters:query,
				      onFailure:null,
				      onSuccess:success_save_write
				  });
    return false;
}
function success_save_write(req){

    try{
	var answer=req.responseXML;
	var a=answer.getElementsByTagName('ctl');
	var html=answer.getElementsByTagName('code');
	if ( a.length == 0 ) { var rec=req.responseText;alert ('erreur :'+rec);}
	var name_ctl=a[0].firstChild.nodeValue;
	var code_html=getNodeText(html[0]);

	code_html=unescape_xml(code_html);
	g(name_ctl).innerHTML=code_html;
    }
    catch (e) {
	alert("success_box"+e.message);}
    try{
	code_html.evalScripts();}
    catch(e){
	alert("answer_box Impossible executer script de la reponse\n"+e.message);}
}