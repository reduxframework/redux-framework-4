import {Fab, Action} from 'react-tiny-fab';
import config from './config';
import './styles.scss';
import {__} from '@wordpress/i18n';

import * as Icons from '~redux-templates/icons'
import { ModalManager } from '~redux-templates/modal-manager';
import FeedbackDialog from '~redux-templates/modal-feedback';

const schema = {
    type: 'object',
    properties: {
        comment: {
            type: 'string'
        },
        agreeToContactFurther: {
            type: 'boolean',
            title: __('Yes, I give Redux permission to contact me for any follow up questions.', redux_templates.i18n)
        }
    }
}
const uiSchema = {
    'comment': {
        'ui:widget': 'textarea',
        'ui:options': {
            label: false
        }
    }
};

export default function FabWrapper() {
    const {mainButtonStyles, actionButtonStyles, position, event, alwaysShowTitle} = config;

    return (
        <Fab
            mainButtonStyles={mainButtonStyles}
            position={position}
            icon={Icons.ReduxTemplatesIcon()}
            event={event}
            // onClick={testing}

            text={__('See Quick Links', redux_templates.i18n)}
        >

            {/*<Action*/}
            {/*    style={actionButtonStyles}*/}
            {/*    text={__('Suggest a Feature', redux_templates.i18n)}*/}
            {/*    onClick={e => {*/}
            {/*        window.open(redux_templates.u, "_blank")*/}
            {/*    }}*/}
            {/*>*/}
            {/*    <i className="fa fa-lightbulb-o"/>*/}
            {/*</Action>*/}
            {/*<Action*/}
            {/*    style={actionButtonStyles}*/}
            {/*    text={__('Contact Us', redux_templates.i18n)}*/}
            {/*    onClick={e => {*/}
            {/*        ModalManager.openFeedback(<FeedbackDialog */}
            {/*            title={__('Help us improve Redux', redux_templates.i18n)} */}
            {/*            description={__('Thank you for reaching out. We will do our best to contact you ba.', redux_templates.i18n)}*/}
            {/*            schema={schema}*/}
            {/*            uiSchema={uiSchema}*/}
            {/*            headerImage={<img className="header-background" src={`${redux_templates.plugin}assets/img/popup-contact.png` } />}*/}
            {/*            buttonLabel={__('Submit Feedback', redux_templates.i18n)}*/}
            {/*            />)*/}
            {/*    }}*/}
            {/*>*/}
            {/*    <i className="fa fa-comments"/>*/}
            {/*</Action>*/}
            <Action
                style={actionButtonStyles}
                text={__('Join our Community', redux_templates.i18n)}
                onClick={e => {
                    window.open('https://www.facebook.com/groups/reduxframework', '_blank')
                }}
            >
                <i className="fa fa-comments"/>
            </Action>
	        <Action
		        style={actionButtonStyles}
		        text={__('Visit our Website', redux_templates.i18n)}
		        onClick={e => {
			        window.open('https://redux.io?utm_source=plugin&utm_medium=modal&utm_campaign=tinyfab', '_blank')
		        }}
	        >
		        <i className="fa fa-external-link-alt"/>
	        </Action>
            {/*<Action*/}
            {/*    style={actionButtonStyles}*/}
            {/*    text={__('Take Our Tour', redux_templates.i18n)}*/}
            {/*    onClick={e => {*/}
            {/*        setTourOpen();*/}
            {/*    }}*/}
            {/*>*/}
            {/*    <i className="fas fa-map-signs"/>*/}
            {/*</Action>*/}
            {/*<Action*/}
            {/*    style={actionButtonStyles}*/}
            {/*    text={__('Support & Docs', redux_templates.i18n)}*/}
            {/*    onClick={e => {*/}
            {/*        window.open('https://docs.redux.io/', "_blank")*/}
            {/*    }}*/}
            {/*>*/}
            {/*    <i className="fa fa-life-ring"/>*/}
            {/*</Action>*/}
	        {
		        // redux_templates.mokama !== '1' &&
		        <Action
			        style={{backgroundColor:'#00a7e5'}}
			        text={__('Upgrade to Redux Pro', redux_templates.i18n)}
			        onClick={e => {
				        window.open(redux_templates.u, '_blank')
			        }}
		        >
			        <i className="fa fa-star"/>
		        </Action>
	        }
        </Fab>
    );
}
