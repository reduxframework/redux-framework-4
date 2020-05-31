import {__} from '@wordpress/i18n';
import {Component, Fragment} from '@wordpress/element';

var onClose, node, customizerNode, feedbackNode;

export class Modal extends Component {
    constructor(props) {
        super(props)
        this.state = {
            afterOpen: false,
            beforeClose: false,
        }
    }

    close() {
        if (!this.props.onRequestClose || this.props.onRequestClose()) {
            if (customizerNode) ModalManager.closeCustomizer()
            else ModalManager.close()
        }
    }

    componentDidMount() {
        const {openTimeoutMS, closeTimeoutMS} = this.props
        setTimeout(() => this.setState({afterOpen: true}), openTimeoutMS ? openTimeoutMS : 150)

        onClose = (callback) => {
            this.setState({beforeClose: true}, () => {
                this.closeTimer = setTimeout(callback, closeTimeoutMS ? closeTimeoutMS : 150)
            });
        };
    }

    componentWillUnmount() {
        onClose = null;
        clearTimeout(this.closeTimer)
    }

    render() {

        return (
            <Fragment>
                <span onClick={e => {
                    this.close()
                }} className={'redux-templates-pagelist-modal-overlay'}>&nbsp;</span>
                <div className={ this.props.compactMode ? 'redux-templates-modal-inner' : 'redux-templates-pagelist-modal-inner'} onClick={e => e.stopPropagation()}>
                    {this.props.children}
                </div>
            </Fragment>
        );
    }
}


export const ModalManager = {
    open(component) {
        if (onClose) {
            this.close();
            // throw __('There is already one modal.It must be closed before one new modal will be opened');
        }
        if (!node) {
            node = document.createElement('div')
            node.className = 'redux-templates-builder-modal'
            document.body.appendChild(node)
        }
        wp.element.render(component, node)
        document.body.classList.add('redux-templates-builder-modal-open')
    },
    close() {
        onClose && onClose(() => {
            wp.element.unmountComponentAtNode(node)
            document.body.classList.remove('redux-templates-builder-modal-open')
        });
    },
    openCustomizer(component) {
        if (!customizerNode) {
            customizerNode = document.createElement('div');
            document.body.appendChild(customizerNode);
        }
        wp.element.render(component, customizerNode);
    },
    closeCustomizer() {
        if (customizerNode) {
            wp.element.unmountComponentAtNode(customizerNode);
            customizerNode = false
        }
    },
    openFeedback(component) {
        if (!feedbackNode) {
            feedbackNode = document.createElement('div');
            feedbackNode.className = 'feedback-wrapper';
            document.body.appendChild(feedbackNode);
        }
        wp.element.render(component, feedbackNode);
    },
    closeFeedback() {
        if (feedbackNode) {
            wp.element.unmountComponentAtNode(feedbackNode);
            feedbackNode = false;
        }
    },
    isCustomizerOpened() {
        return customizerNode ? true : false;
    },
    hide () {
        document.body.classList.remove('redux-templates-builder-modal-open')
        node.classList.add('hidden')
    },
    show () {
        document.body.classList.add('redux-templates-builder-modal-open')
        if (node)
            node.classList.remove('hidden')
    }
}
