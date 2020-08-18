const {__} = wp.i18n

export default function ReduxTemplatesPremiumBox(props) {
    return (
        <div className="redux-templates-modal-body">
            <div className="section-box premium-box">
                <h3>{__('Upgrade to Redux Pro', redux_templates.i18n)}</h3>

                <p>{__('Thanks for giving our library a try! Upgrade to Redux Pro to unlock even more designs and to continue using our library.', redux_templates.i18n)}</p>
                <ul>
	                <li><strong>{redux_templates.stats.sections}</strong> {__('Section Templates', redux_templates.i18n)}</li>
	                <li><strong>{redux_templates.stats.pages}</strong> {__('Full Page Templates', redux_templates.i18n)}</li>
	                <li><strong>{redux_templates.stats.collections}</strong> {__('Template Kits', redux_templates.i18n)}</li>
                    <li><strong>{__('And so much more!', redux_templates.i18n)}</strong></li>
                </ul>
                <p>
                    <a href={redux_templates.u + 'import_wizard'} className="redux-templates-upgrade-button" title="{__('Redux Pro', redux_templates.i18n)}"
                       target='_blank'>{__('Upgrade Now', redux_templates.i18n)}</a>
                </p>
            </div>
        </div>
    );
}
