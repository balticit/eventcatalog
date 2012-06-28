// JavaScript Document
				function trim(str, chars) {
					return ltrim(rtrim(str, chars), chars);
				}
				
				function ltrim(str, chars) {
					chars = chars || "\\s";
					return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
				}
				
				function rtrim(str, chars) {
					chars = chars || "\\s";
					return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
				}
				function showReplies(divID)
				{
					var div = document.getElementById(divID);
					if (trim(div.innerHTML)!='')
						div.style.display = (div.style.display=="")?"none":"";
					if (div.scrollHeight>400)
					{
						div.style.height = "400px";
					}
					return false;
				}
				function loadReplies(iframe,labelID,divID)
				{
					var replies = iframe.contentWindow.document.body.innerHTML;
					replies = trim(replies,' ');
					if (replies!='')
					{
						
						var label = document.getElementById(labelID);
						var div = document.getElementById(divID);
						var res = replies.toLowerCase().split("</p>");
						if (res!=null)
						{
							var l = res.length-1;
                            if (l > 0) {
							  label.firstChild.innerHTML = "Посмотреть отзывы ("+l+")";
                            }
						}
						label.style.display = "";
						div.innerHTML = replies;
					}
				}
				function loadAddReplies(iframe,divID,formID)
				{
					var replies = iframe.contentWindow.document.body.innerHTML;
					var div = document.getElementById(divID);
					div.innerHTML = replies;
					var iform = document.getElementById(formID);
					if (iform)
					{
						iform.target = iframe.name;
						iform.action = iframe.contentWindow.location;
					}
				}
				function showAddReply(divID,iframeID,url)
				{
					var div = document.getElementById(divID);
					var iframe = document.getElementById(iframeID);
					if (div.style.display=="none")
					{
						iframe.contentWindow.location = url;
					}
					div.style.display = (div.style.display=="")?"none":"";
					return false;
				}