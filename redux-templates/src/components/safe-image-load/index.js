const { Spinner } = wp.components;
import ImageLoader from 'react-load-image';

const placeholderImage = redux_templates.plugin + 'assets/img/reduxtemplates-medium.jpg';
const spinnerStyle = {height: 100, display: 'flex', alignItems: 'center', justifyContent: 'center' };
export default function SafeImageLoad({url, alt, className}) {
    return (
        <ImageLoader src={url}>
            <img alt={alt} className={className} />
            <img src={placeholderImage} alt={alt} className={className} />
            <div style={spinnerStyle}>
                <Spinner />
            </div>
        </ImageLoader>
    );

}