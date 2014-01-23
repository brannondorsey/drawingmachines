

// $.fn.autoComplete = function(url) {

// 	return this.each(function (i, el) {
// 		var el = $(el);
// 		var fieldset = el.closest("fieldset");
// 		var hidden = fieldset.find(".autocomplete-output");
// 		var orgs = fieldset.find(".orgs");
// 		var content;

// 		//ul.insertAfter(el);
// 		listenForSelection();

// 		function addToHidden() {
// 			fieldset.find(".org .organization").each(function(i) {
// 				var val = hidden.attr("value");
// 				if(i != 0) {
// 					hidden.attr("value", val + ", " + $(this).text());
// 				} else {
// 					hidden.attr("value", $(this).text());
// 				}
// 			});
// 		}

// 		function listenForRemoval() {
// 			fieldset.find(".remove").click(function(e) {
// 				e.preventDefault();
// 				var parent = $(this).closest(".org");
// 				parent.remove();
// 				addToHidden();
// 			});
// 			addToHidden();
// 		}

// 		// function addOrg(org) {
// 		// 	var wrapper = $("<span />").addClass("org");
// 		// 	var title = $("<a />").attr("href", "#").addClass("organization").text(org);
// 		// 	var close = $("<a />").attr("href", "#").addClass("remove").text("×");

// 		// 	title.appendTo(wrapper);
// 		// 	close.appendTo(wrapper);
// 		// 	wrapper.appendTo(orgs);

// 		// 	closeAutocomplete();
// 		// 	addToHidden();
// 		// 	listenForRemoval();
// 		// 	el.val("");
// 		// }

// 		function listenForSelection() {
// 			fieldset.find(".autocomplete-results a").click(function(e) {
// 				e.preventDefault();
// 				//addOrg($(this).text());
// 				closeAutocomplete();
// 				el.val("");
// 			})
// 		}

// 		function listenForHover() {
// 			$(".autocomplete-results li").on("mouseenter", function(e) {
// 				$(this).closest(".autocomplete-results").find(".selected").removeClass("selected");
// 				$(this).addClass("selected");
// 			})
// 		}

// 		function closeAutocomplete() {
// 			if(fieldset.find(".autocomplete-results")) {
// 				fieldset.find(".autocomplete-results").remove();
// 			}
// 			$(".return-prompt").removeClass("active");
// 		}

// 		function insertData(data) {
// 			fieldset.find(".autocomplete-results").remove();
// 			var ul = $("<ul />").addClass("autocomplete-results");
// 			for(var i=0; i < data.length; i+=1) {
// 				var result = data[i];
// 				var li = $("<li />");
// 				var a = $("<a />").attr("href", "#").text(result);
// 				a.appendTo(li);
// 				li.appendTo(ul);
// 			}
// 		}

// 		function arrowKeyActions(key) {
// 			if(key === 40) {
// 				if(fieldset.find(".autocomplete-results li.selected").length != 0) {
// 					fieldset.find(".autocomplete-results li.selected").removeClass("selected").next("li").addClass("selected");
// 					el.val(fieldset.find(".selected a").text());
// 				} else {
// 					fieldset.find(".autocomplete-results li").eq(0).addClass("selected");
// 					el.val(fieldset.find(".selected a").text());
// 				}
// 			} else if (key === 38) {
// 				if(fieldset.find(".autocomplete-results li.selected").length != 0) {
// 					fieldset.find(".autocomplete-results li.selected").removeClass("selected").prev("li").addClass("selected");
// 					el.val(fieldset.find(".selected a").text());
// 				}
// 			} else if (key === 13) {
// 				console.log("this worked");
// 				if (fieldset.find(".autocomplete-results li.selected").length != 0) {
// 					fieldset.find(".autocomplete-results li.selected a").click();
// 				} else {
// 					addOrg(el.val());
// 				}
// 			}

// 			if(fieldset.find(".selected").length === 0) {
// 				el.val(content);
// 				if(key === 13) {
// 					el.val("");
// 				}
// 			}
// 		}

// 		function initialize() {

// 			listenForRemoval();

// 			el.on("keyup", function(e) {

// 				var _this = $(this);

// 				if (e.keyCode === 38 || e.keyCode === 40 || e.keyCode === 13) {
// 					e.preventDefault();
// 					return false;
// 				} else {
// 					content = $(this).val();
// 				}

// 				if (content != "") {
// 					// AJAX call to api page
// 					var req = $.ajax({
// 						url : "/api/organization_list.php?chars=" + content,
// 						success : function(data) {
// 							var contents = $.parseJSON(data);
// 							var items = contents.data
// 							insertData(items);
// 							listenForHover();
// 						}
// 					});

// 					$(".return-prompt").addClass("active");
// 				} else {
// 					closeAutocomplete();
// 					$(".return-prompt").removeClass("active");
// 				}
// 			});

// 			el.on("keydown", function(e) {
// 				if (e.keyCode === 38 || e.keyCode === 40 || e.keyCode === 13) {
// 					e.preventDefault();
// 					arrowKeyActions(e.keyCode);
// 				}
// 			})
// 		}

// 		initialize();
// 	});
// }