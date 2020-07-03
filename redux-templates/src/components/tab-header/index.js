import {__} from '@wordpress/i18n';
import {compose} from '@wordpress/compose';
import {withDispatch, withSelect} from '@wordpress/data';
import {ModalManager} from '../../modal-manager';
import ChallengeDot from '~redux-templates/challenge/tooltip/ChallengeDot';
export function TabHeader(props) {
    const { activeItemType, searchContext, activeCollection, isChallengeOpen } = props;
    const { setActiveItemType, setSearchContext, setChallengeOpen, clearSearch } = props;

    const isActive = (itemType) => {
        return (activeItemType === itemType) ? 'active' : '';
    }

    const onSearchContextUpdate = (e) => {
        if (activeItemType !=='saved') setSearchContext(e.target.value);
    }

    const changeTab = (tabName) => {
        if (document.getElementById('modalContent')) document.getElementById('modalContent').scrollTop = 0;
        setActiveItemType(tabName);
    }

    const closeModal = () => {
        if (isChallengeOpen === false) {
            ModalManager.close();
        }
    }

    return (
        <div className="redux-templates-builder-modal-header">
            <div className="template-search-box">
                {
                    ((activeItemType !== 'collection'  || activeCollection === null) && activeItemType !== 'saved') &&
                    <div>
                        <i className="fas fa-search" />
                        <input type="text" placeholder={__('Type to search', redux_templates.i18n)} className="form-control" value={searchContext} onChange={onSearchContextUpdate} />
                        <ChallengeDot step={1} />
                        <i className="fas fa-times clear-search" onClick={() => setSearchContext('')} />
                    </div>
                }
            </div>

            <div className="redux-templates-template-list-header" data-tut="tour__navigation">
                <button className={ isActive('section') } onClick={e => changeTab('section')}> {__('Sections', redux_templates.i18n)} </button>
                <button className={ isActive('page') } onClick={e => changeTab('page')}> {__('Templates', redux_templates.i18n)} </button>
                <button className={ isActive('collection') } onClick={e => changeTab('collection')}> {__('Template Kits', redux_templates.i18n)} </button>
                <button className={ isActive('saved') } onClick={e => changeTab('saved')}> {__('Saved', redux_templates.i18n)} </button>
                <ChallengeDot step={0} />
                <button className="redux-templates-builder-close-modal" onClick={closeModal} >
                    <i className={'fas fa-times'} />
                </button>
            </div>
        </div>
    );
}

export default compose([
    withDispatch((dispatch) => {
        const {
            setActiveItemType,
            setSearchContext,
            clearSearch
        } = dispatch('redux-templates/sectionslist');

        return {
            setActiveItemType,
            setSearchContext,
            clearSearch
        };
    }),

    withSelect((select, props) => {
        const { getActiveItemType, getSearchContext, getActiveCollection, getChallengeOpen } = select('redux-templates/sectionslist');
        return { activeItemType: getActiveItemType(), searchContext: getSearchContext(), activeCollection: getActiveCollection(), isChallengeOpen: getChallengeOpen() };
    })

])(TabHeader);
