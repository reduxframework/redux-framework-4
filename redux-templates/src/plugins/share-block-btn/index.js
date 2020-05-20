const { registerPlugin } = wp.plugins;
import { withSelect } from '@wordpress/data'
import ShareBlockButton from './buttons'
import StarterblocksIcon from './icons'
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

registerPlugin( 'starterblocks-share-block-btn', {
    icon: StarterblocksIcon,
    render: Buttons,
} );
