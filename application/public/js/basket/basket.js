function addBasket(elid, parentid, added_pic)
{
  
 //
 // When CMS DOM Basket frame is being simply shown (without previuos
 // `FrontAddCmsDomBasket` action call) there are no numeric components
 // in `elid` and `parentid`.
  if (elid != "a" && parentid != "div") {

   // Parent div of the `Add to Basket` form
    var addBasketFormDiv = window.parent.document.getElementById(parentid);
	if(!addBasketFormDiv)
	{
		return;
	}

   //
   // We need to replace `Add to Basket` pic to `Show Basket` image
    var newImage = window.parent.document.createElement("IMG");

    newImage.setAttribute('src', added_pic);
    newImage.setAttribute('alt', 'Товар добавлен в корзину');
    newImage.setAttribute('title', 'Товар в корзине');
    newImage.setAttribute('width', '20');
    newImage.setAttribute('height', '20');
    newImage.setAttribute('border', '0');

   //
   // Making newly created image point to the Basket content screen
    var newHref = window.parent.document.createElement("A");
    newHref.setAttribute('href', "/FrontBasket");

    newHref.appendChild(newImage);
    
   //
   // This is an `Add to Basket` form which should be replaced by new href
    var oldItem = window.parent.document.getElementById(elid);

   //
   // Replacing images
    addBasketFormDiv.replaceChild(newHref, oldItem);

   //
   // Now replacing item quantity editor with corresponding text (editor should
   // be contained in span with `divx[\d]+` id attribute)
    if (window.parent.document.getElementById("divx" + elid.slice(1)) != null) {    
      var countField = window.parent.document.getElementById("divx" + elid.slice(1));
      countField.innerHTML = "<span class=\"text\" style=\"color: #A0A0A0;\"><b>в корзине</b></span>";
    }
  
  } // END if (elid != "a" && parentid != "div")

}
