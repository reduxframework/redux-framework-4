const {__} = wp.i18n

export default function StarterBlocksPremiumBox(props) {
    return (
        <div className="starterblocks-modal-body">
            <div class="section-box premium-box">
                <h3>{__('StarterBlocks Premium is Required', starterblocks.i18n)}</h3>

                <p>{__('Upgrade now to use this template and enjoy these benefits:', starterblocks.i18n)}</p>
                <ul>
                    <li><strong>{__('Endless', starterblocks.i18n)}</strong> {__('Updates to the Library', starterblocks.i18n)}</li>
                    <li><strong>{__('Unlimited', starterblocks.i18n)}</strong> {__('Use of the Library', starterblocks.i18n)}</li>
                    <li><strong>500+</strong> {__('Section Templates', starterblocks.i18n)}</li>
                    <li><strong>125+</strong> {__('Page Templates', starterblocks.i18n)}</li>
                    <li><strong>20+</strong> {__('Collections', starterblocks.i18n)}</li>
                </ul>
                <p>
                    <a href={starterblocks.u} class="starterblocks-upgrade-button" title="StarterBlocks Premium"
                       target='_blank'>{__('Upgrade Now', starterblocks.i18n)}</a>
                </p>
            </div>
        </div>
    );
}
