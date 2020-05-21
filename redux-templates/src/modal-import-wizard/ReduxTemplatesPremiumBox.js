const {__} = wp.i18n

export default function ReduxTemplatesPremiumBox(props) {
    return (
        <div className="redux-templates-modal-body">
            <div class="section-box premium-box">
                <h3>{__('ReduxTemplates Premium is Required', redux-templates.i18n)}</h3>

                <p>{__('Upgrade now to use this template and enjoy these benefits:', redux-templates.i18n)}</p>
                <ul>
                    <li><strong>{__('Endless', redux-templates.i18n)}</strong> {__('Updates to the Library', redux-templates.i18n)}</li>
                    <li><strong>{__('Unlimited', redux-templates.i18n)}</strong> {__('Use of the Library', redux-templates.i18n)}</li>
                    <li><strong>500+</strong> {__('Section Templates', redux-templates.i18n)}</li>
                    <li><strong>125+</strong> {__('Page Templates', redux-templates.i18n)}</li>
                    <li><strong>20+</strong> {__('Collections', redux-templates.i18n)}</li>
                </ul>
                <p>
                    <a href={redux-templates.u} class="redux-templates-upgrade-button" title="ReduxTemplates Premium"
                       target='_blank'>{__('Upgrade Now', redux-templates.i18n)}</a>
                </p>
            </div>
        </div>
    );
}
