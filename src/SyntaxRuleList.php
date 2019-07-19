<?php

namespace smazur/xpe;

class SyntaxRuleList extends SyntaxRule {
	public $separator;
	public $separator_opt = false;
	public $separator_return = true;
	public $match_empty = false;

	public function __construct( $rule = null, $separator = null ) {
		parent::__construct( $rule );

		$this->separator = $separator;
	}

	public function parse( $tokenizer ) {
		$list = array();

		while( true ) {
			$item = $this->rule->parse( $tokenizer );

			if( false == $item ) {
				break;
			}

			$list[] = $item;

			$state = $tokenizer->get_state();
			$separator = $this->separator->parse( $tokenizer );

			if( false === $separator ) {
				$tokenizer->set_state( $state );

				if( $this->separator_opt ) {
					continue;
				}

				break;
			}

			if( $this->separator_return ) {
				$list[] = $separator;
			}
		}

		if( empty( $list ) && !$this->match_empty ) {
			return false;
		}

		if( $this->match_callback ) {
			$list = ($this->match_callback)( $list );
		}

		return $list;
	}
}
