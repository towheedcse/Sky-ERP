

document.onmousedown=function(e){
  var n=!e?self.event.srcElement.name:e.target.name;
  if (document.layers) {
    with (gfPop) var l=pageX, t=pageY, r=l+clip.width, b=t+clip.height;
    if (n!="popcal"&&(e.pageX>r||e.pageX<l||e.pageY>b||e.pageY<t)) gfPop.fHideCal();
    return routeEvent(e); // must use return here.
  } else if (n!="popcal") gfPop.fHideCal();
}
if (document.layers) document.captureEvents(Event.MOUSEDOWN);

