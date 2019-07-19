<?php
namespace smazur\xpe;

class Tokenizer {

	protected $pos;
	protected $len;
	protected $expr;
	protected $parsers;
	protected $skip_tokens;
	protected $current_line;
	protected $current_line_offset;

	public $token_queue;
	public $token_queue_index;
	public $token_queue_len;

	public $token_eof;

	public function __construct( $expr ) {
		$this->pos = 0;
		$this->len = strlen( $expr );
		$this->expr = $expr;

		$this->parsers = array();
		$this->skip_tokens = array();

		$this->token_queue_index = 0;
		$this->token_queue_len = 0;
		$this->token_queue = array();

		$this->current_line = 1;
		$this->current_line_offset = 1;

		$this->token_eof = null;
	}

	public function skip_token( $tok, $skip = true ) {
		$this->skip_tokens[$tok] = $skip;
	}

	public function add_parser() {
		foreach( func_get_args() as $parser ) {
			$this->parsers[] = $parser;
		}
	}

	public function get_token() {
		while( true ) {
			$token =  $this->get_token_from_queue();

			if( !$token ) {
				if( !is_null( $this->token_eof ) ) {
					$eof = new Token();
					$eof->pos = $this->pos;
					$eof->code = $this->token_eof;
					$eof->len = 0;
					$eof->line = $this->current_line;
					$eof->line_offset = $this->current_line_offset;

					return $eof;
				}

				return false;
			}

			if( !empty( $this->skip_tokens[$token->code] ) ) {
				continue;
			}

			return $token;
		}
	}

	public function check_token() {
		$state = $this->get_state();
		$token = $this->get_token();
		$this->set_state( $state );

		return $token;
	}

	public function is_eof() {
		$next_token = $this->check_token();

		if( !is_null( $this->token_eof ) && $next_token ) {
			return $next_token->code === $this->token_eof ? $next_token : false;
		}

		return false === $this->check_token();
	}

	public function get_token_from_queue() {
		if( $this->token_queue_index < $this->token_queue_len ) {
			return $this->token_queue[$this->token_queue_index++];
		}

		$token = $this->parse_token();

		if( !$token ) {
			return false;
		}

		$this->token_queue[] = $token;
		$this->token_queue_len = sizeof( $this->token_queue );
		$this->token_queue_index++;

		return $token;
	}

	public function parse_token() {
		if( $this->pos >= $this->len ) {
			return false;
		}

		$token = false;

		foreach( $this->parsers as $tok_parser ) {
			$token = $tok_parser->parse( $this->expr, $this->pos );

			if( $token ) {
				$this->pos += $token->len;
				break;
			}
		}

		if( !$token ) {
			throw new ParserException( sprintf( 'Unexpected character \'%s\' at %d:%d', $this->expr[$this->pos], $this->current_line, $this->current_line_offset ), $this->pos );
		}

		$this->sync_current_line( $token );

		return $token;
	}

	protected function sync_current_line( $token ) {
		$token->line = $this->current_line;
		$token->line_offset = $this->current_line_offset;

		$nl_count = substr_count( $token->content, "\n" );
		$this->current_line += $nl_count;

		if( $nl_count > 0 ) {
			$nl_last_pos = strrpos( $token->content, "\n" );
			$this->current_line_offset = $token->len - $nl_last_pos;
		} else {
			$this->current_line_offset += $token->len;
		}
	}

	public function get_state() {
		return $this->token_queue_index;
	}

	public function set_state( $state ) {
		$this->token_queue_index = $state;
	}
}
