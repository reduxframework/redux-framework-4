/**
 * Library Button
 */

/**
 * WordPress dependencies
 */
import domReady from '@wordpress/dom-ready'
import { render } from '@wordpress/element'

/**
 * External dependencies
 */
import './editor.scss'
import './blocks/blocks'
import './plugins/sidebar-share'
import './plugins/share-block-btn'
import './plugins/export'
import './plugins/export-page-menu-item'
import './plugins/library-context-menu-item'
import TooltipBox from './challenge/tooltip/TooltipBox';
import {handlingLocalStorageData} from './stores/helper';
import ReduxTemplatesChallenge from './challenge'
import {ModalManager} from './modal-manager';
import LibraryModal from './modal-library';


domReady(() => {
    setTimeout(() => {
        const challengeDiv = document.createElement('div');
        challengeDiv.className = 'challenge-tooltip-holder';
        document.body.appendChild(challengeDiv);
        const challengeWrapperDiv = document.createElement('div');
        challengeWrapperDiv.className = 'challenge-wrapper';
        document.body.appendChild(challengeWrapperDiv);

        if (window.location.hash == '#redux_challenge=1') {
            window.location.hash = '';
            ModalManager.open(<LibraryModal />);
        }
		if (window.location.hash == '#redux_templates=1') {
			window.location.hash = '';
			ModalManager.open(<LibraryModal />);
		}
        render(<ReduxTemplatesChallenge />, challengeWrapperDiv);
        render(<TooltipBox />, challengeDiv);

        handlingLocalStorageData();
    }, 500)
});
