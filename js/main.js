var error = document.getElementById("error");

error.style.display = "none";
error.onchange = checkInput(this.value, /^[a-zæøå0-9]{4,8}$/, 'brukernavn');

function checkInput(inputField, regex, fieldType) {
    if (regex.test(inputField))
        document.getElementById('error').innerHTML = "Dette " + fieldType + "et er ikke formatert riktig :(";
}



function changeVisibility(element) {
    element.style.display = element.style.display == "none" ? "block" : "none";
}