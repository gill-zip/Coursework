addEventListener("DOMContentLoaded", (event) => {
    const password = document.getElementById("password-input");
    const passwordAlert = document.getElementById("password-alert");
    const requirements = document.querySelectorAll(".requirements");
    const leng = document.querySelector(".leng");
    const bigLetter = document.querySelector(".big-letter");
    const num = document.querySelector(".num");
    const specialChar = document.querySelector(".special-char");
  
    requirements.forEach((element) => element.classList.add("wrong"));
  
    password.addEventListener("focus", () => {
        passwordAlert.classList.remove("d-none");
        if (!password.classList.contains("is-valid")) {
            password.classList.add("is-invalid");
        }
    });
  
    password.addEventListener("input", () => {
        const value = password.value;
        const isLengthValid = value.length >= 8;
        const hasUpperCase = /[A-Z]/.test(value);
        const hasNumber = /\d/.test(value);
        const hasSpecialChar = /[!@#$%^&*()\[\]{}\\|;:'",<.>/?`~]/.test(value);
  
        leng.classList.toggle("good", isLengthValid);
        leng.classList.toggle("wrong", !isLengthValid);
        bigLetter.classList.toggle("good", hasUpperCase);
        bigLetter.classList.toggle("wrong", !hasUpperCase);
        num.classList.toggle("good", hasNumber);
        num.classList.toggle("wrong", !hasNumber);
        specialChar.classList.toggle("good", hasSpecialChar);
        specialChar.classList.toggle("wrong", !hasSpecialChar);
  
        const isPasswordValid = isLengthValid && hasUpperCase && hasNumber && hasSpecialChar;
  
        if (isPasswordValid) {
            password.classList.remove("is-invalid");
            password.classList.add("is-valid");
  
            requirements.forEach((element) => {
                element.classList.remove("wrong");
                element.classList.add("good");
            });
  
            passwordAlert.classList.remove("alert-warning");
            passwordAlert.classList.add("alert-success");
        } else {
            password.classList.remove("is-valid");
            password.classList.add("is-invalid");
  
            passwordAlert.classList.add("alert-warning");
            passwordAlert.classList.remove("alert-success");
        }
    });
  
    password.addEventListener("blur", () => {
        passwordAlert.classList.add("d-none");
    });
  });