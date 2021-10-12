<?php
// watch "tail /var/log/httpd/bpkbingo.local-error_log | grep -o 'PHP.*' "
?>

<!DOCTYPE html>
<html lang="de">
<head>
	<title>BPK Bingo</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" href="/style.css">
	<script type="text/javascript" src="/node_modules/jquery/dist/jquery.min.js"></script>
	<script defer src="/node_modules/@fortawesome/fontawesome-free/js/brands.js"></script>
	<script defer src="/node_modules/@fortawesome/fontawesome-free/js/solid.js"></script>
	<script defer src="/node_modules/@fortawesome/fontawesome-free/js/fontawesome.js"></script>
<script>
	// bingos
	window.switch = [];
	window.statements = [];

	bingos = [
		{"0": "0", "1": "1", "2": "2", "3": "3", "4": "4"},
		{"0": "5", "1": "6", "2": "7", "3": "8", "4": "9"},
		{"0": "10", "1": "11", "2": "12", "3": "13", "4": "14"},
		{"0": "15", "1": "16", "2": "17", "3": "18", "4": "19"},
		{"0": "20", "1": "21", "2": "22", "3": "23", "4": "24"},

		{"0": "0", "1": "5", "2": "10", "3": "15", "4": "20"},
		{"0": "1", "1": "6", "2": "11", "3": "16", "4": "21"},
		{"0": "2", "1": "7", "2": "12", "3": "17", "4": "22"},
		{"0": "3", "1": "8", "2": "13", "3": "18", "4": "23"},
		{"0": "4", "1": "9", "2": "14", "3": "19", "4": "24"},

		{"0": "0", "1": "6", "2": "12", "3": "18", "4": "24"},
		{"0": "4", "1": "8", "2": "12", "3": "16", "4": "20"}
	];

	// get groups
	window.groups = [];
	get_groups = (callBack) => {
		$.ajax({
			url: '/api/group/read.php',
			type: "GET",
			dataType: "json",
			cache: false,
			success: function (data) {
				callBack(data);
			}
		});
	}
	get_groups(function(data) {
		window.groups = data.groups;
		create_group_select(data.groups);
		get_statements();
	});

	// get group name
	get_group_name = (id) => {
		return window.groups.find((group) => group.id === id).name_short;
	}

	// create groups select
	create_group_select = (groups) => {
		let sbody = document.getElementById("setS-group-select");

		let option = document.createElement("option");
		option.value = 15;
		option.text = "everybody";
		sbody.appendChild(option);
		for (let i = 0; i < groups.length; i++) {
			option = document.createElement("option");
			option.value = groups[i].id;
			option.text = groups[i].name_short;
			sbody.appendChild(option);
		}
	}


	// get statements
	function get_api_statements(callBack){
		$.ajax({
			url: "/api/statement/read.php",
			type: "GET",
			dataType: "json",
			cache: false,
			success: function (data) {
			  callBack(data.statements);
			}
		});
	}
	
	// get statements -- getting called from get_groups()
	get_statements = () => {
		get_api_statements(function(data){
			window.statements = data;
			create_statements_table(data);
			load_bingo_content(data);
		});
	}

	//create statement table rows
	create_statement_tableRow = (statement, rowN) => {
		// check if button is disabled
		let disabledUp = "enabled";
		let disabledDown = "enabled";
		if ( $.inArray(statement.id+"up", window.disbutton) !=-1) {
			disabledUp = "disabled";
		}
		if ( $.inArray(statement.id+"down", window.disbutton) !=-1) {
			disabledDown = "disabled";
		}

		let tbody = document.getElementById("statements-tbody") 
		let tr
		let td
		let text 
		let button
		let group_name = get_group_name(statement.group_id)
		
		// create row
		tr = tbody.insertRow();
		tr.setAttribute('id', "field-"+rowN);
		tr.setAttribute('stid', statement.id);
		tr.setAttribute('field', rowN);
		tr.setAttribute('class', "st-tr-"+statement.id);
		tr.setAttribute('onclick', "switch_st(this)");
		td = tr.insertCell();
		text = document.createTextNode(statement.statement);
		td.appendChild(text);
		td = tr.insertCell();
		text = document.createTextNode(group_name);
		td.appendChild(text);
		td = tr.insertCell();
		text = document.createTextNode(statement.rank_up);
		td.appendChild(text);
		button = document.createElement("button");
		button.setAttribute("class", "button-rank");
		button.setAttribute('onclick', `rank_statement(${statement.id}, ${statement.rank_up}, "up")`);
		button.setAttribute(disabledUp, "");
		button.innerHTML = '<i class="fas fa-thumbs-up"></i>';
		td.appendChild(button);
		td = tr.insertCell();
		text = document.createTextNode(statement.rank_down);
		td.appendChild(text);
		button = document.createElement("button");
		button.setAttribute("class", "button-rank");
		button.setAttribute('onclick', `rank_statement(${statement.id}, ${statement.rank_down}, "down")`);
		button.setAttribute(disabledDown, "");
		button.innerHTML = '<i class="fas fa-thumbs-down"></i>';
		td.appendChild(button);
	}

	// create statements table
	create_statements_table = (statements) => {
		document.getElementById("statements-tbody").innerHTML = "";
		for(let i = 0; i < statements.length; i++){
			let rowN = i + 25;
			create_statement_tableRow(statements[i], rowN);
		}
	}

	// create new statement
	create_statement = () => {
		let group_id = document.getElementById("setS-group-select").value;
		let statement = document.getElementById("setS-statement").value;
		if (!$('#check-addStatement').prop('checked')) {
			document.getElementsByClassName("info-text")[0].classList.add("warn")
			document.getElementById("msg").innerHTML = "<p>Bitte bestätige dass du die Informationen gelesen hast.</p>";
			return
		}
		if (statement) {
			document.getElementsByClassName("info-text")[0].classList.remove("warn")
			let statementT = {
				statement: statement,
				group_id: group_id,
				rank_up: 10,
				rank_down: 0
			};
			$.ajax({
				url: "/api/statement/create.php",
				type: "POST",
				dataType: 'json',
				contentType: 'application/json',
				data: JSON.stringify(statementT),
				cache: false,
				success: function (data) {

					//create_statement_tableRow(statementT);
					get_statements();

					// clear input fields
					document.getElementById("msg").innerHTML = "<p>Statement hinzugefügt: " + statementT['statement'] + "</p>";
					document.getElementById("setS-group-select").value = 15;
					document.getElementById("setS-statement").value = "";
				}
			});
		}
	}

	window.disbutton = [];
	rank_statement = (id, rank, direction) => {
		window.disbutton.push(id+direction);
		let URL;
		let dataR
		rank = rank + 1;
		if ( direction === "up" ) {
			URL = "/api/statement/rankUp.php";
			dataRank = {
				id: id,
				rank_up: rank
			};
		} else if ( direction === "down" ) {
			URL = "/api/statement/rankDown.php";
			dataRank = {
				id: id,
				rank_down: rank
			};
		}
		$.ajax({
			url: URL,
			type: "POST",
			dataType: 'json',
			contentType: 'application/json',
			data: JSON.stringify(dataRank),
			cache: false,
			success: function (data) {
				get_statements();
			}
		});
	}

	load_video = () => {
		document.body.classList.remove("noScroll");
		document.getElementsByClassName("embed-video-warning")[0].classList.remove("active");
		let link = document.getElementById("video-link").value;
		if ( link.length > 0 ) {
			link = link.replace("watch?v=", "embed/");
			link = link.replace(".youtube.", ".youtube-nocookie.");
			let videoBody = document.getElementById("video-container");
			videoBody.classList.add("active");
			videoBody.innerHTML = `<iframe width="420" height="315" src="${link}"></iframe>`
		}
	}


	get_random = (min, max) => {
		return Math.floor(Math.random() * (max - min + 1) + min);
	}

	// load bingo content - getting called at get_statements()
	load_bingo_content = (statements) => {
		let newStatements = [...statements];
		let button;
		let bIn;
		let ranS;
		let statementR;

		for (let i = 0; i < 25; i++) {
			ranS = get_random(1, newStatements.length);
			ranS = ranS - 1;
			bId = "field-"+i;
			statementR = newStatements[ranS];
			if ($(`.st-tr-${statementR.id}`)) {
				$(`.st-tr-${statementR.id}`).addClass("loaded");
			}
			button = $(`#${bId}`)[0];
			button.setAttribute("stid", statementR.id);
			button.innerHTML = statementR.statement;

			newStatements.splice(ranS, 1);
		}
	}

	// switch mode 
	switch_mode = () => {
		if (document.getElementsByClassName("switch-button")[0].classList.contains("active") && window.switch.length > 0) {
			let ws = window.switch[1];
			let sField_a = $(`[stid="${ws}"]`)[0];
			let sField_b = $(`[stid="${ws}"]`)[1];
			$(sField_a).removeClass("switchon");
			$(sField_b).removeClass("switchon");
			window.switch = [];
		}
		document.getElementsByClassName("statement-table")[0].classList.toggle("switchmode"); 
		document.getElementsByClassName("bingo-content")[0].classList.toggle("switch"); 
		document.getElementsByClassName("switch-button")[0].classList.toggle("active");
	}
	switch_st = (button) => {
		if ($(".statement-table").hasClass("switchmode")) {
			let field = button.getAttribute("field"); 
			let stid = button.getAttribute("stid");

			if ($(`[stid="${stid}"]`).length > 1) {
				let bField = $(`[stid="${stid}"]`)[0];
				field = bField.getAttribute("field");
			}

			window.switch.push(field);
			window.switch.push(stid);
			
			if ( window.switch.length == 2 ) {
				let sField = $(`[stid="${stid}"]`)[0];
				$(sField).addClass("switchon");
				$(button).addClass("switchon")
			}
			if ( window.switch.length > 2 ) {
				switch_fields();
			}
		}
	}
	switch_fields = () => {
			let container = document.getElementsByClassName("bingo-content")[0];

			let id_a = window.switch[1];
			let id_b = window.switch[3];
			let field_a = window.switch[0];
			let field_b = window.switch[2];
			let stid_a = "field-"+field_a;
			let stid_b = "field-"+field_b;
			let button_a = $(`[stid="${id_a}"]`)[0];
			let button_b = $(`[stid="${id_b}"]`)[0];
			let sttr_a = $(`[stid="${id_a}"]`)[$(`[stid="${id_a}"]`).length-1];
			let sttr_b = $(`[stid="${id_b}"]`)[$(`[stid="${id_b}"]`).length-1];
			
			let statement_a = window.statements.filter(el => {return el["id"] === id_a})[0];
			let statement_b = window.statements.filter(el => {return el["id"] === id_b})[0];

			if ($(button_a).length && field_a < 25) {
				document.getElementById(stid_a).innerHTML = statement_b.statement;
				document.getElementById(stid_a).setAttribute("stid", id_b);
			}
			if ($(button_b).length && field_b < 25) {
				document.getElementById(stid_b).innerHTML = statement_a.statement;
				document.getElementById(stid_b).setAttribute("stid", id_a);
			}

			// fix active
			if ($(button_a).hasClass("active") && !$(button_b).hasClass("active")) {
				$(button_b).addClass("active");
				$(button_a).removeClass("active");
			} else if ($(button_b).hasClass("active") && !$(button_a).hasClass("active")) {
				$(button_a).addClass("active");
				$(button_b).removeClass("active");
			}

			// fix loaded
			if ( $(sttr_a).hasClass("loaded") && !$(sttr_b).hasClass("loaded") ) {
				$(sttr_a).removeClass("loaded");
				$(sttr_b).addClass("loaded");
			}
			if ( $(sttr_b).hasClass("loaded") && !$(sttr_a).hasClass("loaded") ) {
				$(sttr_a).removeClass("loaded");
				$(sttr_b).addClass("loaded");
			}
			// fix switchon
			$(button_a).removeClass("switchon");
			$(sttr_a).removeClass("switchon");

			// clear window switch
			window.switch = [];

	}
	// switch buttons
	switch_buttons = (button) => {
		let field = $(button).attr("field");
		let stid = $(button).attr("stid");
		window.switch.push(field);
		window.switch.push(stid);
		let sl = window.switch.length;
		console.log(stid)
		if (sl == 2 ) {
			let sField = $(`[stid="${stid}"]`)[1];
			$(sField).addClass("switchon")
			$(button).addClass("switchon");
		}
		if ( sl == 4 ) {
			switch_fields();
		} 
	}

	// bingo button click
	bingobutton_click = (button) => {
		if (document.getElementsByClassName("bingo-content")[0].classList.contains("switch")) {
			switch_buttons(button);
			return;
		}
		if ($(button).hasClass("active")) {
			$(button).removeClass("active");
			recheck_bingos(button);
		} else {
			$(button).addClass("active");
			check_bingos();
		}
	}

	recheck_bingos = (button) => {
		let field = button.getAttribute("field"); 
		console.log(field);
		for (let i = 0; i < window.gotBingos.length; i++) {
			let br = window.gotBingos[i];
			if ($.inArray(field, br)) {
				window.gotBingos.splice(i, 1);
			}
		}
	}
	
	window.gotBingos = []
	check_bingos = () => {
		let gotBingo = 0;
		let r = 0
		for (let i = 0; i < bingos.length; i++) {
			if (window.gotBingos.indexOf(i) === -1) { 
				
				let row = bingos[i];
				let isBingo = 0;
				for (let c = 0; c < 5; c++) {
					let r = (i+1)*c
					let button = $(`[field="${row[c]}"]`);
					if ($(button).hasClass("active")) {
						isBingo = isBingo+1;
					} else {
						break
					}
					r++
				}
				if (isBingo === 5) {
					window.gotBingos.push(i);
					gotBingo = gotBingo + 1;
					$('#bingo-success').addClass("active");
					document.getElementById("bingo-success-h").innerHTML = `..${window.gotBingos.length} x Bingo..`;
					window.setTimeout(() => { $('#bingo-success').removeClass("active"); },3000)
				}
			}
		}
	}
