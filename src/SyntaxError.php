<?php
namespace smazur/xpe;

class SyntaxError extends \Exception {
	public $line;
	public $offset;

	public function __construct( $message, $line, $offset, $code = 0, $previous = null ) {
		$this->line   = $line;
		$this->offset = $offset;

		$message = $line . ':' . $offset . ' ' . $message;

		parent::__construct( $message, $code, $previous );
	}
}
