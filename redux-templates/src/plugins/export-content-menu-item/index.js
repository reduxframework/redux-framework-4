/**
 * WordPress dependencies
 */
import { withDispatch, withSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { compose, ifCondition } from '@wordpress/compose';
import { useRef } from '@wordpress/element';
import { download } from '../export/file';
const { registerPlugin } = wp.plugins;
const { Fragment } = wp.element;
const { PluginMoreMenuItem } = wp.editPost;

const redux_templatesIcon = <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 19 19">
  <g>
    <path d="M10.9,17.7H7.4l-0.9-1.5l2.1-2.4L10.9,17.7L10.9,17.7z M5.6,16.1l-1.5,1.6H0.1L4,13.3L5.6,16.1L5.6,16.1z"/>
    <polygon points="6.1,15.6 0.4,5.9 3.9,5.9 6.6,10.4 14.6,1.3 18.9,1.3 6.1,15.6 	"/>
    </g>
  </svg>;

function ExportContentMenuItem( { createNotice, editedPostContent } ) {
  const ref = useRef();
  const exportFullpage = () => {
		const fileContent = JSON.stringify( {
			__file: 'core_block',
			content: editedPostContent,
		}, null, 2 );

		const fileName = 'redux-template.json';
		download( fileName, fileContent, 'application/json' );
  }


  return (
    <Fragment>
      <PluginMoreMenuItem
        icon={ redux_templatesIcon }
        role="menuitemcheckbox"
        onClick={ exportFullpage }
      >
        { __( 'Export full page', redux_templates.i18n ) }
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
  registerPlugin('redux-full-template-export', {
     icon: redux_templatesIcon,
     render: ExportContentMenu,
  });
}
