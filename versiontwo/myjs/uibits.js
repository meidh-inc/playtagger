$(function() {
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    $( "#dialog-tag" ).dialog({
	autoOpen: false,
	position: ["center",300],
	width: 460,
	resizable: false,
	modal: true,
	closeOnEscape: false,
    });
    $( "#tagpop" )
	.button()
	.click(function() {
	    $( "#dialog-tag" ).dialog( "open" );
	});
    $( "#tagdone" )
	.button()
	.click(function() {
	    $( "#dialog-tag" ).dialog( "close" );
	});
    $( "#tagcancel" )
	.button()
	.click(function() {
	    $( "#dialog-tag" ).dialog( "close" );
	});
    $( "#dialog-addp" ).dialog({
	autoOpen: false,
	position: ["center",100],
	width: 460,
	resizable: false,
	modal: true,
	closeOnEscape: false,
    });
    $( "#addppop" )
	.button()
	.click(function() {
	    $( "#dialog-addp" ).dialog( "open" );
	});
    $( "#addpdone" )
	.button()
	.click(function() {
	    $( "#dialog-addp" ).dialog( "close" );
	});
    $( "#addpcancel" )
	.button()
	.click(function() {
	    $( "#dialog-addp" ).dialog( "close" );
	});
    $( "#addgamepop" )
	.button()
	.click(function() {
	    $( "#dialog-addgame" ).dialog( "open" );
	});
    $( "#addgamecancel" )
	.button()
	.click(function() {
	    $( "#dialog-addgame" ).dialog( "close" );
	});
    $( "#gday" ).datepicker({
	changeMonth: true,
	changeYear: true,
	numberOfMonths: 1,
	dateFormat: "yy-mm-dd",
	yearRange: '1980:2015'
    });
    $( "#addteampop" )
	.button()
	.click(function() {
	    $( "#dialog-addteam" ).dialog( "open" );
	});
    $( "#addteamcancel" )
	.button()
	.click(function() {
	    $( "#dialog-addteam" ).dialog( "close" );
	});
    $( "#addcpop" )
	.button()
	.click(function() {
	    $( "#dialog-addc" ).dialog( "open" );
	});
    $( "#addccancel" )
	.button()
	.click(function() {
	    $( "#dialog-addc" ).dialog( "close" );
	});
    $( "#dialog-pay" ).dialog({
	autoOpen: false,
	position: ["center",100],
	width: 750,
	resizable: false,
	modal: true,
    });
    $( "#paypop" )
	.button()
	.click(function() {
	    $( "#dialog-pay" ).dialog( "open" );
	});
    $( "#paypop2" )
	.button()
	.click(function() {
	    $( "#dialog-pay" ).dialog( "open" );
	});
    $( "#paycancel" )
	.button()
	.click(function() {
	    $( "#dialog-pay" ).dialog( "close" );
	});
    $( "#paysub" )
	.button()
	.click(function() {
	    //$( "#dialog-pay" ).dialog( "close" );
	});
    $( "#dialog-help" ).dialog({
	autoOpen: false,
	position: ["center",100],
	width: 750,
	resizable: false,
	modal: true,
    });
    $( "#helppop" )
	.button()
	.click(function() {
	    $( "#dialog-help" ).dialog( "open" );
	});
    $( "#helpclose" )
	.button()
	.click(function() {
	    $( "#dialog-help" ).dialog( "close" );
	});
    $( "#maxday" ).datepicker({
	defaultDate: "+1w",
	changeMonth: true,
	changeYear: true,
	numberOfMonths: 1,
	dateFormat: "yy-mm-dd",
    });
    $( "#minday" ).datepicker({
	defaultDate: "+1w",
	changeMonth: true,
	changeYear: true,
	numberOfMonths: 1,
	dateFormat: "yy-mm-dd",
    });
    $( "button", ".logout" ).button({
        icons: {
            primary: "ui-icon-circle-close"
        }
    });
    $( "button", ".profile" ).button({
        icons: {
            primary: "ui-icon-person"
        }
    });
    $( "button", ".flag" ).button({
        icons: {
            primary: "ui-icon-flag"
        }
    });
});
$(function() {
    $( "a" )
    .button()
});

(function( $ ) {
$.widget( "ui.combobox", {
_create: function() {
var input,
that = this,
select = this.element.hide(),
selected = select.children( ":selected" ),
value = selected.val() ? selected.text() : "",
wrapper = this.wrapper = $( "<span>" )
.addClass( "ui-combobox" )
.insertAfter( select );
function removeIfInvalid(element) {
var value = $( element ).val(),
matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( value ) + "$", "i" ),
valid = false;
select.children( "option" ).each(function() {
if ( $( this ).text().match( matcher ) ) {
this.selected = valid = true;
return false;
}
});
if ( !valid ) {
// remove invalid value, as it didn't match anything
$( element )
.val( "" )
.attr( "title", value + " didn't match any item" )
.tooltip( "open" );
select.val( "" );
setTimeout(function() {
input.tooltip( "close" ).attr( "title", "" );
}, 2500 );
input.data( "autocomplete" ).term = "";
return false;
}
}
input = $( "<input>" )
.appendTo( wrapper )
.val( value )
.attr( "title", "" )
.addClass( "ui-state-default ui-combobox-input" )
.autocomplete({
delay: 0,
minLength: 0,
source: function( request, response ) {
var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
response( select.children( "option" ).map(function() {
var text = $( this ).text();
if ( this.value && ( !request.term || matcher.test(text) ) )
return {
label: text.replace(
new RegExp(
"(?![^&;]+;)(?!<[^<>]*)(" +
$.ui.autocomplete.escapeRegex(request.term) +
")(?![^<>]*>)(?![^&;]+;)", "gi"
), "<strong>$1</strong>" ),
value: text,
option: this
};
}) );
},
select: function( event, ui ) {
ui.item.option.selected = true;
that._trigger( "selected", event, {
item: ui.item.option
});
},
change: function( event, ui ) {
if ( !ui.item )
return removeIfInvalid( this );
}
})
.addClass( "ui-widget ui-widget-content ui-corner-left" );
input.data( "autocomplete" )._renderItem = function( ul, item ) {
return $( "<li>" )
.data( "item.autocomplete", item )
.append( "<a>" + item.label + "</a>" )
.appendTo( ul );
};
$( "<a>" )
.attr( "tabIndex", -1 )
.attr( "title", "List all players" )
.tooltip()
.appendTo( wrapper )
.button({
icons: {
primary: "ui-icon-triangle-1-s"
},
text: false
})
.removeClass( "ui-corner-all" )
.addClass( "ui-corner-right ui-combobox-toggle" )
.click(function() {
// close if already visible
if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
input.autocomplete( "close" );
removeIfInvalid( input );
return;
}
// work around a bug (likely same cause as #5265)
$( this ).blur();
// pass empty string as value to search for, displaying all results
input.autocomplete( "search", "" );
input.focus();
});
input
.tooltip({
position: {
of: this.button
},
tooltipClass: "ui-state-highlight"
});
},
destroy: function() {
this.wrapper.remove();
this.element.show();
$.Widget.prototype.destroy.call( this );
}
});
})( jQuery );
$(function() {
$( "#tagPlayers" ).combobox();
$( "#toggle" ).click(function() {
$( "#tagPlayers" ).toggle();
});
});