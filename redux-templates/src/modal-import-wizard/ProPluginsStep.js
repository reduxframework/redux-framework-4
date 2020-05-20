const {Fragment} = wp.element;
const {__} = wp.i18n;

import StarterBlocksPremiumBox from './StarterBlocksPremiumBox';
import {pluginInfo} from '~starterblocks/stores/dependencyHelper';
const STARTERBLOCKS_PRO_KEY = 'starterblocks-pro';
export default function ProPluginStep(props) {
    const {missingPros, onCloseWizard} = props;

    if ( missingPros.indexOf(STARTERBLOCKS_PRO_KEY) >= 0 ) return <StarterBlocksPremiumBox />
    return (
        <Fragment>
            <div className="starterblocks-modal-body">
                <h5>{__('External Dependencies Required', starterblocks.i18n)}</h5>
                <p>{__('The following premium plugin(s) are required to import this template:', starterblocks.i18n)}</p>
                <ul className="starterblocks-import-progress">
                    {
                        missingPros.map(pluginKey => {
                            let plugin = pluginInfo(pluginKey)
                            return (
                                <li className='installing' key={pluginKey}>
                                    {plugin.name} {plugin.url &&
                                <a href={plugin.url} target="_blank"><i className="fa fa-external-link-alt"/></a>
                                }
                                </li>);
                        })
                    }
                </ul>

            </div>
            <div className="starterblocks-modal-footer">
                <a className="button button-secondary" onClick={onCloseWizard}>
                    {__('Close', starterblocks.i18n)}
                </a>
            </div>
        </Fragment>
    );
}

