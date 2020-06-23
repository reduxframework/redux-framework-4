/**
 * Internal dependencies
 */
import Edit from './components/edit';
import icon from './icon';
import transforms from './transforms';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;

/**
 * Block constants
 */
const name = 'import';

const category = 'common';

const title = __( 'Redux Import', redux_templates.i18n );

const keywords = [
    __( 'import', redux_templates.i18n ),
    __( 'download', redux_templates.i18n ),
    __( 'migrate', redux_templates.i18n ),
];

const blockAttributes = {
    file: {
        type: 'object',
    },
};

const settings = {

    title,

    description: __( 'Import blocks exported using Redux plugin.', redux_templates.i18n ),

    keywords,

    attributes: blockAttributes,

    supports: {
        align: true,
        alignWide: false,
        alignFull: false,
        customClassName: false,
        className: false,
        html: false,
    },

    transforms,

    edit: Edit,

    save() {
        return null;
    },
};

export { name, title, category, icon, settings };
