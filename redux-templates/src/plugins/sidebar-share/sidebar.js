const {__} = wp.i18n;
const {Fragment} = wp.element;
const {PanelBody} = wp.components
const {PluginSidebar, PluginSidebarMoreMenuItem} = wp.editPost;
import sortBy from 'lodash/sortBy';
import map from 'lodash/map';
import {ModalManager} from '../../modal-manager'
import FeedbackDialog from '~redux-templates/modal-feedback';
import {getWithExpiry} from '../../stores/helper';

const options = sortBy(getWithExpiry('page_categories_list'), 'label');
const schema = {
    type: 'object',
    properties: {
        title: {
            type: 'string',
            title: 'Block Title'
        },
        category: {
            type: 'string',
            title: 'Category',
            enum: map(options, 'value'),
            enumNames: map(options, 'label')
        },
        description: {
            type: 'string',
            title: 'Description'
        }
    }
}
const uiSchema = {
    title: {
        classNames: 'fixed-control'
    },
    category: {
        classNames: 'fixed-control'
    },
    description: {
        'ui:widget': 'textarea',
    }
};

export default function Sidebar(props) {
    const onShare = () => {
        ModalManager.openFeedback(<FeedbackDialog 
            title={__('Share this design', redux_templates.i18n)} 
            description={__('We\'re sorry that it took longer than 5 minutes to try our challenge. We aim to ensure our Block Template library is as beginner friendly as possible. Please take a moment to let us know how we can improve our challenge.', redux_templates.i18n)}
            schema={schema}
            uiSchema={uiSchema}
            headerImage='popup-contact.png'
            />)
    }

    return (
        <Fragment>
            <PluginSidebarMoreMenuItem target="redux-templates-share">
                {__('Redux Template', redux_templates.i18n)}
            </PluginSidebarMoreMenuItem>
            <PluginSidebar name="redux-templates-share" title={__('Redux Shares', redux_templates.i18n)}>
                <PanelBody title={__('Share this Design', redux_templates.i18n)} initialOpen={true}>
                    <div className="d-flex justify-content-center">
                        <a className="button button-primary" onClick={onShare}>
                            <i className="fas fa-share"></i>
                            &nbsp;{__('Share this design', redux_templates.i18n)}
                        </a>
                    </div>
                </PanelBody>
            </PluginSidebar>
        </Fragment>
    );
}