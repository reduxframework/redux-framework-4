const {useState, useEffect} = wp.element
const {__} = wp.i18n

import * as Icons from '~reduxtemplates/icons'
import copy from 'clipboard-copy';
import {requiresInstall, requiresPro} from '~reduxtemplates/stores/dependencyHelper'

export default function SidebarContent(props) {
    const {itemData, pro} = props;
    const {hash, name, image, blocks, proDependencies, installDependencies} = itemData;
    const [copied, setCopied] = useState(false);

    const copyHash = () => {
        copy(hash.substring(0, 7));
        setCopied(true);
        setTimeout(function () {
            setCopied(false);
        }, 3500);
    }

    useEffect(() => {
        setCopied(false);
    }, [itemData]);

    return (
        <div className="wp-full-overlay-sidebar-content">
            <div className="install-theme-info">
                <h3 className="theme-name">{name}</h3>
                <div className="theme-screenshot-wrap">
                    <img className="theme-screenshot"
                         src={image ? image : reduxtemplates.plugin + 'assets/img/reduxtemplates-medium.jpg'} alt=""/>{pro ?
                    <span className="reduxtemplates-pro-badge">{__('Premium', reduxtemplates.i18n)}</span> : ''
                }</div>

                <h5 className="theme-hash">
                    <div className="button-container">
                        <span className="button button-secondary the-copy" onClick={copyHash} title={__('Copy Identifier', reduxtemplates.i18n)}><i
                            className="fa fa-copy" aria-hidden="true"></i></span>
                        <span onClick={copyHash} className="button button-secondary the-hash"
                              title={__('Identifier', reduxtemplates.i18n)}>{hash.substring(0, 7)}</span>
                        {copied && <span className="copied hideMe"><br/>{__('copied', reduxtemplates.i18n)}</span>}
                    </div>

                </h5>

                <div className="requirements-list">
                    <div className="list-type">
                        {
                            requiresInstall(itemData) &&
                            <div>
                                <h4>Missing Plugins</h4>
                                <ul>
                                    {
                                        installDependencies.map(pluginKey => {
                                            const pluginInstance = reduxtemplates.supported_plugins[pluginKey];
                                            if (!pluginInstance) return null;
                                            const IconComponent = Icons[pluginKey];
                                            return (
                                                <li key={pluginKey}>
                                                    <a href={pluginInstance.url ? pluginInstance.url : ''}
                                                       target="_blank" className="missing">
                                                        {IconComponent && <IconComponent/>}
                                                        <span
                                                            className="reduxtemplates-dependency-name">{pluginInstance.name}</span>
                                                    </a>
                                                </li>);
                                        })
                                    }
                                </ul>
                            </div>
                        }
                        {
                            requiresPro(itemData) &&
                            <div>
                                <h4>Require to be pro</h4>
                                <ul>
                                    {
                                        proDependencies.map(pluginKey => {
                                            const pluginInstance = reduxtemplates.supported_plugins[pluginKey];
                                            if (!pluginInstance) return null;
                                            const IconComponent = Icons[pluginKey];
                                            return (
                                                <li key={pluginKey}>
                                                    <a href={pluginInstance.url ? pluginInstance.url : ''}
                                                       target="_blank" className="missing">
                                                        {IconComponent && <IconComponent/>}
                                                        <span
                                                            className="reduxtemplates-dependency-name">{pluginInstance.name}</span>
                                                    </a>
                                                </li>
                                            );
                                        })
                                    }
                                </ul>
                            </div>
                        }
                    </div>
                </div>
            </div>
        </div>
    );
}
