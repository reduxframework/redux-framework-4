import {Tooltip} from '@wordpress/components';
import * as Icons from '~redux-templates/icons'
import './style.scss'
const {__} = wp.i18n;

export default function DependentPlugins (props) {
    const {data, showDependencyBlock} = props;
    const {id} = data;

    const isMissingPlugin = (plugin) => {
        return ((data.proDependenciesMissing && data.proDependenciesMissing.indexOf(plugin) >=0)
            || (data.installDependenciesMissing && data.installDependenciesMissing.indexOf(plugin) >=0))
    }

    if (showDependencyBlock)
        return (
            <div className="redux-templates-button-display-dependencies">
                { data.dependencies &&
                    data.dependencies.map(plugin => {
						const pluginInstance = redux_templates.supported_plugins[plugin];
						if (!pluginInstance) {
							console.log( 'Missing plugin details for '+ plugin );
							return null;
						}
                    	const plugin_name = plugin.replace('-pro', '').replace('-premium', '').replace(/\W/g, '').toLowerCase();

						// We don't want two of the same icons showing up.
						if ( ! plugin.includes('-pro') && ! plugin.includes('-premium') ) {
							if ( data.dependencies.includes(plugin + '-pro') || data.dependencies.includes( plugin + '-premium' ) ) {
								return;
							}
						}
						if ( 'redux' === plugin_name ) {
							return;
						}
                        const IconComponent = Icons[plugin_name];
                        if (IconComponent && pluginInstance) {
							return (
								<Tooltip text={(isMissingPlugin(plugin) ? pluginInstance.name+ ' ( '+__('Not Installed', redux_templates.i18n)+' )' : pluginInstance.name)} position="bottom center" key={id + plugin}>
                                    <span className={isMissingPlugin(plugin) ? 'missing-dependency' : ''}>
                                        <IconComponent/>
                                    </span>
								</Tooltip>
							);
						} else if ( 'shareablockcom' !== plugin_name && 'gutenberghubcom' !== plugin_name ) {
                        	console.log('Need icon for ' + plugin_name);
						}

                    })
                }
            </div>
        );
    return null;
}
