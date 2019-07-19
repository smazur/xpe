<?php
namespace smazur/xpe;

class Token {
	public $code;
	public $content;
	public $value;
	public $pos;
	public $len;
	public $line;
	public $line_offset;

	public function print() {
		echo "Code: {$this->code}, content: '{$this->content}', value: {$this->value}, pos: {$this->pos} \n";
	}
}
