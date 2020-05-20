/**
 * WordPress dependencies
 */
import {__} from '@wordpress/i18n'
import CONFIG from '../config';
import helper from '../helper';

const { compose } = wp.compose;
const { withDispatch, withSelect } = wp.data;


const ratingStars = (
    <span class="rating-stars">
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
        <i class="fa fa-star"></i>
    </span>
);

function ChallengeCongrats(props) {
    const {setChallengeStep, setChallengeFinalStatus, setChallengeOpen} = props;
    const closeModal = () => {
        setChallengeStep(CONFIG.beginningStep);
        setChallengeFinalStatus('');
        setChallengeOpen(false);
    }
    return (
        <div className="starterblocks-modal-overlay">
            <div className="starterblocks-modal-wrapper challenge-popup-wrapper">
                <div class="challenge-popup-header challenge-popup-header-congrats" 
                    style={{backgroundImage: `url(${starterblocks.plugin + 'assets/img/popup-congrats.png'})`}}>
                    <a className="challenge-popup-close" onClick={closeModal}>
                        <i class="fa fa-times-circle fa-lg"></i>
                    </a>
                </div>
                <div class="challenge-popup-content">
                    <h3>{__( 'Congrats, you did it!', starterblocks.i18n )}</h3>
                    <p>
                        You completed the Starterblocks Challenge in <b>{helper.getLocalizedDuration()}</b>. 
                        Share your success story with other Starterblocks users and help us spread the word 
                        <b>by giving Starterblocks a 5-star rating ({ratingStars}) on WordPress.org</b>. 
                        Thanks for your support and we look forward to bringing more awesome features.
                    </p>
                    <a href="https://wordpress.org/support/plugin/starterblocks/reviews/?filter=5#new-post" class="challenge-popup-btn challenge-popup-rate-btn" target="_blank" rel="noopener">
                        {__( 'Rate Starterblocks on Wordpress.org', starterblocks.i18n ) }
                        <span class="dashicons dashicons-external"></span>
                    </a>
                </div>
            </div>
        </div>
    );
}

export default compose([
    withDispatch((dispatch) => {
        const { setChallengeStep, setChallengeFinalStatus, setChallengeOpen } = dispatch('starterblocks/sectionslist');
        return {
            setChallengeStep,
            setChallengeFinalStatus,
            setChallengeOpen
        };
    })
])(ChallengeCongrats);
