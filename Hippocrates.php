<?php
class Hippocrates {

/**
 * Hippocrates
 *
 *
 * LICENSE: This source file is subject the MIT license
 * that is available through the world-wide-web at the following URI:
 * http://opensource.org/licenses/MIT.
 *
 * @category   Utilities
 * @package    Hippocrates
 * @author     Olmo Kramer <olmo.kramer@gmail.com>
 * @author     Wouter Vroege <wouter@woutervroege.nl>
 * @copyright  2013 Olmo Kramer / Wouter Vroege
 * @license    http://opensource.org/licenses/MIT
 * @version    1.0.0
 * @since      File available since Release 1.0.0 ; 2013-01-08
 */

/**
 * possible future functions:
 * str_replace
 * str_validate
 * substr
 * other updates:
 * set division sequence ($value / $this->values['x'] OR $this->values['x'] / $values)
 */

	private $template;
	private $document;
	private $settings;
	private $calculatorXML;
	private $placeholders;
	private $values = array();

	/*
	public
	*/

	public function __construct() {
		$this->setDefaultSettings();
	}

	public function generate($template, $document) {
		$this->template = $template;
		$this->document = $document;
		$this->getCalculatorElement();
		$this->applySettings();
		$this->getPlaceholders();
		$this->parseDocumentValues();
		$this->performCalculations();
		$this->parseValues();
		$this->setDefaultSettings();
		return $this->template;
	}

	/*
	*/

	private function setDefaultSettings() {
		$this->settings = new \stdClass();
		$this->settings->decimals_count = 0;
		$this->settings->decimals_separator = ",";
		$this->settings->thousands_separator = ".";
	}

	/*
	*/

	private function applySettings() {
		$xml = simplexml_load_string($this->calculatorXML);
		$settings = $xml->children()->setting;
		foreach($settings as $setting) {
			$this->applySetting($setting);
		}
	}

	/*
	*/

	private function applySetting($setting) {
		$label = $setting['label'];
		$value = $setting['value'];
		$this->settings->$label = $value;
	}

	/*
	check for all <placeholder> elements in the given $template and return matches as array
	*/

	private function getPlaceholders() {
		$pattern = "/<placeholder.*?label=.*?\/>/i";
		preg_match_all($pattern, $this->template, $matches);
		$this->placeholders = $matches[0];
	}

	/*
	parse document placeholders
	*/

	private function parseDocumentValues() {
		foreach($this->document as $fieldName => $fieldValue) {
			switch(gettype($fieldValue)) {
				case 'array':
				case 'object':
					$this->parseDocumentValues($fieldValue);
					break;
				case 'string':
				case 'float':
				case 'integer':
					$this->values[(string)$fieldName] = $fieldValue;
					break;
			}
		}
	}

	/*
	return attribute from xml element
	*/

	private function parseXMLAttribute($xmlElement, $attr) {
		$xml = simplexml_load_string($xmlElement);
		return (isset($xml->attributes()->$attr)) ? (string)$xml->attributes()->$attr : false;
	}

	/*
	*/

	private function performCalculations() {
		$xml = simplexml_load_string($this->calculatorXML);
		$calculations = $xml->children()->calculate;
		foreach($calculations as $calculation) {
			$this->performCalculation($calculation);
		}
	}

	/*
	*/

	private function performCalculation($calculation) {
		$value = (isset($calculation['initial-value'])) ? $this->getInitialValue((string)$calculation['initial-value']) : 0;
		$label = $calculation['label'];
		$terms = $calculation->children()->term;

		foreach($terms as $term) {
			$value = $this->performTerm($term, $value);
		}
		$this->values[(string)$label] = (string)$value;
	}

	/*
	*/

	private function performTerm($term, $value) {

		$operator = (string)$term->attributes()->operator;
		$multiplier = (isset($term->attributes()->multiplier)) ? (float)$term->attributes()->multiplier : 1;
		$fields = explode(",", $term->attributes()->fields);

		foreach($fields as $field) {

			switch(strtolower($operator)) {
				case 'add':
					$value += (float)$this->values[$field];
					break;
				case 'subtract':
				case 'sub':
					$value -= (float)$this->values[$field];
					break;
				case 'multiply':
				case 'mult':
					$value *= (float)$this->values[$field];
					break;
				case 'divide':
				case 'div':
					$value /= (float)$this->values[$field];
					break;
				case 'power':
				case 'pow':
					$value ^= (float)$this->values[$field];
					break;
				default:
					die("operation not supported");
					break;
			}
			$value *= $multiplier;
		}
		if(isset($term->attributes()->inverted)) $value = 1 / $value;

		return $value;

	}

	/*
	*/

	private function getInitialValue($value) {
		if(is_numeric($value)) {
			return (float) $value;
		} elseif(isset($this->values[$value])) {
			return (float) $this->values[$value];
		} else {
			return 0;
		}
	}

	/*
	*/

	private function getCalculatorElement() {
		$pos = strpos($this->template, '<hippocrates>');
		if($pos !== false) {
			$len = strpos($this->template, '</hippocrates>') - $pos + strlen('</hippocrates>');
			$this->calculatorXML = substr($this->template, $pos, $len);
		} else {
			$this->calculatorXML = "<hippocrates></hippocrates>";
		}
	}

	/*
	*/

	private function parseValues() {
		foreach($this->placeholders as $placeholder) {
			$label = $this->parseXMLAttribute($placeholder, "label");
			$replace = $this->values[$label];
			$dec = $this->parseXMLAttribute($placeholder, "decimals");
			if(!$dec) $dec = (int) $this->settings->decimals_count;
			$replace = (is_numeric($replace)) ? number_format($replace, $dec, $this->settings->decimals_separator, $this->settings->thousands_separator) : $replace;
			$this->template = str_replace($placeholder, $replace, $this->template);
		}
		$this->template = str_replace($this->calculatorXML, "", $this->template);
	}
}
?>
