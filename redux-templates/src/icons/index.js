/**
 * External dependencies
 */
import React, {Component} from 'react';
import SVGReduxTemplatesIcon from '../../assets/img/icon.svg'
import SVGReduxTemplatesColorIcon from '../../assets/img/icon-color.svg'
import SVGAdvancedGutenbergBlocksIcon from './images/third-party/advanced-gutenberg-blocks.svg'
import SVGCoBlocksIcon from './images/third-party/coblocks.svg'
import SVGCreativeBlocksIcon from './images/third-party/creative-blocks.svg'
import SVGKiokenIcon from './images/third-party/kioken.svg'
import SVGEssentialBlocksIcon from './images/third-party/eb.svg'
import SVGElegantBlocksIcon from './images/third-party/elegant-blocks.svg'
import SVGQubelyIcon from './images/third-party/qubely.svg'
import SVGStackableIcon from './images/third-party/ugb.svg'

/**
 * WordPress dependencies
 */
import {cloneElement, render} from '@wordpress/element'
import domReady from '@wordpress/dom-ready'
import {updateCategory} from '@wordpress/blocks'

export const colorizeIcon = SvgIcon => {
	return cloneElement(SvgIcon, {
		fill: 'url(#redux-templates-gradient)',
		className: 'redux-templates-icon-gradient',
	})
}

// Add an icon to our block category.
if (typeof window.wp.blocks !== 'undefined' && typeof window.wp.blocks.updateCategory !== 'undefined') {
	updateCategory(redux_templates.i18n, {
		icon: colorizeIcon(<SVGReduxTemplatesIcon className="components-panel__icon" width="20" height="20"/>),
	})
}

// Add our SVG gradient placeholder definition that we'll reuse.
domReady(() => {
	const redux_templatesGradient = document.createElement('DIV')
	document.querySelector('body').appendChild(redux_templatesGradient)
	render(
		<svg
			xmlns="http://www.w3.org/2000/svg"
			className="redux-templates-gradient"
			height="0"
			width="0"
			style={{opacity: 0}}
		>
			<defs>
				<linearGradient id="redux-templates-gradient">
					<stop offset="0%" stopColor="#8c33da" stopOpacity="1"/>
					<stop offset="100%" stopColor="#f34957" stopOpacity="1"/>
				</linearGradient>
			</defs>
		</svg>,
		redux_templatesGradient
	)
})

export const ReduxTemplatesIcon = () => {
	return <SVGReduxTemplatesIcon width="20" height="20"/>
}

export const ReduxTemplatesIconColor = () => {
	return <SVGReduxTemplatesColorIcon width="20" height="20"/>
}

export const ReduxTemplatesIconColorize = () => {
	return colorizeIcon(<SVGReduxTemplatesIcon width="20" height="20"/>)
}

export const AdvancedGutenbergBlocks = () => {
	return <SVGAdvancedGutenbergBlocksIcon width="20" height="20"/>
}
export const advancedgutenbergblocks = () => <AdvancedGutenbergBlocks/>

export const AdvancedGutenberg = () => {
	return <SVGAdvancedGutenbergIcon width="20" height="20"/>
}
export const advancedgutenbergIcon = () => <AdvancedGutenberg/>

export const AtomicBlocks = () => {
	return <SVGAtomicBlocksIcon width="20" height="20"/>
}
export const atomicblocks = () => <AtomicBlocks/>

export const CoBlocks = () => {
	return <SVGCoBlocksIcon width="20" height="20"/>
}
export const Coblocks = () => <CoBlocks/>
export const coblocks = () => <CoBlocks/>

export const Stackable = () => {
	return <SVGStackableIcon width="20" height="20"/>
}
export const stackable = () => <Stackable/>
export const ugb = () => <Stackable/>

export const Qubely = () => {
	return <SVGQubelyIcon width="20" height="20"/>
}
export const qubely = () => <Qubely/>

export const Kioken = () => {
    return <SVGKiokenIcon width="20" height="20"/>
}
export const kioken = () => <Kioken/>


export const CreativeBlocks = () => {
	return <SVGCreativeBlocksIcon width="20" height="20"/>
}
export const creativeblocks = () => <CreativeBlocks/>
export const qb = () => <CreativeBlocks/>

export const EssentialBlocks = () => {
	return <SVGEssentialBlocksIcon width="20" height="20"/>
}
export const essentialblocks = () => <EssentialBlocks/>
export const eb = () => <EssentialBlocks/>
