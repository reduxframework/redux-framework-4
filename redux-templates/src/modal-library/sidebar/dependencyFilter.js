const {Fragment} = wp.element;
const {compose} = wp.compose;
const {withDispatch, withSelect} = wp.data;
const {__} = wp.i18n;
import ChallengeDot from '~redux-templates/challenge/tooltip/ChallengeDot';

import {CheckboxControl, Tooltip} from '@wordpress/components';
import DependencyFilterRow from './dependencyFilterRow';
import {REDUXTEMPLATES_PRO_KEY} from '~redux-templates/stores/helper';

function DependencyFilter(props) {
    const {dependencyFilters, loading, wholePlugins} = props;
    const {setDependencyFilters, selectDependencies} = props;
    // Give the selected category(activeCategory) label className as "active"
    const isNoneChecked = () => {
        if (dependencyFilters.hasOwnProperty('none'))
            return dependencyFilters['none'].hasOwnProperty('value') ? dependencyFilters['none'].value : dependencyFilters['none'];
        return false;
    };

    const toggleNoneChecked = () => {
        setDependencyFilters({...dependencyFilters,
            none: { value: dependencyFilters['none'].value === false, disabled: dependencyFilters['none']['disabled'] === true }
        });
    };

    const setAllCheckedAs = (newVal) => {
        setDependencyFilters(
            Object.keys(dependencyFilters)
                .filter(key => key!=='none')
                .reduce((acc, key) => {
                    const disabled = dependencyFilters[key] ? dependencyFilters[key]['disabled'] : false;
                    return {...acc, [key]: {value: disabled ? false : newVal, disabled }}
                }, {none: {value: true, disabled: false}})
        );
    };

    return (
        <Fragment>
            {!loading && wholePlugins &&
                <div id="redux-templates-filter-dependencies" data-tut="tour__filter_dependencies">
                    <h3>{__('Required Plugins', redux_templates.i18n)}</h3>
                    <div className="redux-templates-select-actions">
                        <Tooltip text={__('Select All', redux_templates.i18n)} position="bottom"><a href="#" onClick={() => selectDependencies('all')}>{__('All', redux_templates.i18n)}</a></Tooltip>
                        <span>&nbsp; / &nbsp;</span>
                        <Tooltip text={__('Native Blocks Only', redux_templates.i18n)} position="bottom"><a href="#" onClick={() => selectDependencies('none')}>{__('None', redux_templates.i18n)}</a></Tooltip>
                        <span>&nbsp; / &nbsp;</span>
                        <Tooltip text={__('Installed Dependencies', redux_templates.i18n)} position="bottom"><a href="#"
                            onClick={() => selectDependencies('installed')}>
                            {__('Installed', redux_templates.i18n)}</a></Tooltip>
                        <span>&nbsp; / &nbsp;</span>
                        <Tooltip text={__('Reset Dependencies', redux_templates.i18n)} position="bottom">
                            <a href="#" onClick={() => selectDependencies('default')}>
                            <i className="fas fa-undo" /></a></Tooltip>
                        <ChallengeDot step={2} />

                    </div>
                    <ul className="redux-templates-sidebar-dependencies">
                        { (loading === false) &&
                            <li>
                                <CheckboxControl
                                    label={__('Native', redux_templates.i18n)}
                                    checked={isNoneChecked()}
                                    onChange={toggleNoneChecked}
                                />
                                <Tooltip text={__('Only default WordPress blocks used.', redux_templates.i18n)} position='right'>
                                    <span style={{float:'right', marginRight:'2px'}}><i className="fa fa-info-circle" /></span>
                                </Tooltip>
                            </li>
                        }
                        {
                            Object.keys(dependencyFilters)
                                .filter(pluginKey => (wholePlugins.indexOf(pluginKey)!==-1 || pluginKey === REDUXTEMPLATES_PRO_KEY))
                                .sort()
                                .map(pluginKey =>
                                    <DependencyFilterRow key={pluginKey} pluginKey={pluginKey} />
                                )
                        }
                    </ul>
                </div>
            }
        </Fragment>
    );
}

export default compose([
    withDispatch((dispatch) => {
        const {setDependencyFilters, selectDependencies} = dispatch('redux-templates/sectionslist');
        return {
            setDependencyFilters,
            selectDependencies
        };
    }),

    withSelect((select) => {
        const {getDependencyFiltersStatistics, getLoading, getWholePlugins} = select('redux-templates/sectionslist');
        return {
            loading: getLoading(),
            dependencyFilters: getDependencyFiltersStatistics(),
            wholePlugins: getWholePlugins()
        };
    })
])(DependencyFilter);
