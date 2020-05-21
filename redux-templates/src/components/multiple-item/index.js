const {__} = wp.i18n
import {Tooltip} from '@wordpress/components';
import {requiresInstall, requiresPro} from '~reduxtemplates/stores/dependencyHelper'
const MultipleItem = (props) => {

    const {data, backgroundImage, onSelectCollection} = props;
    const {pages, homepageData, ID, name} = data;
    const {image} = homepageData || {};

    return (
        <div className="reduxtemplates-multiple-template-box">
            <div className="multiple-template-view" onClick={ () => onSelectCollection( ID ) } >
                <div className="reduxtemplates-default-template-image"><img alt={__('Default Template', reduxtemplates.i18n)} src={backgroundImage(image)} srcSet={backgroundImage(image)+ ' 2x'}/>
                    {requiresPro(data) && <span className="reduxtemplates-pro-badge">{__('Premium', reduxtemplates.i18n)}</span>}
                    {!requiresPro(data) && requiresInstall(data) && <Tooltip text={__('Required Plugins', reduxtemplates.i18n)} position="bottom" key={ID}><div className="reduxtemplates-missing-badge"><i className="fas fa-exclamation-triangle" /></div></Tooltip>}
                </div>
                <div className="reduxtemplates-tmpl-info">
                    <h5 className="reduxtemplates-tmpl-title" dangerouslySetInnerHTML={{__html:name}}/>
                    <span className="reduxtemplates-temp-count">{ pages ? pages.length : 0 } {__('Pages', reduxtemplates.i18n)}</span>
                </div>
            </div>
        </div>
    );
}

export default MultipleItem
