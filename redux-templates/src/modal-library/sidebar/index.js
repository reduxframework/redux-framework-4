const {withSelect} = wp.data;

import './style.scss'

import PriceFilter from './priceFilter';
import CategoryFilter from './categoryFilter';
import DependencyFilter from './dependencyFilter';

function Sidebar(props) {
    const {itemType, layer, loading} = props;
    const hasSidebar = () => {
        return (itemType !== 'collection' || layer === null);
    };
    return (
        <div className="starterblocks-modal-sidebar-content" data-tut="tour__filtering">
            {
                hasSidebar() &&
                <div>
                    <PriceFilter/>
                    <CategoryFilter/>
                    <DependencyFilter/>
                </div>
            }
        </div>
    );
}

export default withSelect((select) => {
    const {getActiveItemType, getActiveCollection} = select('starterblocks/sectionslist');
    return {
        itemType: getActiveItemType(),
        layer: getActiveCollection()
    };
})(Sidebar);
