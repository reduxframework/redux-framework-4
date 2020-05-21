const {__} = wp.i18n
import {Tooltip} from '@wordpress/components';
import {requiresInstall, requiresPro} from '~redux-templates/stores/dependencyHelper'
const MultipleItem = (props) => {

    const {data, backgroundImage, onSelectCollection} = props;
    const {pages, homepageData, ID, name} = data;
    const {image} = homepageData || {};

    return (
        <div className="redux-templates-multiple-template-box">
            <div className="multiple-template-view" onClick={ () => onSelectCollection( ID ) } >
                <div className="redux-templates-default-template-image"><img alt={__('Default Template', redux-templates.i18n)} src={backgroundImage(image)} srcSet={backgroundImage(image)+ ' 2x'}/>
                    {requiresPro(data) && <span className="redux-templates-pro-badge">{__('Premium', redux-templates.i18n)}</span>}
                    {!requiresPro(data) && requiresInstall(data) && <Tooltip text={__('Required Plugins', redux-templates.i18n)} position="bottom" key={ID}><div className="redux-templates-missing-badge"><i className="fas fa-exclamation-triangle" /></div></Tooltip>}
                </div>
                <div className="redux-templates-tmpl-info">
                    <h5 className="redux-templates-tmpl-title" dangerouslySetInnerHTML={{__html:name}}/>
                    <span className="redux-templates-temp-count">{ pages ? pages.length : 0 } {__('Pages', redux-templates.i18n)}</span>
                </div>
            </div>
        </div>
    );
}

export default MultipleItem
