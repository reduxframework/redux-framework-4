const {compose} = wp.compose;
const {withDispatch, withSelect} = wp.data;
const {Component, useState, useEffect} = wp.element
const {Spinner} = wp.components;
const {__} = wp.i18n
import SitePreviewSidebar from './SitePreviewSidebar';
import {ModalManager} from '../modal-manager'
import ImportWizard from '../modal-import-wizard';
import {Fragment} from 'react';
import {processImportHelper} from '~reduxtemplates/stores/actionHelper';
import './style.scss';

function PreviewModal(props) {

    const {startIndex, currentPageData} = props;
    const {setImportingTemplate, importingTemplate} = props;
    const [currentIndex, setCurrentIndex] = useState(startIndex);
    const [previewClass, setPreviewClass] = useState('preview-desktop')
    const [expandedClass, toggleExpanded] = useState('expanded')
    const [importingBlock, setImportingBlock] = useState(null);
    const [missingPluginArray, setMissingPlugin] = useState([]);
    const [missingProArray, setMissingPro] = useState([]);
    const [pressedKey, setPressedKey] = useState(null);
    const [overlayClassname, setOverlayClassname] = useState('wp-full-overlay-main');

    useEffect(() => {
        const handleKeyDown = ({keyCode}) => {
            setPressedKey(keyCode);
        }

        window.addEventListener('keydown', handleKeyDown);
        return () => {
            window.removeEventListener('keydown', handleKeyDown);
        }
    }, []);

    useEffect(() => {
        if (pressedKey !== null) {
            if (pressedKey === 37) onPrevBlock();
            if (pressedKey === 39) onNextBlock();
            setPressedKey(null);
        }
    }, [pressedKey])

    const onCloseCustomizer = () => {
        ModalManager.closeCustomizer();
    }

    const onNextBlock = () => {
        if (currentIndex < currentPageData.length - 1) setCurrentIndex(currentIndex + 1);
    }

    const onPrevBlock = () => {
        if (currentIndex > 0) setCurrentIndex(currentIndex - 1);
    }


    const importStarterBlock = () => {
        setImportingTemplate(itemData);
        ModalManager.closeCustomizer();
    }

    const processImport = () => {
        if (importingTemplate) processImportHelper();
    }

    const hideSpinner = () => {
        setOverlayClassname('wp-full-overlay-main loaded');
    }

    let wrapperClassName = ['wp-full-overlay sites-preview theme-install-overlay ', previewClass, expandedClass].join(' ');
    let itemData = currentPageData[currentIndex];
    let image_url = itemData.image
    if (itemData.image_full) {
        image_url = itemData.image_full;
    }

    return (
        <Fragment>
            <div className={wrapperClassName} style={{display: 'block'}}>
                <SitePreviewSidebar itemData={itemData} previewClass={previewClass} expandedClass={expandedClass}
                                    onNextBlock={onNextBlock} onPrevBlock={onPrevBlock}
                                    onCloseCustomizer={onCloseCustomizer} onToggleExpanded={e => toggleExpanded(e)}
                                    onImport={importStarterBlock}
                                    onChangePreviewClass={e => setPreviewClass(e)}/>
                <div className={overlayClassname}>
                    {itemData.url &&
                        <iframe src={itemData.url} target='Preview' onLoad={hideSpinner}></iframe>
                    }
                    {!itemData.url &&
                        <div className='redux-templates-modal-preview-box'><img
                            src={image_url}
                            title=''/></div>
                    }

                </div>
            </div>
            { importingTemplate && <ImportWizard startImportTemplate={processImport} /> }
        </Fragment>
    );
}

export default compose([
    withDispatch((dispatch) => {
        const {
            setImportingTemplate,
            setCustomizerOpened
        } = dispatch('reduxtemplates/sectionslist');

        return {
            setImportingTemplate,
            setCustomizerOpened
        };
    }),

    withSelect((select, props) => {
        const {getImportingTemplate} = select('reduxtemplates/sectionslist');
        return {
            importingTemplate: getImportingTemplate()
        };
    })
])(PreviewModal);
