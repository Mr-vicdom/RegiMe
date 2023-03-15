const inputs = document.querySelectorAll("form .field input");
const labels = document.querySelectorAll("form .field label");

for (let i = 0; i < inputs.length; i++) {
    inputs[i].addEventListener("change",(e) => {
        if(e.target.value != ""){
            labels[i].classList.add("active");
        } else {
            labels[i].classList.remove("active");
        }
    });
}


const showPass = () => {
    document.getElementById('password').type = "text";
    document.getElementById('show').classList.add("active");
    document.getElementById('hide').classList.remove("active");
}
const hidePass = () => {
    document.getElementById('password').type = "password";
    document.getElementById('hide').classList.add("active");
    document.getElementById('show').classList.remove("active");
}