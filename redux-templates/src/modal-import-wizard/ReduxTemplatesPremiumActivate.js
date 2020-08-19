const {__} = wp.i18n


export default function ReduxTemplatesPremiumActivateBox({onActivateRedux, activating}) {

	return (
		<div className="redux-templates-modal-body">
			<div className="section-box premium-box">
				<h3>{__('Activate Your Account', redux_templates.i18n)}</h3>
				<p>{__(' Register now install Redux Pro and unlock access to our library..', redux_templates.i18n)}</p>

				<p>
					<input type="text" name="" placeholder="Your subscription key." />
					<button className="button button-primary"
					        disabled={activating}
					        onClick={() => onActivateRedux()}>
						{activating && <i className="fas fa-spinner fa-pulse" style={{marginRight:'5px'}}/>}
						<span>{__('Register for Free', redux_templates.i18n)}</span>
					</button>
				</p>
				<p style={{fontSize:'1.1em'}}><small><em dangerouslySetInnerHTML={{__html: redux_templates.tos}} /></small></p>
			</div>
		</div>
	);
}
