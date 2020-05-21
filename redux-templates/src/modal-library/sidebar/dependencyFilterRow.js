import React from 'react';
const {useState, useEffect, useRef} = wp.element;
const {compose} = wp.compose;
const {withDispatch, withSelect} = wp.data;
const {__} = wp.i18n;

import {CheckboxControl, Tooltip} from '@wordpress/components';
import {pluginInfo} from '~reduxtemplates/stores/dependencyHelper';
import groupBy from 'lodash/groupBy';

function DependencyFilterRow(props) {
    const {pluginKey, dependencyFilters} = props;
    const {setDependencyFilters} = props;
    const [isValidPlugin, setIsValidPlugin] = useState(false);
    const [isChecked, setIsChecked] = useState(false);
    const [pluginInstanceURL, setPluginInstanceURL] = useState('');
    const [pluginInstanceName, setPluginInstanceName] = useState('');
    const [pluginClassname, setPluginClassname] = useState('');

    useEffect(() => {
        const pluginInstance = pluginInfo(pluginKey);      
        if (!pluginKey || pluginKey === 'none') {
            setIsValidPlugin(false);
            return;
        }
        if (!pluginInstance || pluginInstance.name == null) {
            setIsValidPlugin(false);
            return;
        }
        setPluginInstanceURL(pluginInstance.url);
        setPluginInstanceName(pluginInstance.name);
        setIsValidPlugin(true);
    }, [pluginKey]);

    useEffect(() => {
        const pluginInstance = pluginInfo(pluginKey);
        if (!dependencyFilters) return;
        if (dependencyFilters.hasOwnProperty(pluginKey)) {
            if (dependencyFilters[pluginKey].disabled)
                setIsChecked(false);
            else
                setIsChecked(dependencyFilters[pluginKey].hasOwnProperty('value') ? dependencyFilters[pluginKey].value : dependencyFilters[pluginKey]);
        } else 
            setIsChecked(false)
        let pluginClassnameList = [];
        pluginClassnameList.push(!pluginInstance.version && !('no_plugin' in pluginInstance) ? 'missing-dependency' : '');
        pluginClassnameList.push((!dependencyFilters[pluginKey] || dependencyFilters[pluginKey].disabled) ? 'disabled' : '');
        setPluginClassname(pluginClassnameList.join(' '));
    }, [JSON.stringify(dependencyFilters)])

    const toggleChecked = () => {
        // disable check first
        if (dependencyFilters[pluginKey] === null || dependencyFilters[pluginKey] === undefined || dependencyFilters[pluginKey].disabled) return;

        let newDependencyFilters = {...dependencyFilters,
            [pluginKey]: { value: dependencyFilters[pluginKey].value === false, disabled: dependencyFilters[pluginKey]['disabled'] === true }};
        let valueCount = groupBy(Object.keys(newDependencyFilters), key => (newDependencyFilters[key] === true || newDependencyFilters[key].value === true));

        if (valueCount['true'] && valueCount['true'].length > 0 && valueCount['false'] && valueCount['false'].length > 0) {
            setDependencyFilters({...newDependencyFilters, none: {value: false, disabled: newDependencyFilters['none']['disabled']}});
        } else {
            setDependencyFilters({...newDependencyFilters, none: {value: true, disabled: newDependencyFilters['none']['disabled']}});
        }
    };

    if (isValidPlugin === false) return null;

    return (
        <li className={pluginClassname}>
            <CheckboxControl
                label={pluginInstanceName}
                checked={isChecked}
                onChange={toggleChecked}
            />
            {pluginClassname.includes('missing-dependency') &&
                <Tooltip text={__('Plugin not Installed', reduxtemplates.i18n)}><i className="fa fa-warning" /></Tooltip>
            }

            {pluginInstanceURL ?
                <a href={pluginInstanceURL} target="_blank">
                    <i className="fa fa-external-link-alt" />
                </a> : null}
        </li>
    );
}


export default compose([
    withDispatch((dispatch) => {
        const {setDependencyFilters} = dispatch('reduxtemplates/sectionslist');
        return {
            setDependencyFilters
        };
    }),

    withSelect((select) => {
        const {getDependencyFiltersStatistics, getLoading, getActiveCategory} = select('reduxtemplates/sectionslist');
        return {
            loading: getLoading(),
            dependencyFilters: getDependencyFiltersStatistics(),
            activeCategory: getActiveCategory()
        };
    })
])(DependencyFilterRow);
