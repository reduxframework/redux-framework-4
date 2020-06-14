const {__} = wp.i18n;
const {compose} = wp.compose;
const {withDispatch, withSelect} = wp.data;
const {useState, useEffect} = wp.element;

import InstallPluginStep from './InstallPluginStep';
import ProPluginStep from './ProPluginsStep';
import ImportingStep from './ImportingStep';
import ReduxTemplatesPremiumBox from './ReduxTemplatesPremiumBox';
import '../modals.scss'
import './style.scss'

const PRO_STEP = 0;
const PLUGIN_STEP = 1;
const IMPORT_STEP = 2;
const REDUX_PRO_STEP = -1;
const tourPlugins = ['qubely', 'kioken-blocks'];
import {requiresInstall, requiresPro} from '~redux-templates/stores/dependencyHelper'
function ImportWizard(props) {
    const {startImportTemplate, setImportingTemplate, isChallengeOpen, importingTemplate} = props;
    const [currentStep, setCurrentStep] = useState(PRO_STEP);
    const [importing, setImporting] = useState(false);

    useEffect(() => {
        if (importingTemplate) {
            // IMPORTANT First check: can you use redux pro?
            const leftTry = isNaN(redux_templates.left) === false ? parseInt(redux_templates.left) : 0;
            if (redux_templates.mokama !== '1' && leftTry < 1) {
                setCurrentStep(REDUX_PRO_STEP);
                return;
            }
            if (importingTemplate && currentStep === PRO_STEP && requiresPro(importingTemplate) === false)
                setCurrentStep(PLUGIN_STEP);
            if (importingTemplate && currentStep === PLUGIN_STEP && requiresInstall(importingTemplate) === false)
                setCurrentStep(IMPORT_STEP);
            if (importingTemplate && currentStep === IMPORT_STEP && importing === false) {
                setImporting(true);
                try {
                    startImportTemplate();
                } catch (e) {
                    console.log('importing exception', e);
                    setImporting(false);
                    setCurrentStep(PLUGIN_STEP);
                    setImportingTemplate(null);
                }
            }
        }
    }, [importingTemplate, currentStep])

    const toNextStep = () => {
        if (isChallengeOpen) return;
        setCurrentStep(currentStep + 1);
    };

    const onCloseWizard = () => {
        if (isChallengeOpen) return; // When in tour mode, we don't accpet mouse event.
        if (importing) return;
        setCurrentStep(PLUGIN_STEP);
        setImportingTemplate(null);
    };


    if (isChallengeOpen) {
        // exception handling for tour mode
        if (currentStep !== PLUGIN_STEP) setCurrentStep(PLUGIN_STEP)
    }

    if (!importingTemplate) return null;
    return (
        <div className="redux-templates-modal-overlay">
            <div className="redux-templates-modal-wrapper" data-tut="tour__import_wizard">
                <div className="redux-templates-modal-header">
                    <h3>{__('Template Import Wizard', redux_templates.i18n)}</h3>
                    <button className="redux-templates-modal-close" onClick={onCloseWizard}>
                        <i className={'fas fa-times'}/>
                    </button>
                </div>
                <div className="redux-templates-importmodal-content">
                    {(currentStep === PRO_STEP) && requiresPro(importingTemplate) &&
                        <ProPluginStep missingPros={importingTemplate.proDependenciesMissing } onCloseWizard={onCloseWizard} />}
                    {(currentStep === PLUGIN_STEP) &&
                        <InstallPluginStep missingPlugins={isChallengeOpen ? tourPlugins : importingTemplate.installDependenciesMissing || []} toNextStep={toNextStep}
                        onCloseWizard={onCloseWizard}/>}
                    {(currentStep === IMPORT_STEP) && <ImportingStep />}
                    {(currentStep === REDUX_PRO_STEP) && <ReduxTemplatesPremiumBox />}
                </div>
            </div>
        </div>
    );
}


export default compose([
    withDispatch((dispatch) => {
        const {setImportingTemplate} = dispatch('redux-templates/sectionslist');
        return {
            setImportingTemplate
        };
    }),

    withSelect((select, props) => {
        const {getChallengeOpen, getImportingTemplate} = select('redux-templates/sectionslist');
        return {
            isChallengeOpen: getChallengeOpen(),
            importingTemplate: getImportingTemplate()
        };
    })
])(ImportWizard);
