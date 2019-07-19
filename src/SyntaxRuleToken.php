<?php

namespace smazur\xpe;

class SyntaxRuleToken extends SyntaxRule {
	public $content;

	public function __construct( $token, $content = null ) {
		parent::__construct( $token );

		$this->content = $content;
	}

	public function parse( $tokenizer ) {
		$state = $tokenizer->get_state();
		$token = $tokenizer->get_token();

		if( $this->match_token( $token, $this->rule ) ) {
			$match = $token;
		} else {
			$tokenizer->set_state( $state );
			$match = false;
		}

		if( $match && $this->match_callback ) {
			$match = ($this->match_callback)( $match );
		}

		return $match;
	}

	public function match_token( $token, $match ) {
		if( !$token ) {
			return false;
		}	

		if( $token->code !== $this->rule ) {
			return false;
		}

		if( $this->content ) {
			if( is_array( $this->content ) ) {
				return in_array( $token->content, $this->content );
			} 

			return $token->content == $this->content;
		}

		return true;
	}
}
