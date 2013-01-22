	<!-- Popup dialog voor Facebook foto upload -->
	<div id="dialog-fbupload" title="Facebook upload" style="display:none">
		In welk album wilt u de foto?<br/>
		<select name="fbAlbumSelect" id="fbAlbumSelect" onchange="fbAlbumSelect(value)">	
		</select><br/><br/>
	
		<div id="fbNieuwAlbum" style="display:none">
			Ik wil een nieuw album nl:<br/>
			Naam album:<br/>
			<input type='text'  id="fbAlbumNaam" name='albumNaam'"/><br/><br/>
		</div>
		
		Zeg iets over de foto:<br/>
		<textarea name='opmerking' id="opmerking" rows='3' cols='33.5' style="resize:none"></textarea>
	</div>
	
	<!-- Popup dialog als het uploaden naar Facebook geslaagd is -->
	<div id="fbUploaderFinish" title="Facebook uploaden klaar" style="display:none">
	 	Het uploaden van de foto naar Facebook is geslaagd!<br /><br />
	 	<span class="ui-icon ui-icon-alert" style="float:left; margin:0 9px 50px 0;"></span>
	 	LET OP! Als u de foto heeft upgeload naar een al bestaand album, moet u eerst de foto goedkeuren in het album op Facebook!
	</div>
	
	<!-- Popup dialog als het uploaden naar Facebook mislukt is -->
	<div id="fbUploadererror" title="OEPS! Foutje" style="display:none">
		<span class="ui-icon ui-icon-alert" style="float:left; margin:0 9px 50px 0;"></span>
		Het uploaden van de foto naar Facebook is niet goed gegaan!<br /><br />
		Probeer het opnieuw!
	</div>
	