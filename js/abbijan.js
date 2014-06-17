///---------(tooltip)-----------------------------------------------------------------------------------------------------

$(document).ready(function() {
	    $(".abbijan_tooltip").hover(
	        function() { $(this).contents("span:last-child").css({ display: "block" }); },
	        function() { $(this).contents("span:last-child").css({ display: "none" }); }
	    );
	    $(".abbijan_tooltip").mousemove(function(e) {
	        var mousex = e.pageX + 10;
	        var mousey = e.pageY + 10;
	        $(this).contents("span:last-child").css({  top: mousey, left: mousex });
	    });
});


//-------(check all checkboxes)-------------------------------------------------------------------------------------------

	var checked = false;
	function checkAll()
	{
		var myform = document.getElementById("form2");
		
		if (checked == false) { checked = true }else{ checked = false }
		for (var i=0; i<myform.elements.length; i++) 
		{
			myform.elements[i].checked = checked;
		}
	}


//--------(countdown time sync)-------------------------------------------------------------------------------------------

function ahead5Mins()
{ 
	var server = new Date(); 
	server.setMinutes(server.getMinutes() + 0); 
	return server; 
}


///---------(tabs)--------------------------------------------------------------------------------------------------------
$(document).ready(function(){
	$(".tab_content").hide();
	$("#tabs li:first").addClass("active").show();
	$(".tab_content:first").show();

	$("#tabs li").click(function() {
		$("#tabs li").removeClass('active');
		$(this).addClass("active");
		$(".tab_content").hide();
		var selected_tab = $(this).find("a").attr("href");
		$(selected_tab).fadeIn();
		return false;
	});

	if(window.location.hash) {
		var hash = window.location.hash;
		$('#tabs li').each(function() {
			if($(this).find('a').attr('href') == hash) {
				$("#tabs li").removeClass("active");
				$(this).addClass("active");
				$(".tab_content").hide();
				var activeTab =  $(this).find('a[href=' + hash + ']').attr('href');
				$(activeTab).fadeIn();
				return false;
			}
		});
	}
});


///---------(top)--------------------------------------------------------------------------------------------------------
 $(document).ready(function() {
	$(window).scroll(function() {
		if ($(this).scrollTop() > 200) {
			$('.scrollup').fadeIn();
		} else {
			$('.scrollup').fadeOut();
		}
		});
 
		$('.scrollup').click(function() {
			$("html, body").animate({ scrollTop: 0 }, 600);
			return false;
		});
});


///---------(carousel)---------------------------------------------------------------------------------------------------
$(document).ready(function() {
    jQuery('#mycarousel').jcarousel({
        vertical: true,
        scroll: 2
    });
	jQuery('#other_deals').jcarousel();
});


///---------(faqs)-------------------------------------------------------------------------------------------------------
$(document).ready(function() {
	$('#faqs h3').each(function() {
		var tis = $(this), state = false, answer = tis.next('div').hide().css('height','auto').slideUp();
		tis.click(function() {
			state = !state;
			answer.slideToggle(state);
			tis.toggleClass('active',state);
		});
	});
});