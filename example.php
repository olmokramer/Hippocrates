<?php
$template = '<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
</head>

<style type="text/css">
	table {
		width: 600px;
		height: auto;
		border: 1px solod black;
		font-family: "Helvetica N&euro;e", Helvetica, Arial, sans-serif;
		margin-bottom: 20px;
		font-weight: 300;
		border-spacing:0;
	}
	
	tr {
		vertical-align:top;
	}
	
	td {
		padding: 5px;
		text-align:right;
		border-spacing:0;
	}
	
	td.item-title {
		font-weight: bold;
	}
	
	td:first-child {
	text-align: left
	}
	
	tr:nth-child(even) {background: #ddd}
	tr:nth-child(odd) {background: #FFF}
	
	tr.divider td {
	border-top: 1px solid black;
	}

</style>

<body>

<div id="wrapper">

	<div id="content">
		<div id="factuur-info">
			<table id="info-table">
				<tr>
                	<td class="item-title">factuurnr </td>
					<td><placeholder label="id"/></td>
                    <td class="item-title">adres</td>
					<td><placeholder label="addresslabel"/></td>
                </tr>
				<tr>
                	<td class="item-title">betreft</td>
					<td><placeholder label="title"/></td>
					<td class="item-title">factuurdatum</td>
					<td><placeholder label="pubdate"/></td>
				</tr>
			</table>
		</div>
		
		<p><br></p>
		
		<div id="prijs-info">
			<table class="info-table">

				<tr>
					<td class="item-title">dienst</td>
					<td class="item-title" colspan="3">stukprijs (&euro;)</td>
					<td class="item-title">uurtarief (&euro;)</td>
					<td class="item-title">totaal</td>
				</tr>

				<tr>
					<td>opnametijd (minuten)</td>
					<td colspan="3"><placeholder label="opnametijd"/></td>
					<td><placeholder label="opnametijd_prijs"/></td>
					<td><placeholder label="opnametijd_totaal"/></td>
				</tr>
				<tr>
					<td>RAW image ontwikkelen</td>
					<td colspan="3"><placeholder label="raw_ontwikkelen"/></td>
					<td><placeholder label="raw_ontwikkelen_prijs"/></td>
					<td><placeholder label="raw_ontwikkelen_totaal"/></td>
				</tr>
				<tr>
                	<td>reiskosten</td>
					<td colspan="3"><placeholder label="reiskosten"/></td>
					<td><placeholder label="reiskosten_prijs"/></td>
					<td colspan="3"><placeholder label="reiskosten_totaal"/></td>
				</tr>
				<tr>
					<td>handling</td>
					<td colspan="3"><placeholder label="handling"/></td>
					<td><placeholder label="handling_prijs"/></td>
					<td colspan="3"><placeholder label="handling_totaal"/></td>
                </tr>
                <tr>
                	<td>prints</td>
					<td colspan="3"><placeholder label="prints"/></td>
					<td><placeholder label="prints_prijs"/></td>
					<td colspan="3"><placeholder label="prints_totaal"/></td>
				</tr>
				<tr>
					<td>diversen</td>
					<td colspan="3"><placeholder label="diversen"/></td>
					<td><placeholder label="diversen_prijs"/></td>
					<td><placeholder label="diversen_totaal"/></td>
				</tr>

				<tr class="divider">
					<td colspan="5">subtotaal</td>
					<td><placeholder label="subtotal"/></td>
				</tr>
				<tr>
					<td colspan="5">btw 21%</td>
					<td><placeholder label="btw"/></td>
				</tr>
				<tr class="divider">
					<td colspan="5">totaal</td>
					<td><placeholder label="total"/></td>
				</tr>
			</table>
		</div>
	</div>

	<modifier>
		<action name="opnametijd_totaal" options="decimals:2" initial-value="1">
			<term operator="mult" fields="opnametijd,opnametijd_prijs"/>
		</action>

		<action name="raw_ontwikkelen_totaal" options="decimals:2" initial-value="1">
			<term operator="mult" fields="raw_ontwikkelen,raw_ontwikkelen_prijs"/>
		</action>

		<action name="reiskosten_totaal" options="decimals:2" initial-value="1">
			<term operator="mult" fields="reiskosten,reiskosten_prijs"/>
		</action>

		<action name="handling_totaal" options="decimals:2" initial-value="1">
			<term operator="mult" fields="handling,handling_prijs"/>
		</action>

		<action name="prints_totaal" options="decimals:2" initial-value="1">
			<term operator="mult" fields="prints,prints_prijs"/>
		</action>

		<action name="diversen_totaal" options="decimals:2" initial-value="1">
			<term operator="mult" fields="diversen,diversen_prijs"/>
		</action>

		<action name="subtotal" options="decimals:2">
			<term operator="add" fields="opnametijd_totaal,raw_ontwikkelen_totaal,reiskosten_totaal,handling_totaal,prints_totaal,diversen_totaal"/>
		</action>

		<action name="btw" options="decimals:2" initial-value="1">
			<term operator="mult" fields="subtotal" multiplier="0.21"/>
		</action>

		<action name="total" options="decimals:2">
			<term operator="add" fields="subtotal,btw"/>
		</action>

	</modifier>
    
</div>

</body>
</html>';

$document = (object) array(
	"id" => "2013-01",
	"addresslabel" => "Wouter Vroege<br>Busken Huetstraat 4-II<br>1054 SZ Amsterdam",
	"title" => "Voorbeeld factuur",
	"pubdate" => "2013-01-10",
	"opnametijd_prijs" => 75,
	"prints" => 100,
	"opnametijd" => 60,
	"raw_ontwikkelen" => 200,
	"handling" => 40,
	"reiskosten" => 19,
	"diversen" => 100,
	"raw_ontwikkelen_prijs" => 30,
	"reiskosten_prijs" => 20,
	"handling_prijs" => 100,
	"prints_prijs" => 500,
	"diversen_prijs" => 250,
);

require("Hippocrates.php");
$hippo = new Hippocrates;
$output = $hippo->generate($template, $document);
echo $output;