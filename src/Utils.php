<?php
namespace smazur\xpe;

class Utils {
	static function pluck_key( $array, $key ) {
		$res = [];

		foreach( $array as $item ) {
			if( is_object( $item ) ) {
				$res[] = @$item->$key;
			} else {
				$res[] = @$item[$key];
			}
		}

		return $res;
	}

	static function last( $array, $deep = true ) {
		if( empty( $array ) ) {
			return null;
		}

		$last = null;

		foreach( $array as $item ) {
			if( !empty( $item ) ) {
				$last = $item;
			}
		}

		if( $deep && is_array( $last ) ) {
			$next_last = self::last( $last, true );
			if( !is_null( $next_last ) ) {
				$last = $next_last;
			}
		}

		return $last;
	}
}
