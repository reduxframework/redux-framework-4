const { registerBlockType } = wp.blocks;
import * as importBlock from './import';

export function registerBlocks() {

    const { name, settings, category } = importBlock;
    registerBlockType( `redux/${ name }`, { category, ...settings } );

}
registerBlocks();
