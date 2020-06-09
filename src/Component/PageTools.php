<?php

namespace MediaWiki\Skin\WikimediaApiPortal\Component;

use Html;
use MediaWiki\MediaWikiServices;
use MediaWiki\Skin\WikimediaApiPortal\OOUI\ButtonMenuSelectWidget;
use MediaWiki\Skin\WikimediaApiPortal\OOUI\MenuOptionWidget;
use Message;
use OOUI\ButtonGroupWidget;
use OOUI\ButtonWidget;
use Skins\Chameleon\Components\Component;
use Skins\Chameleon\IdRegistry;
use Title;

class PageTools extends Component {
	/**
	 * Allowed page actions with config overrides
	 *
	 * @var array
	 */
	private $actions = [
		'views' => [
			'edit' => [
				'visible' => [ 'view' ],
				'icon' => 'edit',
				'section' => 'primary',
			],

		],
		'actions' => [
			'move' => [
				'visible' => [ 'view' ],
				'section' => 'secondary',

			],
			'delete' => [
				'visible' => [ 'view' ],
				'flags' => 'destructive',
				'section' => 'secondary',
			],
		],
	];

	/**
	 * @param bool $mobile
	 * @param array|null $options
	 * @return string
	 */
	public function getHtml( $mobile = false, $options = [] ) {
		if ( !$this->shouldShow() ) {
			return '';
		}

		$mainClasses = [ 'wm-page-actions container d-none' ];
		if ( $mobile ) {
			$mainClasses += [ 'd-lg-none',  'd-xl-none', 'd-block', 'd-md-block' ];
		} else {
			$mainClasses += [ 'd-sm-none', 'd-md-none', 'd-lg-block',  'd-xl-block' ];
		}
		$html = Html::openElement( 'div', [
			'class' => implode( ' ', $mainClasses )
		] );

		if ( $mobile ) {
			$html .= $this->getDiscussionSwitch();
			$html .= $this->getLastEdit();
			$html .= new ButtonGroupWidget( [
				'items' => $this->getButtons( $mobile, $options )
			] );
		} else {
			$html .= Html::openElement( 'div', [
				'class' => 'row'
			] );

			$html .= Html::openElement( 'div', [
				'class' => 'discussion-switch col'
			] );
			$html .= $this->getDiscussionSwitch();
			$html .= Html::closeElement( 'div' );

			$html .= Html::openElement( 'div', [
				'class' => 'last-edit col'
			] );
			$html .= $this->getLastEdit();
			$html .= Html::closeElement( 'div' );

			$html .= Html::openElement( 'div', [
				'class' => 'page-actions col no-gutters'
			] );
			$html .= new ButtonGroupWidget( [
				'items' => $this->getButtons( $mobile, $options )
			] );
			$secondary = $this->getButtons( $mobile, $options, 'secondary' );
			if ( !empty( $secondary ) ) {
				$html .= $this->getMoreButtons( $secondary );
			}
			$html .= Html::closeElement( 'div' );
			$html .= Html::closeElement( 'div' );
		}

		$html .= Html::closeElement( 'div' );

		return $html;
	}

	private function getMoreButtons( $buttons ) {
		$dropdownId = IdRegistry::getRegistry()->getId( 'wm-page-tools-more' );
		$dropdownButton = Html::element( 'button', [
			'id' => $dropdownId,
			'type' => 'button',
			'class' => 'btn dropdown-toggle wm-page-actions-more oo-ui-icon-ellipsis',
			'data-toggle' => 'dropdown',
			'aria-haspopup' => "true",
			'aria-expanded' => "false"
		] );
		$dropdown = Html::openElement( 'div', [
			'class' => 'dropdown-menu',
			'aria-labelledby' => $dropdownId
		] );
		$dropdown .= implode( '' , $buttons );
		$dropdown .= Html::closeElement( 'div' );

		$html = Html::openElement( 'div', [
			'class' => 'btn-group'
		] );
		$html .= $dropdownButton . $dropdown;
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	/**
	 * Get OOUI widgets for available page actions
	 *
	 * @param array|null $options
	 * @param string|null $section
	 * @return array
	 */
	private function getButtons( $includeAll = false, $options = [], $section = 'primary' ) {
		$requestedAction = $this->getRequestedAction();
		$actions = $this->getSkinTemplate()->get( 'content_navigation', null );
		if ( $actions === null ) {
			return [];
		}
		$buttons = [];
		foreach ( $actions as $key => $data ) {
			if ( !isset( $this->actions[$key] ) ) {
				continue;
			}
			foreach ( $actions[$key] as $subKey => $subData ) {
				if ( !isset( $this->actions[$key][$subKey] ) ) {
					continue;
				}

				$actionData = $this->actions[$key][$subKey];
				$visibleOn = $actionData['visible'];
				if ( !in_array( $requestedAction, $visibleOn ) ) {
					continue;
				}

				if ( !$includeAll && $actionData['section'] !== $section ) {
					continue;
				}

				$buttons[] = $this->getButtonForAction( $subData, array_merge( $actionData, $options ) );
			}
		}

		return $buttons;
	}

	private function getButtonForAction( $data, $override = [] ) {
		$data = array_merge(  [
			'id'=> $data['id'],
			'href' => $data['href'],
			'label' => $data['text'],
			'framed' => false
		], $override );

		return new ButtonWidget( $data );
	}

	/**
	 * @return string
	 */
	private function getRequestedAction() {
		return $this->getSkin()->getRequest()->getText( 'action', 'view' );
	}

	/**
	 * Determine if in the current context, buttons should be shown
	 * @return bool
	 */
	private function shouldShow() {
		if ( $this->getSkin()->getTitle()->isSpecialPage() ) {
			return false;
		}

		$permissionManager = MediaWikiServices::getInstance()->getPermissionManager();
		if (
			!$permissionManager->userHasRight( $this->getSkin()->getUser(), 'wa-see-page-actions' )
		) {
			return false;
		}

		return true;
	}

	private function getDiscussionSwitch() {
		$actions = $this->getSkinTemplate()->get( 'content_navigation', null );
		if ( !$this->getSkin()->getTitle()->isTalkPage() ) {
			if ( isset( $actions['namespaces']['talk'] ) ) {
				return $this->getButtonForAction( $actions['namespaces']['talk'], [
					'visible' => [ 'view', 'edit', 'history' ],
					'icon' => 'userTalk',
				] );
			}
		} else {
			if ( isset( $actions['namespaces']['main'] ) ) {
				return $this->getButtonForAction( $actions['namespaces']['main'], [
					'visible' => [ 'view', 'edit', 'history' ],
					'icon' => 'arrowPrevious',
					'label' => Message::newFromKey(
						'wikimediaapiportal-skin-return-to-page-label'
					)->escaped()
				] );
			}
		}
		return '';
	}

	private function getLastEdit() {
		$title = $this->getSkin()->getTitle();
		if( !$title instanceof Title || !$title->exists() ) {
			return '';
		}
		$actions = $this->getSkinTemplate()->get( 'content_navigation', null );
		if ( !isset( $actions['views']['history'] ) ) {
			return '';
		}
		$lastTouched = $title->getTouched();
		if ( $lastTouched === null ) {
			return '';
		}

		$formatted = $this->getSkin()->getLanguage()->userDate(
			$lastTouched, $this->getSkin()->getUser()
		);

		return $this->getButtonForAction( $actions['views']['history'], [
			'icon' => 'history',
			'label' => Message::newFromKey(
				'wikimediaapiportal-skin-updated-ts-label'
			)->params( $formatted )->escaped()
		] );
	}
}
