const modalContainer = document.querySelector(".modal-container-register");
const modalTriggers = document.querySelectorAll(".modal-trigger-register");

modalTriggers.forEach(trigger => trigger.addEventListener("click", toggleModal_register))

function toggleModal_register(){
  modalContainer.classList.toggle("active")
}