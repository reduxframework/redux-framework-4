import {Tooltip} from '@wordpress/components';
import * as Icons from '~redux-templates/icons'
import './style.scss'

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
                        const IconComponent = Icons[plugin];
                        const pluginInstance = redux_templates.supported_plugins[plugin];
                        if (IconComponent && pluginInstance)
                            return (
                                <Tooltip text={pluginInstance.name} position="bottom" key={id + plugin}>
                                    <span className={isMissingPlugin(plugin) ? 'missing-dependency' : ''}>
                                        <IconComponent/>
                                    </span>
                                </Tooltip>
                            );
                    })
                }
            </div>
        );
    return null;
}
