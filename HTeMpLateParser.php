<?php
error_reporting(0);
class HTeMpLateParser {

/**
 * HTeMpLateParser
 *
 *
 * LICENSE: This source file is subject the MIT license
 * that is available through the world-wide-web at the following URI:
 * http://opensource.org/licenses/MIT.
 *
 * @category   Utilities
 * @package    HTeMpLateParser
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
	private $calculatorXML;
	private $placeholders;
	
	private $values = array();
	
	/*
	public
	*/
	
	public function generate($template, $document) {
		
		$this->template = $template;
		$this->document = $document;
		$this->calculatorXML = $this->getCalculatorXML();
		
		$this->getPlaceholders();
		$this->parseDocumentValues($this->document);
		$this->parseCalculatorValues();
		$this->performCalculations();
		$this->parseValues();
		return $this->template;
	}
	
	/*
	check for all <placeholder> elements in the given $template and return matches as array
	*/
	
	private function getPlaceholders() {
		$pattern = "/<placeholder.*?src=\"calculator\".*?\/>/i";
		preg_match_all($pattern, $this->template, $this->placeholders->calculator);
		$pattern = "/<placeholder.*?/>/i";
		preg_match_all($pattern, $this->template, $this->placeholders->all);
	}
	
	/*
	parse document placeholders
	*/
	
	private function parseDocumentValues($documentValues) {
		foreach($documentValues as $field => $value) {
			switch(gettype($value)) {
				case 'array':
					$this->parseDocumentValues($value);
					break;
				case 'object':
					$this->parseDocumentValues($value);
					break;
				case 'string':
					$this->values[(string)$field] = $value;
					break;
				case 'float':
					$this->values[(string)$field] = $value;
					break;
				case 'int':
					$this->values[(string)$field] = $value;
					break;
			}
		}
	}

	/*
	parse calculator placeholders
	*/
	
	private function parseCalculatorValues() {
	
		$placeholders = $this->placeholders->calculator;

		foreach($placeholders as $placeholder) {			
			$placeholder = $this->parseXMLAttribute($placeholder, "label");
			$this->values[(string)$placeholder] = null;
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
		$actions = $xml->children()->action;
		foreach($actions as $action) {
			$this->performCalculation($action);
		}
	}
	
	/*
	*/
	
	private function performCalculation($action) {
				
		$value = (isset($action['initial-value'])) ? $action['initial-value'] : 0;
		$options = (isset($action['options'])) ? $this->parseOptions($action['options']) : 0;
		
		$name = $action['name'];		
		$terms = $action->children()->term;

		foreach($terms as $term) {
			$value = $this->performTerm($term, $value);
		}
		
		$this->values[(string)$name] = (string)$value;
	}
	
	/*
	*/
	
	private function performTerm($term, $value) {
		
		$operator = (string)$term->attributes()->operator;
		$multiplier = (isset($term->attributes()->multiplier)) ? (float)$term->attributes()->multiplier : 1;
		$fields = explode(",", $term->attributes()->fields);
		
		foreach($fields as $field) {
			
			switch($operator) {
				case 'add':
					$value += (float)$this->values[$field];
					break;
				case 'sub':
					$value -= (float)$this->values[$field];
					break;
				case 'mult':
					$value *= (float)$this->values[$field];		
					break;
				case 'div':
					$value /= (float)$this->values[$field];
					break;
				case 'pow':
					$value ^= (float)$this->values[$field];
					break;
				default:
					die("operation not supported");
					break;
			}
			$value *= $multiplier;	
			
		}
		
		return $value;
		
	}
	
	/*
	*/
	
	private function getCalculatorXML() {
		$pos = strpos($this->template, '<calculator>');
		$len = strpos($this->template, '</calculator>') - $pos + strlen('</calculator>');
		$calcXML = substr($this->template, $pos, $len);
		return $calcXML;
	}
	
	/*
	*/
	
	private function parseValues() {
		foreach($this->placeholders->all as $placeholder) {
			$label = parseXMLAttribute($placeholder, 'decimals');
			$value = $this->values[$label];
			$dec = parseXMLAttribute($placeholder, 'decimals');
			if($dec != false) {
				$value = (is_numeric($value)) ? number_format($value, 2, ',', '.') : (string)$value;
			}
			$this->template = preg_replace($placeholder, $value, $this->template);
		}
		/*foreach($this->values as $label => $value) {
			$find = '/<placeholder.*?label="' . (string)$label . '".*?\/>/i';
			$replace = $value;
			$this->template = preg_replace($find, $replace, $this->template);
		}*/
		$this->template = str_replace($this->calculatorXML, '', $this->template);
	}
	
}
?>
