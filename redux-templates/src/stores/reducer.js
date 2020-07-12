import {parseSectionData, parsePageData, parseCollectionData, getInstalledDependencies} from './helper';
import {getDefaultDependencies} from './helper';
import {loadChallengeStep, saveChallengeStep, setWithExpiry, getWithExpiry} from './helper';
const EXIPRY_TIME = 5 * 24 * 3600 * 1000;
export const initialState = {
    loading: false,
    activeItemType: getWithExpiry('itemType', 'section'),
    library: null,
    columns: getWithExpiry('column', ''),
    errorMessages: [],
    section: {
        categories: [],
        data: {},
        priceFilter: getWithExpiry('section_price', ''),
        activeCategory: getWithExpiry('section_category', ''),
        dependencyFilters: {},
        searchContext: '',
        wholePlugins: [],
        sortBy: getWithExpiry('section_sort', 'name'),
        currentPage: getWithExpiry('section_page', 0)
    },
    page: {
        categories: [],
        data: {},
        priceFilter: getWithExpiry('page_price', ''),
        activeCategory: getWithExpiry('page_category', ''),
        dependencyFilters: {},
        searchContext: '',
        wholePlugins: [],
        sortBy: getWithExpiry('page_sort', 'name'),
        currentPage: getWithExpiry('page_page', 0)
    },
    collection: {
        categories: [],
        data: {},
        priceFilter: getWithExpiry('collection_price', ''),
        activeCategory: getWithExpiry('collection_category', 'name'),
        dependencyFilters: {},
        searchContext: '',
        wholePlugins: [],
        activeCollection: null,
        sortBy: getWithExpiry('collection_sort', 'name'),
        currentPage: getWithExpiry('collection_page', 0)
    },
    installedDependencies: false, // used when deciding should or not reload page after importing the template
    isImportToAppend: true, // append to or replace the current page content for importing
    tour: {
        isOpen: false,
        activeButtonGroup: null,
        isPreviewVisible: false
    },
    challenge: {
        isOpen: false,
        currentStep: loadChallengeStep(),
        tooltipRect: {},
        finalStatus: '',
        passed: getWithExpiry('reduxChallengePassed', false),
        listExpanded: true
    },
    plugins: {},
    importingTemplate: null,
    activateDialog: false
};

