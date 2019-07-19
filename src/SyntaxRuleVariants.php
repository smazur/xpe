<?php

namespace smazur/xpe;

class SyntaxRuleVariants extends SyntaxRule {

	public function parse( $tokenizer ) {
		foreach( $this->rule as $el ) {
			$parsed = $el->parse( $tokenizer );

			if( false !== $parsed ) {
				return $parsed;
			}
		}

		return false;
	}
}
