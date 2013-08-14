<?php
$template = '
	<body>
	<placeholder label="first"/>
	<placeholder label="last"/>
	<placeholder label="bill"/>
	<placeholder label="tip"/>
	<placeholder label="totaal"/>

	<hippocrates>
		<calculate label="totaal">
			<term operator="add" fields="bill,tip"/>
		</calculate>
	</hippocrates>
	';

require("../Hippocrates.php");
$hippo = new Hippocrates;
$document = (object)array(
				"first" => "John",
				"last" => "Doe",
				"bill" => 10,
				"tip" => 5,
			);

$output = $hippo->generate($template, $document);
echo $output;
