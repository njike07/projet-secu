let content = document.getElementById("content");
let title = document.getElementById("title");
let form_inscr = document.getElementById("form-inscr");
let form_connect = document.getElementById("form-connect");
let span = document.getElementById("span");
let btn = document.getElementById("btn");
let binary = 0;

form_inscr.classList.add("show");
btn.onclick = () => {
  if (binary === 0) {
    title.innerHTML = "Je m'authentifie";
    content.classList.add("show");
    form_inscr.classList.add("hide");
    form_connect.classList.add("show");
    span.innerHTML = "Avez-vous déja un compte ?";
    btn.innerHTML = "S'inscrire";
    binary = 1;
  } else {
    title.innerHTML = "Je crée mon compte";
    content.classList.remove("show");
    form_inscr.classList.remove("hide");
    form_connect.classList.remove("show");
    span.innerHTML = "J'ai déja un compte";
    btn.innerHTML = "Se connecter";
    binary = 0;
  }
};
