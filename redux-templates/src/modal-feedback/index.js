/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n'
import { ModalManager } from '~redux-templates/modal-manager';
import Form from '@rjsf/core';
const {useState} = wp.element;
const {apiFetch} = wp;

function FeedbackDialog(props) {
    const {title, description, schema, uiSchema, headerImage, headerIcon, data, endpoint} = props;
    const {closeModal, onSuccess} = props;

    const [loading, setLoading] = useState(false);
    const [errorMessage, setErrorMessage] = useState(null);

    const onSubmit = ({formData}) => {
        const path = `redux-templates/v1/${endpoint ? endpoint : 'feedback'}`;
        if (loading) return;
        setLoading(true);
        apiFetch({
            path,
            method: 'POST',
            data: {...data, ...formData}
        }).then(data => {
            setLoading(false);
            if (data.success) {
                setErrorMessage(null);
                if (onSuccess) onSuccess(); else onCloseModal();
            } else {
                console.log('There was an error: ', data);
                setErrorMessage(__('An unexpected error occured, please try again later.', redux_templates.i18n));
            }
        }).catch(err => {
            setLoading(false);
            console.log('There was an error: ', err);
            setErrorMessage(__('An unexpected error occured, please try again later.', redux_templates.i18n));
        });
    }

    const onCloseModal = () => {
        if (closeModal) closeModal(); else ModalManager.closeFeedback();
    }

    return (
        <div className="redux-templates-modal-overlay">
            <div className="redux-templates-modal-wrapper feedback-popup-wrapper">
                <div class="feedback-popup-header feedback-popup-header-contact">
                    {headerImage}
                    {headerIcon}
                    <a className="feedback-popup-close" onClick={onCloseModal}>
                        <i className='fas fa-times' />
                    </a>
                </div>
                <div class="feedback-popup-content">
                    <h3>{title}</h3>
                    {errorMessage && <p className="error-message">{errorMessage}</p>}
                    <p>{description}</p>
                    <Form schema={schema} uiSchema={uiSchema} onSubmit={onSubmit}>
                        <button class="feedback-popup-btn feedback-popup-rate-btn" type="submit">
                            {loading && <i className="fas fa-spinner fa-pulse"/>}
                            {__('Submit Feedback', redux_templates.i18n)}
                        </button>
                    </Form>                    
                </div>
            </div>
        </div>
    );
}

export default FeedbackDialog;
