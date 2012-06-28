function pro2Next()
{
  iw = $('.pro2l_item:first').width();
  var obj = $('#pro2l_container');
  w = $('#pro2l_visblock').width();
  l = obj.position().left;
  w2 = obj.children("div.pro2l_item").length*iw;
  if (w2+l > w)
    obj.animate({
      left: '-='+iw
    },'fast');
  else
    obj.animate({
      left: 0
    },'slow');
}

function pro2Prev()
{
  iw = $('.pro2l_item:first').width();
  var obj = $('#pro2l_container');
  if (obj.position().left<0)
    obj.animate({
      left: '+='+iw
    },'fast');
  else{
    w = $('#pro2l_visblock').width();
    w2 = (obj.children("div.pro2l_item").length - Math.floor(w/iw))*iw;
    obj.animate({
      left: -w2
    },'slow');
  }
}