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
	$.get("fotoconfig.php", {functie: "logout"}, onLogout, 'json');
}

function onLogout(data){
	window.location.replace("../index.php?logout=1");
}

//Als er geen error terug komt, foto doorsturen naar andere functie
function onNeemFoto(fotoData)
{
	if(fotoData.error)
	{
		if(fotoData.reason == 'limit'){
			$("#dialog-message").html(
					'<span class="ui-icon ui-icon-alert" style="float:left; margin:0 9px 50px 0;"></span>U kunt maximaal 4 foto\'s maken, verwijder een foto om een andere foto te maken.'
					);
		}else{
			$("#dialog-message").html(
					'<span class="ui-icon ui-icon-alert" style="float:left; margin:0 9px 50px 0;"></span>De camera is tijdelijk buiten gebruik!'
					);
			$("#dialog-message").data("redirect", true);
		}
		
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
					$(this).dialog( "close" );
					if($(this).data("redirect") == true){
						window.location.replace("speed.php");
					}
				}
			}
		});
	}
	else
	{
		jsVulDiv(fotoData);
	}	
}

// code reusage
function plaatsThumb(fotonaam){
	var thumbContainer = $("#divThumbContainer");
	
	var thumb = $("<div/>")
		.attr("class", "thumb1")
		.attr("id", "thumb"+fotonaam)
		.append(
			$("<img />")
				.attr("class", "thumb")
				.attr("title", "Klik op de foto om hem in ware grootte te zien")
				.attr("src", "./tmp/thumb/"+fotonaam+".jpg")
				.append()
		)
		.append(
			$("<div />")
				.attr("id", "socialThumb")
				.attr("class", "socialThumb"+fotonaam)
				.append(
					$("<img />")
						.attr("class", "SocialIcon")
						.attr("id", "delete"+fotonaam)
						.attr("src", "/styles/default/img/delete.png")
						.attr("title", "Verwijderen")
						.attr("alt", "Verwijderen")
						.data("fotonaam", fotonaam)
						.click(
							deleteImage
						)
				)
		);
	
	thumbContainer.append(thumb);
	
	if(socialMedia == "yes"){
		for (var s = 0; s < socialMediaServ.length; s++)
		{
			var socialicon = $("<img />")
				.attr("class", "SocialIcon")
				.attr("src", "/includes/social/"+socialMediaServ[s]+"/icon.png")
				.attr("title", socialMediaServ[s])
				.attr("alt", socialMediaServ[s])
				.attr("onclick", socialMediaServ[s]+"('"+fotonaam+"')");
				
			var imageHtml = '<img class="'+socialMediaServ[s]+'"   />';
			$(".socialThumb"+fotonaam).append(socialicon);
		}
	}
}

//Als pagina wordt herladen, dan de thumbs opnieuw toewijzen aan div
function jsReload(fotoData)
{
	socialMedia = fotoData.socialMedia;
	socialMediaServ = fotoData.socialMediaServ;
	
	for (var i = 0; i < fotoData.foto.length; i++)
	{
		plaatsThumb(fotoData.foto[i])
	}
	
	$("#neemFoto").css("display", "block");
}



//Als foto wordt gemaakt, thumb toewijzen aan div
function jsVulDiv(fotoData)
{
	socialMedia = fotoData.socialMedia;
	socialMediaServ = fotoData.socialMediaServ;
	
	plaatsThumb(fotoData.foto)
	
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
function deleteImage(event)
{
	var fotoImg = $(event.target);
	var fotoNaam = fotoImg.data("fotonaam");
	
	$("#delete"+fotoNaam).css("display", "none");
	$.get("fotoconfig.php",{functie: "delete", fotonaam: fotoNaam});
	$.ajax({
		success: function(){
			$("#thumb"+fotoNaam).slideUp('fast', function() {$(this).remove();});
		}
	})
	$("#neemFoto").css("display", "inline");
}

