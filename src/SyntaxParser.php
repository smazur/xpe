<?php
namespace smazur\xpe;

class SyntaxParser {
	protected $rules;

	public function __construct() {
		$this->rules = [];
	}

	public function add_token( $name, $code, $content = null ) {
		$rule = new SyntaxRuleToken( $code, $content );
		$this->rules[$name] = $rule;

		return $rule;
	}

	public function add_rule( $name, $rule ) {
		if( !($rule instanceof SyntaxRule) ) {
			$rule = new SyntaxRule( $rule );
		}

		if( !isset( $this->rules[$name] ) ) {
			$this->rules[$name] = $rule;
		} elseif( $this->rules[$name] instanceof SyntaxRuleVariants ) {
			$this->rules[$name]->rule[] = $rule;
		} else {
			$variants = new SyntaxRuleVariants();
			$variants->rule[] = $this->rules[$name];
			$variants->rule[] = $rule;
			$this->rules[$name] = $variants;
		}

		return $rule;
	}

	public function add_list_rule( $name, $elements, $separator ) {
		$rule = new SyntaxRuleList( $elements, $separator );
		$this->rules[$name] = $rule;

		return $rule;
	}

	public function resolve_elements( $elements ) {
		$res = [];

		if( is_array( $elements ) ) {
			foreach( $elements as &$element ) {
				if( is_string( $element ) ) {
					if( !isset( $this->rules[$element] ) ) {
						throw new \Exception( "Unknown rule '{$element}'" );
					}
					$element = $this->rules[$element];
				}
			}
		} else if( is_string( $elements ) ) {
			if( !isset( $this->rules[$elements] ) ) {
				throw new \Exception( "Unknown rule '{$elements}'" );
			}

			$elements = $this->rules[$elements];
		}

		return $elements;
	}

	public function resolve_names() {
		foreach( $this->rules as $rule ) {
			if( $rule instanceof SyntaxRuleToken ) {
				continue;
			}

			if( $rule instanceof SyntaxRuleVariants ) {
				foreach( $rule->rule as $sub_rule ) {
					$sub_rule->rule = $this->resolve_elements( $sub_rule->rule );
				}
				continue;
			}

			if( $rule instanceof SyntaxRuleList ) {
				$rule->rule = $this->resolve_elements( $rule->rule );	
				$rule->separator = $this->resolve_elements( $rule->separator );	
				continue;
			}

			$rule->rule = $this->resolve_elements( $rule->rule );
		}
	}

	public function parse( $name, $tokenizer ) {
		$this->resolve_names();

		if( !isset( $this->rules[$name] ) ) {
			throw new \Exception( "Unknown rule '{$name}'" );
		}

		return $this->rules[$name]->parse( $tokenizer );
	}
}
