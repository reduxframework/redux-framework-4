const {__} = wp.i18n
// const [activating] = useState(null);
function onActivateRedux() {
	// activating = true;
	window.jQuery.get( redux_templates.activate, {}, function() {} );
	delete redux_templates.left;
	alert('Now we should reload the import wizard!');
}


export default function ReduxTemplatesActivateBox(props) {


    return (
        <div className="redux-templates-modal-body">
            <div className="section-box premium-box">
                <h3>{__('Activation Required', redux_templates.i18n)}</h3>

                <p>{__('To import any more, you must activate Redux.', redux_templates.i18n)}</p>
                <ul>
                    <li><strong>{__('Unlimited', redux_templates.i18n)}</strong> {__('access to the Library', redux_templates.i18n)}</li>
                    <li><strong>{__('Google Fonts', redux_templates.i18n)}</strong> {__('always up to date.', redux_templates.i18n)}</li>
                    <li><strong>{__('Advanced Customizer', redux_templates.i18n)}</strong> {__('for settings.', redux_templates.i18n)}</li>
                    <li><strong>{__('And so much more!', redux_templates.i18n)}</strong></li>
                </ul>
                <p>
	                <button className="button button-primary"
	                        // disabled={activating !== null}
	                        onClick={() => onActivateRedux()}>
		                {/*{activating !== null && <i className="fas fa-spinner fa-pulse"/>}*/}
		                <span>{__('Activate Redux', redux_templates.i18n)}</span>
	                </button>
                </p>
	            <p><small><em dangerouslySetInnerHTML={{__html: redux_templates.tos}} /></small></p>
            </div>
        </div>
    );
}
