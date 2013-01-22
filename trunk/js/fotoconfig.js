var socialMedia = "";
var socialMediaServ = "";

$(document).ready()
{
	$.get("fotoconfig.php", {functie: "herladen"}, jsReload, 'json');
}

function neemfoto(button)
{
	$("#neemFoto").css("display", "none");
	$.get("fotoconfig.php", {functie: "maakFoto"}, onNeemFoto, 'json');
}

function logout(event)
{
	$.get("fotoconfig.php", {functie: "logout"});
}
//Als er geen error terug komt, foto doorsturen naar andere functie
function onNeemFoto(fotoData)
{
	if(fotoData.error)
	{
		$("#dialog-message").dialog(
		{
			modal: true,
			resizable: false, 
			draggable: true,
			width:500,
			height:167,
			my:'center',
			at:'center',
			buttons: 
			{
				Sluiten: function() 
				{
					$( this ).dialog( "close" );
				}
			}
		});
	}
	else
	{
		jsVulDiv(fotoData);
	}	
}
//Als pagina wordt herladen, dan de thumbs opnieuw toewijzen aan div
function jsReload(fotoData)
{
	if(fotoData.socialMedia == "no")
	{
		for (var i = 0; i < fotoData.foto.length; i++)
		{
			var imageHtml = $('<div class="thumb1" id="thumb'+fotoData.foto[i]+'"><img onclick="onThumbClick(\''+fotoData.foto[i]+'\')" class="thumb" title="Klik op de foto om hem in ware grootte te zien" src="./tmp/thumb/'+fotoData.foto[i]+'.jpg"/><div id="socialThumb" class="socialThumb'+fotoData.foto[i]+'"><img class="delete" id="delete'+fotoData.foto[i]+'" onclick="deleteImage(\''+fotoData.foto[i]+'\')" src="/styles/default/img/delete.png" title="Verwijderen" alt="Verwijderen"/></div></div>').hide();
			imageHtml.appendTo($("#divThumbContainer")).slideDown('fast');
		}
	}
	else if(fotoData.socialMedia == "yes")
	{
		for (var i = 0; i < fotoData.foto.length; i++)
		{
			var imageHtml = $('<div class="thumb1" id="thumb'+fotoData.foto[i]+'"><img onclick="onThumbClick(\''+fotoData.foto[i]+'\')" class="thumb" title="Klik op de foto om hem in ware grootte te zien" src="./tmp/thumb/'+fotoData.foto[i]+'.jpg"/><div id="socialThumb" class="socialThumb'+fotoData.foto[i]+'"><img class="delete" id="delete'+fotoData.foto[i]+'" onclick="deleteImage(\''+fotoData.foto[i]+'\')" src="/styles/default/img/delete.png" title="Verwijderen" alt="Verwijderen"/></div></div>').hide();
			imageHtml.appendTo($("#divThumbContainer")).slideDown('fast');
		}
	
		for (var i = 0; i < fotoData.foto.length; i++)
		{
			for (var s = 0; s < fotoData.socialMediaServ.length; s++)
			{
				var imageHtml = $('<img class="'+fotoData.socialMediaServ[s]+'" onclick="'+fotoData.socialMediaServ[s]+'(\''+fotoData.foto[i]+'\')" src="/styles/default/img/'+fotoData.socialMediaServ[s]+'.png" title="'+fotoData.socialMediaServ[s]+'" alt="'+fotoData.socialMediaServ[s]+'"/>').hide();
				imageHtml.appendTo($(".socialThumb"+fotoData.foto[i]+"")).slideDown('fast');
			}
		}
	}
	socialMedia = fotoData.socialMedia;
	socialMediaServ = fotoData.socialMediaServ;
	$("#neemFoto").css("display", "block");
}
//Als foto wordt gemaakt, thumb toewijzen aan div
function jsVulDiv(fotoData)
{
	if (fotoData.socialMedia == "no")
	{
		var imageHtml = $('<div class="thumb1" id="thumb'+fotoData.foto+'"><img onclick="onThumbClick(\''+fotoData.foto+'\')" class="thumb" title="Klik op de foto om hem in ware grootte te zien" src="./tmp/thumb/'+fotoData.foto+'.jpg"/><div id="socialThumb" class="socialThumb'+fotoData.foto+'"><img class="delete" id="delete'+fotoData.foto+'" onclick="deleteImage(\''+fotoData.foto+'\')" src="/styles/default/img/delete.png" title="Verwijderen" alt="Verwijderen"/></div></div>').hide();
		imageHtml.appendTo($("#divThumbContainer")).slideDown('fast');
	}
	else if (fotoData.socialMedia == "yes")
	{
		var imageHtml = $('<div class="thumb1" id="thumb'+fotoData.foto+'"><img onclick="onThumbClick(\''+fotoData.foto+'\')" class="thumb" title="Klik op de foto om hem in ware grootte te zien" src="./tmp/thumb/'+fotoData.foto+'.jpg"/><div id="socialThumb" class="socialThumb'+fotoData.foto+'"><img class="delete" id="delete'+fotoData.foto+'" onclick="deleteImage(\''+fotoData.foto+'\')" src="/styles/default/img/delete.png" title="Verwijderen" alt="Verwijderen"/></div></div>').hide();
		imageHtml.appendTo($("#divThumbContainer")).slideDown('fast');
		
		for (var i = 0; i < fotoData.socialMediaServ.length; i++)
		{
			var imageHtml = $('<img class="'+fotoData.socialMediaServ[i]+'" onclick="'+fotoData.socialMediaServ[i]+'(\''+fotoData.foto+'\')" src="/styles/default/img/'+fotoData.socialMediaServ[i]+'.png" title="'+fotoData.socialMediaServ[i]+'" alt="'+fotoData.socialMediaServ[i]+'"/>').hide();
			imageHtml.appendTo($(".socialThumb"+fotoData.foto+"")).slideDown('fast');
		}
	}
	socialMedia = fotoData.socialMedia;
	socialMediaServ = fotoData.socialMediaServ;
	$("#neemFoto").css("display", "block");
}
//Functie voor als je op de thumb klikt
function onThumbClick(fotoNaam)
{
	$("#dialog-foto").html('<img src="./tmp/img/'+fotoNaam+'.jpg"/>');
	
	if(socialMedia == "no")
	{
		$("#dialog-foto").dialog(
		{
			modal: true,
			resizable: false, 
			draggable: false,
			my:'center',
			at:'center',
			open: function(){},
			width:800,
			height:680,
			buttons: 
			{
				"Sluiten": function() 
				{
					$(this).dialog("close");
				}
			}
		});
	}
	else if(socialMedia == "yes")
	{
		$("#dialog-foto").dialog(
		{
			modal: true,
			resizable: false, 
			draggable: false,
			autoOpen: false,
			my:'center',
			at:'center',
			width:785,
			height:640
		});
		
		var dialog_buttons = {};
		for(var i=0;i<socialMediaServ.length;i++)
		{
			(function()
			{
				var socialM = socialMediaServ[i];
				
				dialog_buttons[socialMediaServ[i]]= function()
				{
					$("#wait-dialog").dialog(
					{
						modal: true,
						resizable: false, 
						draggable: false,
						width:'auto',
						height:140,
						my:'center',
						at:'center',
						open: function(){},
						buttons:
						{
							Afbreken:function()
							{
								run = "false";
								$(this).dialog("close");
							}
						}
					});
					$('#wait-dialog').dialog('widget').find(".ui-dialog-titlebar").hide();
					$.get("../includes/social/"+socialM+"/upload.php",{functie: "checkUser", fotonaam: fotoNaam}, window['users_'+socialM+'_check'], 'json');
				}
			})(i);
		};
		dialog_buttons["Sluiten"]= function() 
		{
			$(this).dialog("close");
		};
		$("#dialog-foto").dialog("option", "buttons", dialog_buttons);
		$('#dialog-foto').dialog('widget').find(".ui-dialog-titlebar").hide();
		$('#dialog-foto').dialog('widget').find(".ui-dialog-content").css("padding", ".4em 0.5em");
		$("#dialog-foto").dialog("open");
	}	
	
}
//Functie die uitgevoerd wordt als er op het kruisje geklikt wordt
function deleteImage(fotoNaam)
{
	$("#delete"+fotoNaam).css("display", "none");
	$.get("fotoconfig.php",{functie: "delete", fotonaam: fotoNaam});
	$.ajax({
		success: function(){
			$("#thumb"+fotoNaam).slideUp('fast', function() {$(this).remove();});
		}
	})
	$("#neemFoto").css("display", "inline");
}

