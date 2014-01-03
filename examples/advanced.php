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
                	<td class="item-title">Invoice No.</td>
					<td><placeholder label="id"/></td>
                    <td class="item-title">Address</td>
					<td><placeholder label="addresslabel"/></td>
                </tr>
				<tr>
                	<td class="item-title">Subject</td>
					<td><placeholder label="subject"/></td>
					<td class="item-title">Date</td>
					<td><placeholder label="pubdate"/></td>
				</tr>
			</table>
		</div>

		<p><br></p>

		<div id="prijs-info">
			<table class="info-table">

				<tr>
					<td class="item-title">service</td>
					<td class="item-title" colspan="3">pieces</td>
					<td class="item-title">price/h (&euro;)</td>
					<td class="item-title">total</td>
				</tr>

				<tr>
					<td>production</td>
					<td colspan="3"><placeholder label="production"/></td>
					<td><placeholder label="production_price" decimals="2"/></td>
					<td><placeholder label="production_total" decimals="2"/></td>
				</tr>
				<tr>
					<td>post-production</td>
					<td colspan="3"><placeholder label="post_production"/></td>
					<td><placeholder label="post_production_price" decimals="2"/></td>
					<td><placeholder label="post_production_total" decimals="2"/></td>
				</tr>
				<tr>
                	<td>travelling expenses</td>
					<td colspan="3"><placeholder label="travel_expenses"/></td>
					<td><placeholder label="travel_expenses_price" decimals="2"/></td>
					<td><placeholder label="travel_expenses_total" decimals="2"/></td>
				</tr>

				<tr class="divider">
					<td colspan="5">subtotal</td>
					<td><placeholder label="subtotal" decimals="2"/></td>
				</tr>
				<tr>
					<td colspan="5">VAT 21%</td>
					<td><placeholder label="vat" decimals="2"/></td>
				</tr>
				<tr class="divider">
					<td colspan="5">total</td>
					<td><placeholder label="total" decimals="2"/></td>
				</tr>
			</table>
		</div>
	</div>

	<hippocrates>
		<setting label="decimals_count" value="0" />
		<setting label="decimals_separator" value="." />
		<setting label="thousands_separator" value="," />

		<calculate label="production_total" initial-value="1">
			<term operator="mult" fields="production,production_price"/>
		</calculate>

		<calculate label="post_production_total" initial-value="1">
			<term operator="mult" fields="post_production,post_production_price"/>
		</calculate>

		<calculate label="travel_expenses_total" initial-value="1">
			<term operator="mult" fields="travel_expenses,travel_expenses_price"/>
		</calculate>

		<calculate label="subtotal">
			<term operator="add" fields="production_total,post_production_total,travel_expenses_total"/>
		</calculate>

		<calculate label="vat" initial-value="0.21">
			<term operator="mult" fields="subtotal"/>
		</calculate>

		<calculate label="total">
			<term operator="add" fields="subtotal,vat"/>
		</calculate>

	</hippocrates>

</div>

</body>
</html>';

$document = (object) array(
	"id" => "2013-01",
	"addresslabel" => "J. Doe<br>3, Main Street<br>12345 Sim City",
	"subject" => "Invoice example",
	"pubdate" => "2013-01-10",
	"production" => 75,
	"production_price" => 100,
	"post_production" => 25,
	"post_production_price" => 30,
	"travel_expenses" => 5,
	"travel_expenses_price" => 150,
);

require("../Hippocrates.php");
$hippo = new Hippocrates;
$output = $hippo->generate($template, $document);
echo $output;
