<script>
function adaptToClientHeight(){
  //var largeur = document.documentElement.clientWidth,
  var hauteur = document.documentElement.clientHeight;
  var source = document.getElementById('footer'); // récupère l'id source
  source.style.bottom = hauteur+'px'; // applique la hauteur de la page
}

function addEvent(element, type, listener){
  if(element.addEventListener){
    element.addEventListener(type, listener, false);
  }else if(element.attachEvent){
    element.attachEvent("on"+type, listener);
  }
}
adaptToClientHeight();
addEvent(window, "load", adaptToClientHeight);
addEvent(window, "resize", adaptToClientHeight);
</script>
