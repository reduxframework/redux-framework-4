/**
 * External dependencies
 */
import { kebabCase } from 'lodash';

/**
 * Internal dependencies
 */
import ReduxTemplatesIcon from '../icons';
import exportReusableBlock from './reusable';
import { download } from './file';
/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { withSelect, select } = wp.data;
const { compose } = wp.compose;
const { Fragment } = wp.element;
const { PluginBlockSettingsMenuItem } = wp.editPost;
const { withSpokenMessages } = wp.components;
const { serialize } = wp.blocks;

/**
 * Render plugin
 */
function ExportManager(props) {
	const { selectedBlockCount, selectedBlock, selectedBlocks } = props;

	const saveAsJSON = () => {
		if ( selectedBlockCount < 1 ) {
			return;
		}

		let blocks;
		const title = 'redux_templates/export';

		if ( selectedBlockCount === 1 ) {
			//export as reusable when reusable is selected
			if ( selectedBlock.name === 'core/block' ) {
				exportReusableBlock( selectedBlock.attributes.ref );
				return;
			}

			blocks = serialize( selectedBlock );
		}

		if ( selectedBlockCount > 1 ) {
			blocks = serialize( selectedBlocks );
		}

		//do export magic

		const fileContent = JSON.stringify( {
			__file: 'core_block',
			content: blocks,
		}, null, 2 );

		const fileName = kebabCase( title ) + '.json';
		download( fileName, fileContent, 'application/json' );
	}

	return (
		<Fragment>
			<PluginBlockSettingsMenuItem
				icon={ReduxTemplatesIcon}
				label={ __( 'Export as JSON', 'block-options' ) }
				onClick={ saveAsJSON }
			>

			</PluginBlockSettingsMenuItem>
		</Fragment>
	);
}

export default compose( [
	withSelect( () => {
		const { getSelectedBlockCount, getSelectedBlock, getMultiSelectedBlocks } = select( 'core/block-editor' );
		const { getBlock } = select( 'core/block-editor' );

		return {
			selectedBlockCount: getSelectedBlockCount(),
			selectedBlock: getSelectedBlock(),
			selectedBlocks: getMultiSelectedBlocks(),
			getBlock,
		};
	} ),
	withSpokenMessages,
] )( ExportManager );