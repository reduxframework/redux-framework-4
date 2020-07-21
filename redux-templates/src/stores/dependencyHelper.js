export const getPluginInstance = (pluginKey) => {
    if (pluginKey in redux_templates.supported_plugins) {
        return redux_templates.supported_plugins[pluginKey];
    }
    return false; // Deal with unknown plugins
}

export const needsPluginInstall = (pluginKey) => {
    const pluginInstance = getPluginInstance(pluginKey);
    return !pluginInstance || pluginInstance.hasOwnProperty('version') === false;
}

export const needsPluginPro = (pluginKey) => {
    const pluginInstance = getPluginInstance(pluginKey);
    return (pluginInstance && pluginInstance.hasOwnProperty('has_pro') && pluginInstance.has_pro &&
        (pluginInstance.hasOwnProperty('is_pro') === false || pluginInstance.is_pro === false));
}


export const pluginInfo = (pluginKey) => {
    let pluginInstance = processPlugin(pluginKey);
    if (!pluginInstance) return {name: null, slug: null, url: null};
    return pluginInstance
}


export const processPlugin = (pluginKey) => {
    let pluginInstance = {...getPluginInstance(pluginKey)};
    if (!pluginInstance) {
        return pluginInstance
    }

    if ('free_slug' in pluginInstance && pluginInstance['free_slug'] in redux_templates.supported_plugins) {
        let new_instance = {...getPluginInstance(pluginInstance.free_slug)}
        new_instance.free_slug = pluginInstance.free_slug
        new_instance.name = pluginInstance.name
        if (!('is_pro' in new_instance)) {
            delete new_instance.version
        }
        pluginInstance = new_instance
    }
    pluginInstance.slug = pluginInstance.slug ? pluginInstance.slug : pluginKey;

    return pluginInstance
}

export const requiresPro = (data) => {
    return (data && data.proDependenciesMissing && data.proDependenciesMissing.length > 0) ? true : false;
}
export const requiresInstall = (data) => {
    return (data && data.installDependenciesMissing && data.installDependenciesMissing.length > 0) ? true : false;
}
// Check if redux pro should be installed.
export const requiresReduxPro = (data) => {
    if (!data) return false;
    const reduxProNotInstalled = needsPluginInstall('redux-pro');
    let missingDependencies = [];
    if (requiresInstall(data) === true) missingDependencies = [...data.installDependenciesMissing];
    if (requiresPro(data)) missingDependencies = [...missingDependencies, ...data.proDependenciesMissing];

    return missingDependencies.reduce((acc, curKey) => {
        if (curKey === 'redux-pro') return true;
        return acc || (isPluginReduxProMerged(curKey) && reduxProNotInstalled === true); // main logic, above were execpetion handling
    }, false);
}

export const isPluginReduxProMerged = (pluginKey) => {
    const pluginInstance = getPluginInstance(pluginKey);
    return (pluginInstance !== false && pluginInstance.redux_pro === true);

}

export const isTemplateReadyToInstall = (data) => {
    return (requiresInstall(data) || requiresPro(data)) ? false : true;
}

export const isTemplatePremium = (data, activeDependencyFilter) => {
    if (data && data.proDependencies && data.proDependencies.length > 0) {
        return data.proDependencies.reduce((acc, cur) => {
            if (activeDependencyFilter[cur] === undefined) 
                return false;
            return (acc || activeDependencyFilter[cur].value);
        }, false);
    }
    return (data && data.proDependenciesMissing && data.proDependenciesMissing.length > 0);
}
