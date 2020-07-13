const {__} = wp.i18n;
const {compose} = wp.compose;
const {withDispatch, withSelect} = wp.data;
const {useState, useEffect} = wp.element;
const {apiFetch} = wp;

import InstallPluginStep from './InstallPluginStep';
import ProPluginStep from './ProPluginsStep';
import OptionStep from './OptionStep';
import ImportingStep from './ImportingStep';
import ReduxTemplatesPremiumBox from './ReduxTemplatesPremiumBox';
import ReduxTemplatesActivateBox from './ReduxTeamplatesActivateBox';

import {requiresInstall, requiresPro} from '~redux-templates/stores/dependencyHelper'

import '../modals.scss'
import './style.scss'

const PRO_STEP = 0;
const PLUGIN_STEP = 1;
const OPTION_STEP = 2;
const IMPORT_STEP = 3;
const REDUX_PRO_STEP = -1;
const REDUX_ACTIVATE_STEP = 999;
const tourPlugins = ['qubely', 'kioken-blocks'];

function ImportWizard(props) {
    const {startImportTemplate, setImportingTemplate, setActivateDialogDisplay, appendErrorMessage} = props;
    const {isChallengeOpen, importingTemplate, activateDialogDisplay, isPostEmpty} = props;
    const [currentStep, setCurrentStep] = useState(PRO_STEP);
    const [importing, setImporting] = useState(false);
    const [activating, setActivating] = useState(false);

    useEffect(() => {
        if (importingTemplate) {
            // IMPORTANT First check: can you use redux pro?
            const leftTry = isNaN(redux_templates.left) === false ? parseInt(redux_templates.left) : 0;
            if (redux_templates.mokama !== '1' && leftTry < 1) {
                setCurrentStep(REDUX_ACTIVATE_STEP);
                return;
            }
            if (redux_templates.proDependenciesMissing && redux_templates.proDependenciesMissing.includes('redux-pro')) {
                setCurrentStep(REDUX_PRO_STEP);
                return;
            }
            if (importingTemplate && currentStep === PRO_STEP && requiresPro(importingTemplate) === false)
                setCurrentStep(PLUGIN_STEP);
            if (importingTemplate && currentStep === PLUGIN_STEP && requiresInstall(importingTemplate) === false)
                if (isPostEmpty === false) setCurrentStep(OPTION_STEP); else setCurrentStep(IMPORT_STEP);
            if (importingTemplate && currentStep === OPTION_STEP && isPostEmpty === true)
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
    }, [importingTemplate, currentStep, activateDialogDisplay])

    // Activate dialog disply
    useEffect(() => {
        if (activateDialogDisplay === true) { // Activate dialog hard reset case
            setCurrentStep(REDUX_ACTIVATE_STEP);
            setActivateDialogDisplay(false);
        }
    }, [activateDialogDisplay]);

    // On the initial loading
    useEffect(() => {
        setActivateDialogDisplay(false);
    }, []);

    const toNextStep = () => {
        if (isChallengeOpen) return;
        setCurrentStep(currentStep + 1);
    };

    const onCloseWizard = () => {
        if (isChallengeOpen) return; // When in tour mode, we don't accept mouse event.
        if (importing) return;
        setCurrentStep(PLUGIN_STEP);
        setImportingTemplate(null);
    };

    const activateReduxTracking = () => {
        setActivating(true);
	    apiFetch({path: 'redux/v1/templates/activate'}).then(response => {
		    if (response.success) {
			    redux_templates.left = response.data.left;
		    }
		    setCurrentStep(PRO_STEP);
		    setActivating(false);
	    }).catch(error => {
		    appendErrorMessage(error.code + ' : ' + error.message);
		    setCurrentStep(PRO_STEP);
		    setActivating(false);
	    });
    }


    if (isChallengeOpen) {
        // exception handling for tour mode
        if (currentStep !== PLUGIN_STEP) setCurrentStep(PLUGIN_STEP)
    }

    if (!importingTemplate) return null;
    return (
        <div className="redux-templates-modal-overlay">
            <div className="redux-templates-modal-wrapper" data-tut="tour__import_wizard">
                <div className="redux-templates-modal-header">
                    <h3>{__('Import Wizard', redux_templates.i18n)}</h3>
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
                    {currentStep === OPTION_STEP && <OptionStep toNextStep={toNextStep} onCloseWizard={onCloseWizard} />}
                    {currentStep === IMPORT_STEP && <ImportingStep />}
	                {currentStep === REDUX_ACTIVATE_STEP && <ReduxTemplatesActivateBox onActivateRedux={activateReduxTracking} activating={activating} />}
                    {currentStep === REDUX_PRO_STEP && <ReduxTemplatesPremiumBox />}
                </div>
            </div>
        </div>
    );
}


export default compose([
    withDispatch((dispatch) => {
        const {setImportingTemplate, setActivateDialogDisplay, appendErrorMessage} = dispatch('redux-templates/sectionslist');
        return {
            setImportingTemplate,
            setActivateDialogDisplay,
            appendErrorMessage
        };
    }),

    withSelect((select, props) => {
        const {getChallengeOpen, getImportingTemplate, getActivateDialogDisplay} = select('redux-templates/sectionslist');
        const {isEditedPostEmpty} = select('core/editor');
        return {
            isChallengeOpen: getChallengeOpen(),
            importingTemplate: getImportingTemplate(),
            activateDialogDisplay: getActivateDialogDisplay(),
            isPostEmpty: isEditedPostEmpty()
        };
    })
])(ImportWizard);
