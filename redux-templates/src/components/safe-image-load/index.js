const { Spinner } = wp.components;
import ImageLoader from 'react-load-image';

const placeholderImage = redux_templates.plugin + 'assets/img/reduxtemplates-medium.jpg';
const spinnerStyle = {height: 100, display: 'flex', alignItems: 'center', justifyContent: 'center' };
export default function SafeImageLoad({url}) {
    return (
        <ImageLoader src={url}>
            <img />
            <img src={placeholderImage} />
            <div style={spinnerStyle}>
                <Spinner />
            </div>
        </ImageLoader>
    );

}