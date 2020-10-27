function CShowFullImage(url)
{
  let bLoaded = false;
  Creators.Actions.Modals.progress({name: "Image Preview", innerText: "Loading..."});
  if(!url || url == "") return;

  let IImage = new Image();
  IImage.style.maxWidth = "1000px";
  IImage.style.maxHeight = "1000px";
  IImage.addEventListener("load", function() {
    bLoaded = true;
    Creators.Actions.Modals.alert({name: "Image Preview", innerHTML: IImage.outerHTML, options: {width: `${Math.min(IImage.width, 1000) + 40}px`}});
  }, false);
  IImage.src = url;

  setTimeout(()=>{
    if(!bLoaded) Creators.Actions.Modals.close();
  }, 5000);
}
