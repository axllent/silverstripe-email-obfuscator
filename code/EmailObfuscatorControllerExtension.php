<?php
/**
 * SilverStripe Email Obfuscator
 * =============================
 *
 * Extension/decorator to automatically encode all email
 * addresses (including mailto: links) in outputted HTML.
 * Switches between ASCII & hexadecimal encoding.
 *
 * Usage: Simply extract to your SilverStripe website path
 * and run a ?flush=1
 *
 * License: MIT-style license http://opensource.org/licenses/MIT
 * Authors: Techno Joy development team (www.technojoy.co.nz)
 */

class EmailObfuscatorControllerExtension extends Extension {

	/*
	 * Inject the EmailObfuscatorRequestProcessor post-processing requirement
	 */
	public function onAfterInit() {
		Injector::inst()->get('RequestProcessor')->setFilters(
			array(new EmailObfuscatorRequestProcessor())
		);
	}

}