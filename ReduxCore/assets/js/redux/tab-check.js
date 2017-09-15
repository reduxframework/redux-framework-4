/* global redux */

(function( $ ) {
    'use strict';

    $.redux = $.redux || {};
    
    $.redux.tabCheck = function() {
        var link;

        $( '.redux-group-tab-link-a' ).click(
            function() {
                link = $( this );
                
                if ( link.parent().hasClass( 'empty_section' ) && link.parent().hasClass( 'hasSubSections' ) ) {
                    var elements = $( this ).closest( 'ul' ).find( '.redux-group-tab-link-a' );
                    var index = elements.index( this );
                    
                    link = elements.slice( index + 1, index + 2 );
                }
                
                var el          = link.parents( '.redux-container:first' );
                var relid       = link.data( 'rel' ); // The group ID of interest
                var oldid       = el.find( '.redux-group-tab-link-li.active:first .redux-group-tab-link-a' ).data( 'rel' );
                var opt_name    = el.data('opt-name');

                // change over redux object to correct opt_name
                redux.optName = window['redux_' + opt_name];
                
                //console.log ('container opt_name: ' + opt_name);
                //console.log('id: '+relid+' oldid: '+oldid);

                if ( oldid === relid ) {
                    return;
                }
                
                //console.log ('tab check: ' + redux.optName.args.opt_name);

                var cookieName = '';
                if ( !link.parents( '.postbox-container:first' ).length ) {
                    $( '#currentSection' ).val( relid );
                    
                    cookieName = 'redux_current_tab_' + redux.optName.args.opt_name;
                } else {
                    el.prev( '#currentSection' ).val( relid );

                    var boxIndex = el.data('index');
                    
                    if (boxIndex !== '') {
                        cookieName = 'redux_metabox_' + boxIndex + '_current_tab_' + redux.optName.args.opt_name;
                    }
                }

                // Set the proper page cookie
                $.cookie(
                    cookieName, relid, {
                        expires: 7,
                        path: '/'
                    }
                );

                if ( el.find( '#' + relid + '_section_group_li' ).parents( '.redux-group-tab-link-li' ).length ) {
                    var parentID = el.find( '#' + relid + '_section_group_li' ).parents( '.redux-group-tab-link-li' ).attr( 'id' ).split( '_' );
                    parentID = parentID[0];
                }

                el.find( '#toplevel_page_' + redux.optName.args.slug + ' .wp-submenu a.current' ).removeClass( 'current' );
                el.find( '#toplevel_page_' + redux.optName.args.slug + ' .wp-submenu li.current' ).removeClass( 'current' );

                el.find( '#toplevel_page_' + redux.optName.args.slug + ' .wp-submenu a' ).each(
                    function() {
                        var url = $( this ).attr( 'href' ).split( '&tab=' );
                        if ( url[1] === relid || url[1] === parentID ) {
                            $( this ).addClass( 'current' );
                            $( this ).parent().addClass( 'current' );
                        }
                    }
                );

                if ( el.find( '#' + oldid + '_section_group_li' ).find( '#' + oldid + '_section_group_li' ).length ) {
                    //console.log('RELID is child of oldid');
                    el.find( '#' + oldid + '_section_group_li' ).addClass( 'activeChild' );
                    el.find( '#' + relid + '_section_group_li' ).addClass( 'active' ).removeClass( 'activeChild' );
                } else if ( el.find( '#' + relid + '_section_group_li' ).parents( '#' + oldid + '_section_group_li' ).length || el.find( '#' + oldid + '_section_group_li' ).parents( 'ul.subsection' ).find( '#' + relid + '_section_group_li' ).length ) {
                    //console.log('RELID is sibling or child of OLDID');
                    if ( el.find( '#' + relid + '_section_group_li' ).parents( '#' + oldid + '_section_group_li' ).length ) {
                        //console.log('child of oldid');
                        el.find( '#' + oldid + '_section_group_li' ).addClass( 'activeChild' ).removeClass( 'active' );
                    } else {
                        //console.log('sibling');
                        el.find( '#' + relid + '_section_group_li' ).addClass( 'active' );
                        el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
                    }
                    el.find( '#' + relid + '_section_group_li' ).removeClass( 'activeChild' ).addClass( 'active' );
                } else {
                    el.find( '#' + relid + '_section_group_li' ).addClass( 'active' ).removeClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();

                    if ( el.find( '#' + oldid + '_section_group_li' ).find( 'ul.subsection' ).length ) {
                        //console.log('oldid is parent');
                        //console.log('#' + relid + '_section_group_li');

                        el.find( '#' + oldid + '_section_group_li' ).find( 'ul.subsection' ).slideUp(
                            'fast', function() {
                                el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' ).removeClass( 'activeChild' );
                            }
                        );
                
                        var newParent = el.find( '#' + relid + '_section_group_li' ).parents( '.hasSubSections:first' );

                        if ( newParent.length > 0 ) {
                            el.find( '#' + relid + '_section_group_li' ).removeClass( 'active' );
                            relid = newParent.find( '.redux-group-tab-link-a:first' ).data( 'rel' );
                            //console.log(relid);
                            if ( newParent.hasClass( 'empty_section' ) ) {
                                newParent.find( '.subsection li:first' ).addClass( 'active' );
                                el.find( '#' + relid + '_section_group_li' ).removeClass( 'active' ).addClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
                                newParent = newParent.find( '.subsection li:first' );
                                relid = newParent.find( '.redux-group-tab-link-a:first' ).data( 'rel' );
                                //console.log('Empty section, do the next one?');
                            } else {
                                el.find( '#' + relid + '_section_group_li' ).addClass( 'active' ).removeClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
                            }
                        }
                    } else if ( el.find( '#' + oldid + '_section_group_li' ).parents( 'ul.subsection' ).length ) {
                        //console.log('oldid is a child');
                        if ( !el.find( '#' + oldid + '_section_group_li' ).parents( '#' + relid + '_section_group_li' ).length ) {
                            //console.log('oldid is child, but not of relid');
                            el.find( '#' + oldid + '_section_group_li' ).parents( 'ul.subsection' ).slideUp(
                                'fast', function() {
                                    el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
                                    el.find( '#' + oldid + '_section_group_li' ).parents( '.redux-group-tab-link-li' ).removeClass( 'active' ).removeClass( 'activeChild' );
                                    el.find( '#' + relid + '_section_group_li' ).parents( '.redux-group-tab-link-li' ).addClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
                                    el.find( '#' + relid + '_section_group_li' ).addClass( 'active' );
                                }
                            );
                        } else {
                            //console.log('oldid is child, but not of relid2');
                            el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
                        }
                    } else {
                        //console.log('Normal remove active from child');
                        el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
                        
                        if ( el.find( '#' + relid + '_section_group_li' ).parents( '.redux-group-tab-link-li' ).length ) {
                            //console.log('here');
                            setTimeout( function() {
                                el.find( '#' + relid + '_section_group_li' ).parents( '.redux-group-tab-link-li' ).addClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
                            }, 50 );
                            
                            el.find( '#' + relid + '_section_group_li' ).addClass( 'active' );
                        }
                    }
                }

                // Show the group
                el.find( '#' + oldid + '_section_group' ).hide();

                el.find( '#' + relid + '_section_group' ).fadeIn(
                    200, function() {
                        if ( el.find( '#redux-footer' ).length !== 0 ) {
                            $.redux.stickyInfo(); // race condition fix
                        }
                        $.redux.initFields();
                    }
                );
        
                $( '#toplevel_page_' + redux.optName.args.slug ).find( '.current' ).removeClass( 'current' );
            }
        );

        if ( redux.optName.last_tab !== undefined ) {
            $( '#' + redux.optName.last_tab + '_section_group_li_a' ).click();
            
            return;
        }

        var tab = decodeURI( (new RegExp( 'tab' + '=' + '(.+?)(&|$)' ).exec( location.search ) || [, ''])[1] );

        if ( tab !== "" ) {
            if ( $.cookie( "redux_current_tab_get" ) !== tab ) {
                $.cookie(
                    'redux_current_tab', tab, {
                        expires: 7,
                        path: '/'
                    }
                );
        
                $.cookie(
                    'redux_current_tab_get', tab, {
                        expires: 7,
                        path: '/'
                    }
                );

                $( '#' + tab + '_section_group_li' ).click();
            }
        } else if ( $.cookie( 'redux_current_tab_get' ) !== "" ) {
            $.removeCookie( 'redux_current_tab_get' );
        }

        
        var sTab;
        var cookieName;
        var opt_name;
        
        $( '.redux-container' ).each(function(idx, val) {
            opt_name = $(this).data('opt-name');
            
            if ( !$(this).parents( '.postbox-container:first' ).length ) {
                cookieName = "redux_current_tab_" + opt_name;

                sTab = $(this).find( '#' + $.cookie( cookieName ) + '_section_group_li_a' );
            } else {
                var boxIndex = $(this).data('index');
                //console.log('boxindex: ' + boxIndex);            
                if (boxIndex === '') {
                    boxIndex = 0;
                }

                cookieName = 'redux_metabox_' + boxIndex + '_current_tab_' + opt_name;
                //console.log('cookiename: ' + cookieName);
                //console.log('cookie: ' + $.cookie( cookieName ));
                sTab = $(this).find( '#' + $.cookie( cookieName ) + '_section_group_li_a' );
            }

            // Tab the first item or the saved one
            if ( $.cookie( cookieName ) === null || typeof ($.cookie( cookieName )) === "undefined" || sTab.length === 0 ) {
                $(this).find( '.redux-group-tab-link-a:first' ).click();
            } else {
                sTab.click();
            }            
        });
    };
})(jQuery);