<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Hook\AuthChangeFormFields;

class RemoveRealName {
	/**
	 * @param array $requests
	 * @param array $fieldInfo
	 * @param array &$formDescriptor
	 * @param string $action
	 * @return bool
	 */
	public static function callback( $requests, $fieldInfo, &$formDescriptor, $action ) {
		if ( isset( $formDescriptor['realname'] ) ) {
			unset( $formDescriptor['realname'] );
		}

		return true;
	}
}
