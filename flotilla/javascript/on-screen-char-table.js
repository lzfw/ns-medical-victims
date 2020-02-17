for (const elem of document.getElementsByClassName('special-characters')) {
  elem.addEventListener("click", function(e) {
    if(e.target && e.target.nodeName === "KBD") {
      navigator.clipboard.writeText(e.target.innerText);
    }
  });
}
