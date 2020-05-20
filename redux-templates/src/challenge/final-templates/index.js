/**
 * WordPress dependencies
 */
import ChallengeCongrats from './congrats';
import './style.scss'

export default function ChallengeFinalTemplate({finalStatus}) {
    if (finalStatus === 'success') return <ChallengeCongrats />
    return null;
}
