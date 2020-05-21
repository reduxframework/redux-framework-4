const { registerPlugin } = wp.plugins;
import { withSelect } from '@wordpress/data'
import ShareBlockButton from './buttons'
import ReduxTemplatesIcon from './icons'
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

registerPlugin( 'reduxtemplates-share-block-btn', {
    icon: ReduxTemplatesIcon,
    render: Buttons,
} );