</script>
</head>

<body>
	<!-- Bingo conteiner -->
	<div class="page">
		<div class="bingo-container">
			<div id="bingo-success">
				<h2 id="bingo-success-h"></h2>
			</div>
			<h1 class="main-header">BPK Bingo</h1>
			<div class="switch-container">
				<p>Switch mode: </p>
				<button onclick='switch_mode()' class="switch-button button-whiteBlue">on	off</button>
			</div>
			<!-- Bingo -->
			<div class="bingo-content">
				<button type="button" onclick="bingobutton_click(this)" id="field-0" field="0" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-1" field="1" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-2" field="2" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-3" field="3" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-4" field="4" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-5" field="5" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-6" field="6" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-7" field="7" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-8" field="8" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-9" field="9" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-10" field="10" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-11" field="11" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-12" field="12" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-13" field="13" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-14" field="14" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-15" field="15" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-16" field="16" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-17" field="17" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-18" field="18" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-19" field="19" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-20" field="20" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-21" field="21" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-22" field="22" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-23" field="23" class="bingo-button"></button>
				<button type="button" onclick="bingobutton_click(this)" id="field-24" field="24" class="bingo-button"></button>
			</div>
			<div id="video-container"></div>
			<div class="embed-video">
				<input id="video-link" type="text" placeholder="Video einbetten" name="embed video input"/>
				<button class="button-whiteBlue" onclick='document.getElementsByClassName("embed-video-warning")[0].classList.add("active"); document.body.classList.add("noScroll");' type="button" name="embed video button">Video laden</button>
			</div>
		</div>



		<!-- Statements -->
		<div id="statements-list">
			<table class="statement-table">
				<thead>
					<tr>
						<th class="th-statement">Statement</th>
						<th class="th-group">Group</th>
						<th class="th-rank"></th>
						<th class="th-rank"></th>
					</tr>
				</thead>			
				<tbody id="statements-tbody">
				</tbody>
			</table>
		</div>
		<div id="statements-add">
			<div class="info-text">
				<button onclick='document.getElementsByClassName("info-text")[0].classList.toggle("active")' class="button-info-toggle">Information zum speichern eines Statements</button>
				<p>Wenn du ein Statement speicherst wird dieses in meiner Datenbank gespeichert und jedem Nutzer dieser Seite zur Verfügung gestellt.<br>Es werden folgende Daten gespeichert: "Automatisch generierte id des Statements", "Dein eingegebenes Statement", "Deine ausgewählte Gruppe", "daumenHoch: 10", "daumenRunter: 0".<br><br>Bitte beachte dass dein Statement in eine BPK passt und <u><b>nach Absenden nur über eine Anfrage an mich gelöscht werden kann</b></u> (Informationen im Impressum).</p>
				<input name="check-info-add-statement" type="checkbox" id="check-addStatement"/>
			</div>
			<form methode="post" action="">
				<input id="setS-statement" placeholder="Dein Statement" name="statement" type="text"/>
				<select id="setS-group-select" class="button-whiteBlue"></select>
				<button type="button" name="add-statement" class="button-whiteBlue" onclick="create_statement()">speichern</button>
			</form>
			<div id="msg"></div>
		</div>
	</div>
	<div class="embed-video-warning">
		<p>Mit dem Einbetten eines Videos gibst du einem Drittanbieter und anderen, mit diesem unter Umständen verbundenen Drittanbietern, ggf. die Möglichkeit, Daten über dich zu sammeln. Welche Daten der Anbieter oder die mit ihm verbundenen Unternehmen sammeln, weiß nur er selbst, die Algorithmen des Anbieters werden womöglich nicht offengelegt. Solltest du das Sammeln der Daten ablehnen, dann drücke bitte abbrechen.</p>
		<button name="button-submit" class="button-whiteBlue" onclick='load_video()'>Weiter</button>
		<button name="button-cancle" class="button-whiteBlue" onclick='document.getElementsByClassName("embed-video-warning")[0].classList.remove("active");  document.body.classList.remove("noScroll");'>Abbrechen</button>
	</div>
	<a class="privacy-link" href="https://bpkbingo.de/datenschutz">Datenschutz</a>
	<a class="imprint-link" href="https://bpkbingo.de/impressum">Impressum</a>
</body>
</html>
<?php

?>
