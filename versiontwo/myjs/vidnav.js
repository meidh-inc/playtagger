$(function() {
    $( "#addMenu" ).menu({
	position: { my: "left top", at: "left bottom" },
	icons: { submenu: "ui-icon-triangle-1-s" }
    });
    $( "#addMenu" ).mouseleave( function(){
	$( "#addMenu" ).menu( "collapseAll", null, true );
    });
    
    $( "#searchMenu" ).menu({
	position: { my: "left top", at: "left bottom" },
	icons: { submenu: "ui-icon-triangle-1-s" }
    });
    $( "#searchMenu" ).mouseleave( function(){
	$( "#searchMenu" ).menu( "collapseAll", null, true );
    });
    
    $( "#profileMenu" ).menu({
	position: { my: "left top", at: "left bottom" },
	icons: { submenu: "ui-icon-triangle-1-s" }
    });
    $( "#profileMenu" ).mouseleave( function(){
	$( "#profileMenu" ).menu( "collapseAll", null, true );
    });
    
    $( "#helpMenu" ).menu({
	position: { my: "left top", at: "left bottom" },
	icons: { submenu: "ui-icon-triangle-1-s" }
    });
    $( "#helpMenu" ).mouseleave( function(){
	$( "#helpMenu" ).menu( "collapseAll", null, true );
    });
    
    $( "#logoutMenu" ).menu({
	position: { my: "left top", at: "left bottom" },
	icons: { submenu: "ui-icon-triangle-1-s" }
    });
    $( "#logoutMenu" ).mouseleave( function(){
	$( "#logoutMenu" ).menu( "collapseAll", null, true );
    });
});