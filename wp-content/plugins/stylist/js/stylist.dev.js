;(function( $ ) {
	"use strict";

	// New global app variable.
	window.Stylist = {}
	// Currently selected element (String: css path).
	window.Stylist.selectedEl = false;
	// Responsive preview mode.
	// window.Stylist.responsiveView = false;

	// Placeholder. Redefined in React
	window.Stylist.responsiveView = function() {
		return false;
	}

	// Document ready.
	document.addEventListener( 'DOMContentLoaded', function() {
		// Frame ready.
		window.frames.stylist_iframe.addEventListener( 'load', function() {
			window.Stylist.mainBody = jQuery( document.body );

			// JS global vars.
			window.Stylist.iDoc = window.frames.stylist_iframe.contentDocument;
			window.Stylist.iWin = window.frames.stylist_iframe.contentWindow;
			window.Stylist.iBody = window.Stylist.iDoc.querySelector( 'body' );
			// jQuery global vars.
			window.Stylist.iWinJQ = jQuery( window.Stylist.iWin );
			window.Stylist.iDocJQ = jQuery( window.Stylist.iDoc );
			window.Stylist.iBodyJQ = jQuery( window.Stylist.iDoc.querySelector( 'body' ) );
		}, true );
	} );

	// Global Variable With Funcitons to recode latter.
	window.stlst_legacy = {}

	// All Stylist Functions.
	window.stylist_main = function() {
		/* ---------------------------------------------------- */
		/* Fix multiple load problem.                           */
		/* ---------------------------------------------------- */
		if ( jQuery( "body" ).hasClass( "stlst-stylist-loaded" ) ) {
			return false;
		}

		/* ---------------------------------------------------- */
		/* Windows                                              */
		/* ---------------------------------------------------- */
		window.setSelector = false;
		window.separator = ' ';
		window.minCroppedSelector = false;

		/* ---------------------------------------------------- */
		/* Setup Default Varriables                             */
		/* ---------------------------------------------------- */
		var iframe = jQuery('#stylist_iframe').contents();
		var iframeBody = window.Stylist.iDocJQ.find( "body");
		var body = jQuery( document.body ).add( iframeBody );
		var mainDocument = jQuery( document ).add( iframe);
		var mainBody = jQuery( document.body );

		// puse js
		var iframejs = document.getElementById('stylist_iframe');

		if (null === iframejs ) {

		} else {
			iframejs = ( iframejs.contentWindow || iframejs.contentDocument );
			iframejs = iframejs.document;
		}

		/* ---------------------------------------------------- */
		/* Check Undoable / Redoable                            */
		/* ---------------------------------------------------- */
		// window.Stylist.updateHistory();

		/* ---------------------------------------------------- */
		/* Shows the current media queries                      */
		/* ---------------------------------------------------- */
		window.stlst_legacy.updateResponsiveBreakpoints = function() {
			updateResponsiveBreakpoints();
		}

		function updateResponsiveBreakpoints() {

			// Bar Element
			var bar = jQuery( ".breakpoint-bar");

			// bar.find( "div").tooltip('destroy');

			// Empty
			bar.empty();

			// Getting all media queries as an array.
			var queries = window.stlst_legacy.getAllMediaQueries();
			var mediaTypeSelector = document.getElementById( 'stlst-media-type' )
			var currentMediaType = window.Stylist.currentMediaType()



			// ============================================================

			// Show breakpoint information.
			if ( 'max-width' === currentMediaType ) {
				bar.append( "<span class='breakpoint-right-notice'>defined breakpoints</span>");
			} else {
				bar.append( "<span class='breakpoint-left-notice'>defined breakpoints</span>");
			}

			// number Value
			var val = 0;
			var num = 0;
			var query;

			// Window Width
			var winWidth = jQuery( window ).width();

			// Has queries?
			if ( queries.length > 0 ) {
				// Each of all Queries.
				jQuery.each( queries, function( index, value ) {
					// Simple query.
					query = process_media_query( value );
					// isset?
					if ( window.stlst_legacy.isDefined( query ) ) {
						// String
						query = query.toString();
						// clean < and > symbols.
						val = parseInt( number_filter( query.replace( /\</g, "" ).replace( /\>/g, "" ) ) );

						// Real number value. non proccesed.
						// proccessed convert rem, em to PX
						// we using NUM for find the media in data
						num = space_cleaner( value.match(/\:(.*?)\)/g).toString().replace(/\:/g,"").replace(/\)/g,""));

						// be sure it a simple media query.
						if ( query.indexOf( ",") == -1 && query.indexOf( "and") == -1 ) {
							// Just Min Width.
							if ( query.indexOf( ">") != -1 && currentMediaType === 'min-width' ) {
								// If not has.
								if ( jQuery( document ).find( "#min-breakpoint-"+val+"" ).length == 0 ) {
									// Append.
									bar.append( "<div data-breakpoint='" + val + "' data-media-content='" + value + "' data-breakpoint-data='(min-width:" + num + ")' class='min-width' id='min-breakpoint-" + val + "' style='left:" + parseInt( val ) + "px;width:" + ( winWidth - parseInt( val ) ) + "px;'>" + val + "</div>" );
								}
							}

							// Just Max Width.
							if ( query.indexOf( "<") != -1 && currentMediaType === 'max-width' ) {
								// If not has.
								if ( jQuery( document ).find( "#max-breakpoint-"+val+"").length == 0 ) {
									// Append.
									bar.append( "<div data-breakpoint='"+val+"' data-media-content='"+value+"' data-breakpoint-data='(max-width:"+num+")' class='max-width' id='max-breakpoint-"+val+"' style='width:"+val+"px;'>"+val+"</div>");
								}
							}
						}
					}
				} );

				// Sorting breakpoints by value.
				bar.find('div').sort(function(a, b) {
					return +a.dataset.breakpoint - +b.dataset.breakpoint;
				}).appendTo(bar);

				// Updating Max Width Bar
				var prevWidth = 0;
				bar.find( "div").each( function( i,v) {

					// Object
					var element = jQuery(this);
					var nextElement = element.next( ".max-width");

					// Next Width
					var nextWidth = parseFloat(nextElement.css( "width"));

					// Fix Max width format
					if (element.hasClass( "max-width" ) ) {
						prevWidth = parseFloat(element.css( "width")) + prevWidth;
					}

					// Updating Positions
					nextElement.css( "width",nextWidth-prevWidth);
					nextElement.css( "left",prevWidth);
					element.css( "z-index",i);

				});

				// Updating Min Width Bar
				bar.find( ".min-width").each(function( i,v){

					// Object
					var element = jQuery(this);
					var nextElement = element.nextAll( ".min-width");

					// If has next
					if (nextElement.length > 0){

						// Getting Breakpoint Values
						var elementPoint = parseInt(element.attr( "data-breakpoint"));
						var nextPoint = parseInt(nextElement.attr( "data-breakpoint"));

						// Find Dif
						var maxUntil = nextPoint-elementPoint;

						// Updating width
						element.css( "width",maxUntil+"px");
					}
				});

				// Fix small media queries
				bar.find( "div").each(function(){

					var element = jQuery(this);

					if( parseInt(element.css( "width")) <= 100 ){
						element.css( "background-image","none").css( "text-align","center").css( "padding","0px").css( "min-width","13px");
					}

					if( parseInt(element.css( "width")) <= 40 ){
						element.css( "font-size","10px");
					}
				});

				// Adding Toolip to breakponts
				// jQuery( ".breakpoint-bar div").tooltip({

				//     // Set dynamic title
				//     title: function(){
				//         var text = jQuery( ".media-control").text();
				//         var breakpoint = jQuery(this).attr( "data-breakpoint");
				//         return "<span class='smaller-text-tooltip'>" + breakpoint + "px and "+text+" screens</span>";
				//     },

				//     // Tooltip settings
				//     animation: false,
				//     delay: { show: 10, hide: 0 },
				//     placement: 'bottom',
				//     trigger: 'hover',
				//     container: "body",
				//     html: true

				// });


			}

		}



		/* ---------------------------------------------------- */
		/* get selected element object                          */
		/* ---------------------------------------------------- */
		window.stlst_legacy.get_selected_element = function() {
			return get_selected_element();
		}

		function get_selected_element() {
			var iframeDoc = window.frames['stylist_iframe'].contentDocument;

			return jQuery( iframeDoc.querySelector( ".stlst-selected") );
			// return jQuery( iframe).find( ".stlst-selected");

		}

		/* ---------------------------------------------------- */
		/* IS Functions Lists                                   */
		/* ---------------------------------------------------- */
		window.stlst_legacy.is_content_selected = function () {
			var iframeDoc = window.frames['stylist_iframe'].contentDocument;
			var iframeBody = jQuery( iframeDoc.querySelector( "body" ) );

			return iframeBody.hasClass( "stlst-content-selected");
		}

		window.stlst_legacy.is_dragging = function () {
			return is_dragging()
		}
		function is_dragging(){
			return mainBody.hasClass( "stlst-dragging");
		}

		window.stlst_legacy.is_resizing = function () {
			return is_resizing()
		}
		function is_resizing(){

			return mainBody.hasClass( "stlst-element-resizing");

		}

		window.stlst_legacy.is_visual_editing = function () {
			return is_visual_editing()
		}
		function is_visual_editing(){

			return mainBody.hasClass( "stlst-visual-editing");

		}

		window.stlst_legacy.is_responsive_mod = function(){

			return is_responsive_mod();

		}


		function is_responsive_mod(){

			// return mainBody.hasClass( "stlst-responsive-device-mode");
			return window.Stylist.responsiveView();
		}

		// cache
		window.scroll_width = get_scroll_bar_width();


		/* ---------------------------------------------------- */
		/* Draw Responsive Handles                              */
		/* ---------------------------------------------------- */
		window.stlst_legacy.draw_responsive_handle = function() {
			if ( window.stlst_legacy.is_responsive_mod() === false ) {
				return false;
			}

			// variables
			var iframeElement = jQuery( "#stylist_iframe");

			if ( isUndefined( window.FrameleftOffset ) ){
				var offset = iframeElement.offset();
				window.FrameleftOffset = offset.left;
				window.FrametopOffset = offset.top;
			}

			var w = iframeElement.width();
			var h = iframeElement.height();

			var left = window.FrameleftOffset + w;
			var top = window.FrametopOffset + h;


			jQuery( ".responsive-right-handle" ).css( "left", left )
				.css( "top", window.FrametopOffset )
				.css( "height", h + 20);

			jQuery( ".responsive-bottom-handle" ).css( "left", window.FrameleftOffset )
				.css( "top", top)
				.css( "width", w);

		}

		// Right
		window.Stylist.responsiveDrag = false;
		window.SelectorDisableResizeRight = false;
		window.rulerWasActive = false;
		window.selectorWasActive = false;


		/* ---------------------------------------------------- */
		/* Responsive Right Handle                              */
		/* ---------------------------------------------------- */
		jQuery( ".responsive-right-handle" ).on( "mousedown", function( e ) {

			// jQuery('.responsive-right-handle').tooltip( "hide");

			window.Stylist.responsiveDrag = true;
			// iframeBody?????
			body.addClass( "stlst-clean-look stlst-responsive-resizing stlst-responsive-resizing-right stlst-hide-borders-now");

			if (jQuery( ".stlst-selector-mode").hasClass( "active" ) ) {
				window.selectorWasActive = true;
			}else{
				window.selectorWasActive = false;
			}

			if (jQuery( ".stlst-selector-mode").hasClass( "active") && window.stlst_legacy.is_content_selected() === false) {
				jQuery( ".stlst-selector-mode").trigger( "click");

				window.SelectorDisableResizeRight = true;
			}

		});


		/* ---------------------------------------------------- */
		/* Responsive Right Handle                              */
		/* ---------------------------------------------------- */
		mainDocument.on( "mousemove", function( e ) {
			if ( true === window.Stylist.responsiveDrag ) {
				var hasClass = mainBody.hasClass( "stlst-css-editor-active");
				var ww = jQuery( window ).width();

				if ( true === hasClass ) {
					e.pageX = e.pageX - 450 - 10;
				} else {
					e.pageX = e.pageX - 10;
				}

				// Min 320
				if (e.pageX < 320) {
					e.pageX = 320;
				}

				// Max full-80 W
				if (hasClass) {
					if (e.pageX > ww - 80 - 450) {
						e.pageX = ww - 80 - 450;
					}
				} else {
					if (e.pageX > ww - 80 - 49) {
						e.pageX = ww - 80 - 49;
					}
				}

				jQuery( "#stylist_iframe").width(e.pageX);

				window.stlst_legacy.draw_responsive_handle();
				updateResponsiveSizeNotice();
			}
		} )

		/* ---------------------------------------------------- */
		/* Responsive Right Handle                              */
		/* ---------------------------------------------------- */
		// ============================================================
		mainDocument.on( "mouseup", function() {
			if ( window.Stylist.responsiveDrag === true ) {
				window.Stylist.responsiveDrag = false;

				if ( window.SelectorDisableResizeBottom === false ) {
					window.stlst_legacy.draw();
				}

				body.removeClass( "stlst-clean-look stlst-responsive-resizing stlst-responsive-resizing-right");

				setTimeout(function() {
					body.removeClass( "stlst-hide-borders-now");
				}, 25);

				if ( window.SelectorDisableResizeRight === true ) {
					window.SelectorDisableResizeRight = false;
				}

				if (window.selectorWasActive === true){
					if (jQuery( ".stlst-selector-mode").hasClass( "active") === false){
						jQuery( ".stlst-selector-mode").trigger( "click");
					}
				}else{
					if (jQuery( ".stlst-selector-mode").hasClass( "active" ) ) {
						jQuery( ".stlst-selector-mode").trigger( "click");
					}
				}

				setTimeout(function() {
					jQuery( ".reset-enable").removeClass( "reset-enable");
				}, 10);
			}
		});

		// Bottom
		window.responsiveModeBMDown = false;
		window.SelectorDisableResizeBottom = false;


		/* ---------------------------------------------------- */
		/* Responsive Bottom Handle                             */
		/* ---------------------------------------------------- */
		jQuery( ".responsive-bottom-handle").on( "mousedown", function() {
			window.responsiveModeBMDown = true;
			body.addClass( "stlst-clean-look stlst-responsive-resizing stlst-responsive-resizing-bottom stlst-hide-borders-now");

			if (jQuery( ".stlst-selector-mode").hasClass( "active" ) ) {
				window.selectorWasActive = true;
			}else{
				window.selectorWasActive = false;
			}

			if (jQuery( ".stlst-selector-mode").hasClass( "active") && window.stlst_legacy.is_content_selected() === false) {
				jQuery( ".stlst-selector-mode").trigger( "click");
				window.SelectorDisableResizeBottom = true;
			}

		});


		/* ---------------------------------------------------- */
		/* Responsive Bottom Handle                             */
		/* ---------------------------------------------------- */
		mainDocument.on( "mousemove", function( e ) {
			if (window.responsiveModeBMDown === true) {
				var ext = 0;
				if (mainBody.hasClass( "stlst-html-mod-active" ) ) {
					ext = 42;
				}

				if (jQuery(this).find( "#stylist_iframe").length > 0) {
					e.pageY = e.pageY - 48 - ext;
				}

				// Min 320
				if (e.pageY < 320) {
					e.pageY = 320;
				}

				// Max full-80 H
				if (e.pageY > jQuery( window ).height() - 80 - 40 - ext ) {
					e.pageY = jQuery( window ).height() - 80 - 40 - ext;
				}

				jQuery( "#stylist_iframe").height(e.pagey );
				window.stlst_legacy.draw_responsive_handle();

				updateResponsiveSizeNotice();

			}
		});


		/* ---------------------------------------------------- */
		/* Responsive Bottom Handle                             */
		/* ---------------------------------------------------- */
		mainDocument.on( "mouseup", function() {

			if (window.responsiveModeBMDown === true) {
				window.responsiveModeBMDown = false;

				if (window.SelectorDisableResizeBottom === false) {
					window.stlst_legacy.draw();
				}

				body.removeClass( "stlst-clean-look stlst-responsive-resizing stlst-responsive-resizing-bottom");

				setTimeout(function() {
					body.removeClass( "stlst-hide-borders-now");
				}, 25);

				if (window.SelectorDisableResizeBottom === true) {
					jQuery( ".stlst-selector-mode").trigger( "click");
					window.SelectorDisableResizeBottom = false;
				}

				if (window.selectorWasActive === true){
					if (jQuery( ".stlst-selector-mode").hasClass( "active") === false){
						jQuery( ".stlst-selector-mode").trigger( "click");
					}
				}else{
					if (jQuery( ".stlst-selector-mode").hasClass( "active" ) ) {
						jQuery( ".stlst-selector-mode").trigger( "click");
					}
				}

				// Update options
				// window.stlst_legacy.insert_default_options();

				setTimeout(function() {
					jQuery( ".reset-enable").removeClass( "reset-enable");
				}, 10);

			}

		});


		/* ---------------------------------------------------- */
		/* Disable Form submission in iframe                    */
		/* ---------------------------------------------------- */
		window.Stylist.iDocJQ.find( "form").submit(function( e ) {
			e.preventDefault();
			return false;
		});

		var wasLast = false;
		var wasLastPrefix = false;


		/* ---------------------------------------------------- */
		/* Number filter for numberic properties input          */
		/* ---------------------------------------------------- */
		// @todo: make it work again.
		jQuery( ".stlst-after-css-val").keyup(function( e ) {

			// Number only
			var numbers = jQuery(this).val().replace(/[^0-9.,-]/g,'');

			if (numbers.length === 0){
				numbers = 0;
			}

			// non-number only
			var prefixs = jQuery(this).val().replace(/[0-9.,-]/g,'');

			var prefixSelector = jQuery(this).parent().find( ".stlst-after-prefix");

			if (prefixs.length > 0){

				jQuery(this).val(numbers);

				prefixSelector.val(prefixs);

				// Focus
				prefixSelector.val(prefixSelector.val()).trigger( "focus");

			}

		});


		/* ---------------------------------------------------- */
		/* Getting option ID                                    */
		/* ---------------------------------------------------- */
		function get_option_id(element ) {
			return jQuery(element ).attr( "id").replace( "-group", "");
		}


		/* ---------------------------------------------------- */
		/* hasAttr Fn                                           */
		/* ---------------------------------------------------- */
		$.fn.hasAttr = function(name) {
			return this.attr(name) !== undefined;
		};

		/* ---------------------------------------------------- */
		/* getCursorPosition Fn                                 */
		/* ---------------------------------------------------- */
		$.fn.getCursorPosition = function() {
			var input = this.get(0);
			if (!input ) return; // No ( input ) element found
			if ('selectionStart' in input ) {
				// Standard-compliant browsers
				return input.selectionStart;
			} else if ( document.selection) {
				// IE
				input.focus();
				var sel = document.selection.createRange();
				var selLen = document.selection.createRange().text.length;
				sel.moveStart('character', -input.value.length);
				return sel.text.length - selLen;
			}
		};



		/* ---------------------------------------------------- */
		/* window.stlst_legacy.isDefined                                            */
		/* ---------------------------------------------------- */
		window.stlst_legacy.isDefined = function (a){
			if (typeof a !== typeof undefined && a !== false && a != '' && a != ' ' && a != 'undefined' && a !== null){
				return true;
			}else{
				return false;
			}
		}


		/* ---------------------------------------------------- */
		/* IsUndefined                                          */
		/* ---------------------------------------------------- */
		window.stlst_legacy.isUndefined = function (a) {
			return isUndefined(a);
		}
		function isUndefined(a){
			if (typeof a === typeof undefined || a === false || a === '' || a == ' ' || a == 'undefined' || a === null){
				return true;
			}else{
				return false;
			}
		}


		/* ---------------------------------------------------- */
		/* CSSImportant Fn                                      */
		/* ---------------------------------------------------- */
		$.fn.cssImportant = function(rule, value) {

			// Set default CSS.
			this.css(rule, value);

			// add important
			jQuery(this).attr( "style", this.attr( "style").replace(rule + ": " + value, rule + ": " + value + " !important"));

		};

		/* ---------------------------------------------------- */
		/* Setup The plugin                                     */
		/* ---------------------------------------------------- */
		body.addClass( "stlst-stylist");
		body.addClass( "stlst-stylist-loaded");

		// Default Option Change
		// window.option_changeType = 'auto';
		// window.stlst_legacy.option_change();
		// window.option_changeType = 'default';

		// Setup the title
		jQuery( "title").html( "Stylist: " + window.Stylist.iDocJQ.find( "title").html());


		// Check before exit page.
		window.onbeforeunload = confirm_exit;


		/* ---------------------------------------------------- */
		/* Alert before exit                                    */
		/* ---------------------------------------------------- */
		// @todo: make it work again.
		function confirm_exit() {

			if (jQuery( ".stlst-save-btn").hasClass( "waiting-for-save")) {
				return confirm(stylistJsData.l18_sure);
			}

		}

		/* ---------------------------------------------------- */
		/* Breakpoint bar click                                 */
		/* ---------------------------------------------------- */
		/* jQuery( document ).on( "mouseover click",".breakpoint-bar div",function( e ){

			// Breakpoint element
			var el = jQuery(this);

			// 40ms delay
			window.breakpointHoverTimer = setTimeout(function(){

				// Delete all old viewers
				window.Stylist.iDocJQ.find( ".stlst-element-viewer").remove();

				// variables
				var element_offset,element,topBoxesI,leftBoxesI,widthBoxesI,heightBoxesI,selector,elements;

				// each all founded data
				the_editor_data().find('[data-size-mode="'+el.attr('data-breakpoint-data')+'"]').each(function( i,v){

					// find selectors
					selector = get_foundable_query(jQuery(this).html().match(/\{(.*?)\{/g).toString().replace(/\{/g,""),true,true,true);

					// element object
					elements = window.Stylist.iDocJQ.find(selector);

					// Each all founded elements
					elements.each(function(){

						// element object
						element = jQuery(this);

						// offset
						element_offset = element.offset();

						// check if valid
						if (window.stlst_legacy.isDefined(element_offset )) {

							// getting element positions
							topBoxesI = element_offset.top;
							leftBoxesI = element_offset.left;

							if (leftBoxesI < 0) {
								leftBoxesI = 0;
							}

							// Getting element sizes
							widthBoxesI = element.outerWidth();
							heightBoxesI = element.outerHeight();

							var id = "stlst-element-viewer-"+parseInt(widthBoxesI)+"-"+parseInt(heightBoxesI)+"-"+parseInt(topBoxesI)+"-"+parseInt(leftBoxesI)+"";

							// add the viewer
							if (window.Stylist.iDocJQ.find( "#"+id).length == 0){
								iframeBody.append( "<div class='stlst-element-viewer' id='"+id+"' style='width:"+widthBoxesI+"px;height:"+heightBoxesI+"px;top:"+topBoxesI+"px;left:"+leftBoxesI+"px;'></div>");
							}

						} // element offset if


					}); // elements each

				}); // each founded breakpoint datas

			},40); // delay.

		}); */


		/* ---------------------------------------------------- */
		/* Hide breakpoint on click                             */
		/* ---------------------------------------------------- */
		jQuery( document ).on( "mousedown",".breakpoint-bar div",function( e ){

			// jQuery(this).tooltip( "hide");

		});


		/* ---------------------------------------------------- */
		/* Shows affected elements when mouseover the breakpoint*/
		/* ---------------------------------------------------- */
		/* jQuery( document ).on( "mouseout",".breakpoint-bar div",function( e ){

			clearTimeout(window.breakpointHoverTimer);
			window.Stylist.iDocJQ.find( ".stlst-element-viewer").remove();

		}); */

		/* ---------------------------------------------------- */
		/* Breakpoint bar click                                 */
		/* ---------------------------------------------------- */
		jQuery( document ).on( "click",".breakpoint-bar div",function( e ){

			// jQuery('.responsive-right-handle').tooltip( "hide");

			// Getting breakpoint value
			var n = jQuery(this).attr( "data-breakpoint");

			// Go
			jQuery( "#stylist_iframe").width(n);

			// Update
			window.stlst_legacy.draw_responsive_handle();
			updateResponsiveSizeNotice();
			window.stlst_legacy.draw();

		});


		/* ---------------------------------------------------- */
		/* Updating Responsive Size Notice                      */
		/* ---------------------------------------------------- */
		window.stlst_legacy.updateResponsiveSizeNotice = function() {
			updateResponsiveSizeNotice();
		}

		function updateResponsiveSizeNotice() {

			if ( is_responsive_mod() === false ) {
				return false;
			}

			var s = jQuery( "#stylist_iframe").width();
			var device = '';

			// Set device size.
			jQuery( ".device-size").text(s);

			if ( 'max-width' === document.getElementById( 'stlst-media-type' ).value ) {

				device = '(phones)';

				if (s >= 375) {
					device = '(Large phones)';
				}

				if (s >= 414) {
					device = '(tablets & landscape phones)';
				}

				if (s >= 736) {
					device = '(tablets)';
				}

				if (s >= 768) {
					device = '(small desktops & tablets and phones)';
				}

				if (s >= 992) {
					device = '(medium desktops & tablets and phones)';
				}

				if (s >= 1200) {
					device = '(large desktops & tablets and phones)';
				}

			} else {

				device = '(phones & tablets and desktops)';

				if (s >= 375) {
					device = '(phones & tablets and desktops)';
				}

				if (s >= 414) {
					device = '(large phones & tablets and desktops)';
				}

				// Not mobile.
				if (s >= 736) {
					device = '(landscape phones & tablets and desktops)';
				}

				// Not tablet
				if (s >= 768) {
					device = '( desktops)';
				}

				// Not small desktop
				if (s >= 992) {
					device = '(medium & large desktops)';
				}

				// Not medium desktop
				if (s >= 1200) {
					device = '(large desktops)';
				}

			}

			// Set device name.
			jQuery( ".device-name" ).text( device);


			window.Stylist.setCurrentPreviewWidth( s );

		}

		/* ---------------------------------------------------- */
		/* Updating slider by input                             */
		/* ---------------------------------------------------- */
		function update_slide_by_input(element,value,prefix) {

			var elementParent = element.parent().parent().parent();
			var range;

			if (value === false){
				value = element.parent().find( ".stlst-after-css-val").val();
				prefix = element.parent().find( ".stlst-after-prefix").val();
			}

			var slide = element.parent().parent().find( ".wqNoUi-target");

			// Update PX
			if (prefix == 'px') {
				range = elementParent.data( "px-range").split( ",");
			}

			// Update %.
			if (prefix == '%') {
				range = elementParent.data( "pc-range").split( ",");
			}

			// Update EM.
			if (prefix == 'em') {
				range = elementParent.data( "em-range").split( ",");
			}

			// Update S.
			if (prefix == 's' || prefix == '.s') {
				range = elementParent.data( "em-range").split( ",");
			}

			// min and max values
			if (range === undefined || range === false) {
				return false;
			}

			var min = parseInt(range[0]);
			var max = parseInt(range[1]);

			if (value < min) {
				min = value;
			}

			if (value > max) {
				max = value;
			}

			if ( isNaN(min) === false && isNaN(max) === false && isNaN(value) === false){

				slide.wqNoUiSlider({
					range: {
						'min': parseInt(min),
						'max': parseInt(max)
					},

					start: value
				}, true);

			}

		}

		/* ---------------------------------------------------- */
		/* Right Click disable                                  */
		/* ---------------------------------------------------- */
		mainDocument.contextmenu(function() {
			return false;
		});


		// auto insert.
		window.disable_auto_insert = false;

		/* ---------------------------------------------------- */
		/* Basic Sharp Selector For Editor                      */
		/* ---------------------------------------------------- */
		window.stlst_legacy.get_live_selector = function (element ) {
			return get_live_selector(element );
		}

		function get_live_selector(element ){

			if (element === undefined){
				element = get_selected_element();
			}

			// Be sure this item is valid.
			if (element[0] === undefined || element[0] === false || element[0] === null) {
				return false;
			}

			// Tag info
			var tag = element[0].tagName.toLowerCase();

			// Getting item parents.
			var parents = element.parentsUntil( "body"), selector = 'body', currentSelector;

			// Get last selector
			var lastSelector = window.stlst_legacy.get_best_class(element );

			// Foreach all loops.
			for (var i = parents.length - 1; i >= 0; i--) {

				currentSelector = window.stlst_legacy.get_best_class(parents[i]);

				if (/\.|#/g.test(currentSelector)){
					currentSelector = parents[i].tagName.toLowerCase()+currentSelector;
				}

				selector = space_cleaner(selector).trim() + " > " + currentSelector + window.separator;

			} // Each end.

			selector = space_cleaner(selector + " > " + lastSelector + ".stlst-selected");

			return selector;

		}


		/* ---------------------------------------------------- */
		/* Single Selector                                      */
		/* ---------------------------------------------------- */
		window.stlst_legacy.single_selector = function (selector, test ) {
			return single_selector(selector, test );
		}
		function single_selector(selector,test ) {

			var customClass = 'stlst-selected';
			if (mainBody.hasClass( "stlst-control-key-down") && window.stlst_legacy.is_content_selected()){
				customClass = 'stlst-multiple-selected';
			}

			var selectorArray = get_selector_array(selector);
			var i = 0;
			var indexOf = 0;
			var selectorPlus = '';

			for ( i = 0; i < selectorArray.length; i++) {

				if ( i > 0) {
					selectorPlus += window.separator + selectorArray[i];
				} else {
					selectorPlus += selectorArray[i];
				}

				if (window.Stylist.iDocJQ.find(selectorPlus).length > 1) {

					window.Stylist.iDocJQ.find(selectorPlus).each(function(){

						if (selectorPlus.substr(selectorPlus.length - 1) != ')') {

							if (jQuery(this).parent().length > 0) {

								indexOf = 0;

								jQuery(this).parent().children().each(function() {

									indexOf++;

									if (jQuery(this).find( "."+customClass).length > 0 || jQuery(this).hasClass((customClass))) {

										selectorPlus = selectorPlus + ":nth-child( " + indexOf + ")";

									}

								});

							}

						}

					});

				}

			}


			// Clean no-need nth-childs.
			if (selectorPlus.indexOf( ":nth-child") != -1){

				// Selector Array
				selectorArray = get_selector_array(selectorPlus);

				// Each all selector parts
				for( i = 0; i < selectorArray.length; i++){

					// Get previous parts of selector
					var prevAll = get_previous_item(selectorArray,i).join( " ");

					// Gext next parts of selector
					var nextAll = get_next_item(selectorArray,i).join( " ");

					// check the new selector
					var selectorPlusNew = prevAll + window.separator + selectorArray[i].replace(/:nth-child\((.*?)\)/i,'') + window.separator + nextAll;

					// clean
					selectorPlusNew = space_cleaner(selectorPlusNew);

					// Check the selector without nth-child and be sure have only 1 element.
					if (window.Stylist.iDocJQ.find(selectorPlusNew).length == 1){
						selectorArray[i] = selectorArray[i].replace(/:nth-child\((.*?)\)/i,'');
					}

				}

				// Array to spin, and clean selector.
				selectorPlus = space_cleaner(selectorArray.join( " "));

			}


			// Return the selector without use "add_children_selector" func.
			// Test parement used in window.stlst_legacy.get_parents func
			if (test ){
				return selectorPlus;
			}


			// Ready.
			return multiple_variation(add_children_support(selectorPlus));

		}


		/* ---------------------------------------------------- */
		/* Adds > symbol to single_selector func                */
		/* ---------------------------------------------------- */
		function add_children_support(selector){

			// Add " > " to selector. Stable V.
			var selectorArray = get_selector_array(selector);
			var newSelector = '', inSelected, thisSelector, testSelector;
			for(var i = 0; i < selectorArray.length; i++){

				// Don't use nth-child while spin it
				thisSelector = selectorArray[i].replace(/:nth-child\((.*?)\)/i,'');

				// To check select in select
				testSelector = space_cleaner($.trim(newSelector+window.separator+thisSelector+window.separator+thisSelector));

				// Check if same selector has in the selector
				inSelected = window.Stylist.iDocJQ.find(testSelector).length;

				if ( inSelected > 0){
					newSelector = space_cleaner(newSelector) + " > " + selectorArray[i] + window.separator; // Add With '>' separator
				}else{
					newSelector += selectorArray[i] + window.separator; // Add with space separator
				}

			}


			// Need trim to continue.
			newSelector = $.trim(newSelector);


			// Add > symbol to last if selector still finding more element than one.
			if (window.Stylist.iDocJQ.find(newSelector).length > 1){
				newSelector = newSelector.replace(/(?=[^ ]*$)/i,' > ');
			}

			// Cleans ">" symbols from selector if not need.
			if (newSelector.indexOf( ">") != -1){

				var length = newSelector.split( ">").length;
				var elementLength = window.Stylist.iDocJQ.find(newSelector).length;

				for(var i = 1; i < length; i++){

					if (window.Stylist.iDocJQ.find(newSelector.replace(/ > /i,' ')).length == elementLength){
						newSelector = newSelector.replace(/ > /i,' ');
					}

				}

			}

			// Return it
			return space_cleaner(newSelector);

		}


		/* ---------------------------------------------------- */
		/* Previous array item                                  */
		/* ---------------------------------------------------- */
		function get_previous_item(arr,current ){

			var result = [];

			for(var i = 0; i < arr.length; i++){

				if ( i < current ){

					result.push(arr[i]);

				}

			}

			return result;

		}


		/* ---------------------------------------------------- */
		/* Next array item                                      */
		/* ---------------------------------------------------- */
		function get_next_item(arr,current ){

			var result = [];

			for(var i = 0; i < arr.length; i++){

				if ( i > current ){

					result.push(arr[i]);

				}

			}

			return result;

		}


		/* ---------------------------------------------------- */
		/* Measuring Tool                                       */
		/* ---------------------------------------------------- */
		/* mainDocument.on( "mousemove mousedown", function( e ){

			if (mainBody.hasClass( "stlst-metric-disable") === false) {

				var x = e.pageX;
				var y = e.pageY;
				var cx = e.clientX;
				var cy = e.clientY;
				var ww = jQuery( window ).width();
				var wh = jQuery( window ).height();

				if (mainBody.hasClass( "stlst-responsive-resizing")) {
					y = y - 10;
					x = x - 10;
					cx = cx - 10;
					cy = cy - 10;
				}

				if (jQuery(this).find( "#stylist_iframe").length > 0) {

					if ( is_responsive_mod()) {

						if (mainBody.hasClass( "stlst-responsive-resizing")) {

							// Min 320 W
							if (cx < 320 + 48) {
								cx = 320 + 48;
							}

							// Max full-80 W
							if (cx > ww - 82) {
								cx = ww - 82;
							}

							// Min 320 H
							if (cy < 320 + 40) {
								cy = 320 + 40;
							}

							// Max full-80 H
							if (cy > wh - 80) {
								cy = wh - 80;
							}

						}
					}
				}

				if (jQuery(this).find( "#stylist_iframe").length === 0) {

					if (mainBody.hasClass( "stlst-responsive-resizing")) {

						// Min 320 W
						if (cx < 320) {
							cx = 320;
						}

						// Max full W
						if (cx > ww) {
							cx = ww;
						}

						// Min 320 H
						if (cy < 320) {
							cy = 320;
						}

						// Max full H
						if (cy > wh) {
							cy = wh;
						}

					}
				}

			}

		}); */


		/* ---------------------------------------------------- */
		/* Measuiring Tool: Hover Box                           */
		/* ---------------------------------------------------- */
		/* iframe.on( "mousemove", function( e ) {

			if (mainBody.hasClass( "stlst-metric-disable") === false){

				var element = jQuery(e.target );

				if ( is_resizing() || is_visual_editing() || is_dragging()) {
					element = get_selected_element();
				}

				if (element.hasAttr( "class" ) ) {
					if (/(^|\s+)stlst-(.*?)/g.test(element.attr( "class"))){
						element = get_selected_element();
					}
				}

				// CREATE SIMPLE BOX
				var element_offset = element.offset();

				if (window.stlst_legacy.isDefined(element_offset )) {

					var topBoxesI = element_offset.top;
					var leftBoxesI = element_offset.left;

					if (leftBoxesI < 0) {
						leftBoxesI = 0;
					}

					var widthBoxesI = element.outerWidth();
					var heightBoxesI = element.outerHeight();

					// Dynamic Box
					if (window.Stylist.iDocJQ.find( ".hover-info-box").length === 0) {
						iframeBody.append( "<div class='hover-info-box'></div>");
					}

					window.Stylist.iDocJQ.find( ".hover-info-box").css( "width", widthBoxesI).css( "height", heightBoxesI).css( "top", topBoxesI).css( "left", leftBoxesI);

				}

				if ( isUndefined(element_offset )) {
					return false;
				}

				var topBoxes = element_offset.top;
				var leftBoxes = element_offset.left;

				if (leftBoxes < 0) {
					leftBoxes = 0;
				}

				var widthBoxes = element.outerWidth(false);
				var heightBoxes = element.outerHeight(false);

				var bottomBoxes = topBoxes + heightBoxes;

				if (window.Stylist.iDocJQ.find( ".stlst-size-handle").length === 0) {
					iframeBody.append( "<div class='stlst-size-handle'>W : <span class='ypdw'></span> px<br>H : <span class='ypdh'></span> px</div>");
				}

				var w = element.css( "width");
				var h = element.css( "height");

				window.Stylist.iDocJQ.find( ".stlst-size-handle .ypdw").text(parseInt(w));
				window.Stylist.iDocJQ.find( ".stlst-size-handle .ypdh").text(parseInt(h));

				leftBoxes = leftBoxes + (widthBoxes / 2);

				window.Stylist.iDocJQ.find( ".stlst-size-handle").css( "top", bottomBoxes).css( "bottom", "auto").css( "left", leftBoxes).css( "position", "absolute");

				if (parseFloat(bottomBoxes) > (parseFloat(jQuery( "body #stylist_iframe").height()) + parseFloat( iframe.scrollTop())) + 40) {

					window.Stylist.iDocJQ.find( ".stlst-size-handle").css( "bottom", "10px").css( "top", "auto").css( "left", leftBoxes).css( "position", "fixed");

				}

			}

		});
 */

		/* ---------------------------------------------------- */
		/* Window Resize                                        */
		/* ---------------------------------------------------- */
		jQuery( window ).resize(function(){

			if (mainBody.find( ".stlst-parent-tree").length > 0){

				close_parent_tree();

				setTimeout(function(){
					show_parent_tree();
				},5);

			}

		});

		/* ---------------------------------------------------- */
		/* Updating Draws                                       */
		/* ---------------------------------------------------- */
		window.stlst_legacy.draw = function () {

			// If not visible stop.
			var element = get_selected_element();

			// selected boxed.
			window.stlst_legacy.draw_box();

			var appendSelectData = '';
			var currentData = '';

			// Select Others.
			window.Stylist.iDocJQ.find( ".stlst-selected-others:not(.stlst-multiple-selected)").each(function( i) {

				currentData = window.stlst_legacy.draw_other_box(this, 'stlst-selected-others', i);

				if (typeof currentData === 'string') {
					appendSelectData += currentData;
				}

			});

			if (appendSelectData != ''){
				appendSelectData = '<div>' + appendSelectData + '</div>';

				iframeBody.append(appendSelectData);
			}

			// Dragger update.
			// update_drag_handle_position();

		}

		/* ---------------------------------------------------- */
		/* Getting current media query                          */
		/* ---------------------------------------------------- */
		window.stlst_legacy.create_media_query_before = function () {
			return create_media_query_before();
		}

		function create_media_query_before() {



			if ( is_responsive_mod()) {


				var w = jQuery( "#stylist_iframe").width();
				var format = document.getElementById( 'stlst-media-type' ).value;
				return '@media (' + format + ':' + w + 'px){';

			} else {
				return '';
			}
		}


		/* ---------------------------------------------------- */
		/* Getting media query end                              */
		/* ---------------------------------------------------- */
		window.stlst_legacy.create_media_query_after = function () {
			return create_media_query_after();
		}

		function create_media_query_after() {
			if ( is_responsive_mod()) {
				return '}';
			} else {
				return '';
			}
		}


		/* ---------------------------------------------------- */
		/* Define the plugin elements                           */
		/* ---------------------------------------------------- */
		window.stlst_elements = ".stlst-selected-handle,.stlst-selected-tooltip,.stlst-selected-boxed-margin-top,.stlst-selected-boxed-margin-bottom,.stlst-selected-boxed-margin-left,.stlst-selected-boxed-margin-right,.stlst-selected-boxed-top,.stlst-selected-boxed-bottom,.stlst-selected-boxed-left,.stlst-selected-boxed-right,.stlst-selected-others-box,.stlst-edit-tooltip,.stlst-selected-boxed-padding-top,.stlst-selected-boxed-padding-bottom,.stlst-selected-boxed-padding-left,.stlst-selected-boxed-padding-right,.stlst-edit-menu";


		/* ---------------------------------------------------- */
		/* Get current media condition                          */
		/* ---------------------------------------------------- */
		window.stlst_legacy.get_media_condition = function() {
			return get_media_condition();
		}
		function get_media_condition(){

			// Default
			var size = 'desktop';

			// Is res?
			if ( is_responsive_mod() ) {

				var frameWidth = jQuery( "#stylist_iframe").width();
				var media = document.getElementById( 'stlst-media-type' ).value;
				size = '(' + media + ':' + frameWidth + 'px)';

			}

			return size;
		}

		/* ---------------------------------------------------- */
		/* Clean not etc sysbols                                 */
		/* ---------------------------------------------------- */
		function nice_selectors( data,start ){

			if (start === true){

				// Nth child
				data = data.replace(/:nth-child\((.*?)\)/g, '\.nth-child\.$1\.');

				// Not
				data = data.replace(/:not\((.*?)\)/g, '\.notYP$1YP');

				// lang
				data = data.replace(/:lang\((.*?)\)/g, '\.langYP$1YP');

				// nth-last-child()
				data = data.replace(/:nth-last-child\((.*?)\)/g, '\.nth-last-child\.$1\.');

				// nth-last-of-type()
				data = data.replace(/:nth-last-of-type\((.*?)\)/g, '\.nth-last-of-type\.$1\.');

				// nth-of-type()
				data = data.replace(/:nth-of-type\((.*?)\)/g, '\.nth-of-type\.$1\.');

			}else{

				// Nth child
				data = data.replace(/\.nth-child\.(.*?)\./g, ':nth-child($1)');

				// Not
				data = data.replace(/\.notYP(.*?)YP/g, ':not($1)');

				// lang
				data = data.replace(/\.langYP(.*?)YP/g, ':lang($1)');

				// nth-last-child()
				data = data.replace(/\.nth-last-child\.(.*?)\./g, ':nth-last-child($1)');

				// nth-last-of-type()
				data = data.replace(/\.nth-last-of-type\.(.*?)\./g, ':nth-last-of-type($1)');

				// nth-of-type()
				data = data.replace(/\.nth-of-type\.(.*?)\./g, ':nth-of-type($1)');

			}

			return data;

		}

		/* ---------------------------------------------------- */
		/* ESCAPE                                               */
		/* ---------------------------------------------------- */
		function escape(s) {
			return ('' + s) /* Forces the conversion to string. */
				.replace(/\\/g, '\\\\') /* This MUST be the 1st replacement. */
				.replace(/\t/g, '\\t') /* These 2 replacements protect whitespaces. */
				.replace(/\n/g, '\\n')
				.replace(/\u00A0/g, '\\u00A0') /* Useful but not absolutely necessary. */
				.replace(/&/g, '\\x26') /* These 5 replacements protect from HTML/XML. */
				.replace(/'/g, '\\x27')
				.replace(/"/g, '\\x22')
				.replace(/</g, '\\x3C')
				.replace(/>/g, '\\x3E');
		}

		/* ---------------------------------------------------- */
		/* Check if is safe font family.                        */
		/* ---------------------------------------------------- */
		window.stlst_legacy.is_safe_font = function (a) {
			return is_safe_font(a);
		}
		function is_safe_font(a) {

			if ( isUndefined(a)){
				return false;
			}

			var regex = /\barial\b|\barial black\b|\barial narrow\b|\barial rounded mt bold\b|\bavant garde\b|\bcalibri\b|\bcandara\b|\bcentury gothic\b|\bfranklin gothic medium\b|\bgeneva\b|\bfutura\b|\bgill sans\b|\bhelvetica neue\b|\bimpact\b|\blucida grande\b|\boptima\b|\bsegoe ui\b|\btahoma\b|\btrebuchet ms\b|\bverdana\b|\bbig caslon\b|\bbodoni mt\b|\bbook antiqua\b|\bcalisto mt\b|\bcambria\b|\bdidot\b|\bgaramond\b|\bgeorgia\b|\bgoudy old style\b|\bhoefler text\b|\blucida bright\b|\bpalatino\b|\bperpetua\b|\brockwell\b|\brockwell extra bold\b|\bbaskerville\b|\btimes new roman\b|\bconsolas\b|\bcourier new\b|\blucida console\b|\bhelveticaneue\b/;

			var data = a.toLowerCase();

			return regex.test( data);

		}


		/* ---------------------------------------------------- */
		/* Creating live CSS because more faster. Color/Slider  */
		/* ---------------------------------------------------- */
		window.stlst_legacy.insert_live_css = function ( id, val ) {
			return insert_live_css( id, val );
		}

		function insert_live_css( id, val ) {
			var selector = window.Stylist.cssSelectorCustom;

			// Adds relative automatics
			if ( id == 'top' || id == 'left' || id == 'right' ||id == 'bottom'){

				// If is static
				if (jQuery( ".stlst-radio.active #position-static").length > 0){

					// Insert position relative
					window.stlst_legacy.insert_rule(null, "position", "relative", '');
					jQuery( "#position-group .stlst-radio.active").removeClass( "active");
					jQuery( "#position-relative").parent().addClass( "active");

				}

			}


			// Checks min height and min width and update.
			if ( id == 'height' || id == 'width'){

				// minValue & minFormat
				var minVal = number_filter(jQuery( "#min-"+id+"-value").val());
				var prefix = jQuery( "#"+id+"-after").val();
				var minFormat = jQuery( "#min-"+id+"-after").val();

				// if height is smaller than min-height, so update min height
				if (parseFloat(val) < parseFloat(minVal) && prefix == minFormat ){

					// Insert min-height
					window.stlst_legacy.delete_live_css();
					insert_live_css('min-'+id,val);

				}
			}

			// Responsive helper
			var mediaBefore = create_media_query_before();
			var mediaAfter = create_media_query_after();

			// Style id
			var styleId;
			styleId = "#" + id + "-live-css";

			//Element
			var element = window.Stylist.iDocJQ.find(styleId);

			// Check
			// if ( element.length === 0 ) {

				var idAttr = styleId.replace('#', '').replace('.', '');

				// not use prefix (px,em,% etc)
				if ( id == 'z-index' || id == 'opacity') {
					val = parseFloat(val);
				}


				// Append
				if ( id == 'filter' || id == 'transform' ){ // Webkit support

					iframeBody.append( "<style class='" + idAttr + " stlst-live-css' id='" + idAttr + "'>" + mediaBefore + ".stlst-selected,.stlst-selected-others," + selector + "{" + id + ":" + val + " !important;-webkit-" + id + ":" + val + " !important;}" + mediaAfter + "</style>");

				} else { // default
					iframeBody.append( "<style class='" + idAttr + " stlst-live-css' id='" + idAttr + "'>" + mediaBefore + ".stlst-selected,.stlst-selected-others," + selector + "{" + id + ":" + val + "!important;}" + mediaAfter + "</style>");

				}

			// }

		}


		/* ---------------------------------------------------- */
		/* Delete live CSS                                      */
		/* ---------------------------------------------------- */
		window.stlst_legacy.delete_live_css = function ( id ) {

			// Style id
			var styleId;
			var element = false;

			styleId = "#" + id + "-live-css";

			// var element = get_selected_element();

			// var stylistId = element.attr('data-stylist-id');
			// element = window.Stylist.iDocJQ.find('[data-rule="' + id + '"][data-stylist-el="' + stylistId + '"]')

			// element = window.Stylist.iDocJQ.find(styleId)
			element = window.Stylist.iDocJQ.find( ".stlst_current_styles" )

			// Volod

			// Find element with id #stlst-font-family-test-style or #stlst-font-family-live-css
			// in the iframe and delete it completely

			// var element = window.Stylist.iDocJQ.find(styleId);

			if (element.length > 0) {
				element.remove();
			}

		}


		/* ---------------------------------------------------- */
		/* Clean data that not selected yet.                    */
		/* ---------------------------------------------------- */
		function simple_clean(){

			// Clean basic classes
			body.removeAttr( "data-stlst-selector").removeClass( "stlst-element-float stlst-selector-focus stlst-selector-hover stlst-selector-active stlst-selector-link stlst-selector-visited stlst-css-data-trigger stlst-content-selected stlst-body-select-just-it stlst-has-transform stlst-element-resizing stlst-element-resizing-height-top stlst-element-resizing-height-bottom stlst-element-resizing-width-left stlst-element-resizing-width-right stlst-visual-editing stlst-visual-editing-x stlst-visual-editing-y");
			window.Stylist.selectedEl = false;

			// for html overflow hidden on resizing
			window.Stylist.iDocJQ.find( "html").removeClass( "stlst-element-resizing");

			// Clean classes from selected element
			window.Stylist.iDocJQ.find( ".stlst-selected,.stlst-selected-others").removeClass( "ui-draggable ui-draggable-handle ui-draggable-handle stlst-selected-has-transform");

			// Remove stlst-selected classes
			window.Stylist.iDocJQ.find( ".stlst-selected-others,.stlst-selected").removeClass( "stlst-selected-others").removeClass( "stlst-selected");

			// Remove created elements
			window.Stylist.iDocJQ.find( ".stlst-edit-menu,.stlst-edit-tooltip,.stlst-selected-handle,.stlst-selected-others-box,.stlst-selected-tooltip,.stlst-selected-boxed-top,.stlst-selected-boxed-left,.stlst-selected-boxed-right,.stlst-selected-boxed-bottom,.stlst-selected-boxed-margin-top,.stlst-selected-boxed-margin-left,.stlst-selected-boxed-margin-right,.stlst-selected-boxed-margin-bottom,.selected-just-it-span,.stlst-selected-boxed-padding-top,.stlst-selected-boxed-padding-left,.stlst-selected-boxed-padding-right,.stlst-selected-boxed-padding-bottom,.stlst-live-css,.stlst-selected-tooltip span").remove();

			// Update
			if (mainBody.hasClass( "stlst-select-just-it") === false){
				window.selectorClean = null;
			}

			jQuery( ".stlst-disable-btn.active").removeClass( "active");

		}


		/* ---------------------------------------------------- */
		/* Clean previous changes, settings                     */
		/* ---------------------------------------------------- */
		window.stlst_legacy.clean = function() {

			// Use stlst_simple_clean function for simple clean data.
			if ( window.stlst_legacy.is_content_selected() === false ){
				simple_clean();
				return false;
			} else {

				// Stop if dragging
				if ( is_dragging()){
					return false;
				}

				// Hide if close while playing an animate.
				if (body.hasClass( "stlst-force-hide-select-ui" ) ) {
					body.removeClass( "stlst-force-hide-select-ui stlst-hide-borders-now");
				}

				// destroy ex element draggable feature.
				if (window.Stylist.iDocJQ.find( ".stlst-selected.ui-draggable").length > 0){
					get_selected_element().draggable( "destroy");
				}

				// Clean popovers.
				// jQuery( "#margin-left-group,#margin-right-group,#margin-top-group,#margin-bottom-group,#padding-left-group,#padding-right-group,#padding-top-group,#padding-bottom-group,#background-color-group,#background-size-group,#background-repeat-group,#background-blend-mode-group,#background-attachment-group,#background-position-group,#box-shadow-color-group,#animation-name-group,#list-style-position-group,#list-style-image-group,#list-style-type-group").popover( "destroy");

				// close open menu
				jQuery( ".stlst-editor-list > li.active:not(.stlst-li-about ) > h3").trigger( "click");

				// Remove classes
				jQuery( ".reset-enable").removeClass( "reset-enable");

				// Update panel
				jQuery( ".stlst-option-group").css( "opacity", "1");
				jQuery( ".stlst-after").css( "display", "block");

				// delete cached data.
				jQuery( "li[data-loaded]").removeAttr( "data-loaded");

				// copied by iframe click select section.
				jQuery( ".stlst-editor-list > li.active > h3").not( ".stlst-li-about").not( ".stlst-li-footer").trigger( "click");

				jQuery( ".stlst-disable-contextmenu").removeClass( "stlst-disable-contextmenu");
				jQuery( ".stlst-active-contextmenu").removeClass( "stlst-active-contextmenu");

				// Hide some elements from panel
				jQuery( ".stlst-transform-area,.stlst-filter-area").hide();
				jQuery( ".stlst-on").removeClass( "stlst-on");

				simple_clean();

				// Editor Panel Resetting
				jQuery( ".stlst-border-top-section,.stlst-border-right-section,.stlst-border-bottom-section,.stlst-border-left-section").hide();
				jQuery( ".stlst-border-all-section").show();
				jQuery( ".stlst-gradient-pointer-area").removeClass( "gradient-pointer-no-cursor");
				jQuery( ".stlst_background_assets,.stlst-gradient-section,.stlst_nice_colors_area,.stlst_meterial_colors_area").hide();
				jQuery( ".stlst-bg-img-btn,.stlst-gradient-pointer,#border-type-group .stlst-radio.active").removeClass( "active");

			}

		}


		/* ---------------------------------------------------- */
		/* Data element                                         */
		/* ---------------------------------------------------- */
		window.stlst_legacy.the_editor_data = function () {
			return the_editor_data();
		}

		function the_editor_data(){
			return window.Stylist.iDocJQ.find( "#stlst-styles-area");
		}

		/* ---------------------------------------------------- */
		/* Converting selectors to Array                        */
		/* ---------------------------------------------------- */
		window.stlst_legacy.get_selector_array = function(selector) {
			return get_selector_array(selector);
		}
		function get_selector_array(selector){

			var selectorArray = [];

			// Clean
			selector = $.trim(selector);

			// Clean multispaces
			selector = selector.replace(/\s\s+/g, ' ');

			// Clean spaces before ">,+,~" and after
			selector = selector.replace(/(\s)?(\>|\,|\+|\~)(\s)?/g, '$2');

			// Convert > to space
			selector = selector.replace(/\>/g, ' ');

			selector = $.trim(selector);

			// Check if still there have another selector
			if (selector.indexOf( " ") != -1){

				var selectorSplit = selector.split( " ");

				// Split with space
				var v;
				for(var i = 0; i < selectorSplit.length; i++){

					// Clean
					v = $.trim(selectorSplit[i]);

					// Push
					selectorArray.push(v);

				}

			}else{

				// Push if single.
				selectorArray.push(selector);

			}

			var selectorArrayNew = [];

			// Add spaces again
			$.each(selectorArray,function( i,v){
				selectorArrayNew.push(v.replace(/\~/g,' ~ ').replace(/\+/g,' + '));
			});

			return selectorArrayNew;

		}


		/* ---------------------------------------------------- */
		/* Converting Classes to Array                          */
		/* ---------------------------------------------------- */
		window.stlst_legacy.get_classes_array = function(classes) {
			return get_classes_array(classes);
		}

		function get_classes_array(classes){

			var classesArray = [];

			// Clean
			classes = $.trim(classes);

			// Clean multispaces
			classes = classes.replace(/\s\s+/g, ' ');

			// Check if still there have another class
			if (classes.indexOf( " ") != -1){

				var classessplit = classes.split( " ");

				// Split with space
				var v;
				for(var i = 0; i < classessplit.length; i++){

					// Clean
					v = $.trim(classessplit[i]);

					// Push
					classesArray.push(v);

				}

			}else{

				// Push if single.
				classesArray.push(classes);

			}

			return classesArray;

		}


		/* ---------------------------------------------------- */
		/* FILTERING NEVER-USE CLASSES                          */
		/* ---------------------------------------------------- */
		/* These classes will never be used in the selector.    */
		/* Use only if tag is "div" and not have a alternative. */
		/* ---------------------------------------------------- */
		var blockedClasses = [

			// Stylist utility classes.
			'stlst-selected',

			// Classes from a animate.css
			'([a-zA-Z0-9_-]+)?infinite([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?bounce([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?flash([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?pulse([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?rubberBand([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?shake([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?headShake([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?swing([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?tada([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?wobble([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?jello([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?hinge([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?fade([a-zA-Z0-9_-]+)?',

			'([a-zA-Z0-9_-]+)?slide([a-zA-Z0-9_-]+)?( in|in|Out|out )([a-zA-Z0-9_-]+)?(Up|up|Down|down|Left|left|Right|right )([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?roll([a-zA-Z0-9_-]+)?( in|in|Out|out )([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?fall([a-zA-Z0-9_-]+)?( in|in|Out|out|Up|up|Down|down|Left|left|Right|right )([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?flip([a-zA-Z0-9_-]+)?( in|in|Out|out|Up|up|Down|down|Left|left|Right|right )([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?lightSpeed([a-zA-Z0-9_-]+)?( in|in|Out|out )([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?rotate([a-zA-Z0-9_-]+)?( in|in|Out|out )([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?zoom([a-zA-Z0-9_-]+)?( in|in|Out|out )([a-zA-Z0-9_-]+)?',


			// Post Status classes
			'([a-zA-Z0-9_-]+)?publish([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?draft([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?pending([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?private([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?trash([a-zA-Z0-9_-]+)?',

			// Basic post formats
			'(standard|aside|audio|chat|gallery|image|link|quote|status|video)',
			// 'format-([a-zA-Z0-9_-]+)?',

			// Some functional classes
			'([a-zA-Z0-9_-]+)?viewport([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?ltr([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?padding([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?inherit([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?margin([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?relative([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?transparent([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?visibility([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?hidden([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?active-slide([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?current-slide([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?hide([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?animated([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?draggable([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?resize([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?cloned([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?sortable([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?status([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?-spacing-yes',
			'([a-zA-Z0-9_-s]+)?-spacing-no',
			'([a-zA-Z0-9_-]+)?clearfix([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?clear([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?(clr|clfw)([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?clean([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?hover([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?default_template([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?ready([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?validate([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?false([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?true([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?loading([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?loaded([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?finished([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?center([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?delay([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?enabled([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?disabled([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?ga-track([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?raw_code([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?raw_html([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?padded([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?bold([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?initialised([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?even([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?odd([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?dismissable([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?underlined([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?flippable([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?clickable([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?gutter([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?animation([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?animate([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?transition([a-zA-Z0-9_-]+)?',

			// Functional 3rd party classes
			'([a-zA-Z0-9_-]+)?withbg([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?bg_layout([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-s]+)?rand',
			'mrg-(btm|top|left|right|tp|lft|rght )-([a-zA-Z0-9_-]+)',
			'is([_-])([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)none',

			// Wordpress Core
			'page([_-])item',
			'([a-zA-Z0-9_-]+)?object([_-])page',
			'([a-zA-Z0-9_-]+)?closed',
			'status-([a-zA-Z0-9_-]+)?',
			'thread([_-])alt',
			'([a-zA-Z0-9_-]+)?([_-])has([_-])?([a-zA-Z0-9_-]+)|([a-zA-Z0-9_-]+)?([_-])?has([_-])([a-zA-Z0-9_-]+)',
			'screen([_-])reader([_-])text',
			'tag-link([a-zA-Z0-9_-]+)?',
			'post-no-media',
			// 'category-([a-zA-Z0-9_-]+)?',

			// Browser Classes
			'([a-zA-Z0-9_-]+)?opera([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?firefox([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?safari([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?chrome([a-zA-Z0-9_-]+)?',

			// WooCommerce
			'([a-zA-Z0-9_-]+)?product_tag([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?product_cat([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?taxable([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?onsale([a-zA-Z0-9_-]+)?',
			'calculated_shipping',
			'currency([a-zA-Z0-9_-]+)?',
			'woocommerce-MyAccount-navigation-link--([a-zA-Z0-9_-]+)?',

			// Etc
			'img-responsive',
			'ls-active',
			'disappear',
			'appear',
			'noSwipe',
			'wow',
			'bootstrap-touchspin-down',
			'section--no',
			'cat-item',
			'([a-zA-Z0-9_-]+)?direction-ltr',
			'([a-zA-Z0-9_-]+)?show-dropdown', // it is a hover class.
			'kc-elm',
			'kc_column',
			'selected',
			'alternate_color', // enfold
			'open-mega-a', // enfold
			'sf-menu',
			'sf-arrows',

			// Bounce after tests
			'([a-zA-Z0-9_-]+)?nojquery([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?js-comp-ver([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?wpb-js-composer([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?-shd',
			'([a-zA-Z0-9_-]+)?with([_-])([a-zA-Z0-9]+)',
			'([a-zA-Z0-9_-]+)?m-t-([0-9])+([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?(serif|sans|font|webfont )([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?uppercase([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?([_-])(to|from)([_-])(top|left|right|bottom)([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?(cursor|pointer)([a-zA-Z0-9_-]+)?',
			'(not|no)([_-])([a-zA-Z0-9_-]+)?',
			'ajax',
			'neg-marg',
			'([a-zA-Z0-9_-]+)?video-aspect-ratio-([a-zA-Z0-9_-]+)',
			'lazy',
			'lazy-img'

		];

		window.stlst_legacy.blockedClasses = blockedClasses;


		/* ---------------------------------------------------- */
		/* FILTERING UNPREFERED CLASSES                         */
		/* ---------------------------------------------------- */
		/* UnPrefered Classes, these classes is not a priority  */
		/* ---------------------------------------------------- */
		var unPreferedClasses = [

			// Logical
			'([a-zA-Z0-9_-]+)([_-])', // End with -_
			'([_-])([a-zA-Z0-9_-]+)', // start with -_
			// '([a-zA-Z0-9_-]+)?([_-])([_-])([a-zA-Z0-9_-]+)?', // multiple -_ ex: bad--class--name

			// WordPress Dynamic Classes
			'([a-zA-Z0-9_-]+)?(tag|category|cat )([_-])([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?format([a-zA-Z0-9_-]+)?',
			'menu([_-])item([_-])type([_-])post([_-])type',
			'menu([_-])item([_-])object([_-])page',
			'menu([_-])item([_-])(object|type)([_-])custom',
			'widget_([a-zA-Z0-9_-]+)',
			'bg-([a-zA-Z0-9_-]+)',

			// Modern Columns.
			'([a-zA-Z0-9_-]+)?([_-])(l|m|s|xs)([_-])[0-9]+',
			'([a-zA-Z0-9_-]+)?pure([_-])([a-zA-Z0-9_-]+)?([_-])u([_-])[0-9]+([_-])[0-9]+',
			'([a-zA-Z0-9_-]+)?col([_-])([a-zA-Z0-9_-]+)?([_-])[0-9]+',
			'([a-zA-Z0-9_-]+)?col([_-])([a-zA-Z0-9_-]+)?([_-])offset([_-])[0-9]+',
			'([a-zA-Z0-9_-]+)?(medium|large|small)([_-])[0-9]+',
			'([a-zA-Z0-9_-]+)?(medium|large|small)([_-])([a-zA-Z0-9_-]+)?([_-])[0-9]+',

			// Bootstrap Classes
			'([a-zA-Z0-9_-]+)?(small|medium|large)([_-])(push|pull)([_-])[0-9]+',
			'([a-zA-Z0-9_-]+)?span[0-9]+',
			'([a-zA-Z0-9_-]+)?span([_-])[0-9]+',
			'([a-zA-Z0-9_-]+)?col([_-])[0-9]+([_-])[0-9]+',
			'([a-zA-Z0-9_-]+)?col([_-])[0-9]+',

			// Classic Grid Columns
			'(column|columns|col)',
			'([a-zA-Z0-9_-]+)(one|two|three|four|five|six|seven|eight|nine|ten|eleven|twelve)([_-]+[a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?(one|two|three|four|five|six|seven|eight|nine|ten|eleven|twelve)([_-]+[a-zA-Z0-9_-]+)',

			// Structural
			'([a-zA-Z0-9_-]+)?sticky([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?fixed([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?logged([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?print([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?visible([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?trigger([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?required([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?pull([a-zA-Z0-9_-]+)(left|right )',
			'(left|right )',
			'([a-zA-Z0-9_-]+)?([_-])(yes|no)([_-])([a-zA-Z0-9_-]+)?', // _yes_
			'([a-zA-Z0-9_-]+)?(yes|no)([_-])([a-zA-Z0-9_-]+)?', // yes_
			'([a-zA-Z0-9_-]+)?([_-])(yes|no)([a-zA-Z0-9_-]+)?', // _yes,
			'([a-zA-Z0-9_-]+)?is([_-])active([a-zA-Z0-9_-]+)?', // is_active,

			// Dynamic CSS classes.
			'([a-zA-Z0-9_-]+)?background([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?width([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?height([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?position([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?parent([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?color([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?layout([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?invert([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)style([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?scroll([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?equal([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?square([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?([_-])skin([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?skin([_-])([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?toggled([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?style([0-9_-]+)',
			'([a-zA-Z0-9_-]+)?rounded([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?radius([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?type([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?dynamic([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?first',
			'([a-zA-Z0-9_-]+)?last',
			'([a-zA-Z0-9_-]+)?text([_-])justify',
			'([a-zA-Z0-9_-]+)?row([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?border([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?align([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?effect([0-9_-]+)',
			'([a-zA-Z0-9_-]+)?dimension([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?inline-inside([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?corner-pointed([a-zA-Z0-9_-]+)?',

			// General Theme Option Classes
			'([a-zA-Z0-9_-]+)([_-])(on|off)',
			'([a-zA-Z0-9_-]+)default([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)size([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)mobile([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)desktop([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)populated([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?hide([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?show([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?parallax([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?responsive([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?intense([a-zA-Z0-9_-]+)?',

			// Unyson
			'unyson-page',
			'end',

			// Pagenavi
			'larger',
			'smaller',

			// Buddypress
			'created_group',
			'mini',
			'activity_update',

			// Force Builder
			'forge-block',
			'forge-',

			// Elementor
			'elementor-section-items-middle',

			// Themify
			'themify_builder',
			'module',

			// live composer
			'dslc-post-no-thumb',

			// Woocommerce
			'downloadable',
			'purchasable',
			'instock',

			// Others
			'above',
			'open',

			// Enfold
			'template-page',
			'alpha',
			'units',
			'flex_column_div',
			'([a-zA-Z0-9_-]+)?no-sibling([a-zA-Z0-9_-]+)?',

			// bounce after tests
			'([a-zA-Z0-9_-]+)?float([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?(blue|black|red|dark|white|light|green|yellow|purple|pink|orange|brown|gray )([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?(black|dark|white|light )([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?last-child([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?first-child([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)([_-])only',
			'([a-zA-Z0-9_-]+)?(text-left|text-right )([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?(round|scale|shadow|rotate|default|minimal|animsition|nimation)([a-zA-Z0-9_-]+)?',
			'woocommerce',
			'affix-top',
			'st-menu',
			'circle',
			'vc_figure',
			'vc_general',
			'waves-effect',
			'collapsed',
			'collapse'

		];

		window.stlst_legacy.unPreferedClasses = unPreferedClasses;


		/* ---------------------------------------------------- */
		/* FILTERING UNPREFED CLASSES                           */
		/* ---------------------------------------------------- */
		/* This filter excluding the structural classes in the  */
		/* selector. ex: col-md-8, exclude it if no need.       */
		/* ---------------------------------------------------- */
		var unPreferedSelectors = [

			// General
			'([a-zA-Z0-9_-]+)?inner([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?overlay([a-zA-Z0-9_-]+)?',
			'([a-zA-Z0-9_-]+)?container([a-zA-Z0-9_-]+)?',

			// new visual composer
			'([a-zA-Z0-9_-]+)?google-fonts([a-zA-Z0-9_-]+)?',

			// siteorigin
			'([a-zA-Z0-9_-]+)?fl-col-content([a-zA-Z0-9_-]+)?',

			// Enfold
			'av-content-full'

		];

		unPreferedSelectors.concat(blockedClasses);
		unPreferedSelectors.concat(unPreferedClasses);

		window.stlst_legacy.unPreferedSelectors = unPreferedSelectors;


		/* ---------------------------------------------------- */
		/* Prefered tags as selector                            */
		/* ---------------------------------------------------- */
		var simpleLikedTags = [
			"h1",
			"h2",
			"h3",
			"h4",
			"h5",
			"h6",
			"p",
			"span",
			"img",
			"strong",
			"a",
			"li",
			"i",
			"ul",
			"header",
			"footer",
			"article",
			"b",
			"em",
			"code",
			"form",
			"label",
			"ol",
			"small",
			"blockquote",
			"nav"
		];
		window.stlst_legacy.simpleLikedTags = simpleLikedTags;


		/* ---------------------------------------------------- */
		/* Filtering post format classes                        */
		/* ---------------------------------------------------- */
		var postFormatFilters = [

			// Don't care post formats
			'((?=.*post )|(?=.*blog)|(?=.*content )|(?=.*entry )|(?=.*page)|(?=.*hentry ))(?=.*standard)',
			'((?=.*post )|(?=.*blog)|(?=.*content )|(?=.*entry )|(?=.*page)|(?=.*hentry ))(?=.*aside)',
			'((?=.*post )|(?=.*blog)|(?=.*content )|(?=.*entry )|(?=.*page)|(?=.*hentry ))(?=.*audio)',
			'((?=.*post )|(?=.*blog)|(?=.*content )|(?=.*entry )|(?=.*page)|(?=.*hentry ))(?=.*chat )',
			'((?=.*post )|(?=.*blog)|(?=.*content )|(?=.*entry )|(?=.*page)|(?=.*hentry ))(?=.*gallery )',
			'((?=.*post )|(?=.*blog)|(?=.*content )|(?=.*entry )|(?=.*page)|(?=.*hentry ))(?=.*image)',
			'((?=.*post )|(?=.*blog)|(?=.*content )|(?=.*entry )|(?=.*page)|(?=.*hentry ))(?=.*link)',
			'((?=.*post )|(?=.*blog)|(?=.*content )|(?=.*entry )|(?=.*page)|(?=.*hentry ))(?=.*quote)',
			'((?=.*post )|(?=.*blog)|(?=.*content )|(?=.*entry )|(?=.*page)|(?=.*hentry ))(?=.*status)',
			'((?=.*post )|(?=.*blog)|(?=.*content )|(?=.*entry )|(?=.*page)|(?=.*hentry ))(?=.*video)',

		];



		/* ---------------------------------------------------- */
		/* Finds matchless2 Classes                             */
		/* ---------------------------------------------------- */
		function matchless2(classes){

			// ai_class returns false
			if (classes === false || classes == ''){
				return false;
			}

			// Find
			var matchlessClasses = classes.sort(function(b, a) {
				return iframeBody.find( "."+b).length - iframeBody.find( "."+a).length;
			});

			// be sure it is just 1
			if ( iframeBody.find( "."+matchlessClasses[0]).length == 1){

				return "." + matchlessClasses[0];

			}

			return false;

		}


		/* ---------------------------------------------------- */
		/* Finds AI Classes                                     */
		/* ---------------------------------------------------- */
		window.stlst_legacy.ai_class = function(classes,array,alternative){
			return ai_class(classes,array,alternative);
		}
		function ai_class(classes,array,alternative){

			// 0 classes
			if (classes.length == 0){
				return false;
			}

			// 1 classes: has alternative
			if (classes.length == 1 && alternative == true){
				return false;
			}

			// 1 classes: no alternative
			if (array == false){

				if (classes.length == 1 && alternative == false){

					if (classes[0].length > 1){

						return "." + classes[0];

					}else{

						return false;

					}

				}

			}else if (classes.length == 1 && alternative == false){

				if (classes[0].length > 1){

					return classes;

				}else{

					return false;

				}

			}


			// Variables
			var newClasses = [],new2Classes = [],depthArray = [],tagArray = [],i,new4Classes = [];

			// Data
			var a1 = []; // just classes which has "-"
			var a2 = []; // just classes which has "_"

			// Filter: Difference parent depth and difference HTML tags
			for( i = 0; i < classes.length; i++){

				// Arrays
				depthArray  = [];
				tagArray = [];

				if (/(\,|\[|\]|\#)/g.test(classes[i])){
					continue;
				}

				// Each current class
				window.Stylist.iDocJQ.find( "." + classes[i]).each(function(){

					var element = jQuery(this);

					// Push depth len
					depthArray.push(element.parents().length);

					// Push tags used
					tagArray.push(element[0].nodeName);

				});

				var depthArrayEquals = depthArray.every(function(v, i, a){
					return v === a[0];
				});

				var tagArrayEquals = tagArray.every(function(v, i, a){
					return v === a[0];
				});

				// Passed depth and tag methods
				if ( depthArrayEquals && tagArrayEquals){
					newClasses.push(classes[i]);
				}

			}

			// Use default if newClasses is not avaiable
			if (newClasses.length == 0){
				newClasses = classes;
			}


			// Filter a1
			for( i = 0; i < newClasses.length; i++){

				// just classes which has "-"
				if (/\_/g.test(newClasses[i]) == false && /\-/g.test(newClasses[i]) == true){
					a1.push(newClasses[i]);
				}

			}


			// Try a2 if a1 is empty.
			if (a1.length == 0){

				// Filter a2
				for( i = 0; i < newClasses.length; i++){

					// just classes which has "-"
					if (/\-/g.test(newClasses[i]) == false && /\_/g.test(newClasses[i]) == true){
						a2.push(newClasses[i]);
					}

				}

				// a1 and a2 is empty
				if (a2.length == 0){

					new2Classes = newClasses;

				}else{

					// Use a2 if it is available
					new2Classes = a2;

				}

			}else{ // Use a1 if available

				new2Classes = a1;

			}



			// Filter: 3 time repeated char classes
			for( i = 0; i < new2Classes.length; i++){

				// If char not repeat 3 time as tessst.
				if (/(.)\1\1/.test(new2Classes[i]) == false){
					new4Classes.push(new2Classes[i]);
				}

			}

			// Use non filtered classes if any class cant pass
			if (new4Classes.length == 0){
				new4Classes = newClasses;
			}


			// Sort
			new4Classes.sort(function(a, b){
				return b.length - a.length;
			});


			// Return format
			if (array ){
				return new4Classes;
			}else{
				return "." + new4Classes[0];
			}

		}

		/* ---------------------------------------------------- */
		/* Finds bad queries                                    */
		/* ---------------------------------------------------- */
		function filter_bad_queries( data){
			return  data.replace(/[\u2018\u2019\u201A\u201B\u2032\u2035\u201C\u201D]/g,'');
		}


		/* ---------------------------------------------------- */
		/* Checks if selector valid                             */
		/* ---------------------------------------------------- */
		window.stlst_legacy.check_selector = function (selector, mustHas, searchInBody ) {
			return check_selector(selector, mustHas, searchInBody );
		}
		function check_selector(selector,mustHas,searchInBody ){

			var content;
			if (searchInBody ){
				content = iframeBody;
			}else{
				content = iframe;
			}

			try {

				// Get element
				var element = content.find(selector);

				// Return false if document not have this element
				if (mustHas == true && element.length == 0){
					return false;
				}else{
					return element; // return true if valid
				}


			} catch ( e ) {
				return false;
			}

		}


		/* ---------------------------------------------------- */
		/* Getting minimized CSS. Cleaning spaces.              */
		/* ---------------------------------------------------- */
		function get_minimized_css( data,media){

			// Clean.
			data = data.replace(/(\r\n|\n|\r)/g, "").replace(/\t/g, '');

			// Don't care rules in comment.
			data = data.replace(/\/\*(.*?)\*\//g, "");

			// clean.
			data = data.replace(/\}\s+\}/g, '}}').replace(/\s+\{/g, '{');

			// clean.
			data = data.replace(/\s+\}/g, '}').replace(/\{\s+/g, '{');

			// replace queries
			data = filter_bad_queries( data);

			// Don't care rules in media query
			if (media === true){
				data = data.replace(/@media(.*?)\}\}/g, '').replace(/@?([a-zA-Z0-9_-]+)?keyframes(.*?)\}\}/g, '').replace(/@font-face(.*?)\}\}/g, '').replace(/@import(.*?)\;/g,'').replace(/@charset(.*?)\;/g,'');
			}

			// data
			return data;

		}


		// Get defined selector controller.
		window.definedSelectorArray = [];
		window.definedSelectorArrayEnd = false;

		/* ---------------------------------------------------- */
		/* Get Defined Selectors                                */
		/* ---------------------------------------------------- */
		window.stlst_legacy.get_defined_selector = function() {
			return get_defined_selector();
		}
		function get_defined_selector(){

			var data = window.definedStyleData; // All CSS code on the page.
			var allSelectors; // – All CSS selectors available on the page.
			var i;
			var foundedSelectors = [];
			var selector;
			var element, passedClasses;
			var foundedNewSelectors = []; // New selectors
			var o,selectorBefore,re;



			// Don't search it always
			if ( window.definedSelectorArray.length === 0 ){

				// If page has no css data, stop.
				if ( data == '' ){
					return false;
				}

				data = data.toString().replace(/\}\,/g, "}");

				// Getting All CSS Selectors on the page.
				allSelectors = array_cleaner( data.replace(/\{(.*?)\}/g, '|BREAK|').split( "|BREAK|"));

			}

			// Get cached array of selectors
			if ( window.definedSelectorArrayEnd ){
				allSelectors = window.definedSelectorArray;
			}



			if ( isUndefined(allSelectors) ){
				return false;
			}

			// Filtering bad classes

			// Each All Selectors
			for ( i = 0; i < allSelectors.length; i++){
				// Get Selector.
				selector = space_cleaner(allSelectors[i]);
				selector = space_cleaner(selector.replace(/(\{|\})/g,'').replace(/>(\.|\#|[a-zA-Z-_])/g, "> ").replace(/(\.|\#|[a-zA-Z-_])>/g, " >"));
				// We don't like so advanced selectors.
				if (selector.indexOf( ",") != -1 || selector.indexOf( "*") != -1 || selector.indexOf( "/") != -1){
					continue;
				}
				// skip html5 advanced terms
				if (/([\u2018\u2019\u201A\u201B\u2032\u2035\u201C\u201D]|\{|\}|\:|\<|\>|\(|\)|\[|\]|\~|\"|\'|\?|\\)/g.test(selector) == true){
					continue;
				}

				// Not basic html tag selectors.
				if (selector.indexOf( "#") == -1 && selector.indexOf( ".") == -1){
					continue;
				}

				// If minimum two elemenents in selector.
				if ( get_selector_array(selector).length < 2 ) {
					continue;
				}

				// Leave only CSS selectors for the elements we can find on the page.
				element = check_selector( selector, true, true );
				// Expensive - calls JQuery .find.

				// be sure it valid
				if ( element === false ) {
					continue;
				}

				// Bad EX: span.class, h1#id
				if ( /[a-zA-Z-_0-9](\.|#)/g.test(selector) ) {
					continue;
				}

				// Cache other selectors.
				if ( window.definedSelectorArrayEnd === false ){
					window.definedSelectorArray.push(selector);
				}

				// If element for current selector has .stlst-selected!
				if ( element.hasClass( "stlst-selected") ) {
					foundedSelectors.push(selector);
				}

			}

			// Don't read again css files. cache all defined CSS selectors.
			window.definedSelectorArrayEnd = true;



			// Each all founded selectors.
			// Don't use if has non useful classes as format-link etc.
			for( i = 0; i < foundedSelectors.length; i++){

				selectorBefore = foundedSelectors[i];
				passedClasses = true;

				// Check if has blockedClasses
				for(o = 0; o < blockedClasses.length; o++){

					// Regex
					re = new RegExp( "(\\s|^|\\.|\#)" + blockedClasses[o] + "(\\s|$)","gi");

					// Found a non useful class.
					if ( re.test( selectorBefore ) ) {
						passedClasses = false;
						break;
					}

				}
				/* // Check if has an useful class
				for(o = 0; o < blockedClasses.length; o++){

					// Regex
					re = new RegExp( "(\\s|^|\.|\#)" + blockedClasses[o] + "(\\s|$)","gi");

					// Founded an non useful class.
					if (re.test(selectorBefore)){
						passedClasses = false;
						break;
					}

				} */

				// Check if has unPreferedClasses
				for(o = 0; o < unPreferedClasses.length; o++){

					// Regex
					re = new RegExp( "(\\s|^|\\.|\#)" + unPreferedClasses[o] + "(\\s|$)","gi");

					// Founded an non useful class.
					if (re.test(selectorBefore)){
						passedClasses = false;
						break;
					}

				}

				// Check if has postFormatFilters
				for(o = 0; o < postFormatFilters.length; o++){

					// Regex
					re = new RegExp( "(\\s|^|\\.|\#)" + postFormatFilters[o] + "(\\s|$)","gi");

					// Founded an bad class.
					if (re.test(selectorBefore)){
						passedClasses = false;
						break;
					}

				}
				// Successful.
				if (passedClasses === true){
					foundedNewSelectors.push(foundedSelectors[i]);
				}

			}



			return foundedNewSelectors;

		}


		/* ---------------------------------------------------- */
		/* This function cropping selector until 5 class        */
		/* ---------------------------------------------------- */
		function crop_selector(selector){

			var limit = 5;

			// generate long selector as we want with: "window.minCroppedSelector"
			if (window.minCroppedSelector != false){
				limit = window.minCroppedSelector;
			}

			// Keep selectors smart and short!
			if (get_selector_array(selector).length > 5){

				// short Selector Ready
				var shortSelectorReady = false;

				// Find a founded elements
				var foundedElements = window.Stylist.iDocJQ.find(selector).length;

				// Get array from selector.
				var shortSelector = get_selector_array(selector);

				// Each array items
				$.each(shortSelector,function(){

					if (shortSelectorReady === false){

						// Shift
						shortSelector.shift();

						// make it short
						var shortSelectorString = shortSelector.toString().replace(/\,/g," ");

						// Search
						var foundedElShort =  window.Stylist.iDocJQ.find(shortSelectorString).length;

						// Shift until make it maximum 5 item
						if (shortSelector.length <= 5 && foundedElements == foundedElShort ){
							shortSelectorReady = true;
							selector = shortSelectorString;
						}

					}

				});

			}

			return selector;

		}


		/* ---------------------------------------------------- */
		/* This function trying all settings for the selector   */
		/* if there 10 classes in the selector                  */
		/* it will try 100 combine                              */
		/* ---------------------------------------------------- */
		window.stlst_legacy.multiple_variation = function(selector){
			return multiple_variation(selector);
		}
		function multiple_variation(selector){

			// Get current selector length
			var selectorLen = iframejs.querySelectorAll(selector).length;

			// Orginal selector
			var selectorOrginal = crop_selector(selector);

			// will keep the results in this array
			var resultArray = [];

			var last,first,cssSelector = '';

			// Need to first and last
			if (selector.indexOf( ">") == -1){

				// The Array
				selector = get_selector_array(selector);

				// Last element
				last = selector[selector.length - 1];
				selector.pop();

				// First element
				first = selector[0];
				selector.shift();

			// Getting first and last in the selector which has ">" symbols.
			}else{

				var getEnd,getStart,arr,firstReg,lastReg,centerSelector,centerMatch;
				var type = null;

				// Match for Gettin End
				getEnd = selector.match(/\>(?!.*\>)(.*?)$/g).toString().replace(/(\s)?>(\s)?/, '');

				// Match for getting start
				getStart = selector.match(/^(.*?)\s\>/g).toString().replace(/(\s)?>(\s)?/, '');

				// Check if > in start or end.
				if (getEnd.indexOf(' ') == -1){
					arr = getStart.split( " ");
					type = "getStart";
				} else if (getStart.indexOf(' ') == -1) {
					arr = getEnd.split( " ");
					type = "getEnd";
				}

				// get the getEnd
				if (type == null){

					if (getEnd.split( " ").length > getStart.split( " ").length){
						arr = getEnd.split( " ");
						type = "getEnd";
					}else{ // get the getStart
						arr = getStart.split( " ");
						type = "getStart";
					}

				}

				// cache first and last selectors and delete
				if (arr.length - 2 > 1) {

					// cache first & last
					first = arr[0];
					last = arr[arr.length - 1];

					// RegExp for parsing
					firstReg = new RegExp( "^(.*?) > " + first, "g");
					lastReg = new RegExp(last + " > (.*?)$", "g");

					if (selector.match(firstReg) != null && selector.match(firstReg) != null){

						// Check the type and update first/last
						if (type == "getEnd") {
							first = selector.match(firstReg).toString();
						} else if (type == 'getStart') {
							last = selector.match(lastReg).toString();
						}

						// Get just between
						arr.pop();
						arr.shift();

						centerSelector = arr;

					}

				}

				// Trying another method. It will get center of selector.
				if (centerSelector == undefined){

					// Test it
					if (/\>(.*?)\>/g.test(selector)) {

						// Get center
						centerMatch = selector.match(/\>(.*?)\>/g).toString().replace(/(\s)?>(\s)?/g, '');

						// must has a space
						if (centerMatch.indexOf( " ") != -1) {

							// parse
							arr = centerMatch.split( " ");

							// cache first and last selectors and delete
							if (arr.length - 2 > 1) {

								// Cache first and last.
								first = arr[0];
								last = arr[arr.length - 1];

								// RegExp for parsing
								firstReg = new RegExp( "^(.*?) > " + first, "g");
								lastReg = new RegExp(last + " > (.*?)$", "g");

								// Check the type and update first & last
								first = selector.match(firstReg).toString();
								last = selector.match(lastReg).toString();

								arr.pop();
								arr.shift();

								centerSelector = arr;

							}

						}

					}

				}

				// Check if it is available.
				if (centerSelector != undefined){
					selector = centerSelector;
				}else{
					return selectorOrginal;
				}

			} // > symbol codes end here.


			// Try to delete structural selectors
			var newSelector = [],passed,r;
			for(var i = 0; i < selector.length; i++){

				passed = true;

				for(var ix = 0; ix < unPreferedSelectors.length; ix++){

					// Regex
					r = new RegExp( "(\\s|^)\." + unPreferedSelectors[ix] + "(\\s|$)","gi");

					// has
					if (r.test(selector[i])){
						passed = false;
						break;
					}

				}

				if (passed){
					newSelector.push(selector[i]);
				}

			}


			if ( isUndefined(first ) || isUndefined(first )){
				return selectorOrginal;
			}


			// just try combine first and last
			if (first.indexOf( " ") == -1 && last.indexOf( " ") == -1){
				cssSelector = space_cleaner(first + window.separator + last );
			}


			// is valid? // first & last
			if (check_selector(cssSelector,false,false) && window.minCroppedSelector == false){

				// Combine just first and last if there were a lot selector but all were structural selectors.
				if (selector.length >= 1 && newSelector.length == 0 && window.Stylist.iDocJQ.find(cssSelector).length == selectorLen){
					return cssSelector;
				}

			}


			// Update only if high than 1
			if (newSelector.length > 1){
				selector = newSelector;
			}


			// Must be minimum 2 class excluding
			// first and last classes in the selector.
			if (selector.length < 2){
				return selectorOrginal;
			}


			// Prefer only classes which used in same parent depth and same tag names.
			// The class that which used in difference depth is structural class
			// The class that which used in difference tags is structural class.
			var depthArray,tagArray,ek;
			var newSelector2 = [];
			for( i = 0; i < selector.length; i++){

				if (/.|#/g.test(selector[i])){

					// Arrays
					depthArray  = [];
					tagArray = [];

					// Each current class
					window.Stylist.iDocJQ.find(selector[i]).each(function(){

						ek = jQuery(this);

						// Push depth len
						depthArray.push(ek.parents().length);

						// Push tags used
						tagArray.push(ek[0].nodeName);

					});

					var depthArrayEquals = depthArray.every(function(v, i, a){
						return v === a[0];
					});

					var tagArrayEquals = tagArray.every(function(v, i, a){
						return v === a[0];
					});

					// Passed depth and tag methods
					if ( depthArrayEquals && tagArrayEquals){
						newSelector2.push(selector[i]);
					}

				}

			}


			// Update only if high than 1
			if (newSelector2.length > 1){
				selector = newSelector2;
			}


			// Variables
			var nexts = selector.slice(0);
			var current,i,n,currentNext,testSelector;

			// Combine All
			for( i = 0; i < selector.length; i++){

				current = selector[i];

				// all selector for test
				testSelector = first + window.separator + current + window.separator + last;

				// Add if pass
				if ( iframejs.querySelectorAll(testSelector).length == selectorLen){
					resultArray.push(testSelector);
				}

				// Shift on  each
				nexts.shift();

				// Use current with all other selector parts
				for(n = 0; n < nexts.length; n++){

					currentNext = nexts[n];

					// all selector for test
					testSelector = first + window.separator + current + window.separator + currentNext + window.separator + last;

					// Add if pass
					if ( iframejs.querySelectorAll(testSelector).length == selectorLen){
						resultArray.push(testSelector);
					}

				}

			}


			// There not have any variation
			if (resultArray.length == 0){
				return selectorOrginal;
			}


			// Find the short
			resultArray.sort(function(a,b){
				return a.length - b.length;
			});


			// Find the selector long as we want with "window.minCroppedSelector"
			if (window.minCroppedSelector != false){

				// loop the results
				for(var k = 0; k < resultArray.length; k++){

					// find the longer selector
					if (get_selector_array(resultArray[k]).length >= window.minCroppedSelector){
						return space_cleaner(resultArray[k]);
					}

				}

				return selectorOrginal;

			}


			// Return the result
			return space_cleaner(resultArray[0]);

		}


		/* ---------------------------------------------------- */
		/* Get Parents                                          */
		/* ---------------------------------------------------- */
		window.stlst_legacy.get_parents = function(element, status){

			// If parent already has.
			// var parentsv = body.attr( "data-clickable-select");
			var parentsv = window.Stylist.selectedEl;

			// If status default, return current data.
			if (status == 'default' && window.minCroppedSelector == false) {

				// If defined
				if (window.stlst_legacy.isDefined(parentsv)) {

					// if valid return
					if (check_selector(parentsv,true,false) != false){
						return parentsv;
					}

				}

			}

			if (status == 'defaultNoCache'){
				status = 'default';
			}


			if (element === null){
				element = get_selected_element();
			}



			// Be sure this item is valid.
			if (element[0] === undefined || element[0] === false || element[0] === null) {

				return false;
			}


			// Tag info
			var tag = element[0].tagName;


			// Is Single Inspector
			if (mainBody.hasClass( "stlst-sharp-selector-mode-active" ) ) {
				status = 'sharp';
			}


			// HTML tag not supported.
			if ( isUndefined(tag) || tag == 'HTML'){
				return false;
			}

			// If body, return.
			if (tag == 'BODY') {
				return 'body';
			}


			// Getting item parents.
			var parents = element.parentsUntil( "body"), selector = '', reseted, inSelected, currentSelector,previousSelector = '';


			// Get last selector
			var lastSelector = window.stlst_legacy.get_best_class(element );


			// Return if element selector is ID.
			if (/#/g.test(lastSelector) == true){
				return lastSelector;
			}


			// Resets
			var resetSelectors = [], dontReset = false, dontResetLive = false;


			// Check if there is waited selector
			if (window.minCroppedSelector != false){

				// waited selector by long.
				if (window.minCroppedSelector >= parents.length){
					dontReset = true;
				}

			}


			// Foreach all loops.
			for (var i = parents.length - 1; i >= 0; i--) {

				// Default false
				reseted = false;

				// Get Selector of the current parent element.
				currentSelector = window.stlst_legacy.get_best_class(parents[i]);

				// Don't reset if waited selector is long
				dontResetLive = false;
				if (window.minCroppedSelector != false){
					if (( i-1) <= window.minCroppedSelector){
						dontResetLive = true;
					}
				}

				// Check if this has a class or ID.
				if (/\.|#/g.test(currentSelector) == true && dontReset == false && dontResetLive == false){

					// Check if need or no need for generated previous selectors
					if (window.Stylist.iDocJQ.find(currentSelector).length == 1){

						// No Need to previous Selectors
						if (status != 'sharp') {

							// Reset old selectors
							selector = currentSelector + window.separator;

							resetSelectors.push(currentSelector);
							reseted = true;

						}else{

							// In Single Selector reset only if no need nth-child selector.
							if (single_selector(selector, true).indexOf( "nth-child") == -1) {

								// Reset old selectors
								selector = currentSelector + window.separator;

								resetSelectors.push(currentSelector);
								reseted = true;

							}

						}

					}

				}


				// Can't reset.
				// Continue to add current class name/id/tag to current selector
				if (reseted == false){

					// Check if same selector has in the selector
					inSelected = window.Stylist.iDocJQ.find(selector+window.separator+currentSelector+window.separator+currentSelector+","+selector+window.separator+previousSelector+window.separator+currentSelector).length;

					if (status == 'default' && inSelected > 0 && space_cleaner(selector).trim() != ''){
						selector = space_cleaner(selector).trim() + " > " + currentSelector + window.separator; // Add With '>' separator
					}else{
						selector += currentSelector + window.separator; // Add with space separator
					}

				}

				previousSelector = currentSelector;


			} // Each end.


			// Fix google map contents
			if (selector.indexOf( ".gm-style") != -1){
				selector = '.gm-style';
			}


			// Check if same selector has in the selector
			inSelected = window.Stylist.iDocJQ.find(selector+window.separator+lastSelector+window.separator+lastSelector+","+selector+window.separator+currentSelector+window.separator+lastSelector).length;

			if ( inSelected > 0){
				selector = space_cleaner(selector + " > " + lastSelector); // Add With '>' separator
			}else{
				selector = space_cleaner(selector + window.separator + lastSelector); // Add with space separator
			}


			// If there is multiple reset indexs and the selected reset is not a ID
			// so lets find the best reset selector.
			if (resetSelectors.length > 1 && selector.charAt(0) != '#'){


				// Try to delete structural selectors
				var newReset1 = [],passed,r;
				for(var i = 0; i < resetSelectors.length; i++){

					passed = true;

					for(var ix = 0; ix < unPreferedSelectors.length; ix++){

						// Regex
						r = new RegExp( "(\\s|^)\." + unPreferedSelectors[ix] + "(\\s|$)","gi");

						// has
						if (r.test(resetSelectors[i])){
							passed = false;
							break;
						}

					}

					if (passed){
						newReset1.push(resetSelectors[i]);
					}

				}


				// Try newReset2 if newReset1 has more items than 1.
				if (newReset1.length > 1){

					// Prefer only classes which used in same parent depth and same tag names.
					// The class that which used in difference depth is structural class
					// The class that which used in difference tags is structural class.
					var depthArray,tagArray,ek;
					var newReset2 = [];
					for( i = 0; i < newReset1.length; i++){

						// Arrays
						depthArray  = [];
						tagArray = [];

						// Each current class
						window.Stylist.iDocJQ.find(newReset1[i]).each(function(){

							ek = jQuery(this);

							// Push depth len
							depthArray.push(ek.parents().length);

							// Push tags used
							tagArray.push(ek[0].nodeName);

						});

						var depthArrayEquals = depthArray.every(function(v, i, a){
							return v === a[0];
						});

						var tagArrayEquals = tagArray.every(function(v, i, a){
							return v === a[0];
						});

						// Passed depth and tag methods
						if ( depthArrayEquals && tagArrayEquals){
							newReset2.push(newReset1[i]);
						}

					}

				}


				// empty if is undefined
				if ( isUndefined(newReset2)){
					var newReset2 = [];
				}


				// null as default
				var newFirstSelector = null;


				// Get first selector
				if (newReset2.length > 0){
					newFirstSelector = newReset2[newReset2.length -1];
				}else if (newReset1.length > 0){
					newFirstSelector = newReset1[newReset1.length -1];
				}


				// if is valid
				if (newFirstSelector != null){

					// Get all selector exlcude first class
					var newSelector = selector.match(/(\s)(.*?)$/g).join('').toString();

					// Get ready the new selector
					newSelector = newFirstSelector + newSelector;

					// be sure it is valid
					if (check_selector(newSelector, true,false)){

						// be sure this selectors make same work
						if (window.Stylist.iDocJQ.find(newSelector).length == window.Stylist.iDocJQ.find(selector).length){

							// update
							selector = newSelector;

						}

					}

				} // not valid

			}


			// Last Parent Query Status
			window.lastParentQueryStatus = status;


			// Return if is single selector
			if (status == 'sharp') {
				return single_selector(selector, false);
			}


			// Check all others elements has same nodename or not.
			if (simpleLikedTags.indexOf(tag.toLowerCase()) != -1){

				var foundedTags = [], n;
				window.Stylist.iDocJQ.find(selector).each(function(){

					n = jQuery(this)[0].nodeName;

					if (foundedTags.indexOf(n) == -1){
						foundedTags.push(n);
					}

				});

				// h1.test | div.test = Use "h1"
				if (foundedTags.length > 1){

					selector = $.trim(selector.match(new RegExp( "^(.*?)" + prepare_regex_string(lastSelector) + "$","g")).join('').toString()) + window.separator + tag.toLowerCase();

				// if this is a single element, use the tag.
				}else if ( is_matchless2(selector,lastSelector,tag)){

					selector = $.trim(selector.match(new RegExp( "^(.*?)(?=" + prepare_regex_string(lastSelector) + "$)","g")).join('').toString()) + window.separator + tag.toLowerCase();

				}

			}


			// Getting selectors by CSS files.
			if (get_selector_array(selector).length > 1 && window.minCroppedSelector == false){

				// Get defined selectors
				var definedSelectors = get_defined_selector();

				// Get valid defined selectors
				var goodDefinedSelectors = [];

				// Check is valid
				if ( definedSelectors.length > 0){

					// Each founded selectors
					$.each( definedSelectors,function(qx){

						// Find the best in defined selectors
						if (window.Stylist.iDocJQ.find( definedSelectors[qx]).length == window.Stylist.iDocJQ.find(selector).length){

							// Push
							goodDefinedSelectors.push( definedSelectors[qx]);

						}

					});

					// There is good selectors?
					if (goodDefinedSelectors.length > 0){

						// Find max long selector
						var maxSelector = goodDefinedSelectors.sort(function(a, b) {
							return b.length - a.length;
						});

						// Be sure more long than 10 char
						if (maxSelector[0].length > 10){

							// Update
							selector = maxSelector[0];

						}

					}

				}

			}


			// remove multiple spaces
			selector = space_cleaner(selector);


			// Cleans ">" symbols from selector if not need.
			if (selector.indexOf( ">") != -1){

				var length = selector.split( ">").length;
				var elementLength = window.Stylist.iDocJQ.find(selector).length;

				for(var i = 1; i < length; i++){

					if (window.Stylist.iDocJQ.find(selector.replace(/ > /i,' ')).length == elementLength){
						selector = selector.replace(/ > /i,' ');
					}

				}

			}


			// Ready
			selector = multiple_variation(space_cleaner(selector));


			// Use as single inspector if selector is div and more than 20
			if (/( |>)div$/g.test(selector) && window.Stylist.iDocJQ.find(selector).length >= 20){
				return single_selector(selector);
			}


			// Return result.
			return selector;

		}


		/* ---------------------------------------------------- */
		/* Return true if the element in same parent or         */
		/* selector match a single element                      */
		/* ---------------------------------------------------- */
		window.stlst_legacy.is_matchless2 = function (selector, lastSelector, tag) {
			return is_matchless2(selector, lastSelector, tag);
		}
		function is_matchless2(selector, lastSelector, tag){

			var element = window.Stylist.iDocJQ.find(selector);
			var element2 = window.Stylist.iDocJQ.find($.trim(selector.match(new RegExp( "^(.*?)(?=" + prepare_regex_string(lastSelector) + "$)","g")).join('').toString()) + window.separator + tag.toLowerCase());

			// If tag and class selector length is same
			if (element.length == 1 && element2.length == 1){
				return true;

			// continue if the element length is same
			}else if (element.length == element2.length && /.|#/g.test(lastSelector)){

				// Arrays
				var depthArray  = [];
				var tagArray = [];

				// Each current class
				window.Stylist.iDocJQ.find(lastSelector).each(function(){

					var element = jQuery(this);

					// Push depth len
					depthArray.push(element.parents().length);

					// Push tags used
					tagArray.push(element[0].nodeName);

				});

				var depthArrayEquals = depthArray.every(function(v, i, a){
					return v === a[0];
				});

				var tagArrayEquals = tagArray.every(function(v, i, a){
					return v === a[0];
				});

				// Passed depth and tag methods
				if ( depthArrayEquals && tagArrayEquals){
					return true;
				}

			}

			return false;

		}


		/* ---------------------------------------------------- */
		/* Draw borders/margin/padding controls.                */
		/* ---------------------------------------------------- */
		window.stlst_legacy.draw_box = function() {
			// var element = ".stlst-selected";
			var classes = "stlst-selected-boxed";

			var iframeDoc = window.frames['stylist_iframe'].contentDocument;
			var iframe = jQuery( jQuery('#stylist_iframe').contents().get(0) );
			// var iframeBody = window.Stylist.iDocJQ.find( "body");
			var element_p_js = iframeDoc.querySelector( ".stlst-selected" );
			var iframeBody = jQuery( iframeDoc.querySelector( "body" ) );

			// Continue only if selected element exists.
			if ( element_p_js === null ) {
				return;
			}

			var element_p = jQuery( element_p_js );

			var marginTop = element_p.css( "marginTop");
			var marginBottom = element_p.css( "marginBottom");
			var marginLeft = element_p.css( "marginLeft");
			var marginRight = element_p.css( "marginRight");

			var paddingTop = element_p.css( "paddingTop");
			var paddingBottom = element_p.css( "paddingBottom");
			var paddingLeft = element_p.css( "paddingLeft");
			var paddingRight = element_p.css( "paddingRight");

			var marginLeftOr = marginLeft;
			var marginRightOr = marginRight;

			if (parseInt(paddingTop) == 0){paddingTop = "7px";}
			if (parseInt(paddingRight ) == 0){paddingRight = "5px";}
			if (parseInt(paddingBottom) == 0){paddingBottom = "7px";}
			if (parseInt(paddingLeft ) == 0){paddingLeft = "7px";}

			window.Stylist.iDocJQ.find( ".stlst-zero-margin-h").removeClass( "stlst-zero-margin-h");

			if (parseInt(marginTop) == 0){
				marginTop = "5px";
				window.Stylist.iDocJQ.find( ".stlst-selected-boxed-margin-top").addClass( "stlst-zero-margin-h");
			}

			//Dynamic boxes variables
			var element_offset = element_p.offset();
			var topBoxes = element_offset.top;
			var leftBoxes = element_offset.left;
			if (leftBoxes < 0) {
				leftBoxes = 0;
			}

			// Width
			var widthBoxes = element_p.outerWidth(false);
			var heightBoxes = element_p.outerHeight(false);
			var bottomBoxes = topBoxes + heightBoxes;

			// Frame Height
			var iframeHeight = iframe.height();

			// Show 5px Margin Bottom if element not in bottom.
			if ( parseInt(marginBottom) == 0 && Math.round(bottomBoxes) != iframeHeight && Math.round(bottomBoxes+2) != iframeHeight ){
				marginBottom = "5px";
				window.Stylist.iDocJQ.find( ".stlst-selected-boxed-margin-bottom").addClass( "stlst-zero-margin-h");
			}

			// bottom element or not
			if (Math.round(bottomBoxes) == iframeHeight || Math.round(bottomBoxes+2) == iframeHeight ) {
				window.Stylist.iBodyJQ.addClass( "stlst-selected-bottom");
			}else{
				window.Stylist.iBodyJQ.removeClass( "stlst-selected-bottom");
			}

			// Window Width
			var iframeWidth = jQuery( "#stylist_iframe").width();
			var scroll_width = iframeWidth - window.Stylist.iDocJQ.find( "html").width();
			var windowWidth = jQuery( window ).width() - scroll_width;

			// Extra
			var rightExtra = 1;
			if (window.stlst_legacy.is_content_selected()) {
				rightExtra = 2;
			}


			// Fix scroll problems
			if ((leftBoxes + widthBoxes) > windowWidth) {
				widthBoxes = windowWidth - leftBoxes - rightExtra;

			} else if ( is_responsive_mod()) {

				if ((leftBoxes + widthBoxes) > iframeWidth) {
					widthBoxes = iframeWidth - leftBoxes - scroll_width;
				}

				if ( iframeWidth == widthBoxes && window.Stylist.iDocJQ.find( "html").height() > jQuery( "#stylist_iframe").height()){
					widthBoxes = widthBoxes - scroll_width;
				}

			}

			// Left in pixel to right border
			var rightBoxes = leftBoxes + widthBoxes - rightExtra;


			// if element full width
			if ((leftBoxes + widthBoxes + 2) >= ( iframeWidth - scroll_width)) {
				window.Stylist.iBodyJQ.addClass( "stlst-full-width-selected");

			} else {
				window.Stylist.iBodyJQ.removeClass( "stlst-full-width-selected");
			}

			// firefox dont get marginRight if is auto, so this fix problem.
			var isMarginAuto = false;
			if ( iframeWidth == (parseFloat(marginLeft * 2)) + widthBoxes && parseFloat(marginLeft ) > 0){
				isMarginAuto = true;
			}else if (element_p.parent().length > 0){
				if (parseFloat(element_p.parent().width()) == ((parseFloat(marginLeft ) * 2) + widthBoxes) && parseFloat(marginLeft ) > 0){
					isMarginAuto = true;
				}
			}

			// Show empty margin left/right just if there have free space ( if not full width)
			if ( ( iframeWidth - scroll_width ) > (leftBoxes + widthBoxes + 30 ) ){

				window.Stylist.iDocJQ.find( ".stlst-zero-margin-w").removeClass( "stlst-zero-margin-w");

				if (parseInt(marginRight ) == 0){
					marginRight = "5px";
					window.Stylist.iDocJQ.find( ".stlst-selected-boxed-margin-right").addClass( "stlst-zero-margin-w");
				}

				if (parseInt(marginLeft ) == 0){
					marginLeft = "5px";
					window.Stylist.iDocJQ.find( ".stlst-selected-boxed-margin-left").addClass( "stlst-zero-margin-w");
				}
			}


			// Margin default values
			var marginTopText = '', marginRightText = '', marginBottomText = '', marginLeftText = '';
			if (parseInt(marginTop) > 30){marginTopText = parseInt(marginTop)+"px";}
			if (parseInt(marginRight ) > 30){marginRightText = parseInt(marginRight )+"px";}
			if (parseInt(marginBottom) > 30){marginBottomText = parseInt(marginBottom)+"px";}
			if (parseInt(marginLeft ) > 30){marginLeftText = parseInt(marginLeft )+"px";}


			// Padding default values
			var paddingTopText = '', paddingRightText = '', paddingBottomText = '', paddingLeftText = '';
			if (parseInt(paddingTop) > 30){paddingTopText = parseInt(paddingTop)+"px";}
			if (parseInt(paddingRight ) > 30){paddingRightText = parseInt(paddingRight )+"px";}
			if (parseInt(paddingBottom) > 30){paddingBottomText = parseInt(paddingBottom)+"px";}
			if (parseInt(paddingLeft ) > 30){paddingLeftText = parseInt(paddingLeft )+"px";}

			// Shows Auto text
			if ( isMarginAuto){
				marginLeftText = "Auto";
				marginRightText = "Auto";
			}

			// Append border elements
			if (heightBoxes > 1 && widthBoxes > 1) {

				if (window.Stylist.iDocJQ.find( "." + classes + "-top").length === 0) {
					// Border
					var appendBox = "<div class='" + classes + "-top'></div><div class='" + classes + "-bottom'></div><div class='" + classes + "-left'></div><div class='" + classes + "-right'></div>";
					// margin
					// appendBox += "<div class='" + classes + "-margin-top'>"+marginTopText+"</div><div class='" + classes + "-margin-bottom'>"+marginBottomText+"</div><div class='" + classes + "-margin-left'>"+marginLeftText+"</div><div class='" + classes + "-margin-right'>"+marginRightText+"</div>";
					// padding
					// appendBox += "<div class='" + classes + "-padding-top'>"+paddingTopText+"</div><div class='" + classes + "-padding-bottom'>"+paddingBottomText+"</div><div class='" + classes + "-padding-left'>"+paddingLeftText+"</div><div class='" + classes + "-padding-right'>"+paddingRightText+"</div>";

					appendBox = '<div class="stlst-selected-box" id="stlst-selected-box">' + appendBox + '</div>'

					// Append
					window.Stylist.iBodyJQ.append(appendBox);

				} else {

					// Update margin box value
					window.Stylist.iDocJQ.find( "." + classes + "-margin-top").text(marginTopText );
					window.Stylist.iDocJQ.find( "." + classes + "-margin-right").text(marginRightText );
					window.Stylist.iDocJQ.find( "." + classes + "-margin-bottom").text(marginBottomText );
					window.Stylist.iDocJQ.find( "." + classes + "-margin-left").text(marginLeftText );

					// Update padding box value
					window.Stylist.iDocJQ.find( "." + classes + "-padding-top").text(paddingTopText );
					window.Stylist.iDocJQ.find( "." + classes + "-padding-right").text(paddingRightText );
					window.Stylist.iDocJQ.find( "." + classes + "-padding-bottom").text(paddingBottomText );
					window.Stylist.iDocJQ.find( "." + classes + "-padding-left").text(paddingLeftText );
				}

				// Variables for inline CSS
				var topTop = parseFloat(topBoxes) - parseFloat(marginTop);
				var leftLeft = parseFloat(leftBoxes) - parseFloat(marginLeft );
				var bottomBottom = bottomBoxes - parseFloat(paddingBottom);
				var rightRight = rightBoxes - parseFloat(paddingRight );


				// Box Border
				var style = "." + classes + "-top{top:"+topBoxes+"px !important;left:"+leftBoxes+"px !important;width:"+widthBoxes+"px !important;}";
				style += "." + classes + "-bottom{top:"+bottomBoxes+"px !important;left:"+leftBoxes+"px !important;width:"+widthBoxes+"px !important;}";
				style += "." + classes + "-left{top:"+topBoxes+"px !important;left:"+leftBoxes+"px !important;height:"+heightBoxes+"px !important;}";
				style += "." + classes + "-right{top:"+topBoxes+"px !important;left:"+rightBoxes+"px !important;height:"+heightBoxes+"px !important;}";


				// Max margin right position
				var marginRightX = parseFloat(marginRight );
				if (((rightBoxes+2) + parseFloat(marginRight )) > ( iframeWidth - scroll_width)){
					marginRightX = ( iframeWidth - scroll_width) - (rightBoxes + 2);
				}


				// Margin
				style += "." + classes + "-margin-top{top:"+topTop+"px !important;left:"+(parseFloat(leftBoxes) - parseFloat(marginLeftOr))+"px !important;width:"+(parseFloat(widthBoxes) + parseFloat(marginLeftOr) + parseFloat(marginRightOr))+"px !important;height:"+parseFloat(marginTop)+"px !important;}";
				style += "." + classes + "-margin-bottom{top:"+bottomBoxes+"px !important;left:"+(parseFloat(leftBoxes) - parseFloat(marginLeftOr))+"px !important;width:"+(parseFloat(widthBoxes) + parseFloat(marginLeftOr) + parseFloat(marginRightOr))+"px !important;height:"+parseFloat(marginBottom)+"px !important;}";
				style += "." + classes + "-margin-left{top:"+topBoxes+"px !important;left:"+leftLeft+"px !important;width:"+parseFloat(marginLeft )+"px !important;height:"+heightBoxes+"px !important;}";
				style += "." + classes + "-margin-right{top:"+topBoxes+"px !important;left:"+(parseFloat(rightBoxes)+2)+"px !important;width:"+parseFloat(marginRightX)+"px !important;height:"+heightBoxes+"px !important;}";

				// Padding
				style += "." + classes + "-padding-top{top:"+parseFloat(topBoxes)+"px !important;left:"+parseFloat(leftBoxes)+"px !important;width:"+widthBoxes+"px !important;height:"+parseFloat(paddingTop)+"px !important;}";
				style += "." + classes + "-padding-bottom{top:"+bottomBottom+"px !important;left:"+parseFloat(leftBoxes)+"px !important;width:"+widthBoxes+"px !important;height:"+parseFloat(paddingBottom)+"px !important;}";
				style += "." + classes + "-padding-left{top:"+parseFloat(topBoxes)+"px !important;left:"+parseFloat(leftBoxes)+"px !important;width:"+parseFloat(paddingLeft )+"px !important;height:"+parseFloat(heightBoxes)+"px !important;}";
				style += "." + classes + "-padding-right{top:"+parseFloat(topBoxes)+"px !important;left:"+rightRight+"px !important;width:"+parseFloat(paddingRight )+"px !important;height:"+parseFloat(heightBoxes)+"px !important;}";


				// Style#stlst-draw-box
				var drawBoxStyle = window.Stylist.iBodyJQ.find( "#stlst-draw-box");

				// Append
				if ( drawBoxStyle.length > 0 ) {

					drawBoxStyle.html(style);
				} else {

					window.Stylist.iBodyJQ.append( "<style id='stlst-draw-box'>"+style+"</style>");
				}

				if ( is_resizing() == false && is_dragging() == false && is_visual_editing() == false ){
					window.Stylist.iDocJQ.find( ".stlst-selected-handle").css( "left", leftBoxes).css( "top", topBoxes);
				}
			}
		}


		/* ---------------------------------------------------- */
		/* Process media queries                                */
		/* ---------------------------------------------------- */
		function process_media_query(condition){

			var die = false;

			// Not processable
			var conRex = /\bhandheld\b|\baural\b|\bbraille\b|\bembossed\b|\bprojection\b|\btty\b|\btv\b|\bprint\b|\b3d-glasses\b/;

			if (conRex.test(condition)){
				return null;
			}

			// not and , not acceptable
			var conRex2 = /,|\bnot\b/;
			if (conRex2.test(condition)){
				return false;
			}

			// For replace em & and rem
			var fontSizeRotio = parseFloat(window.Stylist.iDocJQ.find( "html").css( "fontSize"));

			// replacing rem & em to PX
			condition = condition.replace(/[0-9. ]+(rem|em)/g, function(match, contents, offset, s){
					return parseFloat(match)*fontSizeRotio+"px";
				}
			);

			// Get all queries
			var queries = condition.match(/\((.*?)\)/g);

			var goValue = [];
			var minmaxRex = /max-width|min-width/;

			// loop queries
			$.each(queries, function( index,query ){

				// Just max and min width
				if (minmaxRex.test(query ) == false){
					die = true;
					return false;
				}

				// Cleaning
				query = query.replace(/\(|\)|:|px|\s+/g,'');

				// max min widths
				query = query.replace(/min-width/g,'>');
				query = query.replace(/max-width/g,'<');

				goValue.push(query );

			});

			// Return
			if ( die == false){
				return goValue;
			}

		}


		/* ---------------------------------------------------- */
		/* Cleans selector for regex                            */
		/* ---------------------------------------------------- */
		window.stlst_legacy.prepare_regex_string = function (selector ) {
			return prepare_regex_string(selector);
		}
		function prepare_regex_string( selector ){
			return selector
			.replace(   /\\/g, "\\\\") // \
			.replace(/\./g, "\\.")  // [
			.replace(/\[/g, "\\[")  // [
			.replace(/\]/g, "\\]")  // ]
			.replace(/\(/g, "\\( ")  // (
			.replace(/\)/g, "\\)")  // )
			.replace(/\^/g, "\\^")  // ^
			.replace(/\$/g, "\\$")  // $
			.replace(/\*/g, "\\*")  // *
			.replace(/\:/g, "\\:")  // :
			.replace(/\+/g, "\\+"); // +
		}


		/* ---------------------------------------------------- */
		/* Generate automatic media query                       */
		/* ---------------------------------------------------- */
		function creating_auto_media_query(arrMedia){

			var condition = false;
			var closestLow;

			// Current Width
			var currentWidth = jQuery( window ).width();

			var upArr = [];
			var downArr = [];

			// ARR Media
			$.each(arrMedia,function( index,value){

				if (value != null){
					value = value.toString();
					if (value.indexOf( "<") != -1){
						downArr.push(value.replace(/\</g,''));
					}else if (value.indexOf( ">") != -1){
						upArr.push(value.replace(/\>/g,''));
					}
				}

			});

			// High to low
			upArr = upArr.sort(function(a, b){return b-a});
			downArr = downArr.sort(function(a, b){return b-a});

			$.each( downArr, function(){
				if (this <= currentWidth && (closestLow == null || (currentWidth - this) < (currentWidth - closestLow))) {
				closestLow = this;
				}
			});

			// if min-width high and max-width low than current width
			if ( downArr.length > 0 && upArr.length > 0){
				if (upArr[0] > currentWidth && downArr[0] < currentWidth){
					condition = '@media (min-width:'+downArr[0]+'px) and (max-width:'+upArr[0]+'px)';
				}
			}

			// if min-width and max-width high than current width
			if ( downArr.length > 0 && upArr.length > 0){
				if (upArr[0] > currentWidth && downArr[0] > currentWidth){

					if (closestLow < currentWidth){
						condition = '@media (max-width:'+upArr[0]+'px) and (min-width:'+closestLow+'px)';
					}else{
						condition = '@media (max-width:'+upArr[0]+'px)';
					}

				}
			}

			// if min-width and max-width high than current width
			if ( downArr.length == 0 && upArr.length > 0){
				if (upArr[0] > currentWidth){
					condition = '@media (max-width:'+upArr[0]+'px)';
				}
			}

			// if min-width and max-width low than current width
			if ( downArr.length > 0 && upArr.length > 0){
				if (upArr[0] < currentWidth && downArr[0] < currentWidth){
					condition = '@media (min-width:'+downArr[0]+'px)';
				}
			}

			// if min-width and max-width low than current width
			if ( downArr.length > 0 && upArr.length == 0){
				if ( downArr[0] < currentWidth){
					condition = '@media (min-width:'+downArr[0]+'px)';
				}
			}

			// if min-width and max-width low than current width
			if ( downArr.length > 1 && upArr.length == 0){
				if ( downArr[0] > currentWidth && closestLow < currentWidth){
					condition = '@media (max-width:'+downArr[0]+'px) and (min-width:'+closestLow+'px)';
				}
			}

			return condition;

		}


		/* ---------------------------------------------------- */
		/* Getting the scrollbar Width                          */
		/* ---------------------------------------------------- */
		function get_scroll_bar_width() {

			// no need on responsive mode.
			if ( is_responsive_mod()) {
				return 0;
			}

			// If no scrollbar, return zero.
			if ( iframe.height() <= jQuery( window ).height() && mainBody.hasClass( "stlst-metric-disable")) {
				return 0;
			}

			var inner = document.createElement('p');
			inner.style.width = "100%";
			inner.style.height = "200px";

			var outer = document.createElement('div');
			outer.style.position = "absolute";
			outer.style.top = "0px";
			outer.style.left = "0px";
			outer.style.visibility = "hidden";
			outer.style.width = "200px";
			outer.style.height = "150px";
			outer.style.overflow = "hidden";
			outer.appendChild( inner);

			document.body.appendChild(outer);
			var w1 = inner.offsetWidth;
			outer.style.overflow = 'scroll';
			var w2 = inner.offsetWidth;
			if (w1 == w2) w2 = outer.clientWidth;

			document.body.removeChild(outer);

			return (w1 - w2);

		}


		/* ---------------------------------------------------- */
		/* Draw other borders.                                  */
		/* ---------------------------------------------------- */
		window.stlst_legacy.draw_other_box = function(element, classes, $i) {
			var element_p = jQuery(element );

			var elementClasses = element_p.attr( "class");

			if (element_p === null) {
				return false;
			}

			if (element_p[0].nodeName == "HTML" || element_p[0].nodeName == "BODY") {
				return false;
			}

			if (element_p.length === 0) {
				return false;
			}

			// Be sure this is visible on screen
			if (element_p.css( "display") == 'none' || element_p.css( "visibility") == 'hidden' || element_p.css( "opacity") == '0') {
				return false;
			}

			// Not show if p tag and is empty.
			if (element_p.html() == '&nbsp;' && element_p.prop( "tagName") == 'P') {
				return false;
			}

			// Stop.
			if (mainBody.hasClass( "stlst-has-transform" ) ) {
				return false;
			}

			// not draw new box and delete last.
			if (window.stlst_legacy.isDefined(elementClasses)){

				elementClasses = elementClasses.replace(/stlst-selected-others/g,'');

				var pluginelRex = /stlst-selected|stlst-tooltip-small|stlst-edit-/;

				if (pluginelRex.test(elementClasses) || element_p.hasClass( "stlst-selected-others-box" ) ) {
					if (window.Stylist.iDocJQ.find( "." + classes + "-" + $i + "-box").length > 0){
						window.Stylist.iDocJQ.find( "." + classes + "-" + $i + "-box").remove();
					}

					return false;

				}

			}

			//Dynamic boxes variables
			var element_offset = element_p.offset();
			var topBoxes = element_offset.top;
			var leftBoxes = element_offset.left;
			var widthBoxes = element_p.outerWidth(false);
			var heightBoxes = element_p.outerHeight(false);

			if (heightBoxes > 1 && widthBoxes > 1) {

				// Append Dynamic Box
				if (window.Stylist.iDocJQ.find( "." + classes + "-" + $i + "-box").length === 0) {

					return "<div class='" + classes + "-box " + classes + "-" + $i + "-box' style='top:"+parseFloat(topBoxes)+"px !important;left:"+parseFloat(leftBoxes)+"px !important;width:"+parseFloat(widthBoxes)+"px !important;height:"+parseFloat(heightBoxes)+"px !important;'></div>";

				}else{

					// Update boxes
					iframeBody.find( "." + classes + "-" + $i + "-box").css( "top",parseFloat(topBoxes)+"px").css( "left",parseFloat(leftBoxes)+"px").css( "width",parseFloat(widthBoxes)+"px").css( "height",parseFloat(heightBoxes)+"px");

				}

			}

		}


		/* ---------------------------------------------------- */
		/* Visible Height in scroll.                            */
		/* ---------------------------------------------------- */
		function get_visible_height(t ) {
			var top = t.offset().top;
			var scrollTop = iframe.scrollTop();
			var height = t.outerHeight();

			if (top < scrollTop) {
				return height - (scrollTop - top);
			} else {
				return height;
			}

		}

		/* ---------------------------------------------------- */
		/* Trigger mouseup event if mouseup on iframe.          */
		/* ---------------------------------------------------- */
		iframe.on( "mouseup", iframe, function() {

			jQuery( document ).trigger( "mouseup");

		});


		/* ---------------------------------------------------- */
		/* Updating drag icon position                          */
		/* ---------------------------------------------------- */
		function update_drag_handle_position() {

			// Element selected?
			if (!window.stlst_legacy.is_content_selected()) {
				return false;
			}

			// element
			var element = get_selected_element();

			/*
			var handle = window.Stylist.iDocJQ.find( ".stlst-selected-handle");

			// Add new
			if (element.height() > 20 && element.width() > 60 && handle.length === 0) {
				var selectedBox = window.Stylist.iBodyJQ.find('.stlst-selected-box');
				selectedBox.append( "<span class='stlst-selected-handle'></span>");
			}

			handle.css( "left", window.Stylist.iDocJQ.find( ".stlst-selected-boxed-right").css( "left"));
			handle.css( "top", window.Stylist.iDocJQ.find( ".stlst-selected-boxed-bottom").css( "top"));
			handle.css( "opacity", window.Stylist.iDocJQ.find( ".stlst-selected-boxed-bottom").css( "opacity"));
			*/

		}

		/* ---------------------------------------------------- */
		/* Creating smart guides while resize & dragging        */
		/* ---------------------------------------------------- */
		function create_smart_guides(){
			if (body.hasClass( "stlst-smart-guide-disabled") || mainBody.hasClass( "stlst-has-transform" ) ) {
				return false;
			}

			var maxWidth = 0;
			var maxWidthEl = null;
			var k = jQuery( window ).width();

			// Smart guides: START
			var Allelements = iframeBody.find(get_all_elements( ":not(ul li)"));

			for (var i=0; i < Allelements.length; i++){

				// Element
				var el = jQuery(Allelements[i]);
				var otherWidth = el.outerWidth();


				// 720 768 940 960 980 1030 1040 1170 1210 1268
				if (otherWidth >= 720 && otherWidth <= 1268 && otherWidth < (k-80)){

					if (otherWidth > maxWidth){
						maxWidthEl = el;
					}

					maxWidth = Math.max(otherWidth, maxWidth);

				}


				if (el.parents( ".stlst-selected").length <= 0 && el.parents( ".stlst-selected-others").length <= 0 && el.css( "display") != 'none' && el.css( "opacity") != "0" && el.css( "visibility") != 'hidden' && el.height() >= 10){

					var offset = el.offset();

					// Getting sizes
					var otherTop = Math.round(offset.top);
					var otherLeft = Math.round(offset.left );
					var otherHeight = Math.round(el.outerHeight());

						// don't add "inner" same size elements.
						if ( iframeBody.find('[data-stlst-top="'+otherTop+'"][data-stlst-left="'+otherLeft+'"][data-stlst-width="'+otherWidth+'"][data-stlst-height="'+otherHeight+'"]').length <= 0){

							// Saving for use on drag event.
							// faster performance.
							el.addClass( "stlst-smart-guide-elements")
							.attr( "data-stlst-top",otherTop)
							.attr( "data-stlst-left",otherLeft )
							.attr( "data-stlst-top-round",stlst_round(otherTop))
							.attr( "data-stlst-bottom-round",stlst_round(otherTop+otherHeight ))
							.attr( "data-stlst-left-round",stlst_round(otherLeft ))
							.attr( "data-stlst-right-round",stlst_round(otherLeft+otherWidth))
							.attr( "data-stlst-width",otherWidth)
							.attr( "data-stlst-height",otherHeight );
						}

					}

			}

			// Not adding on responsive mode.
			if (maxWidthEl !== null){

				var Pleft = maxWidthEl.offset().left;

				if (Pleft > 50){

					var Pright = Pleft+maxWidth;

					if (parseInt(Pleft ) == parseInt( iframe.width()-Pright )){

						iframeBody.append( "<div class='stlst-page-border-left' style='left:"+Pleft+"px;'></div><div class='stlst-page-border-right' style='left:"+Pright+"px;'></div>");

					}

				}

			}

			// Adding distance borders
			iframeBody.append( "<div class='stlst-x-distance-border'></div><div class='stlst-y-distance-border'></div>");

		}

		/* ---------------------------------------------------- */
		/* Clean up smart guides                                */
		/* ---------------------------------------------------- */
		function clean_smart_guides(){

			iframeBody.find( ".stlst-page-border-left,.stlst-page-border-right").remove();

			// Removing distance borders
			iframeBody.find( ".stlst-x-distance-border,.stlst-y-distance-border,.stlst-helper-tooltip").remove();

			iframeBody.find( ".stlst-smart-guide-elements").removeClass( "stlst-smart-guide-elements")
				.removeAttr( "data-stlst-top")
				.removeAttr( "data-stlst-left")
				.removeAttr( "data-stlst-width")
				.removeAttr( "data-stlst-top-round")
				.removeAttr( "data-stlst-bottom-round")
				.removeAttr( "data-stlst-left-round")
				.removeAttr( "data-stlst-right-round")
				.removeAttr( "data-stlst-height");

		}

		/* ---------------------------------------------------- */
		/* Calcature Smart Sizes 100%, 100vh etc                */
		/* ---------------------------------------------------- */
		function calcature_smart_sizes(element,val){

			// Variable
			var result = [];

			var founded = false;

			// Check parent details.
			if (element.parent().length > 0){

				// IF not any inline or table display
				if (element.parent().css( "display").indexOf( "table") == -1 && element.parent().css( "display") != 'inline' && element.parent().css( "display") != 'inline-flex'){

					var parentWidth = element.parent().width();

					// if start width percent, use automatic percent all time while resizing.
					if (window.liveResizeWPercent == true){

						// Flag
						founded = true;

						// Update
						result.val = Math.round((parseFloat(val)*100/parseFloat(parentWidth)) * 10 ) / 10;
						result.format = '%';


					}

					// if width is same with parent width, so set 100%!
					if (parseInt(parentWidth) == parseInt(val) && founded == false) {

						// Flag
						founded = true;

						// Update
						result.val = 100;
						result.format = '%';

					}

					// if width is 50% with parent width, so set 50%!
					if (parseInt(parentWidth/2) == parseInt(val) && founded == false) {

						// Flag
						founded = true;

						// Update
						result.val = 50;
						result.format = '%';

					}

					// if width is 25% with parent width, so set 25%!
					if (parseInt(parentWidth/4) == parseInt(val) && founded == false) {

						// Flag
						founded = true;

						// Update
						result.val = 25;
						result.format = '%';

					}

					// if width is 20% with parent width, so set 20%!
					if (parseInt(parentWidth/5) == parseInt(val) && founded == false) {

						// Flag
						founded = true;

						// Update
						result.val = 20;
						result.format = '%';

					}

				}

			}

			// Return default
			if (founded === false){
				result.val = val;
				result.format = 'px';
			}

			return result;

		}

		/* ---------------------------------------------------- */
		/* Rounding numbers                                     */
		/* ---------------------------------------------------- */
		function stlst_round(x){
			return Math.round(x / 6) * 6;
		}


		window.visualEdit = false;
		window.visualEditDelay = null;

	// ============================================================


	/* ---------------------------------------------------- */
	/* Doesn't getting styles while element hover           */
	/* because the editor must load only non-hover styles   */
	/* ---------------------------------------------------- */
	body.on('mousedown', '.stlst-editor-list > li:not(.stlst-li-footer):not(.stlst-li-about ):not(.active)', function() {

		if (window.stlst_legacy.is_content_selected() === true) {

			// Get data
			var data = jQuery(this).attr( "data-loaded");

			// If no data, so set.
			if (typeof data == typeof undefined || data === false) {

				// Set default values
				jQuery(this).find( ".stlst-option-group").each(function() {
					window.stlst_legacy.getDefaultValue(get_option_id(this));
				});

				// cache to loaded data.
				jQuery(this).attr( "data-loaded", "true");

			}

		}

	});

	/* ---------------------------------------------------- */
	/* Getting All ideal elements. Used by smart guides.    */
	/* ---------------------------------------------------- */
	function get_all_elements(custom){

		var selector = '*';

		var notSelectors = [
			".stlst-x-distance-border",
			".stlst-y-distance-border",
			".hover-info-box",
			".stlst-size-handle",
			".stlst-edit-tooltip",
			".stlst-edit-menu",
			".stlst-selected-tooltip",
			".stlst-tooltip-small",
			".stlst-helper-tooltip",
			"[class^='stlst-selected-boxed-']",
			"[class^='stlst-selected-others-box']",
			"link",
			"style",
			"script",
			"param",
			"option",
			"tr",
			"td",
			"th",
			"thead",
			"tbody",
			"tfoot",
			"iframe",
			"noscript",
			"scene-1",
			"scene-2",
			"scene-3",
			"scene-4",
			"scene-5",
			"scene-6",
			"stlst-anim-scenes",
			"animate-test-drive"
		];

		// Get classes added by editor
		var pluginClasses = window.plugin_classes_list.split( "|");

		for(var x = 0; x < pluginClasses.length; x++){
			pluginClasses[x] = "." + pluginClasses[x];
		}

		// concat
		notSelectors = notSelectors.concat(pluginClasses);

		// Adding not selectors
		for(var i = 0; i < notSelectors.length; i++){
			selector += ":not( "+notSelectors[i]+")";
		}

		// parement
		if (custom !== undefined){
			selector += custom;
		}

		// Visible filter
		selector += ":visible";

		return selector;

	}


	/* ---------------------------------------------------- */
	/* None / Disable Buttons API      VVVVVV                      */
	/* ---------------------------------------------------- */
	jQuery( ".stlst-btn-action").click(function ( clickEvent ) {

		// window.stlst_legacy.defaultButtonAction( clickEvent );
	});

	/* ---------------------------------------------------- */
	/* Collapse List                                        */
	/* ---------------------------------------------------- */
	jQuery( ".stlst-editor-list > li > h3").click(function() {

		var element = jQuery(this);
		var elementParent = element.parent();

		if (elementParent.hasClass( "stlst-li-about") || elementParent.hasClass( "stlst-li-footer")) {
			return '';
		}

		elementParent.addClass( "current");

		// Disable.
		jQuery( ".stlst-editor-list > li.active:not(.current )").each(function() {

			jQuery( ".stlst-editor-list > li").show();
			element.find( ".stlst-this-content").hide().parent().removeClass( "active");
		});

		if (elementParent.hasClass( "active")) {
			elementParent.removeClass( "active");
		} else {
			elementParent.addClass( "active");
			jQuery( ".stlst-editor-list > li:not(.active)").hide();
		}

		elementParent.find( ".stlst-this-content").toggle();
		elementParent.removeClass( "current");

		if (jQuery( ".stlst-close-btn.dashicons-menu").length > 0) {
			jQuery( ".stlst-close-btn").removeClass( "dashicons-menu").addClass( "dashicons-no-alt");
			// jQuery( ".stlst-close-btn").tooltip('hide').attr('data-original-title', stylistJsData.l18_close_editor).tooltip('fixTitle');
		}

		if (jQuery( ".stlst-editor-list > li.active:not(.stlst-li-about ):not(.stlst-li-footer) > h3").length > 0) {
			jQuery( ".stlst-close-btn").removeClass( "dashicons-no-alt").addClass( "dashicons-menu");
			// jQuery( ".stlst-close-btn").tooltip('hide').attr('data-original-title', stylistJsData.l18_back_to_menu).tooltip('fixTitle');

		}

		jQuery('.stlst-editor-list').scrollTop(0);

	});


	/* ---------------------------------------------------- */
	/* Filters                                              */
	/* ---------------------------------------------------- */

	window.stlst_legacy.number_filter = function (a) {
		return number_filter(a);
	}
	function number_filter(a) {
		if (typeof a !== "undefined" && a != '') {
			if (a.replace(/[^\d.-]/g, '') === null || a.replace(/[^\d.-]/g, '') === undefined) {
				return 0;
			} else {
				return a.replace(/[^\d.-]/g, '');
			}
		} else {
			return 0;
		}
	}

	window.stlst_legacy.alfa_filter = function (a) {
		return alfa_filter(a);
	}
	function alfa_filter(a) {
		if (typeof a !== "undefined" && a != '') {
			return a.replace(/\d/g, '').replace( ".px", "px");
		} else {
			return '';
		}
	}

	window.stlst_legacy.get_basic_id = function (str) {
		return get_basic_id(str);
	}
	var get_basic_id = function(str) {
		if (typeof str !== "undefined" && str != '') {
			str = str.replace(/\W+/g, "");
			return str;
		} else {
			return '';
		}
	};

	window.stlst_legacy.get_id = function (str) {
		return get_id(str);
	}
	function get_id(str) {
		if (typeof str !== "undefined" && str != '') {

			// \^\#\+\$\(\)\[\]\=\*\-\:\.\>\,\~\@\/\! work in process.
			// str = str.replace(/\:/g, "stlst-sym-p")
			// .replace(/\^/g, "stlst-sym-a")
			// .replace(/\#/g, "stlst-sym-c")
			// .replace(/\+/g, "stlst-sym-o")
			// .replace(/\$/g, "stlst-sym-q")
			// .replace(/\(/g, "stlst-sym-e")
			// .replace(/\)/g, "stlst-sym-s")
			// .replace(/\[/g, "stlst-sym-g")
			// .replace(/\]/g, "stlst-sym-x")
			// .replace(/\=/g, "stlst-sym-k")
			// .replace(/\*/g, "stlst-sym-n")
			// .replace(/\-/g, "stlst-sym-t")
			// .replace(/\./g, "stlst-sym-u")
			// .replace(/\>/g, "stlst-sym-l")
			// .replace(/\,/g, "stlst-sym-b")
			// .replace(/\~/g, "stlst-sym-m")
			// .replace(/\@/g, "stlst-sym-i")
			// .replace(/\//g, "stlst-sym-y")
			// .replace(/\!/g, "stlst-sym-v")
			// .replace(/[^a-zA-Z0-9_\^\#\+\$\(\)\[\]\=\*\-\:\.\>\,\~\@\/\!]/g, "");

			str = str.replace(/\:/g, "-")
			.replace(/\^/g, "")
			.replace(/\#/g, "__")
			.replace(/\+/g, "")
			.replace(/\$/g, "")
			.replace(/\(/g, "")
			.replace(/\)/g, "")
			.replace(/\[/g, "")
			.replace(/\]/g, "")
			.replace(/\=/g, "")
			.replace(/\*/g, "")
			.replace(/\-/g, "-")
			.replace(/\./g, "__")
			.replace(/\>/g, "")
			.replace(/\,/g, "")
			.replace(/\~/g, "")
			.replace(/\@/g, "")
			.replace(/\//g, "")
			.replace(/\!/g, "")
			.replace(/\s/g, "__")
			.replace(/[^a-zA-Z0-9_\^\#\+\$\(\)\[\]\=\*\-\:\.\>\,\~\@\/\!]/g, "");

			str = str.replace(/\_\_\_\_/g, "__")
			str = str.substring(2, str.length)

			return str;
		} else {
			return '';
		}
	}

	window.stlst_legacy.array_cleaner = function (actual) {
		return array_cleaner(actual);
	}
	function array_cleaner(actual) {

		var uniqueArray = actual.filter(function( item, pos) {
			return actual.indexOf( item) == pos;
		});

		return uniqueArray;

	}

	window.stlst_legacy.uppercase_first_letter = function (str) {
		return uppercase_first_letter(str);
	}
	function uppercase_first_letter(str){
		return str.replace(/\w\S*/g, function(txt ){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
	}

	window.stlst_legacy.letter_repeat = function (str) {
		return letter_repeat(str);
	}
	function letter_repeat(str) {
		var reg = /^([a-z])\1+$/;
		var d = reg.test(str);
		return d;
	}

	window.stlst_legacy.title_case = function (str) {
		return title_case(str);
	}
	function title_case(string) {
		return string.charAt(0).toUpperCase() + string.slice(1);
	}


	/* ---------------------------------------------------- */
	/* Getting selected element name                        */
	/* ---------------------------------------------------- */
	window.stlst_legacy.get_tag_information = function(selectors){

		var selectorsArray = selectors.split( ",");

		// If is one selector
		if (selectorsArray.length == 1){
			return get_single_tag_information(selectors);
		}


		// Multi Selectors
		var allTagNames = [];
		var name = '';

		// Get all tag names by selectors
		for(var i = 0; i < selectorsArray.length; i++){

			// Get tag name
			name = get_single_tag_information(selectorsArray[i]);

			// Push if the name not in name-list
			if (allTagNames.indexOf(name) == -1){
				allTagNames.push(name);
			}

		}

		return allTagNames.toString().replace(/\,/g,", ");

	}


	/* ---------------------------------------------------- */
	/* Need it while processing stylesheet selectors        */
	/* ---------------------------------------------------- */
	function get_foundable_query(selector,css,body,animation){

		if (css === true){

			// Hover Focus active visited link
			selector = selector.replace(/:hover/g,'').replace(/:focus/g,'').replace(/:active/g,'').replace(/:visited/g,'').replace(/:link/g,'');

			// After
			selector = selector.replace(/:after/g,'').replace(/::after/g,'');

			// Before
			selector = selector.replace(/:before/g,'').replace(/::before/g,'');

			// First Letter
			selector = selector.replace(/:first-letter/g,'').replace(/::first-letter/g,'');

			// First Line
			selector = selector.replace(/:first-line/g,'').replace(/::first-line/g,'');

			// Selection
			selector = selector.replace(/:selection/g,'').replace(/::selection/g,'');

		}

		if (body === true){

			// YP Selector Hover
			selector = selector.replace(/body\.stlst-selector-hover/g,'').replace(/\.stlst-selector-hover/g,'');

			// YP Selector Focus
			selector = selector.replace(/body\.stlst-selector-focus/g,'').replace(/\.stlst-selector-focus/g,'');

			// YP Selector active
			selector = selector.replace(/body\.stlst-selector-active/g,'').replace(/\.stlst-selector-active/g,'');

			// YP Selector visited
			selector = selector.replace(/body\.stlst-selector-visited/g,'').replace(/\.stlst-selector-visited/g,'');

			// YP Selector link
			selector = selector.replace(/body\.stlst-selector-link/g,'').replace(/\.stlst-selector-link/g,'');

		}

		if (animation === true){

			// YP Animations
			selector = selector.replace(/.stlst_onscreen/g,'').replace(/.stlst_focus/g,'').replace(/.stlst_hover/g,'').replace(/.stlst_click/g,'');

		}

		return selector.trim();

	}


	/* ---------------------------------------------------- */
	/* Cleans multiple spaces                               */
	/* ---------------------------------------------------- */
	window.stlst_legacy.space_cleaner = function( data) {
		return space_cleaner( data);
	}
	function space_cleaner( data){
		return $.trim( data.replace(/\s\s+/g,' '));
	}


	/* ---------------------------------------------------- */
	/* Simple Defined Element Names                         */
	/* ---------------------------------------------------- */
	function get_single_tag_information(selector){

		selector = get_foundable_query(selector,true,true,true);

		if (window.Stylist.iDocJQ.find(selector).length <= 0){
			return;
		}

		var PPname,Pname;

		// tagName
		var a = window.Stylist.iDocJQ.find(selector)[0].nodeName;

		// length
		var length = get_selector_array(selector).length - 1;

		// Names
		var n = get_selector_array(selector)[length].toUpperCase();
		if (n.indexOf( ".") != -1){
			n = n.split( ".")[1].replace(/[^\w\s]/gi, '');
		}

		// Class Names
		var className = $.trim(get_selector_array(selector)[length]);
		if (className.indexOf( ".") != -1) {
			className = className.split( ".")[1];
		}

		// ID
		var id = get_selected_element().attr( "id");

		if (window.stlst_legacy.isDefined( id)) {
			id = id.toUpperCase().replace(/[^\w\s]/gi, '');
		}

		// Parents 1
		if (length > 1) {
			Pname = get_selector_array(selector)[length - 1].toUpperCase();
			if (Pname.indexOf( ".") != -1) {
				Pname = Pname.split( ".")[1].replace(/[^\w\s]/gi, '');
			}
		} else {
			Pname = '';
		}

		// Parents 2
		if (length > 2) {
			PPname = get_selector_array(selector)[length - 2].toUpperCase();
			if (PPname.indexOf( ".") != -1) {
				PPname = PPname.split( ".")[1].replace(/[^\w\s]/gi, '');
			}
		} else {
			PPname = '';
		}

		// ID
		if ( id == 'TOPBAR') {
			return stylistJsData.l18_topbar;
		} else if ( id == 'HEADER') {
			return stylistJsData.l18_header;
		} else if ( id == 'FOOTER') {
			return stylistJsData.l18_footer;
		} else if ( id == 'CONTENT') {
			return stylistJsData.l18_content;
		}

		// Parrents Class
		if (PPname == 'LOGO' || PPname == 'SITETITLE' || Pname == 'LOGO' || Pname == 'SITETITLE') {
			return stylistJsData.l18_logo;
		} else if (n == 'MAPCANVAS') {
			return stylistJsData.l18_google_map;
		} else if (Pname == 'ENTRYTITLE' && a == 'A') {
			return stylistJsData.l18_entry_title_link;
		} else if (Pname == 'CATLINKS' && a == 'A') {
			return stylistJsData.l18_category_link;
		} else if (Pname == 'TAGSLINKS' && a == 'A') {
			return stylistJsData.l18_tag_link;
		}

		// Current Classes
		if (n == 'WIDGET') {
			return stylistJsData.l18_widget;
		} else if (n == 'FA' || get_selector_array(selector)[length].toUpperCase().indexOf( "FA-") >= 0) {
			return stylistJsData.l18_font_awesome_icon;
		} else if (n == 'SUBMIT' && a == 'INPUT') {
			return stylistJsData.l18_submit_button;
		} else if (n == 'MENUITEM') {
			return stylistJsData.l18_menu_item;
		} else if (n == 'ENTRYMETA' || n == 'ENTRYMETABOX' || n == 'POSTMETABOX') {
			return stylistJsData.l18_post_meta_division;
		} else if (n == 'COMMENTREPLYTITLE') {
			return stylistJsData.l18_comment_reply_title;
		} else if (n == 'LOGGEDINAS') {
			return stylistJsData.l18_login_info;
		} else if (n == 'FORMALLOWEDTAGS') {
			return stylistJsData.l18_allowed_tags;
		} else if (n == 'LOGO') {
			return stylistJsData.l18_logo;
		} else if (n == 'ENTRYTITLE' || n == 'POSTTITLE') {
			return stylistJsData.l18_post_title;
		} else if (n == 'COMMENTFORM') {
			return stylistJsData.l18_comment_form;
		} else if (n == 'WIDGETTITLE') {
			return stylistJsData.l18_widget_title;
		} else if (n == 'TAGCLOUD') {
			return stylistJsData.l18_tag_cloud;
		} else if (n == 'ROW' || n == 'VCROW') {
			return stylistJsData.l18_row;
		} else if (n == 'BUTTON') {
			return stylistJsData.l18_button;
		} else if (n == 'BTN') {
			return stylistJsData.l18_button;
		} else if (n == 'LEAD') {
			return stylistJsData.l18_lead;
		} else if (n == 'WELL') {
			return stylistJsData.l18_well;
		} else if (n == 'ACCORDIONTOGGLE') {
			return stylistJsData.l18_accordion_toggle;
		} else if (n == 'PANELBODY') {
			return stylistJsData.l18_accordion_content;
		} else if (n == 'ALERT') {
			return stylistJsData.l18_alert_division;
		} else if (n == 'FOOTERCONTENT') {
			return stylistJsData.l18_footer_content;
		} else if (n == 'GLOBALSECTION' || n == 'VCSSECTION') {
			return stylistJsData.l18_global_section;
		} else if (n == 'MORELINK') {
			return stylistJsData.l18_show_more_link;
		} else if (n == 'CONTAINER' || n == 'WRAPPER') {
			return stylistJsData.l18_wrapper;
		} else if (n == 'DEFAULTTITLE') {
			return stylistJsData.l18_article_title;
		} else if (n == 'MENULINK' || n == 'MENUICON' || n == 'MENUBTN' || n == 'MENUBUTTON') {
			return stylistJsData.l18_menu_link;
		} else if (n == 'SUBMENU') {
			return stylistJsData.l18_submenu;

			// Bootstrap Columns
		} else if (n.indexOf('COLMD12') != -1 || n == 'MEDIUM12' || n == 'LARGE12' || n == 'SMALL12') {
			return stylistJsData.l18_column + ' 12/12';
		} else if (n.indexOf('COLMD11') != -1 || n == 'MEDIUM11' || n == 'LARGE11' || n == 'SMALL11') {
			return stylistJsData.l18_column + ' 11/12';
		} else if (n.indexOf('COLMD10') != -1 || n == 'MEDIUM10' || n == 'LARGE10' || n == 'SMALL10') {
			return stylistJsData.l18_column + ' 10/12';
		} else if (n.indexOf('COLMD9') != -1 || n == 'MEDIUM9' || n == 'LARGE9' || n == 'SMALL9') {
			return stylistJsData.l18_column + ' 9/12';
		} else if (n.indexOf('COLMD8') != -1 || n == 'MEDIUM8' || n == 'LARGE8' || n == 'SMALL8') {
			return stylistJsData.l18_column + ' 8/12';
		} else if (n.indexOf('COLMD7') != -1 || n == 'MEDIUM7' || n == 'LARGE7' || n == 'SMALL7') {
			return stylistJsData.l18_column + ' 7/12';
		} else if (n.indexOf('COLMD6') != -1 || n == 'MEDIUM6' || n == 'LARGE6' || n == 'SMALL6') {
			return stylistJsData.l18_column + ' 6/12';
		} else if (n.indexOf('COLMD5') != -1 || n == 'MEDIUM5' || n == 'LARGE5' || n == 'SMALL5') {
			return stylistJsData.l18_column + ' 5/12';
		} else if (n.indexOf('COLMD4') != -1 || n == 'MEDIUM4' || n == 'LARGE4' || n == 'SMALL4') {
			return stylistJsData.l18_column + ' 4/12';
		} else if (n.indexOf('COLMD3') != -1 || n == 'MEDIUM3' || n == 'LARGE3' || n == 'SMALL3') {
			return stylistJsData.l18_column + ' 3/12';
		} else if (n.indexOf('COLMD2') != -1 || n == 'MEDIUM2' || n == 'LARGE2' || n == 'SMALL2') {
			return stylistJsData.l18_column + ' 2/12';
		} else if (n.indexOf('COLMD1') != -1 || n == 'MEDIUM1' || n == 'LARGE1' || n == 'SMALL1') {
			return stylistJsData.l18_column + ' 1/12';
		} else if (n.indexOf('COLXS12') != -1) {
			return stylistJsData.l18_column + ' 12/12';
		} else if (n.indexOf('COLXS11') != -1) {
			return stylistJsData.l18_column + ' 11/12';
		} else if (n.indexOf('COLXS10') != -1) {
			return stylistJsData.l18_column + ' 10/12';
		} else if (n.indexOf('COLXS9') != -1) {
			return stylistJsData.l18_column + ' 9/12';
		} else if (n.indexOf('COLXS8') != -1) {
			return stylistJsData.l18_column + ' 8/12';
		} else if (n.indexOf('COLXS7') != -1) {
			return stylistJsData.l18_column + ' 7/12';
		} else if (n.indexOf('COLXS6') != -1) {
			return stylistJsData.l18_column + ' 6/12';
		} else if (n.indexOf('COLXS5') != -1) {
			return stylistJsData.l18_column + ' 5/12';
		} else if (n.indexOf('COLXS4') != -1) {
			return stylistJsData.l18_column + ' 4/12';
		} else if (n.indexOf('COLXS3') != -1) {
			return stylistJsData.l18_column + ' 3/12';
		} else if (n.indexOf('COLXS2') != -1) {
			return stylistJsData.l18_column + ' 2/12';
		} else if (n.indexOf('COLXS1') != -1) {
			return stylistJsData.l18_column + ' 1/12';
		} else if (n.indexOf('COLSM12') != -1) {
			return stylistJsData.l18_column + ' 12/12';
		} else if (n.indexOf('COLSM11') != -1) {
			return stylistJsData.l18_column + ' 11/12';
		} else if (n.indexOf('COLSM10') != -1) {
			return stylistJsData.l18_column + ' 10/12';
		} else if (n.indexOf('COLSM9') != -1) {
			return stylistJsData.l18_column + ' 9/12';
		} else if (n.indexOf('COLSM8') != -1) {
			return stylistJsData.l18_column + ' 8/12';
		} else if (n.indexOf('COLSM7') != -1) {
			return stylistJsData.l18_column + ' 7/12';
		} else if (n.indexOf('COLSM6') != -1) {
			return stylistJsData.l18_column + ' 6/12';
		} else if (n.indexOf('COLSM5') != -1) {
			return stylistJsData.l18_column + ' 5/12';
		} else if (n.indexOf('COLSM4') != -1) {
			return stylistJsData.l18_column + ' 4/12';
		} else if (n.indexOf('COLSM3') != -1) {
			return stylistJsData.l18_column + ' 3/12';
		} else if (n.indexOf('COLSM2') != -1) {
			return stylistJsData.l18_column + ' 2/12';
		} else if (n.indexOf('COLSM1') != -1) {
			return stylistJsData.l18_column + ' 1/12';
		} else if (n.indexOf('COLLG12') != -1) {
			return stylistJsData.l18_column + ' 12/12';
		} else if (n.indexOf('COLLG11') != -1) {
			return stylistJsData.l18_column + ' 11/12';
		} else if (n.indexOf('COLLG10') != -1) {
			return stylistJsData.l18_column + ' 10/12';
		} else if (n.indexOf('COLLG9') != -1) {
			return stylistJsData.l18_column + ' 9/12';
		} else if (n.indexOf('COLLG8') != -1) {
			return stylistJsData.l18_column + ' 8/12';
		} else if (n.indexOf('COLLG7') != -1) {
			return stylistJsData.l18_column + ' 7/12';
		} else if (n.indexOf('COLLG6') != -1) {
			return stylistJsData.l18_column + ' 6/12';
		} else if (n.indexOf('COLLG5') != -1) {
			return stylistJsData.l18_column + ' 5/12';
		} else if (n.indexOf('COLLG4') != -1) {
			return stylistJsData.l18_column + ' 4/12';
		} else if (n.indexOf('COLLG3') != -1) {
			return stylistJsData.l18_column + ' 3/12';
		} else if (n.indexOf('COLLG2') != -1) {
			return stylistJsData.l18_column + ' 2/12';
		} else if (n.indexOf('COLLG1') != -1) {
			return stylistJsData.l18_column + ' 1/12';
		} else if (n == 'POSTBODY') {
			return stylistJsData.l18_post_division;
		} else if (n == 'POST') {
			return stylistJsData.l18_post_division;
		} else if (n == 'CONTENT' || n == 'DEFAULTCONTENT') {
			return stylistJsData.l18_content_division;
		} else if (n == 'ENTRYTITLE') {
			return stylistJsData.l18_entry_title;
		} else if (n == 'ENTRYCONTENT') {
			return stylistJsData.l18_entry_content;
		} else if (n == 'ENTRYFOOTER') {
			return stylistJsData.l18_entry_footer;
		} else if (n == 'ENTRYHEADER') {
			return stylistJsData.l18_entry_header;
		} else if (n == 'ENTRYTIME') {
			return stylistJsData.l18_entry_time;
		} else if (n == 'POSTEDITLINK') {
			return stylistJsData.l18_post_edit_link;
		} else if (n == 'POSTTHUMBNAIL') {
			return stylistJsData.l18_post_thumbnail;
		} else if (n == 'THUMBNAIL') {
			return stylistJsData.l18_thumbnail;
		} else if (n.indexOf( "ATTACHMENT") >= 0) {
			return stylistJsData.l18_thumbnail_image;
		} else if (n == 'EDITLINK') {
			return stylistJsData.l18_edit_link;
		} else if (n == 'COMMENTSLINK') {
			return stylistJsData.l18_comments_link_division;
		} else if (n == 'SITEDESCRIPTION') {
			return stylistJsData.l18_site_description;
		} else if (n == 'POSTCLEAR' || n == 'POSTBREAK') {
			return stylistJsData.l18_post_break;
		}

		// Smart For ID
		if (get_name_by_classes( id) !== false) {
			return get_name_by_classes( id);
		}

		// Smart For Class
		if (get_name_by_classes(className) !== false) {
			return get_name_by_classes(className);
		}

		// If not have name found, use clear.
		if (n.indexOf( "CLEARFIX") != -1 || n.indexOf( "CLEARBOTH") != -1 || n == "CLEAR") {
			return stylistJsData.l18_clear;
		}

		// TAG NAME START
		if (a == 'P') {
			return stylistJsData.l18_paragraph;
		} else if (a == 'BR') {
			return stylistJsData.l18_line_break;
		} else if (a == 'HR') {
			return stylistJsData.l18_horizontal_rule;
		} else if (a == 'A') {
			return stylistJsData.l18_link;
		} else if (a == 'LI') {
			return stylistJsData.l18_list_item;
		} else if (a == 'UL') {
			return stylistJsData.l18_unorganized_list;
		} else if (a == 'OL') {
			return stylistJsData.l18_unorganized_list;
		} else if (a == 'IMG') {
			return stylistJsData.l18_image;
		} else if (a == 'B') {
			return stylistJsData.l18_bold_tag;
		} else if (a == 'I') {
			return stylistJsData.l18_italic_tag;
		} else if (a == 'STRONG') {
			return stylistJsData.l18_strong_tag;
		} else if (a == 'Em') {
			return stylistJsData.l18_italic_tag;
		} else if (a == 'BLOCKQUOTE') {
			return stylistJsData.l18_blockquote;
		} else if (a == 'PRE') {
			return stylistJsData.l18_preformatted;
		} else if (a == 'TABLE') {
			return stylistJsData.l18_table;
		} else if (a == 'TR') {
			return stylistJsData.l18_table_row;
		} else if (a == 'TD') {
			return stylistJsData.l18_table_data;
		} else if (a == 'HEADER' || n == 'HEADER') {
			return stylistJsData.l18_header_division;
		} else if (a == 'FOOTER' || n == 'FOOTER') {
			return stylistJsData.l18_footer_division;
		} else if (a == 'SECTION' || n == 'SECTION') {
			return stylistJsData.l18_section;
		} else if (a == 'FORM') {
			return stylistJsData.l18_form_division;
		} else if (a == 'BUTTON') {
			return stylistJsData.l18_button;
		} else if (a == 'CENTER') {
			return stylistJsData.l18_centred_block;
		} else if (a == 'DL') {
			return stylistJsData.l18_definition_list;
		} else if (a == 'DT') {
			return stylistJsData.l18_definition_term;
		} else if (a == 'DD') {
			return stylistJsData.l18_definition_description;
		} else if (a == 'H1') {
			return stylistJsData.l18_header + ' (' + stylistJsData.l18_level + ' 1)';
		} else if (a == 'H2') {
			return stylistJsData.l18_header + ' (' + stylistJsData.l18_level + ' 2)';
		} else if (a == 'H3') {
			return stylistJsData.l18_header + ' (' + stylistJsData.l18_level + ' 3)';
		} else if (a == 'H4') {
			return stylistJsData.l18_header + ' (' + stylistJsData.l18_level + ' 4)';
		} else if (a == 'H5') {
			return stylistJsData.l18_header + ' (' + stylistJsData.l18_level + ' 5)';
		} else if (a == 'H6') {
			return stylistJsData.l18_header + ' (' + stylistJsData.l18_level + ' 6)';
		} else if (a == 'SMALL') {
			return stylistJsData.l18_smaller_text;
		} else if (a == 'TEXTAREA') {
			return stylistJsData.l18_text_area;
		} else if (a == 'TBODY') {
			return stylistJsData.l18_body_of_table;
		} else if (a == 'THEAD') {
			return stylistJsData.l18_head_of_table;
		} else if (a == 'TFOOT') {
			return stylistJsData.l18_foot_of_table;
		} else if (a == 'U') {
			return stylistJsData.l18_underline_text;
		} else if (a == 'SPAN') {
			return stylistJsData.l18_span;
		} else if (a == 'Q') {
			return stylistJsData.l18_quotation;
		} else if (a == 'CITE') {
			return stylistJsData.l18_citation;
		} else if (a == 'CODE') {
			return stylistJsData.l18_expract_of_code;
		} else if (a == 'NAV' || n == 'NAVIGATION' || n == 'NAVIGATIONCONTENT') {
			return stylistJsData.l18_navigation;
		} else if (a == 'LABEL') {
			return stylistJsData.l18_label;
		} else if (a == 'TIME') {
			return stylistJsData.l18_time;
		} else if (a == 'DIV') {
			return stylistJsData.l18_division;
		} else if (a == 'CAPTION') {
			return stylistJsData.l18_caption_of_table;
		} else if (a == 'INPUT') {
			return stylistJsData.l18_input;
		} else {
			return a.toLowerCase();
		}

	}


	/* ---------------------------------------------------- */
	/* Reading nice class names                             */
	/* ---------------------------------------------------- */
	function get_name_by_classes(className) {

		if (typeof className == typeof undefined || className === false) {
			return false;
		}

		// RegExp
		var upperCase = new RegExp('[A-Z]');
		var numbers = new RegExp('[0-9]');
		var bottomRex = /_/;
		var topRex = /-/;

		// Only - or _
		if (bottomRex.test(className) && topRex.test(className)) {
			return false;
		}

		// max 3 -
		if (topRex.test(className)) {
			if (className.match(/-/g).length >= 3) {
				return false;
			}
		}

		// max 3 _
		if (bottomRex.test(className)) {
			if (className.match(/_/g).length >= 3) {
				return false;
			}
		}

		// Clean
		className = className.replace(/_/g, ' ').replace(/-/g, ' ');

		var classNames = get_classes_array(className);

		var i = 0;
		for ( i = 0; i < classNames.length; i++) {
			if (classNames[i].length < 4 || classNames[i].length > 12) {
				return false;
			}
		}

		// if all lowerCase
		// if not any number
		// if minimum 3 and max 20
		if (className.match(upperCase) || className.match(numbers) || className.length < 5 || className.length > 20) {
			return false;
		}

		if (letter_repeat(className)) {
			return false;
		}

		// For id.
		className = className.replace( "#", "");

		return title_case(className);

	}

	/* ---------------------------------------------------- */
	/* RGB To hex                                           */
	/* ---------------------------------------------------- */
	window.stlst_legacy.get_color = function(rgb) {
		return get_color(rgb);
	}
	function get_color(rgb) {
		if (typeof rgb !== 'undefined') {

			if (rgb.indexOf( "rgba") != -1){
				return rgb.replace(/\s+/g,"");
			}

			rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);

			return (rgb && rgb.length === 4) ? "#" + ( "0" + parseInt(rgb[1], 10).toString(16)).slice(-2) + ( "0" + parseInt(rgb[2], 10).toString(16)).slice(-2) + ( "0" + parseInt(rgb[3], 10).toString(16)).slice(-2) : '';

		} else {
			return '';
		}
	}

	/* ---------------------------------------------------- */
	/* Hex To RGB                                           */
	/* ---------------------------------------------------- */
	window.stlst_legacy.hex_to_rgb = function(hex) {
		return hex_to_rgb(hex);
	}
	function hex_to_rgb(hex){

		var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
		hex = hex.replace(shorthandRegex, function(m, r, g, b) {
			return r + r + g + g + b + b;
		});

		var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

		if (result ){
			return 'rgb('+parseInt(result[1], 16)+','+parseInt(result[2], 16)+','+parseInt(result[3], 16)+')';
		}else{
			return null;
		}

	}

	// Long to short sorted for replacement.
	window.plugin_classes_list_sorted = window.plugin_classes_list.split( "|").sort(function(a, b){return b.length - a.length;}).join( "|");


	/* ---------------------------------------------------- */
	/* Clean all classes which added by the plugin.         */
	/* ---------------------------------------------------- */
	window.stlst_legacy.class_cleaner = function( data) {
		return class_cleaner( data);
	}

	function class_cleaner( data) {

		if ( isUndefined( data)) {
			return '';
		}

		return data.replace(new RegExp(window.plugin_classes_list_sorted,"gi"), '');

	}

	/* ---------------------------------------------------- */
	/* Adds class to body on a selector string.             */
	/* ---------------------------------------------------- */
	function add_class_to_body(selector, prefix) {

		var selectorOrginal = selector;

		// Basic
		if (selector == 'body') {
			selector = selector + "." + prefix;
		}

		// If class added, return.
		if (selector.indexOf( "body." + prefix) != -1) {
			return selector;
		}

		var firstHTML = '';
		if (get_selector_array(selector).length > 0) {

			var firstSelector = $.trim(get_selector_array(selector)[0]);

			if (firstSelector.toLowerCase() == 'html') {
				firstHTML = firstSelector;
			}

			if (window.Stylist.iDocJQ.find(firstSelector).length > 0) {
				if (firstSelector.indexOf( "#") != -1) {
					if (window.Stylist.iDocJQ.find(firstSelector)[0].nodeName == 'HTML') {
						firstHTML = firstSelector;
					}
				}

				if (firstSelector.indexOf( ".") != -1) {
					if (window.Stylist.iDocJQ.find(firstSelector)[0].nodeName == 'HTML') {
						firstHTML = firstSelector;
					}
				}
			}

			if (firstHTML != '') {
				selector = get_selector_array(selector)[1];
			}

		}

		// find body tag
		selector = selector.replace(/\bbody\./g, 'body.' + prefix + ".");
		selector = selector.replace(' body ', ' body.' + prefix + " ");

		// If class added, return.
		if (selector.indexOf( "body." + prefix) != -1) {
			if (firstHTML != '') {
				selector = firstHTML + " " + selector;
			}

			return selector;
		}

		// Get all body classes.
		if ( iframeBody.attr( "class") !== undefined && iframeBody.attr( "class") !== null) {

			// Find element
			var element = window.Stylist.iDocJQ.find(selectorOrginal);

			if (element.length > 0){

				if (element[0].nodeName == 'BODY'){

					var bodyClasses = get_classes_array( iframeBody.attr( "class"));

					// Adding to next to classes.
					for (var i = 0; i < bodyClasses.length; i++) {
						selector = selector.replace( "." + bodyClasses[i] + " ", "." + bodyClasses[i] + "." + prefix + " ");

						if (get_selector_array(selector).length == 1 && bodyClasses[i] == selector.replace( ".", "")) {
							selector = selector + "." + prefix;
						}

					}

				}

			}

		}

		// If class added, return.
		if (selector.indexOf( "." + prefix + " ") != -1) {
			if (firstHTML != '') {
				selector = firstHTML + " " + selector;
			}

			return selector;
		}

		// If class added, return.
		if (selector.indexOf( "." + prefix) != -1 && get_selector_array(selector).length == 1) {
			if (firstHTML != '') {
				selector = firstHTML + " " + selector;
			}

			return selector;
		}

		// Get body id.
		var bodyID = iframeBody.attr( "id");

		selector = selector.replace( "#" + bodyID + " ", "#" + bodyID + "." + prefix + " ");

		// If class added, return.
		if (selector.indexOf( "." + prefix + " ") != -1) {
			if (firstHTML != '') {
				selector = firstHTML + " " + selector;
			}

			return selector;
		}

		selector = "YPIREFIX" + selector;
		selector = selector.replace(/YPIREFIXbody /g, 'body.' + prefix + " ");
		selector = selector.replace( "YPIREFIX", "");

		// If class added, return.
		if (selector.indexOf( "body." + prefix + " ") != -1) {
			if (firstHTML != '') {
				selector = firstHTML + " " + selector;
			}

			return selector;
		}

		if (selector.indexOf( " body ") == -1 || selector.indexOf( " body.") == -1) {
			selector = "body." + prefix + " " + selector;
		}

		if (firstHTML != '') {
			selector = firstHTML + " " + selector;
		}

		return selector;

	}

	/* ---------------------------------------------------- */
	/* FullScreen Event.                                    */
	/* ---------------------------------------------------- */
	jQuery( document ).bind('webkitfullscreenchange mozfullscreenchange fullscreenchange', function( e ) {
		var state = document.fullScreen || document.mozFullScreen || document.webkitIsFullScreen;
		var event = state ? 'FullscreenOn' : 'FullscreenOff';

		if (event == 'FullscreenOff') {
			body.removeClass( "stlst-fullscreen");
		}

		if (event == 'FullscreenOn') {
			body.addClass( "stlst-fullscreen");
		}

	});

	/* ---------------------------------------------------- */
	/* Disable history shift mouse.                         */
	/* ---------------------------------------------------- */
	mainDocument.keydown(function( e ){

		var tag = e.target.tagName.toLowerCase();

		if (tag != 'input' && tag != 'textarea'){

			if (e.shiftKey && (e.which == '61' || e.which == '107' || e.which == '173' || e.which == '109'  || e.which == '187'  || e.which == '189')){
					e.preventDefault();
			}

		}

	});

	var event = new Event('readyToStart');

	// Listen for the event.
	// elem.addEventListener('build', function ( e ) { //... }, false);

	// Dispatch the event.
	document.dispatchEvent(event );


	}; // Stylist main function.

}(jQuery ));

// ============================================================

(function ($) {

	// All plugin element list
	window.plugin_classes_list = 'stlst-styles-area|stlst-animating|stlst-animate-data|stlst-scene-1|stlst-sharp-selector-mode-active|stlst-scene-2|stlst-scene-3|stlst-scene-4|stlst-scene-5|stlst-scene-6|stlst-anim-creator|data-anim-scene|stlst-anim-link-toggle|stlst-animate-test-playing|ui-draggable-handle|stlst-css-data-trigger|stlst-stylist-demo-mode|stlst-stylist-loaded|stlst-element-resized|resize-time-delay|stlst-selected-handle|stlst-parallax-disabled|stlst_onscreen|stlst_hover|stlst_click|stlst_focus|stlst-recent-hover-element|stlst-selected-others|stlst-multiple-selected|stlst-demo-link|stlst-live-editor-link|stlst-stylist|stlst-content-selected|stlst-selected-has-transform|stlst-hide-borders-now|ui-draggable|stlst-target-active|stlst-stylist-disable-links|stlst-closed|stlst-responsive-device-mode|stlst-metric-disable|stlst-css-editor-active|wtfv|stlst-clean-look|stlst-has-transform|stlst-will-selected|stlst-selected|stlst-fullscreen-editor|stlst-element-resizing|stlst-element-resizing-width-left|stlst-element-resizing-width-right|stlst-element-resizing-height-top|stlst-element-resizing-height-bottom|context-menu-active|stlst-selectors-hide|stlst-contextmenuopen|stlst-control-key-down|stlst-selected-others-multiable-box';
	// Any visible element.
	window.simple_not_selector = 'head, script, style, [class^="stlst-"], [class*=" stylist-"], link, meta, title, noscript, svg, canvas';
	// Basic simple.
	window.basic_not_selector = '*:not(script ):not(style):not(link):not(meta):not(title):not(noscript ):not(head):not(circle):not(rect ):not(polygon):not( defs):not(linearGradient ):not(stop):not(ellipse):not(text ):not(canvas):not(line):not(polyline):not(path):not(g):not(tspan)';

	// Variable
	window.loadStatus = false;

	// Document ready.
	document.addEventListener( "DOMContentLoaded", function() {
		// Load iframe.
		var s = jQuery( "#stylist_iframe").attr( "data-href");
		jQuery( "#stylist_iframe").attr( "src", s);

		const iframe = document.getElementById('stylist_iframe');

		// Frame ready
		iframe.addEventListener('load', function () {
			var iframe = jQuery( jQuery('#stylist_iframe').contents().get(0) );
			var iframeBody = window.Stylist.iDocJQ.find( "body");
			var body = jQuery( document.body ).add( iframeBody );

			// Loading Styles
			var styles = [];
			/*
				"<?php echo esc_url( includes_url( 'css/dashicons.min.css' , __FILE__ ) ); ?>",
				"<?php $prtcl = is_ssl() ? 'https' : 'http';
					echo $prtcl; ?>://fonts.googleapis.com/css?family=Open+Sans:400,300,600&subset=latin,latin-ext",

				<!-- + -->
				"<?php echo esc_url( plugins_url( 'css/stylist.css?ver=' . Stylist_Core::get_version() . '' , __FILE__ ) ); ?>"
				];
				*/

			// Loading.
			for (var i = 0; i < styles.length; i++) {
				stlst_load_css(styles[i]);
			}

			//setup object to store results of AJAX requests
			var responses = {};

			//create function that evaluates each response in order
			function stlst_eval_scripts() {

				// New List
				var newLoadList = Array();

				// Getting all CSS Stylesheets from iframe
				window.definedStyleData = '';
				window.Stylist.iDocJQ.find( "link[rel='stylesheet'][href]").each(function () {

					// Get href
					var href = jQuery(this).attr( "href");

					// check and add
					// Filter out some commong CSS that makes no sense
					// for our product.
					// @todo: why not to use OCSS?
					if (href.indexOf( "stylist-stylist") == -1 &&
						href.indexOf( "animate") == -1 &&
						href.indexOf( "webkit") == -1 &&
						href.indexOf( "animation") == -1 &&
						href.indexOf( "keyframe") == -1 &&
						href.indexOf( "font") == -1 &&
						href.indexOf( "icon") == -1 &&
						href.indexOf( "googleapis.com") == -1 &&
						href.indexOf( "print") == -1 &&
						href.indexOf( "reset") == -1 &&

						href.indexOf( "preloader") == -1 &&
						href.indexOf( "fancybox") == -1 &&
						href.indexOf( "colorbox") == -1 &&
						href.indexOf( "prettyPhoto") == -1 &&
						href.indexOf( "popup") == -1 &&

						href.indexOf( "player") == -1 &&
						href.indexOf( "video") == -1 &&
						href.indexOf( "audio") == -1 &&

						href != 'ie' &&
						href.indexOf( "ie6") == -1 &&
						href.indexOf( "ie7") == -1 &&
						href.indexOf( "ie8") == -1 &&
						href.indexOf( "ie9") == -1 &&
						href.indexOf( "ie10") == -1 &&
						href.indexOf( "ie11") == -1 &&
						href.indexOf( "jquery") == -1 &&

						href.indexOf( "skin") == -1 &&
						href.indexOf( "scheme") == -1 &&

						href.indexOf( "setting") == -1 &&
						href.indexOf( "admin") == -1 &&

						// page builders
						href.indexOf( "visualcomposer-assets") == -1 &&
						href.indexOf( "elementor/css") == -1 &&
						href.indexOf( "elementor/css") == -1 &&
						href.indexOf( "page-builder-sandwich") == -1 &&
						href.indexOf( "/Divi/") == -1 &&
						href.indexOf( "live-composer-page-builder") == -1 &&
						newLoadList.length <= 10) {

						// Add
						newLoadList.push(href);


					}

				});


				// There not have css stylesheets to load?, so start editor.
				if (newLoadList.length == 0) {
					stlst_start_editor();
				}


				// Loading all stylesheets and Open Editor.
				// Scripts get all the scripts from iframe
				// and put them all into single variable window.definedStyleData
				var load_style_loop = function ( i) {




					if ( i < newLoadList.length) {

						// Load styles
						jQuery.get({
							url: newLoadList[i],
							timeout: 2000,
							cache: true
						}).always(function ( data) {

							// Update
							if ( jQuery.type( data) === "string" ) {
								window.definedStyleData += stlst_minimize_css( data);
							}

							// If last
							if ( i + 1 == newLoadList.length) {

								stlst_start_editor();

							}

							// Repait
							load_style_loop( i + 1);

						});

					}

				};

				// Go
				load_style_loop(0);

			}


			// Minimize CSS before load
			function stlst_minimize_css( data) {

				// Clean.
				data = data.replace(/(\r\n|\n|\r)/g, "").replace(/\t/g, '');

				// Don't care rules in comment.
				data = data.replace(/\/\*(.*?)\*\//g, "");

				// clean.
				data = data.replace(/\}\s+\}/g, '}}').replace(/\s+\{/g, '{');

				// clean.
				data = data.replace(/\s+\}/g, '}').replace(/\{\s+/g, '{');
				data = data.replace(/[\u2018\u2019\u201A\u201B\u2032\u2035\u201C\u201D]/g, '');

				// data
				return data;

			}

			// Stop load and call editor function.
			function stlst_start_editor() {
				// Set true.
				window.loadStatus = true;

				// Okay. Load it.
				stylist_main();
			}

			stlst_eval_scripts();

		}, true);

	});

	// CSS Loader
	function stlst_load_css(link) {
		jQuery('<link>').appendTo('head').attr({ type: 'text/css', rel: 'stylesheet', href: link });
	}

})(jQuery );

// ============================================================

/*
	A Helper javascript function for Stylist Editor;
	CSS Animation trigger and Custom CSS Engine.

	Original GPL licensed code by WaspThemes.
*/
(function ($) {
	"use strict";

	// Reverse prototype
	String.prototype.reverse = function () {
		return this.split('').reverse().join('');
	};

	// Replace last reverse item
	String.prototype.replaceLast = function (what, replacement ) {
		return this.reverse().replace(new RegExp(what.reverse()), replacement.reverse()).reverse();
	};


	// Check if this is editor page.
	function is_stylist() {

		if (jQuery( "body").hasClass( "stlst-stylist")) {
			return true;
		} else {
			return false;
		}
	}

	window.stlst_legacy.get_minimized_css = function( data, media ) {
		return get_minimized_css( data, media );
	}

	// Minify Output CSS
	function get_minimized_css( data, media) {

		// Clean.
		data = data.replace(/(\r\n|\n|\r)/g, "").replace(/\t/g, '');

		// Don't care rules in comment.
		data = data.replace(/\/\*(.*?)\*\//g, "");

		// clean.
		data = data.replace(/\}\s+\}/g, '}}').replace(/\s+\{/g, '{');

		// clean.
		data = data.replace(/\s+\}/g, '}').replace(/\{\s+/g, '{');
		data = filter_bad_queries( data);

		// Don't care rules in media query
		if (media === true) {
			data = data.replace(/@media(.*?)\}\}/g, '').replace(/@?([a-zA-Z0-9_-]+)?keyframes(.*?)\}\}/g, '').replace(/@font-face(.*?)\}\}/g, '').replace(/@import(.*?)\;/g, '').replace(/@charset(.*?)\;/g, '');
		}

		// data
		return data;

	}

	// Filtering bad queries
	function filter_bad_queries( data) {
		return data.replace(/[\u2018\u2019\u201A\u201B\u2032\u2035\u201C\u201D]/g, '');
	}

	// Delete the empty array items
	function array_cleaner(actual) {

		var uniqueArray = [];
		$.each(actual, function ( i, el) {
			if ($.inArray(el, uniqueArray ) === -1) uniqueArray.push(el);
		});

		return uniqueArray;
	}

	// Loads CSS once.
	window.cacheCSS = false;


	/* ---------------------------------------------------- */
	/* CSS Engine Function		                            */
	/* ---------------------------------------------------- */
	function cssEngine(rule, scriptMarkup, defaults, data) {


		// Minify data
		data = get_minimized_css( data, true);

		// get all matches
		var matches = data.match(new RegExp(rule + "(\s+)?:", "gi"));

		if (!matches) {
			return false;
		}

		// Check if has matches
		if (matches.length > 0) {

			// Each all matches
			for (var ix = 0; ix < matches.length; ix++) {

				var output = scriptMarkup;
				var selector = '';

				// ruleData : Selector, rule, value
				var ruleData = data.match(new RegExp('}(.*){(.*)' + rule + '(\s+)?:(.*);'));

				// Delete proccessed rule.
				data = data.replaceLast(rule + ":", "processed-rule:");

				// check
				if (ruleData == null) {
					return true;
				}

				// Get Selector
				selector = $.trim(ruleData[1]);

				// Clean selector
				if (selector.indexOf( "}") != -1) {
					selector = $.trim(ruleData[1].split( "}")[ruleData[1].split( "}").length - 1]);
				}

				// Check selector. Support just nth-child. ( don't support ex: hover, focus etc.)
				if (selector.indexOf( ":") >= 0 && selector.indexOf( ":nth") == -1) {
					return true;
				}

				// Getting rule value
				var value = $.trim(ruleData[4].split( ";")[0]);


				// Getting other properties for current selector.
				// Need for getting params of the custom CSS rule.
				var otherProperties = ruleData[4];

				// Clean
				if (ruleData[4].indexOf( "}") != -1) {
					otherProperties = ruleData[4].split( "}")[0];
				}

				// Getting all rules of selector
				var allRules = (ruleData[2] + otherProperties).replace(value, "").split( ";");

				var allRulesArray = [];
				var item, ruleName;
				for (var i = 0; i < allRules.length; i++) {

					// loop item
					item = $.trim(allRules[i]);

					// Adds if valid
					if ( item != null && item != '' && item != ',' && item != undefined) {

						// Push.
						allRulesArray.push(allRules[i]);

					}


				}

				// All other properties in an array.
				allRules = allRulesArray;


				// Getting Default values
				if ( defaults != undefined) {

					// Each properties
					for (var e = 0; e < Object.keys( defaults).length; e++) {

						// Checks
						if (allRules.join( ",").indexOf(Object.keys( defaults)[e] + ":") == -1) {

							// Update Keys as rule name.
							allRules.push(Object.keys( defaults)[e] + ":" + defaults[Object.keys( defaults)[e]]);

						}

					}

				}



				// Replace default rule names to the rules value in script output
				for (var s = 0; s < allRules.length; s++) {

					// Getting Rule Name
					ruleName = allRules[s].replace(/\"\)*$/, '').split( ":")[0];

					// Replace with rule value
					output = output.replace(ruleName, allRules[s].replace(/\"\)*$/, '').split( ":")[1]);

				}



				// USE: $selector  ->  rule address. (string)
				output = output.replace(/\$selector/g, selector);

				// USE: $value  ->  rule value. (string)
				output = output.replace(/\$value/g, value);

				// USE: $rule  ->  rule. (string)
				if (rule.indexOf( "jquery-") != -1) {
					output = output.replace(/\$rule/g, rule.split( "-")[1]);
				} else {
					output = output.replace(/\$rule/g, rule);
				}

				// USE: $self  ->  rule address. (object )
				output = output.replace(/\$self/g, "jQuery('" + selector + "')");

				// Replacing..
				output = output.replace(/undefined/g, "0");

				// If main rule value is active.
				if (value != 'none' && value != '0') {

					// If is the editor page
					if ( is_stylist()) {

						var ifrm = jQuery( "#stylist_iframe")[0], iwind = ifrm.contentWindow;

						iwind.eval( "(function($){" + output + "}(jQuery ));");

					} else {

						// eval scripts for website.
						eval(output );

					}
				}
			}
		}
	}

	/* ---------------------------------------------------- */
	/* Setup   							                    */
	/* ---------------------------------------------------- */
	if ( ! is_stylist() ) {
		// Checks if has external CSS File
		if (jQuery( "link#stlst-custom-css").length > 0) {

			// Define
			window.externalCSS = true;
			window.externalData = true;

			// Getting URL
			var href = jQuery( "link#stlst-custom-css").attr( "href");

			// Load the CSS output from custom CSS file
			$.when($.get(href)).done(function ( data) {
				// Cache custom CSS Data
				window.externalData = data;
			});

		} else {
			// Define
			window.externalCSS = false;
		}
	}

}(jQuery ));
