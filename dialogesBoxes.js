inner_h = "";
  $( function() {
    var dialog, dialog2, form, form2,

      // From http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
      emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
      nodeId = $("#nodeId");
      elementId = $("#elementId");
      innerHtml = $("#innerHtml");
      parentId = $("#parentId");

    function updateTips( t ) {
      tips
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tips.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }

    function checkLength( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTips( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }

    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "ui-state-error" );
        updateTips( n );
        return false;
      } else {
        return true;
      }
    }


    function updateNode(){
	var valid = true;

	//validate
	if(valid){
		var args = "update_node=yes&node_id="+nodeId.val()+"&element_id="+elementId.val()+"&parent_id="+parentId.val()+"&inner_html="+encodeURI(innerHtml.val());
		postAjax("ajax_operations.php", args, function(resp){alert(resp);location.reload()});
	}
	return valid;
    }

    function updateElementCss(){
	var valid = true;

	var elem_name = $("#el_name"),
	wep = $("#el_css_web_page_id"),
	css = $("#el_css");

	//validate
	if(valid){
		var args = "update_element_css=yes&e_name="+elem_name.val()+"&wep="+wep.val()+"&css="+css.val();
		postAjax("ajax_operations.php", args, function(resp){alert(resp);location.reload()});
	}
	return valid;
    }



    dialog = $("#u-node-dialog-form").dialog({
      autoOpen: false,
      height: 400,
      width: 350,
      modal: true,
      buttons: {
        "Update node": updateNode,
        Cancel: function() {
          dialog.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
        //allFields.removeClass( "ui-state-error" );
      }
    });

    //editing element-css post
    dialog2 = $("#alter_element_css").dialog({
      autoOpen: false,
      height: 400,
      width: 350,
      modal: true,
      buttons: {
        "Update element css": updateElementCss,
        Cancel: function() {
          dialog.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
        //allFields.removeClass( "ui-state-error" );
      }
    });
 
    form = dialog.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      addUser();
    });

    form2 = dialog2.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      console.log("edit elem css");
    });

 
    $( "#create-user" ).button().on( "click", function() {
      dialog.dialog( "open" );
    });

    $( ".node-contact" ).button().on( "click", function(event) {//contact was because Treant had a standard way of making link with a 'contact'
      event.preventDefault();
      //console.log(event);
      var parent = event.target.parentNode;

      console.log(parent);

      inner_h = parent.getElementsByClassName("node-data_innerhtml")[0].innerText;
      console.log("innerHtml");
      console.log(inner_h);
      console.log(innerHtml);
      var element_id = parent.getElementsByClassName("node-data_elementid")[0].innerHTML;

      var parent_id = parent.getElementsByClassName("node-data_parentid")[0].innerHTML;

      var is_empty_tag = parent.getElementsByClassName("node-data_isemptytag")[0].innerHTML;

      var is_empty = is_empty_tag>0;

      var empty = is_empty ? "yes" : "no";

      var myId = event.target.parentNode.id;
      //fill form
      $("#nodeId").val(getAfter_(myId));
      $("#elementId").val(element_id);
      $("#parentId").val(parent_id);
      $("#innerHtml").val(inner_h);
      $("#isEmptyTag").val(empty);
      dialog.dialog( "open" );
    });

    $(".editElemCss").on("click", function(event){
	event.preventDefault();
	var parent = event.target.parentNode;
	console.log(parent);

	var id = getAfter_(parent.id);
	var eleme = parent.dataset.element;
	var css = parent.dataset.css;
	var wep = parent.dataset.wep;

	$("#el_css_web_page_id").val(wep);
	$("#el_name").val(eleme);
	$("#el_css").val(css);
	dialog2.dialog("open");
    });

  } );

