/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n'
import {CheckboxControl} from '@wordpress/components';
import { ModalManager } from '~redux-templates/modal-manager';

const {compose} = wp.compose;
const {useState} = wp.element;
const {withDispatch, withSelect} = wp.data;


function FeedbackDialog(props) {
    const {title, description, data, endpoint, fields} = props;
    const {closeModal} = props;
    const [comment, setComment] = useState('');
    const [agreeToContactFurther, setAgreement] = useState(false);

    const handleChange = (e) => {
        setComment(e.target.value);
    }

    const contactRedux = () => {
        //sending data
        console.log('contact information', comment, agreeToContactFurther);
        if (closeModal) closeModal(); else ModalManager.closeFeedback();
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
                    <textarea value={comment} onChange={handleChange}></textarea>
                    <CheckboxControl
                        label={__('Yes, I give Redux permission to contact me for any follow up questions.', redux_templates.i18n)}
                        checked={agreeToContactFurther}
                        onChange={() => setAgreement(!agreeToContactFurther)}
                    />
                    <button class="feedback-popup-btn feedback-popup-rate-btn" onClick={contactRedux}>
                        {__('Submit Feedback', redux_templates.i18n)}
                    </button>
                </div>
            </div>
        </div>
    );
}

export default FeedbackDialog;
