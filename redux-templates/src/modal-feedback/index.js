/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n'
import { ModalManager } from '~redux-templates/modal-manager';
import Form from '@rjsf/core';


function FeedbackDialog(props) {
    const {title, description, schema, uiSchema, data, endpoint, fields} = props;
    const {closeModal} = props;

    const onSubmit = ({formData}) => {
        console.log('onForm submit', formData);
        
    }

    const onCloseModal = () => {
        if (closeModal) closeModal(); else ModalManager.closeFeedback();
    }

    return (
        <div className="redux-templates-modal-overlay">
            <div className="redux-templates-modal-wrapper feedback-popup-wrapper">
                <div class="feedback-popup-header feedback-popup-header-contact"
                    style={{ backgroundImage: `url(${redux_templates.plugin + 'assets/img/popup-contact.png'})` }}>
                    <a className="feedback-popup-close" onClick={onCloseModal}>
                        <i className='fas fa-times' />
                    </a>
                </div>
                <div class="feedback-popup-content">
                    <h3>{title}</h3>
                    <p>{description}</p>
                    <Form schema={schema} uiSchema={uiSchema} onSubmit={onSubmit}>
                        <button class="feedback-popup-btn feedback-popup-rate-btn" type="submit">
                            {__('Submit Feedback', redux_templates.i18n)}
                        </button>
                    </Form>                    
                </div>
            </div>
        </div>
    );
}

export default FeedbackDialog;
