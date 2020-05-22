/**
 * WordPress dependencies
 */
import { IconButton } from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import './style.scss'

/**
 * External dependencies
 */
import {ModalManager} from '../modal-manager';
import LibraryModal from '../modal-library';
import { ReduxTemplatesIcon } from '~redux-templates/icons'
const {Component, useEffect} = wp.element;

function ToolbarLibraryButton (props) {
	return (
		<IconButton data-tut="tour__library_button"
			onClick={ () => {
				ModalManager.open(<LibraryModal />);
			} }
			className="sb-insert-library-button"
			label={ __( 'Open Library', redux_templates.i18n ) }
			icon={ <ReduxTemplatesIcon /> }
		>{ __( 'Library', redux_templates.i18n ) }</IconButton>
	)
}

export default ToolbarLibraryButton
