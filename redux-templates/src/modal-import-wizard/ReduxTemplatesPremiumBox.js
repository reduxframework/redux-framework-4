const {__} = wp.i18n

export default function ReduxTemplatesPremiumBox(props) {
    return (
        <div className="redux-templates-modal-body">
            <div className="section-box premium-box">
                <h3>{__('Upgrade to Redux Pro', redux_templates.i18n)}</h3>

                <p>{__('Upgrade to unlock 1,000+ designs to build your pages quick!', redux_templates.i18n)}</p>
                <ul>
                    <li><strong>{__('Unlimited', redux_templates.i18n)}</strong> {__('access to the Library', redux_templates.i18n)}</li>
                    <li><strong>{__('Google Fonts', redux_templates.i18n)}</strong> {__('always up to date.', redux_templates.i18n)}</li>
                    <li><strong>{__('Advanced Customizer', redux_templates.i18n)}</strong> {__('for settings.', redux_templates.i18n)}</li>
                    <li><strong>{__('And so much more!', redux_templates.i18n)}</strong></li>
                </ul>
                <p>
                    <a href={redux_templates.u} className="redux-templates-upgrade-button" title="{__('Redux Pro', redux_templates.i18n)}"
                       target='_blank'>{__('Upgrade Now', redux_templates.i18n)}</a>
                </p>
            </div>
        </div>
    );
}
