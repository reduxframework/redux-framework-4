import {pluginInfo} from '~redux-templates/stores/dependencyHelper';

const {apiFetch} = wp;
const {compose} = wp.compose;
const {withDispatch} = wp.data;
const {Fragment, useState} = wp.element;
const {__} = wp.i18n;

function OptionStep(props) {

    const {setImportToAppend, toNextStep, onCloseWizard} = props;

    const onNextStep = (isToAppend) => {
        setImportToAppend(isToAppend);
        toNextStep();
    }

    return (

        <Fragment>
            <div className="redux-templates-modal-body">
                <h5>{__('Append or Replace', redux_templates.i18n)}</h5>
                <p>{__('Please let us know whether you want to append or .', redux_templates.i18n)}</p>
            </div>
            <div className="redux-templates-modal-footer">
                <button className="button button-primary" onClick={() => onNextStep(true)}>
                    {__('Append Template', redux_templates.i18n)}
                </button>
                <button className="button button-primary" onClick={() => onNextStep(false)}>
                    {__('Replace all Content', redux_templates.i18n)}
                </button>
                <button className="button button-secondary" onClick={onCloseWizard}>
                    {__('Cancel', redux_templates.i18n)}
                </button>
            </div>
        </Fragment>
    );
}


export default compose([
    withDispatch((dispatch) => {
        const {
            setImportToAppend
        } = dispatch('redux-templates/sectionslist');
        return {
            setImportToAppend
        };
    })
])(OptionStep);
