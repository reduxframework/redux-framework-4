import {Tooltip} from '@wordpress/components';
import * as Icons from '~reduxtemplates/icons'
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
            <div className="reduxtemplates-button-display-dependencies">
                { data.dependencies &&
                    data.dependencies.map(plugin => {
                        const IconComponent = Icons[plugin];
                        const pluginInstance = reduxtemplates.supported_plugins[plugin];
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