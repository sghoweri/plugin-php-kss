<?php

/*!
 * Pattern Data KSS Plugin Listener Class
 *
 * Copyright (c) 2014 Dave Olsen, http://dmolsen.com
 * Licensed under the MIT license
 *
 * Add the listener and call back to the event dispatcher. Is automatically found & invoked by the dispatcher.
 *
 */

namespace PatternLab\KSSPlugin;

use \PatternLab\PatternData\Event;
use \PatternLab\KSSPlugin\Helper;

class Listener extends \PatternLab\Listener {
	
	/**
	* Add the listeners for this plug-in
	*/
	public function __construct() {
		
		$this->addListener("patternData.codeHelperStart","runHelper");
		
	}
	
	/**
	* Run the KSS Plugin Helper
	* @param  {Object}        the event object with any properties that might need to be passed
	*/
	public function runHelper(Event $event) {
		
		$options = $event->getOptions();
		
		$KSSHelper = new Helper($options);
		$KSSHelper->run();
		
	}
	
}