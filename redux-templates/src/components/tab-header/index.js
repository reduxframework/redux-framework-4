import {__} from '@wordpress/i18n';
import {compose} from '@wordpress/compose';
import {withDispatch, withSelect} from '@wordpress/data';
import {ModalManager} from '../../modal-manager';
import ChallengeDot from '~starterblocks/challenge/tooltip/ChallengeDot';
export function TabHeader(props) {
    const { activeItemType, searchContext, activeCollection } = props;
    const { setActiveItemType, setSearchContext } = props;

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

    return (
        <div className="starterblocks-builder-modal-header">
            <div className="template-search-box">
                {
                    ((activeItemType !== 'collection'  || activeCollection === null) && activeItemType !== 'saved') &&
                    <div>
                        <i className="fas fa-search" />
                        <input type="search" placeholder={__('Type to search', starterblocks.i18n)} className="form-control" value={searchContext} onChange={onSearchContextUpdate} />
                        <ChallengeDot step={1} />
                    </div>
                }
            </div>

            <div className="starterblocks-template-list-header" data-tut="tour__navigation">
                <button className={ isActive('section') } onClick={e => changeTab('section')}> {__('Sections', starterblocks.i18n)} </button>
                <button className={ isActive('page') } onClick={e => changeTab('page')}> {__('Pages', starterblocks.i18n)} </button>
                <button className={ isActive('collection') } onClick={e => changeTab('collection')}> {__('Collections', starterblocks.i18n)} </button>
                <button className={ isActive('saved') } onClick={e => changeTab('saved')}> {__('Saved', starterblocks.i18n)} </button>
                <ChallengeDot step={0} />
                <button className="starterblocks-builder-close-modal" onClick={e => { ModalManager.close() }} >
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
            setSearchContext
        } = dispatch('starterblocks/sectionslist');

        return {
            setActiveItemType,
            setSearchContext
        };
    }),

    withSelect((select, props) => {
        const { getActiveItemType, getSearchContext, getActiveCollection } = select('starterblocks/sectionslist');
        return { activeItemType: getActiveItemType(), searchContext: getSearchContext(), activeCollection: getActiveCollection() };
    })

])(TabHeader);