export const reducer = ( state = initialState, action ) => {

    switch ( action.type ) {
        case 'SET_LIBRARY':
            redux_templates.supported_plugins = action.library.plugins;
            const dependencies = getDefaultDependencies(Object.keys(action.library.dependencies));
            const parsedSection = parseSectionData(action.library.sections);
            const parsedPage = parsePageData(action.library.pages);
			const parsedCollection = parseCollectionData(action.library);
            return {
                ...state,
                loading: false,
                library: action.library,
                section: {
                    ...state.section,
                    ...parsedSection,
                    dependencyFilters: {...dependencies, ...getWithExpiry('section_plugin')}
                },
                page: {
                    ...state.page,
                    ...parsedPage,
                    dependencyFilters: {...dependencies, ...getWithExpiry('page_plugin')}
                },
                collection: {
                    ...state.collection,
                    ...parsedCollection,
                    dependencyFilters: {...dependencies, ...getWithExpiry('collection_plugin')}
                }
            };
        case 'SET_ACTIVE_CATEGORY':
            setWithExpiry(state.activeItemType + '_category', action.activeCategory, EXIPRY_TIME);
            setWithExpiry(state.activeItemType + '_page', 0, EXIPRY_TIME);
            return {
                ...state,
                [state.activeItemType]: {
                    ...state[state.activeItemType],
                    currentPage: 0,
                    activeCategory: action.activeCategory
                }
            };
        case 'SET_SEARCH_CONTEXT':
            setWithExpiry(state.activeItemType + '_search', action.searchContext, EXIPRY_TIME);
            setWithExpiry(state.activeItemType + '_page', 0, EXIPRY_TIME);
            return {
                ...state,
                [state.activeItemType]: {
                    ...state[state.activeItemType],
                    currentPage: 0,
                    searchContext: action.searchContext
                }
            };
        case 'SET_ACTIVE_PRICE_FILTER':
            setWithExpiry(state.activeItemType + '_price', action.activePriceFilter, EXIPRY_TIME);
            setWithExpiry(state.activeItemType + '_page', 0, EXIPRY_TIME);
            return {
                ...state,
                [state.activeItemType]: {
                    ...state[state.activeItemType],
                    currentPage: 0,
                    priceFilter: action.activePriceFilter
                }
            };
        case 'SET_ACTIVE_ITEM_TYPE':
            setWithExpiry('itemType', action.activeItemType, EXIPRY_TIME);
            return {
                ...state,
                activeItemType: action.activeItemType
            };
        case 'SET_DEPENDENCY_FILTERS':
            setWithExpiry(state.activeItemType + '_plugin', action.dependencyFilters, EXIPRY_TIME);
            setWithExpiry(state.activeItemType + '_page', 0, EXIPRY_TIME);
            return {
                ...state,
                [state.activeItemType]: {
                    ...state[state.activeItemType],
                    currentPage: 0,
                    dependencyFilters: action.dependencyFilters
                }
            }
        case 'SET_SORT_BY':
            setWithExpiry(state.activeItemType + '_sort', action.sortBy, EXIPRY_TIME);
            setWithExpiry(state.activeItemType + '_page', 0, EXIPRY_TIME);
            return {
                ...state,
                [state.activeItemType]: {
                    ...state[state.activeItemType],
                    currentPage: 0,
                    sortBy: action.sortBy
                }
            };
        case 'SET_CURRENT_PAGE':
            setWithExpiry(state.activeItemType + '_page', action.currentPage, EXIPRY_TIME);
            return {
                ...state,
                [state.activeItemType]: {
                    ...state[state.activeItemType],
                    currentPage: action.currentPage
                }
            };
        case 'SET_ACTIVE_COLLECTION':
            return {
                ...state,
                collection: {
                    ...state.collection,
                    activeCollection: action.activeCollection
                }
            };
        case 'SET_LOADING':
            return {
                ...state,
                loading: action.loading
            }
        case 'SET_COLUMNS':
            setWithExpiry('column', action.columns, EXIPRY_TIME);
            return {
                ...state,
                columns: action.columns
            }
        case 'APPEND_ERROR_MESSAGE':
            return {
                ...state,
                errorMessages: state.errorMessages.concat([action.errorMessage])
            }
        case 'DISCARD_ALL_ERROR_MESSAGES':
            return {
                ...state,
                errorMessages: []
            }
        case 'SET_INSTALLED_DEPENDENCIES':
            return {
                ...state,
                installedDependencies: action.installedDependencies
            }
        case 'SET_TOUR_OPEN':
            return {
                ...state,
                tour:  {
                    ...state.tour,
                    isOpen: action.isTourOpen
                }
            };
        case 'SET_TOUR_ACTIVE_BUTTON_GROUP':
            return {
                ...state,
                tour:  {
                    ...state.tour,
                    activeButtonGroup: action.data
                }
            };
        case 'SET_PREVIEW_VISIBLE':
            return {
                ...state,
                tour:  {
                    ...state.tour,
                    isPreviewVisible: action.isVisible
                }
            };
        case 'SET_IMPORTING_TEMPLATE':
            return {
                ...state,
                importingTemplate: action.importingTemplate
            }
        case 'SET_CHALLENGE_STEP':
            saveChallengeStep(action.data);
            return {
                ...state,
                challenge: {
                    ...state.challenge,
                    currentStep: action.data
                }
            }
        case 'SET_CHALLENGE_OPEN':
            return {
                ...state,
                challenge: {
                    ...state.challenge,
                    isOpen: action.data
                }
            }
        case 'SET_CHALLENGE_TOOLTIP_RECT':
            return {
                ...state,
                challenge: {
                    ...state.challenge,
                    tooltipRect: action.data
                }
            }
        case 'SET_CHALLENGE_FINAL_STATUS':
            return {
                ...state,
                challenge: {
                    ...state.challenge,
                    finalStatus: action.data
                }
            }
        case 'SET_CHALLENGE_PASSED':
            setWithExpiry('reduxChallengePassed', action.data, EXIPRY_TIME);
            return {
                ...state,
                challenge: {
                    ...state.challenge,
                    passed: action.data
                }
            }
        case 'SET_CHALLENGE_LIST_EXPANDED':
            return {
                ...state,
                challenge: {
                    ...state.challenge,
                    listExpanded: action.data
                }
            }
        case 'SET_ACTIVATE_DIALOG_DISPLAY':
            return {
                ...state,
                activateDialog: action.data
            }
        case 'SET_IMPORT_TO_APPEND':
            return {
                ...state,
                isImportToAppend: action.data
            }
        // Dependency Shortcut click handler: All, None, Installed and Reset
        case 'SELECT_DEPENDENCIES':
            const types = ['section', 'page', 'collection'];
            let atomHandler;
            switch(action.data) {
                case 'all': 
                case 'none':
                    const newValue = action.data === 'all';
                    atomHandler = (plugins) => plugins
                        .filter(plugin => ['none', 'gutenberghub.com', 'shareablock.com'].includes(plugin) === false)
                        .reduce(
                            (acc, key) => {
                                return { ...acc, [key]: { value: newValue, disabled: false } }
                            }, 
                            {
                                none: {value: true, disabled: false},
                                'gutenberghub.com': {value: true, disabled: false},
                                'shareablock.com': {value: true, disabled: false}
                            }
                        )
                    break;
                case 'installed':
                    atomHandler = (plugins) => getInstalledDependencies(plugins);
                    break;
                default:
                    atomHandler = (plugins) => getDefaultDependencies(plugins);
                    break;
            }
            const filtered = types.reduce( (acc, cur) => {
                // save to the local storage as well
                setWithExpiry(state.activeItemType + '_plugin', atomHandler(state[cur].wholePlugins), EXIPRY_TIME);
                return {
                    ...acc,
                    [cur]: {
                        ...state[cur],
                        dependencyFilters: atomHandler(state[cur].wholePlugins)
                    }
                }
            }, {});
            return {
                ...state,
                ...filtered
            };
        case 'CLEAR_SEARCH':
            return {
                ...state,
                section: {
                    ...state.section,
                    searchContext: ''
                },
                page: {
                    ...state.page,
                    searchContext: ''
                },
                collection: {
                    ...state.collection,
                    searchContext: ''
                }
            }
		case 'CLEAR_STATE':
			return {
				...state,
				section: {
					...state.section,
					priceFilter: '',
					activeCategory: '',
					searchContext: '',
				},
				page: {
					...state.page,
					priceFilter: '',
					activeCategory: '',
					searchContext: '',
				},
				collection: {
					...state.collection,
					priceFilter: '',
					activeCategory: '',
					searchContext: '',
				}
			}
    }

    return state;
};
