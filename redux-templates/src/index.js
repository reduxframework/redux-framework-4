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
import './plugins/sidebar-share'
import './plugins/share-block-btn'
import ToolbarLibraryButton from './toolbar-library-button'
import TooltipBox from './challenge/tooltip/TooltipBox';
import {handlingLocalStorageData} from './stores/helper';
import ReduxTemplatesChallenge from './challenge'
import {ModalManager} from './modal-manager';
import LibraryModal from './modal-library';


domReady(() => {
	const toolbar = document.querySelector('.edit-post-header-toolbar')
	if (!toolbar) {
		return
	}
    const challengeDiv = document.createElement('div')
    challengeDiv.className = 'challenge-tooltip-holder';
    document.body.appendChild(challengeDiv);
    const challengeWrapperDiv = document.createElement('div');
    challengeWrapperDiv.className = 'challenge-wrapper';
    document.body.appendChild(challengeWrapperDiv);

    const buttonDiv = document.createElement('div')
    toolbar.appendChild(buttonDiv);
    render(<ToolbarLibraryButton/>, buttonDiv)
    
    if (window.location.hash == '#reduxtemplates_tour=1') {
        window.location.hash = '';
        ModalManager.open(<LibraryModal />);
    }
    render(<ReduxTemplatesChallenge />, challengeWrapperDiv);
    render(<TooltipBox />, challengeDiv);

    handlingLocalStorageData();
});
