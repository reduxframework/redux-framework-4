const {__} = wp.i18n


export default function ReduxTemplatesPremiumActivate({onActivateRedux, activating}) {

	return (
		<div className="redux-templates-modal-body">
			<div className="section-box premium-box">
				<h3>{__('Activate Redux Pro', redux_templates.i18n)}</h3>

				<p>{__('Enter your API key to activate and install your copy of Redux Pro.', redux_templates.i18n)}</p>

				<div>DISPLAY STATUS MESSAGE HERE OF EACH STEP</div>

				<p>
					<input type="text" placeholder="Your subscription key" />
				</p>
				<p className="subscription_key">
					<button type="button" className="components-button" aria-label="Activate my key">Activate</button>
				</p>
			</div>
		</div>
	);
}
