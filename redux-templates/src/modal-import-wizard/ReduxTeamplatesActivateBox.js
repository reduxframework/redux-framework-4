const {__} = wp.i18n


export default function ReduxTemplatesActivateBox({onActivateRedux, activating}) {

    return (
        <div className="redux-templates-modal-body">
            <div className="section-box premium-box">
                <h3>{__('Activation Required', redux_templates.i18n)}</h3>

                <p>{__('To continue using our library, you must activate Redux.', redux_templates.i18n)}</p>
                <ul>
                    <li><strong>{__('Unlimited', redux_templates.i18n)}</strong> {__('use of our free templates.', redux_templates.i18n)}</li>
	                <li><strong>{__('Updates', redux_templates.i18n)}</strong> {__('to the library.', redux_templates.i18n)}</li>
                    <li><strong>{__('Google Fonts', redux_templates.i18n)}</strong> {__('manual updates.', redux_templates.i18n)}</li>
                    <li><strong>{__('And so much more!', redux_templates.i18n)}</strong></li>
                </ul>
                <p>
	                <button className="button button-primary"
	                        disabled={activating}
	                        onClick={() => onActivateRedux()}>
		                {activating && <i className="fas fa-spinner fa-pulse" style={{marginRight:'5px'}}/>}
		                <span>{__('Activate Redux for Free', redux_templates.i18n)}</span>
	                </button>
                </p>
	            <p style={{fontSize:'1.1em'}}><small><em dangerouslySetInnerHTML={{__html: redux_templates.tos.replace('to our', 'to our<br />')}} /></small></p>
            </div>
        </div>
    );
}
