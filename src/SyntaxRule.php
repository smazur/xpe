<?php
namespace smazur/xpe;

class SyntaxRule {
	public $rule;
	public $match_callback;
	public $error_callback;

	public function __construct( $rule = null ) {
		$this->rule = $rule;
	}

	public function onmatch( $callback ) {
		if( !is_null( $callback ) && !is_callable( $callback ) ) {
			throw new \Exception( 'Match callback must be a valid callback or null' );
		}

		$this->match_callback = $callback;

		return $this;
	}

	public function onerror() {
		$arg = 0;
		$callback = null;
		$args = func_get_args();
		$argn = count( $args );

		foreach( $args as $arg ) {
			if( is_numeric( $arg ) ) {
				$this->error_callback[$arg] = &$callback;
			} else if( is_callable( $arg ) ) {
				$callback = $arg;
			}
		}

		if( $argn == 1 && $callback ) {
			$this->error_callback[0] = $callback;
		}

		return $this;
	}

	public function parse( $tokenizer ) {
		if( is_array( $this->rule ) ) {
			$state = $tokenizer->get_state();
			$parsed = array();

			foreach( $this->rule as $el ) {
				$match = $el->parse( $tokenizer );

				if( false === $match ) {
					$this->handle_error( $parsed, $tokenizer );
					$tokenizer->set_state( $state );
					return false;
				}

				$parsed[] = $match;
			}
		} else {
			$parsed = $this->rule->parse( $tokenizer );
		}

		if( false !== $parsed && $this->match_callback ) {
			$parsed = ($this->match_callback)( $parsed );
		}

		return $parsed;
	}

	public function handle_error( $parsed, $tokenizer ) {
		$parsed_size = sizeof( $parsed );

		if( $parsed_size > 0  ) {
			if( isset( $this->error_callback[0] ) ) {
				call_user_func( $this->error_callback[0], $parsed, $tokenizer );
			}

			if( isset( $this->error_callback[$parsed_size] ) ) {
				call_user_func( $this->error_callback[$parsed_size], $parsed, $tokenizer );
			}
		}
	}

	public function opts( $opts ) {
		foreach( $opts as $opt_name => $opt_val ) {
			if( property_exists( $this, $opt_name ) ) {
				$this->$opt_name = $opt_val;
			}
		}

		return $this;
	}
}
