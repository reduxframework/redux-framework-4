const {__} = wp.i18n;
const { apiFetch } = wp;
const { dispatch } = wp.data;
const { useState } = wp.element;
const { createSuccessNotice, createErrorNotice } = dispatch('core/notices');

import { Button, ButtonGroup } from '@wordpress/components';

import '../modals.scss';
import './style.scss';

export default function PromotorScoreModal(props) {
    const {propOnClose} = props; // from parent
    const [score, setScore] = useState(-1);


    const afterPost = (response) => {
        if (response.data && response.data.success) {
            createSuccessNotice(__('Thanks for your feedback, your input is very much valued.'), { type: 'snackbar' });
        } else {
            createErrorNotice(response.data.message || __('Error'), { type: 'snackbar' });
        }
        delete redux_templates.nps;

    }

    const onCloseWizard = () => {
        apiFetch({path: 'redux/v1/templates/nps', method: 'POST', data: {nps: 'no-thanks'}}).then(afterPost).catch(afterPost);
        propOnClose();
    };

    const submitScore = () => {
        apiFetch({path: 'redux/v1/templates/nps', method: 'POST', data: {nps: score + 1}}).then(afterPost).catch(afterPost);
        propOnClose();
    }


    return (
        <div className="redux-templates-modal-overlay">
            <div className="redux-templates-modal-wrapper">
                <div className="redux-templates-modal-header">
                    <h3>{__('Can we ask you a question?', redux_templates.i18n)}</h3>
                    <button className="redux-templates-modal-close" onClick={onCloseWizard}>
                        <i className={'fas fa-times'}/>
                    </button>
                </div>
                <div className="redux-templates-psmodal-content">
	                <h3>{redux_templates.nps}</h3>
                    <ButtonGroup>
                        {
                            [...Array(10).keys()].map((i) => <Button key={i} isPrimary={score === i} onClick={()=>setScore(i)}>{ i + 1 }</Button>)
                        }
                    </ButtonGroup>
                </div>
                <div className="redux-templates-modal-footer nps-footer">
                    <button className="button button-primary" disabled={!score} onClick={() => submitScore()}>
                        {__('Submit', redux_templates.i18n)}
                    </button>
                    <a href="#" onClick={onCloseWizard}>
                        {__('Close', redux_templates.i18n)}
                    </a>
                </div>
            </div>
        </div>
    );
};
