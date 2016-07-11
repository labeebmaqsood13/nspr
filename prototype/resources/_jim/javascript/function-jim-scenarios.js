/*!
 * Copyright 2013 Justinmind. All rights reserved.
 */

(function(window, undefined) {
	
	var margin = 5;
	
	//MOBILE VARIABLES
	var mobileCase = $("#jim-case");
	var jimContainer = $("#jim-container");
	var jimMobileWrapper = $("#jim-mobile");
	var sim = $("#simulation");
	var toppanel = $("#toppanel"); 
	var body = $("#jim-body");
	
	var value;
	
	function buildScenarioGraph() {
	  var wrapper = $("#scenarioWrapper");
	  var nodes = $(".ui-scenario .Page");
	  var graph = {};
	  
	  graph['width'] = wrapper.outerWidth();
	  graph['height'] = wrapper.outerHeight();
	  
	  nodes.each(function () {
		var edges = [];
		if ($(this).attr("links") != "") edges = $(this).attr("links").split(",").map(function(e) {return parseInt(e);});
		var node = {screenId : $(this).attr("value"), left: parseInt($(this).css("left")), top: parseInt($(this).css("top")), width: parseInt($(this).css("width")), links : edges};	
		  
	    graph[parseInt($(this).attr("id").substring(1))] = node;
	  });	
	  
	  return graph;
	}
	
    var jimScenarios = {
	  "bindScreenEvents" : function() {
	    $(".ui-scenario .Page").on('click', function (event) {
	      value = $(this).attr("value");
	      jimScenarios.currentNode = parseInt($(this).attr("id").substring(1));
		  $(".ui-scenario .scenarioShadow").fadeIn();
		  $(".ui-scenario .filterDialog").fadeIn();
		  $("#simulation").trigger("scroll");
		});
	    
	    $(".scenarioShadow, #filterCloseButton").on('click', function (event) {
	      $(".scenarioShadow,.filterDialog").fadeOut();
	    });
	    
	    $(".ui-scenario .scenarioButton").on('click', function (event) {     
	      if ($(this).attr("id") == "startScenarioButton") {
	        jimScenarios.activeScenario = buildScenarioGraph();
	        $("#infoContent .filterText").css({"display": "inline-block"});
	        $("#infoContent #scenarioName").text($(".ui-scenario").attr("name"));
	        $("#scenarioThumbnail > img").attr("src", $("#scenarioWrapper > img").attr("src"));
	        $("#sidepanel").addClass("toClose");
	      }
	      else jimScenarios.currentNode = -1;
	      $("#screenTab").trigger("click");
	      jimMain.navigate("screens/" + value, {"transition": "none"}, 1);
	    });
	    
		var scrollHandler = $("#simulation").bind('scroll', function () {
	      var dialog = $(".filterDialog");
	      if (dialog.length > 0) {	    	  
	        var scrollY = $(this).scrollTop();
	        var scrollX = $(this).scrollLeft();
	      
	        var pos = dialog[0].getBoundingClientRect();
	        scrollY = scrollY - pos.height/2;
	        scrollX = scrollX - pos.width/2;
	        dialog.css({"transform": "translate(" + scrollX + "px," + scrollY + "px)", 
	           "-webkit-transform" : "translate(" + scrollX + "px," + scrollY + "px)", 
	           "-o-transform"      : "translate(" + scrollX + "px," + scrollY + "px)", 
	           "-ms-transform"	   : "translate(" + scrollX + "px," + scrollY + "px)"});
	      }
	      else {
	      	$(this).unbind('scroll', scrollHandler);  
	      }
	    });
	  },
	  "isValidLink" : function (target) {
    	var links = jimScenarios.activeScenario[jimScenarios.currentNode].links;
    	var t = target.substring(target.lastIndexOf("/") + 1);
    	var valid = false;
    	  
    	for (var i = 0; i < links.length; ++i) {
    	  var sId = jimScenarios.activeScenario[links[i]].screenId;
    	  if (sId == t) {
      	    jimScenarios.currentNode = links[i];
    	    valid = true;
    	    break;
    	  }
    	}
    	return valid;
	  },
	  "deleteFilter" : function () {
		jimScenarios.currentNode = -1;
        $("#infoContent .filterText").css({"display": ""});  
	  },
	  "activateThumbnail" : function () {
		var namePos = $("#scenarioName")[0].getBoundingClientRect();
		var scenario = jimScenarios.activeScenario;
		var thumbnail = $("#scenarioThumbnail");
		thumbnail.stop(true, true);
		var tWidth = thumbnail.outerWidth();
		var tHeight = thumbnail.outerHeight();
		
		//Apply scaling if the scenario is too big
		var width = scenario.width;
		var height = scenario.height;
		var scale = (tWidth -  35)/ width;
		
		if (height * scale > (tHeight - 70)) scale = (tHeight - 70) / height;
		
		var image = $("#thumbnailImage");		
		image.css({"height": height * scale, "width": width * scale});

		var triangle = $("#thumbnailTriangle");
		triangle.stop(true, true);
		triangle.css({"left": (namePos.left + namePos.width/2) - 25});
		
		//Obtain img initial position and scale node pos 
		var pos = $("#thumbnailPos");
		var node = jimScenarios.activeScenario[jimScenarios.currentNode];
		pos.css({"left": (tWidth/2 - (width * scale)/2) + (node.left + node.width/2)*scale - 7, "top": node.top * scale + (tHeight/2 - (height * scale)/2) - 16});
		
		triangle.fadeIn();
		thumbnail.fadeIn();
	  },
	  "closeThumbnail" : function () {
		$("#scenarioThumbnail").fadeOut();
		$("#thumbnailTriangle").fadeOut();
	  },
	  "initializeScenarios" : function () {
		var separator = $(".rightcontrols .comments-separator");
		var highlight = $(".highlight");
		var commentscontrol = $("#commentscontrol");
		
		if ($(".ui-scenario").length > 0) {
		  highlight.css("opacity", 0);
		  $('#comments-separator1').css("opacity", '0');
		  $('#highlight-select').css('cursor', 'default');
		}
    	else  if (!commentscontrol.hasClass("active")) {
    	  $('#comments-separator1').css("opacity", '');
      	  highlight.css("opacity", '');
      	  $('#highlight-select').css('cursor', '');
    	}
	  },
	  "removeMobileCase" : function () {
		mobileCase = $("#jim-case");
		jimContainer = $("#jim-container");
	    jimMobileWrapper = $("#jim-mobile");
		sim.unwrap().unwrap();
		
		$("#jim-case").remove();
		if (!toppanel.hasClass("hidden")) {
		  toppanel.css("display","none");
		  body.removeClass("controlled");
		}
      },
	  "restoreMobileCase" : function () {
        body.children().not(".comments-dialog-layer").wrapAll(jimMobileWrapper).wrapAll(jimContainer);
		sim.parent().parent().prepend(mobileCase);
		if (!toppanel.hasClass("hidden")) {
		  toppanel.css("display","");
		  body.addClass("controlled");
		  
		  //Restore toppanel zoom
		  value = parseInt($("#zoom-slider-full").css("width"));
		  jimMobile.setZoom((value/2 + 50) /100);
		}
	  },
	  "activeScenario" : {},
	  "currentNode" : -1
    };

	window.jimScenarios = jimScenarios;
})(window);