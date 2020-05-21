const { Fragment } = wp.element;
import Sidebar from '../sidebar';
import TemplateListSubHeader from '~reduxtemplates/components/template-list-subheader';
import TemplateList from '../view-template-list';

export default function WithSidebarCollection (props) {
    return (
        <Fragment>
            <div id="reduxtemplates-collection-modal-sidebar" className="reduxtemplates-collection-modal-sidebar">
                <Sidebar />
            </div>
            <div className="reduxtemplates-collection-modal-content-area" data-tut="tour__main_body" id="modalContent">
                <TemplateListSubHeader />
                <TemplateList />
            </div>
        </Fragment>
    );
}
