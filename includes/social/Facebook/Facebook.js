// Variable die belangrijk is voor het afbreken van de script op wens van de gebruiker
var run = "";
// Functie voor als de gebruiker op het Facebook icontje klikt
function Facebook(fotoData)
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
	$.get("../includes/social/Facebook/upload.php",{functie: "checkUser", fotonaam: fotoData}, users_Facebook_check, 'json');
}
//Functie die checkt of de gebruiker is ingelogd op Facebook
function users_Facebook_check(userData)
{
	if(userData.loggedIn != 'false')
	{
		fbUpload(userData);
	}
	else
	{
		console.log(userData.login);
		window.open(userData.login);
		$.get("../includes/social/Facebook/upload.php", {functie: "checklogin", fotonaam: userData.fotonaam}, fbLoop, 'json');
	}
}
//Functie die checkt of de gebruiker al klaar is met inloggen
function fbLoop(userData)
{
	if(run != "false")
	{
		if(userData.loggedIn != "false")
		{
			fbUpload(userData);
		}
		else
		{
			setTimeout(function(){$.get("../includes/social/Facebook/upload.php", {functie: "checklogin", fotonaam: userData.fotonaam}, fbLoop, 'json');},3000);
		}
	}
}
//Functie die het upload scherm laat zien
function fbUpload(userData)
{
	var t = '<option disabled="disabled" selected="selected">Selecteer een Facebook album</option><option value="nieuwFbAlbum">Nieuw album</option>';
	for (var i = 0; i < userData.albums.data.length; i++)
	{
		t +='<option value='+userData.albums.data[i].id+'>'+userData.albums.data[i].name+'</option>';
	}
	$('#fbAlbumSelect').html(t);
	
	$('#dialog-fbupload').dialog(
	{
		modal: true,
		resizable: false,
		draggable: false,
		my:'center',
		at:'center',
		width:400,
		height:400,
		buttons:
		{
			"Annuleren": function()
			{
				$(this).dialog("close");
			},
			"Uploaden": function()
			{
				var fbAlbumSelect = $("select#fbAlbumSelect").val();
				var	fbAlbumNaam = $("#fbAlbumNaam").val();
				var opmerking = $("#opmerking").val();
				
				$("#wait-dialog").dialog(
						{
							modal: true,
							resizable: false, 
							draggable: false,
							width:'auto',
							height:90,
							my:'center',
							at:'center',
							open: function(){}
						});
						$('#wait-dialog').dialog('widget').find(".ui-dialog-titlebar").hide();
				$.get("/includes/social/Facebook/upload.php", {functie: "fbUploaden", fotonaam:userData.fotonaam, fbAlbumSelect: fbAlbumSelect, fbAlbumNaam : fbAlbumNaam, opmerking : opmerking}, fbUploaderFinish, 'json');
			}
		}
	});
	$("#wait-dialog").dialog("close");
}
//Functie die de velden om een nieuw album te maken laat zien
function fbAlbumSelect(data)
{
	if(data == "nieuwFbAlbum")
	{
		$("#fbNieuwAlbum").css('display','block');
	}
	else
	{
		$("#fbNieuwAlbum").css('display','none');
	}
}
//Functie die wordt uitgevoerd als het uploaden klaar is
function fbUploaderFinish(upData)
{
	$("#wait-dialog").dialog("close");
	if(upData.uploaden == 'true')
	{
		$('#dialog-fbupload').dialog("close");
		$('#fbUploaderFinish').dialog(
		{
			modal:true,
			resizable:false,
			draggable:false,
			my:'center',
			at:'center',
			width:'367px',
			buttons:
			{
				"Naar webcam": function()
				{
					$(this).dialog("close");
					$('#dialog-foto').dialog("close");
				},
				"Naar Facebook": function()
				{
					window.open('https://facebook.com', '_blank');
				}
			}
		})
	}
	else if(upData.geenalbum == "ja")
	{
		$('#fbUploadererror').html("U heeft geen album uitgekozen!");
		$('#fbUploadererror').dialog(
				{
					modal:true,
					resizable:false,
					draggable:false,
					my:'center',
					at:'center',
					width:300,
					height:130,
					buttons:
					{
						Sluiten: function()
						{
							$(this).dialog("close");
						}
					}
				})
	}
	else
	{
		$('#fbUploadererror').dialog(
		{
			modal:true,
			resizable:false,
			draggable:false,
			my:'center',
			at:'center',
			width:'554px',
			buttons:
			{
				Opnieuw: function()
				{
					$("#wait-dialog").dialog(
							{
								modal: true,
								resizable: false, 
								draggable: false,
								width:'auto',
								height:90,
								my:'center',
								at:'center',
								open: function(){}
							});
							$('#wait-dialog').dialog('widget').find(".ui-dialog-titlebar").hide();
					onThumbClick(upData.fotonaam);
					$(this).dialog("close");
					$("#wait-dialog").dialog("close");
				}
			}
		})
	}
}