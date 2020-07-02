const {compose} = wp.compose;
const {withDispatch, withSelect} = wp.data;
const { useState, useEffect} = wp.element;
import '../stores';

import {Modal, ModalManager} from '../modal-manager'
import TabHeader from '../components/tab-header';
import WithSidebarLayout from './layout-with-sidebar';
import CollectionView from './view-collection';
import SavedView from './view-saved';
import ImportWizard from '../modal-import-wizard';
import ErrorNotice from '../components/error-notice';
import ChallengeFinalTemplate from '~redux-templates/challenge/final-templates';
import FabWrapper from '../components/fab-wrapper';
import {processImportHelper} from '~redux-templates/stores/actionHelper';
import './style.scss'


function LibraryModal(props) {
    const {
        fetchLibraryFromAPI, activeCollection, activeItemType, errorMessages, importingTemplate, challengeFinalStatus, isChallengeOpen,
        setLoading, setImportingTemplate, clearSearch
    } = props;
    const [loaded, setLoaded] = useState(false);
    const [escKeyPressed, setEscKeyPressed] = useState(false);

    let stateLibrary = null;
    useEffect(() => {
        stateLibrary = fetchLibraryFromAPI();
        if (stateLibrary === null && loaded === false) { // One to be called at first.
            setLoading(true);
            setLoaded(true);
        }
        const handleKeyUp = ({keyCode}) => {
            if (keyCode === 27) {
                setEscKeyPressed(true);
            }
        }
        document.addEventListener('keyup', handleKeyUp);
        return () => {
            document.removeEventListener('keyup', handleKeyUp);
        }
    }, []);

    useEffect(() => {
        if (escKeyPressed) {
            setEscKeyPressed(false);
            if (ModalManager.isCustomizerOpened()) {
                ModalManager.closeCustomizer();
            } else {
                if (importingTemplate)
                    setImportingTemplate(null);
                else {
                    ModalManager.close();
                    clearSearch();
                }
            }
        }
    }, [escKeyPressed])

    const hasSidebar = () => {
        return ((activeItemType !== 'collection' || activeCollection === null) && activeItemType !== 'saved');
    }

    // read block data to import and give the control to actual import
    const processImport = () => {
        if (importingTemplate) processImportHelper();
    }


    return (
        <Modal className="redux-templates-builder-modal-pages-list"
               customClass="redux-templates-builder-modal-template-list"
               openTimeoutMS={0} closeTimeoutMS={0}>
            <TabHeader/>
            {
                errorMessages && errorMessages.length > 0 &&
                <ErrorNotice errorMessages={errorMessages}/>
            }
            <div className="redux-templates-collections-modal-body">
                {hasSidebar() && <WithSidebarLayout/>}
                {(hasSidebar() === false && activeItemType === 'collection') && <CollectionView/>}
                {(hasSidebar() === false && activeItemType !== 'collection') && <SavedView/>}
            </div>
            {
                importingTemplate && <ImportWizard startImportTemplate={processImport} />
            }
            { (challengeFinalStatus !== '') && <ChallengeFinalTemplate finalStatus={challengeFinalStatus} /> }
            { !isChallengeOpen && <FabWrapper /> }
        </Modal>
    );
}


export default compose([
    withDispatch((dispatch) => {
        const {
            setLoading,
            setLibrary,
            setImportingTemplate,
            clearSearch
        } = dispatch('redux-templates/sectionslist');

        return {
            setLoading,
            setLibrary,
            setImportingTemplate,
            clearSearch
        };
    }),

    withSelect((select) => {
        const {fetchLibraryFromAPI, getActiveCollection, getActiveItemType, getErrorMessages, getImportingTemplate, getChallengeOpen, getChallengeFinalStatus} = select('redux-templates/sectionslist');
        return {
            fetchLibraryFromAPI,
            activeCollection: getActiveCollection(),
            activeItemType: getActiveItemType(),
            errorMessages: getErrorMessages(),
            importingTemplate: getImportingTemplate(),
            challengeFinalStatus: getChallengeFinalStatus(),
            isChallengeOpen: getChallengeOpen()
        };
    })
])(LibraryModal);
