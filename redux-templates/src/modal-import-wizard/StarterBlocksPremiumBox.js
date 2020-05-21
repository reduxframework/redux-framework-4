const {__} = wp.i18n

export default function ReduxTemplatesPremiumBox(props) {
    return (
        <div className="reduxtemplates-modal-body">
            <div class="section-box premium-box">
                <h3>{__('ReduxTemplates Premium is Required', reduxtemplates.i18n)}</h3>

                <p>{__('Upgrade now to use this template and enjoy these benefits:', reduxtemplates.i18n)}</p>
                <ul>
                    <li><strong>{__('Endless', reduxtemplates.i18n)}</strong> {__('Updates to the Library', reduxtemplates.i18n)}</li>
                    <li><strong>{__('Unlimited', reduxtemplates.i18n)}</strong> {__('Use of the Library', reduxtemplates.i18n)}</li>
                    <li><strong>500+</strong> {__('Section Templates', reduxtemplates.i18n)}</li>
                    <li><strong>125+</strong> {__('Page Templates', reduxtemplates.i18n)}</li>
                    <li><strong>20+</strong> {__('Collections', reduxtemplates.i18n)}</li>
                </ul>
                <p>
                    <a href={reduxtemplates.u} class="reduxtemplates-upgrade-button" title="ReduxTemplates Premium"
                       target='_blank'>{__('Upgrade Now', reduxtemplates.i18n)}</a>
                </p>
            </div>
        </div>
    );
}
