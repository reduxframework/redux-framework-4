import {__} from '@wordpress/i18n';

const {compose} = wp.compose;
const {withDispatch, withSelect} = wp.data;
import {openSitePreviewModal} from '~starterblocks/stores/actionHelper';
import ChallengeDot from '~starterblocks/challenge/tooltip/ChallengeDot';
import './style.scss'

function PreviewImportButton(props) {
    const {data, index, pageData} = props;
    const {setImportingTemplate, tourActiveButtonGroup} = props;
    let spinner = null;
    const triggerImportTemplate = (data) => {
        if (spinner === null) {
            spinner = data.ID;
            setImportingTemplate(data);
        }
    }

    return (
        <div className="action-buttons">
            {
                pageData[index] && pageData[index]['source'] !== 'wp_block_patterns' &&
                <a className="starterblocks-button preview-button" target="_blank"
                   onClick={() => openSitePreviewModal(index, pageData)}>
                    <i className="fa fa-share"/> {__('Preview', starterblocks.i18n)}
                </a>
            }

            <a className="starterblocks-button download-button"
               onClick={() => triggerImportTemplate(data)}>
                <i className="fas fa-download"/>{__('Import', starterblocks.i18n)}
            </a>
            {tourActiveButtonGroup && tourActiveButtonGroup.ID === pageData[index].ID && <ChallengeDot step={4} /> }
        </div>
    );
}


export default compose([
    withDispatch((dispatch) => {
        const {
            setImportingTemplate
        } = dispatch('starterblocks/sectionslist');

        return {
            setImportingTemplate
        };
    }),
    withSelect((select, props) => {
        const {getTourActiveButtonGroup} = select('starterblocks/sectionslist');
        return {
            tourActiveButtonGroup: getTourActiveButtonGroup()
        };
    })
])(PreviewImportButton);
