import { withSelect } from '@wordpress/data'
import ShareBlockButton from './buttons'
import ReduxTemplatesIcon from './icons'
if (wp.plugins) {
    const { registerPlugin } = wp.plugins;
    const Buttons = withSelect( select => {
        const { getSelectedBlockClientIds } = select( 'core/block-editor' )

        // Only supported by WP >= 5.3.
        if ( ! getSelectedBlockClientIds ) {
            return {}
        }

        return {
            clientIds: getSelectedBlockClientIds(),
        }
    } )( ShareBlockButton )

    registerPlugin( 'redux-templates-share-block-btn', {
        icon: ReduxTemplatesIcon,
        render: Buttons,
    } );
}