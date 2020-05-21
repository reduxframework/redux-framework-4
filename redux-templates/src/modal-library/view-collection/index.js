const {useState, useEffect} = wp.element;
const {compose} = wp.compose;
const {withDispatch, withSelect} = wp.data;
const {__} = wp.i18n

import './style.scss'

import ButtonGroup from '~redux-templates/components/button-group';
import {requiresInstall, requiresPro} from '~redux-templates/stores/dependencyHelper'

function CollectionView(props) {
    const {pageData, activeCollectionData} = props;
    const {setActiveCollection} = props;
    const [previewData, setPreviewData] = useState(null);
    const [previewDataIndex, setPreviewDataIndex] = useState(0);

    const dataLength = pageData.length;

    useEffect(() => {
        if (pageData) setPreviewData(pageData[previewDataIndex]);
    }, [pageData, previewDataIndex]);

    if (previewData)
        return (
            <div className="redux-templates-collection-details-view">
                <div className="redux-templates-collection-details-left">
                    <div className="details-back" onClick={() => setActiveCollection(null)}>
                        <span className="dashicons dashicons-arrow-left-alt"/>&nbsp;{__('Back to Collections', redux-templates.i18n)}
                    </div>
                    <div className="details-preview" style={{backgroundImage: `url('${previewData.image}')`}}>

                    </div>
                </div>
                <div className="redux-templates-collection-details-right">
                    <div className="details-title">
                        <h3>{activeCollectionData.name}</h3>
                        <span>{dataLength} {__('pages', redux-templates.i18n)}</span>
                    </div>
                    <div className="details-list">
                        <div className="details-inner">
                            {
                                pageData.map((detail, index) => {
                                    let className = (previewData.ID === detail.ID) ? 'detail-select detail-select-active' : 'detail-select';
                                    let divStyle = {
                                        backgroundImage: 'url(' + detail.image + ')',
                                    };

                                    return (
                                        <div className={className} onClick={() => setPreviewDataIndex(index)} key={index}>
                                            <div className="detail-image" style={divStyle}>
                                                {requiresPro(detail) && <span className="pro">{__('Premium', redux-templates.i18n)}</span>}
                                                {!requiresPro(detail) && requiresInstall(detail) && <span className="install"><i className="fas fa-exclamation-triangle" /></span>}
                                                <div className="detail-label">{detail.name}</div>
                                            </div>
                                        </div>
                                    );
                                })
                            }
                        </div>
                    </div>
                </div>
                <div className="redux-templates-collection-details-footer">
                    <div className="footer-grid">
                        <ButtonGroup index={previewDataIndex} showDependencyBlock={false} data={previewData} pageData={pageData} />
                    </div>
                </div>
            </div>
        );
    return null;
}


export default compose([
    withDispatch((dispatch) => {
        const {
            setActiveCollection
        } = dispatch('redux-templates/sectionslist');

        return {
            setActiveCollection
        };
    }),

    withSelect((select, props) => {
        const {getPageData, getLoading, getActiveCollectionData, getActiveItemType} = select('redux-templates/sectionslist');
        return {
            pageData: getPageData(),
            loading: getLoading(),
            activeItemType: getActiveItemType(),
            activeCollectionData: getActiveCollectionData()
        };
    })
])(CollectionView);
