// Functie voor als de gebruiker op het Email icontje wordt geklikt
function Email(data)
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
				open: function(){}
			});
			$('#wait-dialog').dialog('widget').find(".ui-dialog-titlebar").hide();
	$.get("../includes/social/Email/upload.php",{functie: "checkUser", fotonaam: data}, users_Email_check, 'json');
}
//Functie die het email dialogje laat zien
function users_Email_check(data)
{
	var naamVerzender = "";
	var emailVerzender = "";
	var emailOntvanger = "";
	var noemFoto = "";
	var emailOnderwerp = "";
	var emailBericht = "";
	
	$("#wait-dialog").dialog("close");
	var t = 'Uw naam:<br /><input type="text" size="40" id="naamVerzender"/><br />' +
				'Uw email:<br /><input type="text" size="40" id="emailVerzender"/><br /><br />' +
				'Email ontvanger:<br /><input type="text" size="40" id="emailOntvanger"/><br /><br />' +
				'Onderwerp:<br /><input type="text" size="40" id="emailOnderwerp"/><br />' +
				'Bericht:<br /><textarea cols="39" rows="5" id="emailBericht"></textarea><br /><br />' +
				'Bijlage:<br />' +
				'<div class="noemFoto">Geef de foto een naam:<br /><input type="text" size="24" id="noemFoto"/></div>' +
				'<img onclick="onEmailClick(\''+data.fotonaam+'\')" src="./tmp/thumb/'+data.fotonaam+'.jpg" style="cursor:pointer"/>';
	$("#email-dialog").html(t);
	$("#email-dialog").dialog(
	{
		modal:true,
		draggable:false,
		resizable:false,
		my:'center',
		at:'center',
		width:460,
		height:650,
		buttons:
		{
			"Annuleren": function()
			{
				$(this).dialog("close");
			},
			"Verzenden": function()
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
				
				naamVerzender = $("#naamVerzender").val();
				emailVerzender = $('#emailVerzender').val();
				emailOntvanger = $('#emailOntvanger').val();
				noemFoto = $('#noemFoto').val();
				emailOnderwerp = $('#emailOnderwerp').val();
				emailBericht = $('#emailBericht').val();
				
				$.get("../includes/social/Email/upload.php", {functie: "emailVerzenden", fotonaam: data.fotonaam, naamVerzender: naamVerzender, emailVerzender: emailVerzender, emailOntvanger: emailOntvanger, noemFoto: noemFoto , emailOnderwerp: emailOnderwerp, emailBericht: emailBericht}, verzendenGelukt, 'json');
			}
		}
	})
}
//Functie die de foto in het groot laat zien, zonder sociale media knoppen erbij
function onEmailClick(data)
{
	$("#email-foto").html('<img src="./tmp/img/'+data+'.jpg"/>');
	$("#email-foto").dialog(
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
	$('#email-foto').dialog('widget').find(".ui-dialog-content").css("padding", ".4em 0.5em");
	$('#email-foto').dialog('widget').find(".ui-dialog-titlebar").hide();
}
//Functie voor als het verzenden gelukt is
function verzendenGelukt(data)
{
	if(data.error != true && data.error1 != true && data.error2 != true)
	{
		$("#wait-dialog").dialog("close");
		$("#email-gelukt").html("De email is succesvol verzonden!");
		$("#email-gelukt").dialog(
		{
			modal:true,
			resizable:false,
			draggable:false,
			my:'center',
			at:'center',
			width:'auto',
			height:'auto',
			buttons:
			{
				"Terug naar de cam": function()
				{
					$(this).dialog("close");
					$("#email-dialog").dialog("close");
					$("#dialog-foto").dialog("close");
				}
			}
		})
	}
	else if(data.error1 == true)
	{
		$("#wait-dialog").dialog("close");
		$("#email-gelukt").html("<span class='ui-icon ui-icon-alert' style='float:left; margin:0 9px 50px 0;'></span> U heeft meer dan 1 emailadres ingevoerd bij het veld: 'Email ontvanger'.");
		$("#email-gelukt").dialog(
		{
			modal:true,
			resizable:false,
			draggable:false,
			my:'center',
			at:'center',
			width:'auto',
			height:'auto',
			buttons:
			{
				"Sluiten": function()
				{
					$(this).dialog("close");
				}
			}
		})
	}
	else if(data.error2 == true)
	{
		$("#wait-dialog").dialog("close");
		$("#email-gelukt").html("<span class='ui-icon ui-icon-alert' style='float:left; margin:0 9px 50px 0;'></span> U heeft niet alle velden ingevuld!");
		$("#email-gelukt").dialog(
		{
			modal:true,
			resizable:false,
			draggable:false,
			my:'center',
			at:'center',
			width:'auto',
			height:'auto',
			buttons:
			{
				"Sluiten": function()
				{
					$(this).dialog("close");
				}
			}
		})
	}
	else
	{
		$("#wait-dialog").dialog("close");
		$("#email-gelukt").html("Bij het verzenden van de email is een fout opgetreden. Probeer het opnieuw.");
		$("#email-gelukt").dialog(
		{
			modal:true,
			resizable:false,
			draggable:false,
			my:'center',
			at:'center',
			width:'auto',
			height:'auto',
			buttons:
			{
				"Opnieuw": function()
				{
					$.get("../includes/social/Email/upload.php", {functie: "emailVerzenden", fotonaam: data.fotonaam, naamVerzender: naamVerzender, emailVerzender: emailVerzender, emailOntvanger: emailOntvanger, emailOnderwerp: emailOnderwerp, emailBericht: emailBericht}, verzendenGelukt);
				}
			}
		})
	}
}