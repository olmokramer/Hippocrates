<?php
$template = '
	<body>
	<placeholder label="first"/>
	<placeholder label="last"/>
	<placeholder label="boodschappen"/>
	<placeholder label="fooi"/>
	<placeholder label="totaal"/>
	
	<hippocrates>
		<calculate label="totaal">
			<term operator="add" fields="boodschappen,fooi"/>
		</calculate>
	</hippocrates>
	';

require("../Hippocrates.php");
$hippo = new Hippocrates;
$document = (object)array(
				"first" => "John",
				"last" => "Doe",
				"boodschappen" => 10,
				"fooi" => 5,
			);

$output = $hippo->generate($template, $document);
echo $output;