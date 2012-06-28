/*

   proScroll v0.3 (26.08.2010)
   plugin for jQuery
   (c) http://developing.name/, http://otpro.ru/

   demo-page: http://developing.name/divscroll/

*/


(function($) {



	$.event.special.mousewheel = {

		setup: function() {

			if (this.addEventListener)
				this.addEventListener('DOMMouseScroll', $.event.special.mousewheel.handler, false);

			this.onmousewheel = $.event.special.mousewheel.handler;

		},

		teardown: function() {

			if (this.removeEventListener)
				this.removeEventListener('DOMMouseScroll', $.event.special.mousewheel.handler, false);

			this.onmousewheel = null;
		},

		handler: function( event ) {

			event = $.event.fix(event || window.event);

			event.type = "mousewheel";
			event.delta = 0;

			if ( event.wheelDelta < 0 || event.detail > 0 )
				event.delta = -1;
			else if (  event.wheelDelta > 0 || event.detail < 0 )
				event.delta = 1;

			$.event.handle.apply( this, [event] );

			return ;
		}
	};



	$.fn.scroll = function(options) {

		options = $.extend({
			width: '100%',
			height: 180,
			background: "",
			style: "",

			button_img_up: "scroll/top.png",
			button_img_down: "scroll/bot.png",
			button_img_up_active: "scroll/top_active.png",
			button_img_down_active: "scroll/bot_active.png",
			button_width: 17,
			button_height: 11,

			bar_img_top: "scroll/pixel.gif",
			bar_img_middle: "scroll/pixel.gif",
			bar_img_bottom: "scroll/pixel.gif",
			bar_drag: "scroll/pixel.gif",
			bar_width: 17,

			scroll_speed: 300,

			bar_align: "right",

			box_style: "border: none;"

		}, options);


		if (options.button_height * 2 > options.height)
		{
			options.height = options.button_height * 2;
		}

		$(this).each( function(i) {

			if ( $(this).attr("id") == '' )
			{
				$(this).attr("id", 'scrollingrnd' + i);
			}

			var id_prosc = 'prosc';
			var id_rnd = $(this).attr("id") + id_prosc;
			var id_box = 'box' + id_rnd;
			var id_content = 'content' + id_rnd;
			var id_bar = "bar" + id_rnd;
			var id_line = "line" + id_rnd;
			var id_up = "up" + id_rnd;
			var id_down = "down" + id_rnd;

			code_bar  = '<table cellpadding="0" cellspacing="0" width="100%" height="100%" border="0" id="' + id_bar + '">';
			code_bar += '<tr><td height="4" style="background: url(' + options.bar_img_top + ') no-repeat;"></td></tr>';
			code_bar += '<tr><td style="background: url(' + options.bar_img_middle + ') repeat-y; width: ' + options.button_width + 'px;"></td></tr>';
			code_bar += '<tr><td height="4" style="background: url(' + options.bar_img_bottom + ') no-repeat;"></td></tr>';
			code_bar += '</table>';

			code_scrollbar  = '<table height="' + options.height + '" width="' + options.bar_width + '" cellpadding="0" cellspacing="0">';
			code_scrollbar += '<tr><td width="' + options.button_width + '" height="' + options.button_height + '"><a href="" id="' + id_up + '"><img src="' + options.button_img_up + '" border="0"></a></td></tr>';
			code_scrollbar += '<tr><td style="background: url(' + options.bar_drag + ') center repeat-y;" valign="top" id="' + id_line + '">' + code_bar + '</td></tr>';
			code_scrollbar += '<tr><td width="' + options.button_width + '" height="' + options.button_height + '"><a href="" id="' + id_down + '"><img src="' + options.button_img_down + '" border="0"></a></td></tr>';
			code_scrollbar += '</table>';

			code_box  = '<table id="' + id_box + '" border="0" cellpadding="0" cellspacing="0" width="' + options.width + '" height="' + options.height + '" bgcolor="' + options.background + '" style="' + options.style + '">';
			code_box += '<tr>' + (options.bar_align == 'left' ? '<td width="' + options.bar_width + '">' + code_scrollbar + '</td>' : '' ) + '<td><div style="position:relative; overflow:hidden; width:' + (options.width - options.bar_width) + 'px; height:' + options.height + 'px; ' + options.box_style + ' ">';
			code_box += '<div id="' + id_content + '" style="position:absolute; left:0px; top:0px; visibility: visible;">' + $(this).html() + '</div><div class=bg></div></div></td>' + (options.bar_align == 'right' ? '<td width="' + options.bar_width + '"><div id="scroll_box" style="opacity: 0">' + code_scrollbar + '</div></td>' : '' ) + '</tr><table>';

			$(this).replaceWith(code_box);

			var bar_top = $('#' + id_line).position().top;

			var content_height = 0;
			var content_top = 0;
			var bar_height_max = options.height - options.button_height * 2;
			var bar_height_cur = 0;
			var bar_top_max = 0;
			var bar_top_cur = 0;
			var bar_move = false;
			var bar_top_max = 0;

			setInterval(function() {

				var content_height_true = parseInt($('#' + id_content).innerHeight());

				if (content_height == content_height_true)
				{
					return;
				}

				content_height = content_height_true;

				content_top = options.height - content_height;

				if (content_height > options.height)
				{
					bar_height_cur = bar_height_max * options.height / content_height;
				} else {
					bar_height_cur = bar_height_max;
				}

				bar_top_max = bar_height_max - bar_height_cur;

				$('#' + id_bar).css({
					'height' : bar_height_cur
				});

			}, 500);

             /*
			$('#' + id_down).bind("mouseover", function(e) {
				if (!bar_move)
				{
					$('#' + id_bar).animate({marginTop: bar_top_max + "px"}, 1500)
					$('#' + id_content).animate({"top": content_top + "px"}, 1500);
				}
			});


			$('#' + id_up).bind("mouseover", function(e) {
				if (!bar_move)
				{
					$('#' + id_bar).animate({marginTop: "0px"}, 1500);
					$('#' + id_content).animate({"top": "0px"}, 1500);
				}
			});

			$('#' + id_down + ', #' + id_up).bind("mouseout", function(e) {
				if (!bar_move)
				{
					$('#' + id_content).stop();
					$('#' + id_bar).stop();
				}
			}); */



			$('body').bind('mouseenter', function(e) {				$('#scroll_box').animate({'opacity':'1'}, 'fast');
			});

			$('body').bind('mouseleave', function(e) {
				$('#scroll_box').animate({'opacity':'0'}, 'fast');
			});



			$('#' + id_down).bind("mouseover", function() {				$('#' + id_down).find('img').attr('src', options.button_img_down_active);
			});
			$('#' + id_down).bind("mouseout", function() {
				$('#' + id_down).find('img').attr('src', options.button_img_down);
			});
			$('#' + id_up).bind("mouseover", function() {
				$('#' + id_up).find('img').attr('src', options.button_img_up_active);
			});
			$('#' + id_up).bind("mouseout", function() {
				$('#' + id_up).find('img').attr('src', options.button_img_up);
			});



			$('#' + id_down).bind("click", function(e) {

				bar_top_get();

				$('#' + id_content).stop();
				$('#' + id_bar).stop();

				move_content_bar(bar_top_cur + 38, options.scroll_speed, false);

				return false;
			});

			$('#' + id_up).bind("click", function(e) {

				bar_top_get();

				$('#' + id_content).stop();
				$('#' + id_bar).stop();

				move_content_bar(bar_top_cur - 38, options.scroll_speed, false);

				return false;
			});


			$('#' + id_bar).bind("mousedown", function(e) {

				if (bar_move) return false;

				bar_top_get();
				bar_top_cur = (e.pageY - bar_top) - bar_top_cur;

				$("body").bind("mousemove", function(e) {

					bar_move = true;

					move_margin = (e.pageY - bar_top) - bar_top_cur;
					move_content_bar(move_margin, 0, true);

					return false;
				});

				$("body").one("mouseup", function(e) {
					$("body").unbind("mousemove");
					bar_move = false;
				});

				return false;
			});


			$('#' + id_line).bind("click", function(e) {

				if (bar_move) return false;

				bar_move = true;

				bar_top_get();

				move_margin = (e.pageY - bar_top - bar_height_cur / 2);

				move_content_bar(move_margin, 0, false);

				return false;
			});


			$('#' + id_box).bind('mousewheel', function (event) {

				if (bar_move) return false;

				bar_move = true;

				bar_top_get();

				move_margin = bar_top_cur + (-1 * event.delta * 90 * 50 / content_height);
				move_content_bar(move_margin, options.scroll_speed, false);

				return false;
			});


			function bar_top_get()
			{

				if (bar_top != $('#' + id_line).position().top )
				{
					bar_top = $('#' + id_line).position().top;
				}

				bar_top_cur = parseInt($('#' + id_bar).css('marginTop'));
				bar_top_cur = (isNaN(bar_top_cur) ? 0 : bar_top_cur);

				return ;
			}


			function move_content_bar(top, sec, move)
			{

				if (top > bar_top_max)
				{
					top = bar_top_max;
				}
				else if (top < 0)
				{
					top = 0;
				}

				$('#' + id_bar).animate({marginTop: top + "px"}, sec)

				top = top * content_top / bar_top_max;

				$('#' + id_content).animate({"top": top + "px"}, sec, function () {
					bar_move = move;
				});
			}

		});

	}

})(jQuery);
