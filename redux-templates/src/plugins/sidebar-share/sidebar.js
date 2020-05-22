const {apiFetch} = wp;
const {__} = wp.i18n;
const {Component, Fragment, useState} = wp.element;
const {compose} = wp.compose;
const {PanelBody, Spinner} = wp.components
const {withDispatch, select} = wp.data;
const {PluginSidebar, PluginSidebarMoreMenuItem} = wp.editPost;
import {installedBlocksTypes} from '~redux-templates/stores/actionHelper';
import {Modal, ModalManager} from '../../modal-manager'
import ShareModal from '../share-block-btn/modal'

function Sidebar(props) {

    const {savePost} = props;
    const [loading, setLoading] = useState(false);

    const onShare = () => {
        ModalManager.open(<ShareModal type='page' />);
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
                            {loading ? <i className="fas fa-spinner fa-pulse"/> : <i className="fas fa-share"></i>}
                            &nbsp;{__('Share this design', redux_templates.i18n)}
                        </a>
                    </div>
                </PanelBody>
            </PluginSidebar>
        </Fragment>
    );
}

export default compose([
    withDispatch((dispatch, ownProps) => {
        const {savePost} = dispatch('core/editor');
        return {savePost};
    })
])(Sidebar)
