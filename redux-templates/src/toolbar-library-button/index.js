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
import {Tooltip} from '@wordpress/components';


function ToolbarLibraryButton (props) {
    return (
        <Tooltip text={`Block Templates for ${redux_templates.theme_name}`}>
            <IconButton data-tut="tour__library_button"
                onClick={ () => {
                    ModalManager.open(<LibraryModal />);
                } }
                className="redux-templates-insert-library-button"
                label={ __( 'Open Library', redux_templates.i18n ) }
                icon={ <ReduxTemplatesIcon /> }
            >{ __( 'Templates', redux_templates.i18n ) }</IconButton>
        </Tooltip>
    )
}

export default ToolbarLibraryButton
