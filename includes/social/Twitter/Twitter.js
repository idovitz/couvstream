// Variable die belangrijk is voor het afbreken van de script op wens van de gebruiker
var run = "";
// Functie voor als de gebruiker op het Twitter icontje klikt
function Twitter(fotodata)
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
	
	$.get("../includes/social/Twitter/upload.php",{functie: "checkUser", fotonaam: fotodata}, users_Twitter_check, 'json');
}
//Functie die checkt of er iets fout gegaan is tijdens het verkrijgen van de request token
function users_Twitter_check(data)
{
	if(data.error != 'true')
	{
		window.open(data.response);
		setTimeout(function(){$.get("../includes/social/Twitter/upload.php",{functie: "getAccess", fotonaam: data.fotonaam, oauth_token: data.oauth_token, oauth_token_secret: data.oauth_token_secret}, twitLoop, 'json')},5000);
	}
	else
	{
		$("#wait-dialog").dialog("close");
		$("#twit-error").html("Bij het verwerken van uw aanvraag is iets fout gegaan. Probeer het opnieuw!");
		$("#twit-error").dialog(
		{
			modal:true,
			resizable:false,
			draggable:false,
			width:305,
			height:220,
			my:'center',
			at:'center',
			open: function(){},
			buttons:
				{
					"Opnieuw":function()
					{
						Twitter(data);
					}
				}
		});
		$('#twit-error').dialog('widget').find(".ui-dialog-titlebar").hide();
	}
}
//Functie die checkt of de gebruiker al klaar is met inloggen
function twitLoop(data)
{
	if(run != "false")
	{
		if(data.error != 'true')
		{
			tweet_dialog(data);
		}
		else
		{
			setTimeout(function(){$.get("../includes/social/Twitter/upload.php",{functie: "getAccess", fotonaam: data.fotonaam, oauth_token: data.oauth_token, oauth_token_secret: data.oauth_token_secret}, twitLoop, 'json');},3000);
		}
	}
}
//Functie die het upload scherm laat zien
function tweet_dialog(data)
{
	$("#wait-dialog").dialog("close");
	var t = "<label class='twit-ingelogd'>Ingelogd als: <a href='https://twitter.com/"+data.naam+"' target='_blank'>"+data.naam+"</a></label>" +
			"Uw tweet:<br /> <textarea id='tweet-status' rows='2' cols='33.5' style='resize:none'></textarea><br /><br />" +
			"De foto: <br />" +
			"<img onclick='onTwitterClick(\""+data.fotonaam+"\")' src='./tmp/thumb/"+data.fotonaam+".jpg' style='cursor:pointer'/>";
	$("#dialog-tweet").html(t);
	$("#dialog-tweet").dialog(
			{
				modal:true,
				resizable:false,
				draggable:false,
				width:400,
				height:350,
				my:'center',
				at:'center',
				buttons:
				{
					"Annuleren":function()
					{
						$(this).dialog("close");
					},
					"Uploaden":function()
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
						
						var status = $("#tweet-status").val();
						
						$.get("../includes/social/Twitter/upload.php", {functie: "twit-upload", fotonaam: data.fotonaam, token: data.token, secret: data.secret, status: status}, twitUploadGelukt, 'json');
					}
				}
			})
}
//Functie die de foto in het groot laat zien, zonder sociale media knoppen erbij
function onTwitterClick(data)
{
	$("#twitter-foto").html('<img src="./tmp/img/'+data+'.jpg"/>');
	$("#twitter-foto").dialog(
	{
		modal: true,
		resizable: false, 
		draggable: false,
		my:'center',
		at:'center',
		width:785,
		height:685,
		buttons:
		{
			"Sluiten": function()
			{
				$(this).dialog("close");
			}
		}
	});
	$('#twitter-foto').dialog('widget').find(".ui-dialog-content").css("padding", ".4em 0.5em");
	$('#twitter-foto').dialog('widget').find(".ui-dialog-titlebar").hide();
}
//Functie die uitgevoerd wordt als het uploaden klaar is
function twitUploadGelukt(data)
{
	$("#wait-dialog").dialog("close");
	if(data.error != 'true' && data.count != 'yes')
	{
		$("#tweeten-gelukt").dialog(
		{
			modal: true,
			resizable: false, 
			draggable: false,
			my:'center',
			at:'center',
			width:293,
			height:170,
			buttons:
			{
				"Naar webcam": function()
				{
					$(this).dialog("close");
					$("#dialog-tweet").dialog("close");
					$('#dialog-foto').dialog("close");
				},
				"Naar Twitter": function()
				{
					window.open('https://twitter.com', '_blank');
				}
			}
		})
	}
	else if(data.count == 'yes')
	{
		$("#twit-error").html("<span class='ui-icon ui-icon-alert' style='float:left; margin:0 9px 50px 0;'></span>Let op, u mag maximaal 100 karakters gebruiken in verband met de link van de foto die in de tweet komt.<br /><br /> Probeer het opnieuw!");
		$("#twit-error").dialog(
		{
			modal: true,
			resizable: false, 
			draggable: false,
			my:'center',
			at:'center',
			width:400,
			height:205,
			buttons:
			{
				"Sluiten": function()
				{
					$(this).dialog("close");
				}
			}
		});
	}
	else
	{
		$("#twit-error").html("Er is een fout opgetreden bij het tweeten. Probeer het opnieuw!");
		$("#twit-error").dialog(
		{
			modal: true,
			resizable: false, 
			draggable: false,
			my:'center',
			at:'center',
			width:400,
			height:150,
			buttons:
			{
				"Opnieuw":function()
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
					
					var status = $("#tweet-status").val();
					
					$.get("../includes/social/Twitter/upload.php", {functie: "twit-upload", fotonaam: data.fotonaam, token: data.token, secret: data.secret, status: status}, twitUploadGelukt, 'json');
				}
			}
		});
	}
}