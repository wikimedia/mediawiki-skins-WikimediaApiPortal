<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @file
 */
namespace MediaWiki\Skin\WikimediaApiPortal\Component;

use MediaWiki\Context\IContextSource;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Title\Title;
use MediaWiki\User\User;
use OOUI\ButtonGroupWidget;
use OOUI\ButtonWidget;
use Wikimedia\Message\IMessageFormatterFactory;

class PageToolsComponent extends MessageComponent {
	// Allowed page actions with config overrides
	private const PAGE_TOOLS_ALLOWED_LIST = [
		'views' => [
			'edit' => [
				'visible' => [ 'view' ],
				'icon' => 'edit',
				'group' => 'primary',
			],
		],
		'actions' => [
			'move' => [
				'visible' => [ 'view' ],
				'group' => 'secondary',

			],
			'delete' => [
				'visible' => [ 'view' ],
				'flags' => 'destructive',
				'group' => 'secondary',
			],
		],
	];

	/**
	 * @param IMessageFormatterFactory $messageFormatterFactory
	 * @param IContextSource $contextSource
	 * @param PermissionManager $permissionManager
	 * @param string $requestedAction
	 * @param array $actions
	 * @param bool $mobile
	 */
	public function __construct(
		IMessageFormatterFactory $messageFormatterFactory,
		IContextSource $contextSource,
		PermissionManager $permissionManager,
		string $requestedAction,
		array $actions,
		bool $mobile
	) {
		parent::__construct(
			$mobile ? 'PageToolsMobile' : 'PageTools',
			$messageFormatterFactory,
			$contextSource
		);

		if ( $requestedAction !== 'view' && $requestedAction !== 'history' ) {
			$this->args = null;
			return;
		}

		$title = $contextSource->getTitle();

		if ( $title->isSpecialPage() ) {
			$this->args = null;
			return;
		}

		$user = $contextSource->getUser();

		if ( $mobile ) {
			$buttons = $this->getContentNavButtons(
				$title,
				$user,
				$permissionManager,
				$requestedAction,
				'all',
				$actions,
				[ 'framed' => true ]
			);

			$this->args = [
				'html-discussionSwitch' => $this->getDiscussionSwitch(
					$title,
					$requestedAction,
					$actions
				),
				'html-lastEdit' => $this->getLastEdit( $requestedAction, $contextSource, $actions ) ?: '',
				'html-buttons' => new ButtonGroupWidget( [ 'items' => $buttons ] ),
			];

		} else {
			$primary = $this->getContentNavButtons(
				$title,
				$user,
				$permissionManager,
				$requestedAction,
				'primary', $actions
			);
			$secondary = $this->getContentNavButtons(
				$title,
				$user,
				$permissionManager,
				$requestedAction,
				'secondary',
				$actions
			);

			$this->args = [
				'html-discussionSwitch' => $this->getDiscussionSwitch(
					$title,
					$requestedAction,
					$actions
				) ?: '',
				'html-lastEdit' => $this->getLastEdit( $requestedAction, $contextSource, $actions ),
				'html-primaryButtons' => new ButtonGroupWidget( [ 'items' => $primary ] ),
				'secondaryButtons' => $secondary,
			];
		}
	}

	/**
	 * @param Title $title
	 * @param string $requestedAction
	 * @param array $actions
	 * @return ?ButtonWidget
	 */
	private function getDiscussionSwitch(
		Title $title,
		string $requestedAction,
		array $actions
	): ?ButtonWidget {
		if ( $requestedAction === 'view' ) {
			if ( $title->isTalkPage() ) {
				if ( isset( $actions['namespaces']['main'] ) ) {
					return $this->getButtonForContentAction( $actions['namespaces']['main'], [
						'icon' => 'arrowPrevious',
						'label' => $this->formatMessage( 'wikimediaapiportal-skin-return-to-page-label' )
					] );
				}
			} elseif ( isset( $actions['namespaces']['talk'] ) ) {
				return $this->getButtonForContentAction(
					$actions['namespaces']['talk'],
					[ 'icon' => 'speechBubbles' ]
				);
			}
		} elseif ( $requestedAction === 'history' ) {
			return $this->getButtonForContentAction( $actions['views']['view'], [
				'icon' => 'arrowPrevious',
				'label' => $this->formatMessage( 'wikimediaapiportal-skin-return-to-page-label' )
			] );
		}
		return null;
	}

	/**
	 * @param string $requestedAction
	 * @param IContextSource $contextSource
	 * @param array $actions
	 * @return ?ButtonWidget
	 */
	private function getLastEdit(
		string $requestedAction,
		IContextSource $contextSource,
		array $actions
	): ?ButtonWidget {
		if ( $requestedAction !== 'view' ) {
			return null;
		}

		if ( !isset( $actions['views']['history'] ) ) {
			return null;
		}

		$title = $contextSource->getTitle();

		if ( !$title->exists() ) {
			return null;
		}

		$lastTouched = $title->getTouched();
		if ( $lastTouched === null ) {
			return null;
		}

		return $this->getButtonForContentAction( $actions['views']['history'], [
			'icon' => 'history',
			'label' => $this->formatMessage(
				'wikimediaapiportal-skin-updated-ts-label',
				[ $contextSource->getLanguage()->userDate( $lastTouched, $contextSource->getUser() ) ]
			)
		] );
	}

	/**
	 * Get OOUI widgets for available content actions. Helper for PageToolsComponent.
	 * @param Title $title
	 * @param User $user
	 * @param PermissionManager $permissionManager
	 * @param string $requestedAction
	 * @param string $group One of 'primary', 'secondary', or 'all'
	 * @param array $actions
	 * @param array $oouiOptions
	 * @return ButtonWidget[]
	 */
	private function getContentNavButtons(
		Title $title,
		User $user,
		PermissionManager $permissionManager,
		string $requestedAction,
		string $group,
		array $actions,
		array $oouiOptions = []
	): array {
		if ( !$actions ) {
			return [];
		}

		if ( !$permissionManager->userHasRight( $user, 'edit-docs' ) ) {
			$actions['actions'] = [];
		}

		if ( !$permissionManager->userHasRight( $user, 'edit-docs' ) && !$title->isTalkPage() ) {
			return [];
		}

		$buttons = [];
		foreach ( $actions as $sectionKey => $section ) {
			foreach ( $section as $actionKey => $action ) {
				if ( !isset( self::PAGE_TOOLS_ALLOWED_LIST[$sectionKey][$actionKey] ) ) {
					continue;
				}

				$allowedListData = self::PAGE_TOOLS_ALLOWED_LIST[$sectionKey][$actionKey];
				if ( $group !== 'all' && $group !== $allowedListData['group'] ) {
					continue;
				}
				if ( !in_array( $requestedAction, $allowedListData['visible'] ) ) {
					continue;
				}

				$buttons[] = $this->getButtonForContentAction( $action, array_merge(
					$allowedListData,
					$oouiOptions
				) );
			}
		}

		return $buttons;
	}

	/**
	 * Helper for PageToolsComponent.
	 *
	 * @param array $action Data from 'content_navigation' entry
	 * @param array $oouiOptions Override
	 * @return ButtonWidget
	 */
	private function getButtonForContentAction(
		array $action,
		$oouiOptions = []
	): ButtonWidget {
		return new ButtonWidget( $oouiOptions + [
			'id' => $action['id'],
			'href' => $action['href'],
			'label' => $action['text'],
			'framed' => false,
		] );
	}
}
