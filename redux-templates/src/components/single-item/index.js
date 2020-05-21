import {Tooltip} from '@wordpress/components';

const {__} = wp.i18n
const {withSelect} = wp.data;
const {useState, useEffect} = wp.element;

import ButtonGroup from '../button-group';
import BackgroundImage from '../background-image';
import {requiresInstall, requiresPro} from '~reduxtemplates/stores/dependencyHelper'

import './style.scss'


function SingleItem (props) {
    // Decoupling props
    const {pageData, tourActiveButtonGroup, index} = props;
    const {backgroundImage} = props;
    const [data, setData] = useState(null);
    // const {ID, image, url, pro, source, requirements} = data;
    const [innerClassname, setInnerClassname] = useState('reduxtemplates-single-item-inner reduxtemplates-item-wrapper ');

    useEffect(() => {
        if (pageData) setData(pageData[index]);
    }, [index, pageData]);

    useEffect(() => {
        setInnerClassname((pageData && pageData[index] && tourActiveButtonGroup && tourActiveButtonGroup.ID === pageData[index].ID) ?
            'reduxtemplates-single-item-inner reduxtemplates-item-wrapper focused' : 'reduxtemplates-single-item-inner reduxtemplates-item-wrapper');
    }, [tourActiveButtonGroup, pageData, index]);

    if (!data) return null;
    return (
        <div className="reduxtemplates-single-section-item">
            <div className={innerClassname}>
                <div className="reduxtemplates-default-template-image">

                    {data.source !== 'wp_block_patterns' &&<img className="lazy" src={backgroundImage(data.image)}/>}
                    {data.source === 'wp_block_patterns' && <BackgroundImage data={data} />}
                    {requiresPro(data) && <span className="reduxtemplates-pro-badge">{__('Premium', reduxtemplates.i18n)}</span>}
                    {!requiresPro(data) && requiresInstall(data) && <span className="reduxtemplates-missing-badge"><i className="fas fa-exclamation-triangle" /></span>}
                    <div className="reduxtemplates-tmpl-title">{data.name}</div>
                </div>
                {/* reduxtemplates-default-template-image */}
                <div className="reduxtemplates-button-overlay">
                    {requiresPro(data) && <span className="reduxtemplates-pro-badge">{__('Premium', reduxtemplates.i18n)}</span>}
                    {!requiresPro(data) && requiresInstall(data) && <Tooltip text={__('Required Plugins', reduxtemplates.i18n)} position="bottom" key={data.source+data.source_id}><div className="reduxtemplates-missing-badge"><i className="fas fa-exclamation-triangle" /></div></Tooltip>}
                    <ButtonGroup index={index} showDependencyBlock={true} data={data} pageData={pageData} />
                </div>

            </div>
            {/* reduxtemplates-item-wrapper */}
        </div>
    )
}


export default withSelect((select, props) => {
    const {getTourActiveButtonGroup, getPageData} = select('reduxtemplates/sectionslist');
    return {
        pageData: getPageData(),
        tourActiveButtonGroup: getTourActiveButtonGroup()
    };
})(SingleItem);
