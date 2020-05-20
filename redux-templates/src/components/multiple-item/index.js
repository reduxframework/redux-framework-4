const {__} = wp.i18n
import {Tooltip} from '@wordpress/components';
import {requiresInstall, requiresPro} from '~starterblocks/stores/dependencyHelper'
const MultipleItem = (props) => {

    const {data, backgroundImage, onSelectCollection} = props;
    const {pages, homepageData, ID, name} = data;
    const {image} = homepageData || {};

    return (
        <div className="starterblocks-multiple-template-box">
            <div className="multiple-template-view" onClick={ () => onSelectCollection( ID ) } >
                <div className="starterblocks-default-template-image"><img alt={__('Default Template', starterblocks.i18n)} src={backgroundImage(image)} srcSet={backgroundImage(image)+ ' 2x'}/>
                    {requiresPro(data) && <span className="starterblocks-pro-badge">{__('Premium', starterblocks.i18n)}</span>}
                    {!requiresPro(data) && requiresInstall(data) && <Tooltip text={__('Required Plugins', starterblocks.i18n)} position="bottom" key={ID}><div className="starterblocks-missing-badge"><i className="fas fa-exclamation-triangle" /></div></Tooltip>}
                </div>
                <div className="starterblocks-tmpl-info">
                    <h5 className="starterblocks-tmpl-title" dangerouslySetInnerHTML={{__html:name}}/>
                    <span className="starterblocks-temp-count">{ pages ? pages.length : 0 } {__('Pages', starterblocks.i18n)}</span>
                </div>
            </div>
        </div>
    );
}

export default MultipleItem
