/**
 * WordPress dependencies
 */
import { withDispatch, withSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { compose, ifCondition } from '@wordpress/compose';
import { download } from '../export/file';
const { Fragment } = wp.element;
import { ReduxTemplatesIconColor } from '../../icons';

function ExportContentMenuItem( { createNotice, editedPostContent } ) {
    if (!wp.plugins) return null;

    const { PluginMoreMenuItem } = wp.editPost;

    const exportFullpage = () => {
        const fileContent = JSON.stringify( {
            __file: 'core_block',
            content: editedPostContent,
        }, null, 2 );

        const fileName = 'redux-template-export.json';
        download( fileName, fileContent, 'application/json' );
    }


    return (
        <Fragment>
            <PluginMoreMenuItem
                icon={ ReduxTemplatesIconColor() }
                role="menuitemcheckbox"
                onClick={ exportFullpage }
            >
                { __( 'Export Page', redux_templates.i18n ) }
            </PluginMoreMenuItem>
        </Fragment>
    );
}

const ExportContentMenu = compose(
    withSelect( ( select ) => ( {
        editedPostContent: select( 'core/editor' ).getEditedPostAttribute(
            'content'
        ),
    } ) ),
    withDispatch( ( dispatch ) => {
        const { createNotice } = dispatch( 'core/notices' );

        return {
            createNotice,
        };
    } ),
    ifCondition( ( { editedPostContent } ) => editedPostContent.length > 0 )
)( ExportContentMenuItem );

if (wp.plugins) {
    const { registerPlugin } = wp.plugins;
    registerPlugin('redux-full-template-export', {
         render: ExportContentMenu,
    });
}
