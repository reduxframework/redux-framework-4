import {Fab, Action} from 'react-tiny-fab';
import config from './config';
import './styles.scss';
import {__} from '@wordpress/i18n';



import * as Icons from '~starterblocks/icons'

export default function FabWrapper() {
    const {mainButtonStyles, actionButtonStyles, position, event, alwaysShowTitle} = config;

    return (
        <Fab
            mainButtonStyles={mainButtonStyles}
            position={position}
            icon={Icons.StarterBlocksIcon()}
            event={event}
            // onClick={testing}

            text={__('See Quick Links', starterblocks.i18n)}
        >

            {/*<Action*/}
            {/*    style={actionButtonStyles}*/}
            {/*    text={__('Suggest a Feature', starterblocks.i18n)}*/}
            {/*    onClick={e => {*/}
            {/*        window.open(starterblocks.u, "_blank")*/}
            {/*    }}*/}
            {/*>*/}
            {/*    <i className="fa fa-lightbulb-o"/>*/}
            {/*</Action>*/}
            <Action
                style={actionButtonStyles}
                text={__('Join Our Community', starterblocks.i18n)}
                onClick={e => {
                    window.open('https://www.facebook.com/groups/reduxframework', '_blank')
                }}
            >
                <i className="fa fa-comments"/>
            </Action>
            {/*<Action*/}
            {/*    style={actionButtonStyles}*/}
            {/*    text={__('Take Our Tour', starterblocks.i18n)}*/}
            {/*    onClick={e => {*/}
            {/*        setTourOpen();*/}
            {/*    }}*/}
            {/*>*/}
            {/*    <i className="fas fa-map-signs"/>*/}
            {/*</Action>*/}
            {/*<Action*/}
            {/*    style={actionButtonStyles}*/}
            {/*    text={__('Support & Docs', starterblocks.i18n)}*/}
            {/*    onClick={e => {*/}
            {/*        window.open('https://docs.redux.io/', "_blank")*/}
            {/*    }}*/}
            {/*>*/}
            {/*    <i className="fa fa-life-ring"/>*/}
            {/*</Action>*/}
            {
                starterblocks.mokama !== 1 &&
                <Action
                    style={actionButtonStyles}
                    text={__('Upgrade to Redux Pro', starterblocks.i18n)}
                    onClick={e => {
                        window.open(starterblocks.u, '_blank')
                    }}
                >
                    <i className="fa fa-star"/>
                </Action>
            }
        </Fab>
    );
}
