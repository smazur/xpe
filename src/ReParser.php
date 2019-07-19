<?php
namespace smazur\xpe;

class ReParser {
	protected $re;
	protected $code;
	protected $value_callback;

	public function __construct( $re, $code, $value_callback = false ) {
		if( $value_callback !== false && !is_callable( $value_callback ) ) {
			throw new Exception( 'Invalid usage' );
		}

		$this->re = $re;
		$this->code = $code;
		$this->value_callback = $value_callback;
	}

	public function parse( $str, $pos = 0 ) {
		if( preg_match( $this->re, $str, $match, 0, $pos ) ) {
			$token = new Token();

			$token->pos  = $pos;
			$token->code = $this->code;
			$token->len  = strlen( $match[0] );
			$token->content = $match[0];

			if( $this->value_callback ) {
				$token->value = ($this->value_callback)( $match );
			} else {
				$token->value = $match[0];
			}

			return $token;
		}

		return false;
	}
}
