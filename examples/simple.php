<?php
echo "<pre>";
$template = '
	<body>
	<placeholder label="first"/>
	<placeholder label="last"/>
	<placeholder label="boodschappen"/>
	<placeholder label="fooi"/>
	<placeholder label="totaal"/>
	
	<calculator>
		<action name="totaal">
			<term operator="add" fields="boodschappen,fooi"/>
		</action>
	</calculator>
	</body>
	
	
	';

require("Hippocrates.php");
$hippo = new Hippocrates;
$document = (object)array(
				"first" => "John",
				"last" => "Doe",
				"boodschappen" => 10,
				"fooi" => 5,
			);

$output = $hippo->generate($template, $document);
print_r($output);