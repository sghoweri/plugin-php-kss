<?php

/*!
 * Pattern Data KSS Plugin Helper Class
 *
 * Copyright (c) 2014 Dave Olsen, http://dmolsen.com
 * Licensed under the MIT license
 *
 * Find KSS info and then add it to the relevant patterns in PatternData::$store
 *
 */

namespace PatternLab\KSSPlugin;

use \Scan\Kss\Parser;
use \PatternLab\Config;
use \PatternLab\Data;
use \PatternLab\PatternData;
use \PatternLab\PatternData\Helper as PatternDataHelper;
use \PatternLab\PatternEngine;
use \PatternLab\Template;
use \PatternLab\Timer;

class Helper extends PatternDataHelper {
	
	protected $patternPaths    = array();
	protected $descTemplate    = "";
	protected $exampleTemplate = "";
	
	/**
	* Sets-up some required attributes for the helper to properly run
	*/
	public function __construct($options = array()) {
		
		parent::__construct($options);
		
		$this->patternPaths    = $options["patternPaths"];
		$this->descTemplate    = file_get_contents(__DIR__."/Views/descTemplate.mustache");
		$this->exampleTemplate = file_get_contents(__DIR__."/Views/exampleTemplate.mustache");
		
	}
	
	/**
	* Runs a KSS parser against the project and then checks each pattern against the results
	* to see if any of them have been described via KSS. Modifies PatternData::$store with relevant
	* information to be used later when outputting to the front-end.
	*/
	public function run() {
		
		// various set-up options
		$options                 = array();
		$options["patternPaths"] = $this->patternPaths;
		$patternDataStore        = PatternData::get();
		$stringLoader            = Template::getStringLoader();
		
		// load the pattern loader
		$patternEngineBasePath   = PatternEngine::getInstance()->getBasePath();
		$patternLoaderClass      = $patternEngineBasePath."\Loaders\PatternLoader";
		$patternLoader           = new $patternLoaderClass($options);
		
		// parse all of the CSS in the project
		$kss = new Parser(Config::getOption("sourceDir"));
		
		foreach ($patternDataStore as $patternStoreKey => $patternStoreData) {
			
			if ($patternStoreData["category"] == "pattern") {
				
				$kssSection = "";
				
				try {
					$kssSection = $kss->getSection($patternStoreKey);
				} catch(\Exception $e) {
					$kssSection = "";
				}
				
				// see if this pattern has a section in the loaded KSS
				if (!empty($kssSection)) {
					
					// update the name and desc based on the KSS
					PatternData::setPatternOption($patternStoreKey, "name", $kssSection->getTitle());
					PatternData::setPatternOption($patternStoreKey, "desc", $kssSection->getDescription());
					PatternData::setPatternOption($patternStoreKey, "descExists", true);
					
					// find the kss modifiers
					$modifiers = $kssSection->getModifiers();
					
					if (!empty($modifiers)) {
						
						$patternModifiers = array();
						
						// work through each modifier
						foreach ($modifiers as $modifier) {
							
							$name               = $modifier->getName();
							$class              = $modifier->getClassName();
							$desc               = $modifier->getDescription();
							$code               = "";
							$modifierCodeExists = false;
							
							// if it's not a pseudo class render it
							if (strpos($name,":") === false) {
								
								$data    = Data::getPatternSpecificData($patternStoreKey);
								$data    = array_merge($data,array("styleModifier" => $class));
								
								$srcPath = (isset($patternStoreData["pseudo"])) ? PatternData::getPatternOption($patternStoreData["original"],"pathName") : $patternStoreData["pathName"];
								$code    = $patternLoader->render(array("pattern" => $srcPath, "data" => $data));
								
								$modifierCodeExists    = true;
								
							}
							
							// add pattern modifier info
							$patternModifiers[] = array("modifierName"       => $name,
														"modifierDesc"       => $desc,
														"modifierCode"       => $code,
														"modifierCodeExists" => $modifierCodeExists);
							
							$patternModifiersOutput[] = array("modifierName" => $name,
														      "modifierDesc" => $desc);
							
						}
						
						// this is silly but keeps it looking cleaner to me
						$patternModifierData = array("patternModifiers" => $patternModifiers);
						$patternModifierOutputData = array("patternModifiersExist" => true, "patternModifiers" => $patternModifiersOutput);
						
						// grab extra sections based on KSS syntax
						if (!empty($kssSection->getCompatibility())) {
							$patternModifierData["compatibility"]             = $kssSection->getCompatibility();
							$patternModifierData["compatibilityExists"]       = true;
							$patternModifierOutputData["compatibility"]       = $kssSection->getCompatibility();
							$patternModifierOutputData["compatibilityExists"] = true;
						}
						if (!empty($kssSection->getExperimental())) {
							$patternModifierData["experimental"]              = $kssSection->getExperimental();
							$patternModifierData["experimentalExists"]        = true;
							$patternModifierOutputData["experimental"]        = $kssSection->getExperimental();
							$patternModifierOutputData["experimentalExists"]  = true;
						}
						if (!empty($kssSection->getDeprecated())) {
							$patternModifierData["deprecated"]                = $kssSection->getDeprecated();
							$patternModifierData["deprecatedExists"]          = true;
							$patternModifierOutputData["deprecated"]          = $kssSection->getDeprecated();
							$patternModifierOutputData["deprecatedExists"]    = true;
						}
						if (!empty($kssSection->getParameters())) {
							$results = array();
							$options = $kssSection->getParameters();
							foreach ($options as $option) {
								$results[] = array("name" => $option->getName(), "description" => $option->getDescription());
							}
							$patternModifierData["parameters"]                = $results;
							$patternModifierData["parametersExists"]          = true;
							$patternModifierOutputData["parameters"]          = $results;
							$patternModifierOutputData["parametersExists"]    = true;
						}
						
						// render the views for the plug-in
						$partialViewDescAddition    = $stringLoader->render(array("string" => $this->descTemplate, "data" => $patternModifierData));
						$partialViewExampleAddition = $stringLoader->render(array("string" => $this->exampleTemplate, "data" => $patternModifierData));
						
						// add the views to the appropriate containers in the patterndata::$store
						PatternData::setPatternOptionArray($patternStoreKey, "partialViewDescAdditions", $partialViewDescAddition);
						PatternData::setPatternOptionArray($patternStoreKey, "partialViewExampleAdditions", $partialViewExampleAddition);
						PatternData::setPatternOptionArray($patternStoreKey, "extraOutput", $patternModifierOutputData, "patternLabPluginKSS");
						
					}
					
				}
				
			}
			
		}
		
		unset($kss);
		
	}
	
}

