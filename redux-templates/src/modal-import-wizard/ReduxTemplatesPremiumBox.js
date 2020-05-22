const {__} = wp.i18n

export default function ReduxTemplatesPremiumBox(props) {
    return (
        <div className="redux-templates-modal-body">
            <div class="section-box premium-box">
                <h3>{__('ReduxTemplates Premium is Required', redux_templates.i18n)}</h3>

                <p>{__('Upgrade now to use this template and enjoy these benefits:', redux_templates.i18n)}</p>
                <ul>
                    <li><strong>{__('Endless', redux_templates.i18n)}</strong> {__('Updates to the Library', redux_templates.i18n)}</li>
                    <li><strong>{__('Unlimited', redux_templates.i18n)}</strong> {__('Use of the Library', redux_templates.i18n)}</li>
                    <li><strong>500+</strong> {__('Section Templates', redux_templates.i18n)}</li>
                    <li><strong>125+</strong> {__('Page Templates', redux_templates.i18n)}</li>
                    <li><strong>20+</strong> {__('Collections', redux_templates.i18n)}</li>
                </ul>
                <p>
                    <a href={redux_templates.u} class="redux-templates-upgrade-button" title="ReduxTemplates Premium"
                       target='_blank'>{__('Upgrade Now', redux_templates.i18n)}</a>
                </p>
            </div>
        </div>
    );
}
