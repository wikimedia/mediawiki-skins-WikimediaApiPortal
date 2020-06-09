<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Hook\AuthChangeFormFields;

class RemoveRealName {
	public static function callback( $requests, $fieldInfo, &$formDescriptor, $action ) {
		if ( isset( $formDescriptor['realname'] ) ) {
			unset( $formDescriptor['realname'] );
		}

		return true;
	}
}
