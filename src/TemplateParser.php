<?php

namespace MediaWiki\Skin\WikimediaApiPortal;

use LightnCandy\LightnCandy;
use TemplateParser as TemplateParserBase;

class TemplateParser extends TemplateParserBase {

	/**
	 * @param string $templateDir
	 */
	public function __construct( $templateDir ) {
		parent::__construct( $templateDir );

		$this->compileFlags |= LightnCandy::FLAG_NAMEDARG
			| LightnCandy::FLAG_ADVARNAME | LightnCandy::FLAG_ELSE;
	}
}
