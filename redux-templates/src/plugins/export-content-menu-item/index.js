/**
 * WordPress dependencies
 */
import { MenuItem } from '@wordpress/components';
import { withDispatch, withSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useCopyOnClick, compose, ifCondition } from '@wordpress/compose';
import { useRef, useEffect } from '@wordpress/element';

function ExportContentMenuItem( { createNotice, editedPostContent } ) {
	const ref = useRef();
	const hasCopied = useCopyOnClick( ref, editedPostContent );

	useEffect( () => {
		if ( ! hasCopied ) {
			return;
		}

		createNotice( 'info', __( 'Export file created.' ), {
			isDismissible: true,
			type: 'snackbar',
		} );
	}, [ hasCopied ] );

	return (
		<MenuItem ref={ ref }>
			{ hasCopied ? __( 'Copied!' ) : __( 'Export all to JSON' ) }
		</MenuItem>
	);
}

export default compose(
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
	// TODO - Get this working.
	// const redux_templatesIcon = <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 19 19">
	// 	<g>
	// 		<path d="M10.9,17.7H7.4l-0.9-1.5l2.1-2.4L10.9,17.7L10.9,17.7z M5.6,16.1l-1.5,1.6H0.1L4,13.3L5.6,16.1L5.6,16.1z"/>
	// 		<polygon points="6.1,15.6 0.4,5.9 3.9,5.9 6.6,10.4 14.6,1.3 18.9,1.3 6.1,15.6 	"/>
	// 	</g>
	// </svg>
	// const { registerPlugin } = wp.plugins;
	// registerPlugin('redux-full-template-export', {
	// 	icon: redux_templatesIcon,
	// 	render: ExportContentMenuItem,
	// });
}
