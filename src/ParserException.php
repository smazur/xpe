<?php
namespace smazur\xpe;

class ParserException extends \Exception {
	protected $pos;

	public function __construct( $message, $pos, $code = 0, $previous = null ) {
		parent::__construct( $message, $code, $previous );
		$this->pos = $pos;
	}

	public function getPos() {
		return $this->pos;
	}
}
