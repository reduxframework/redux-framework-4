import {__} from '@wordpress/i18n'
import CONFIG from './config';
export default {

    /**
     * Get number of seconds left to complete the Challenge.
     */
    getSecondsLeft: function() {

        var secondsLeft = localStorage.getItem( 'starterblocksChallengeSecondsLeft' );

        secondsLeft = secondsLeft ? parseInt( secondsLeft, 10 ) : CONFIG.initialSecondsLeft;

        return secondsLeft;
    },

    /**
     * Get number of seconds spent completing the Challenge.
     */
    getSecondsSpent: function( secondsLeft ) {

        secondsLeft = secondsLeft || getSecondsLeft();

        return CONFIG.initialSecondsLeft - secondsLeft;
    },

    /**
     * Save number of seconds left to complete the Challenge.
     */
    saveSecondsLeft: function( secondsLeft ) {

        localStorage.setItem( 'starterblocksChallengeSecondsLeft', secondsLeft );
    },

    /**
     * Get 'minutes' part of timer display.
     */
    getMinutesFormatted: function( secondsLeft ) {

        secondsLeft = secondsLeft || this.getSecondsLeft();

        return Math.floor( secondsLeft / 60 );
    },

    /**
     * Get 'seconds' part of timer display.
     */
    getSecondsFormatted: function( secondsLeft ) {

        secondsLeft = secondsLeft || this.getSecondsLeft();

        return secondsLeft % 60;
    },

    /**
     * Get formatted timer for display.
     */
    getFormatted: function( secondsLeft ) {

        secondsLeft = secondsLeft || this.getSecondsLeft();

        var timerMinutes = this.getMinutesFormatted( secondsLeft );
        var timerSeconds = this.getSecondsFormatted( secondsLeft );

        return timerMinutes + ( 9 < timerSeconds ? ':' : ':0' ) + timerSeconds;
    },

    /**
     * Get Localized time string for display
     */
    getLocalizedDuration: function( secondsLeft ) {
        secondsLeft = secondsLeft || this.getSecondsLeft();
        secondsLeft = CONFIG.initialSecondsLeft - secondsLeft;

        var timerMinutes = this.getMinutesFormatted( secondsLeft );
        var timerSeconds = this.getSecondsFormatted( secondsLeft );

        const minutesString = timerMinutes ? timerMinutes + ' ' + __( 'minutes', starterblocks.i18n ) + ' ' : '';
        const secondsString = timerSeconds ? timerSeconds + ' ' + __( 'seconds', starterblocks.i18n ) : '';
        return minutesString + secondsString;
    },

    /**
     * Get last saved step.
     */
    loadStep: function() {

        var step = localStorage.getItem( 'starterblocksChallengeStep' );
        step = step ? parseInt( step, 10 ) : -1;

        return step;
    },

    /**
     * Save Challenge step.
     */
    saveStep: function( step ) {
        localStorage.setItem( 'starterblocksChallengeStep', step );
    },
};