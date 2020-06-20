const {__} = wp.i18n

export default function ReduxTemplatesActivateBox(props) {
    return (
        <div className="redux-templates-modal-body">
            <div class="section-box premium-box">
                <h3>{__('Activate Redux for more!', redux_templates.i18n)}</h3>

                <p>{__('We hope you are enjoying our library. Now that you have imported a number of templates, we must activate Redux to continue.', redux_templates.i18n)}</p>
                <ul>
                    <li><strong>{__('Unlimited', redux_templates.i18n)}</strong> {__('access to the Library', redux_templates.i18n)}</li>
                    <li><strong>{__('Google Fonts', redux_templates.i18n)}</strong> {__('always up to date.', redux_templates.i18n)}</li>
                    <li><strong>{__('Advanced Customizer', redux_templates.i18n)}</strong> {__('for settings.', redux_templates.i18n)}</li>
                    <li><strong>{__('And so much more!', redux_templates.i18n)}</strong></li>
                </ul>
                <p>
                    <a href={redux_templates.u} onClick="alert();" class="redux-templates-upgrade-button" title="{__('Redux Pro', redux_templates.i18n)}"
                       target='_blank'>{__('Activate Now', redux_templates.i18n)}</a>
                </p>
            </div>
        </div>
    );
}
