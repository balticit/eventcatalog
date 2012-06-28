//
// CMS stuff
//

function sendVars(message, target_window, form_id)
{
	if(allRTEs.length >0)
	{
	  updateRTEs(); // copy all iframe content to hidden form controls
	}

  form_id = ("" == form_id) ? "visForm" : form_id;

  t_form = document.getElementById(form_id)
  if(!t_form && typeof(document.forms[form_id])!="undefined")
	  t_form = document.forms[form_id];

  if (t_form) {
    if (("" != target_window) && (null != window.opener)) {
      window.opener.name = target_window;
      t_form.target = target_window;
    }
    if ("" != message) {
      if (confirm(message)) { 
        t_form.submit();
        if (("" != target_window) && (null != window.opener)) self.close();
      }
    } else {
      t_form.submit();
      if (target_window) self.close();
    }
  } else {
    alert("Invalid form ID supplied. Cannot submit data.");
  }
}

function submitRTEForm($message) {
	if(typeof($message)=="string" && $message.length > 0)
	{
		if(!confirm($message))
			return false;
	}
	
	//make sure hidden and iframe values are in sync before submitting form
	//to sync only 1 rte, use updateRTE(rte)
	//to sync all rtes, use updateRTEs
	//updateRTE('rte1');
	updateRTEs();
    //alert("rte1 = " + document.RTEDemo.rte1.value);
	
	//change the following line to true to submit form
	return true;
}

//Usage: initRTE(imagesPath, includesPath, cssFile)
initRTE("/js/rte/images/", "/js/rte/", "");
