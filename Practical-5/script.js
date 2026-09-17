//register
// Get elements
let name = document.getElementById("name");
let email = document.getElementById("email");
let password = document.getElementById("ps");
let cpassword = document.getElementById("cps");
let courseInput = document.getElementById("course");
let yearInput = document.getElementById("year");
let termsInput = document.getElementById("terms");

let nameError = document.getElementById("nameerror");
let emailError = document.getElementById("emailerror");
let passwordError = document.getElementById("passworderror");
let cpasswordError = document.getElementById("cpassworderror");

let registrationForm = document.getElementById("registrationform");
// Regular Expressions

let namePattern = /^[A-Za-z ]+$/;

let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

let passwordPattern = /^(?=.*[A-Za-z])(?=.*[0-9]).{8,}$/;


// ---------------- Full Name ----------------

name.addEventListener("blur", function () {

    let value = name.value.trim();

    if (value === "") {
        nameError.textContent = "Full name is required";
        return;
    }

    if (!namePattern.test(value)) {
        nameError.textContent =
            "Name should contain characters only";
        return;
    }

    nameError.textContent = "";
});


// ---------------- Email ----------------

email.addEventListener("blur", function () {

    let value = email.value.trim();

    if (value === "") {
        emailError.textContent = "Email is required";
        return;
    }

    if (!emailPattern.test(value)) {
        emailError.textContent =
            "Enter a valid email address";
        return;
    }

    emailError.textContent = "";
});


// ---------------- Password ----------------

password.addEventListener("blur", function () {

    let value = password.value;

    if (value === "") {
        passwordError.textContent = "Password is required";
        return;
    }

    if (!passwordPattern.test(value)) {
        passwordError.textContent =
            "Password must contain 8 characters, letters and numbers";
        return;
    }

    passwordError.textContent = "";
});


// ---------------- Confirm Password ----------------

cpassword.addEventListener("blur", function () {

    let value = cpassword.value;

    if (value === "") {
        cpasswordError.textContent =
            "Confirm password is required";
        return;
    }

    if (value !== password.value) {
        cpasswordError.textContent =
            "Passwords do not match";
        return;
    }

    cpasswordError.textContent = "";
});

// Course Validation
function validateCourse() {

  if (courseInput.value === "") {
    document.getElementById("courseError").textContent =
      "Please select your course.";

    return false;
  }

  document.getElementById("courseError").textContent = "";

  return true;
}

// Year Validation
function validateYear() {

  if (yearInput.value === "") {
    document.getElementById("yearError").textContent =
      "Please select your year.";

    return false;
  }

  document.getElementById("yearError").textContent = "";

  return true;
}

// Gender Validation
function validateGender() {

  const gender =
    document.querySelector('input[name="gender"]:checked');

  if (gender === null) {
    document.getElementById("genderError").textContent =
      "Please select your gender.";

    return false;
  }

  document.getElementById("genderError").textContent = "";

  return true;
}

// Terms Validation
function validateTerms() {

  if (!termsInput.checked) {
    document.getElementById("termsError").textContent =
      "Please accept the Terms & Conditions.";

    return false;
  }

  document.getElementById("termsError").textContent = "";

  return true;
}

courseInput.addEventListener(
  "change",
  validateCourse
);

yearInput.addEventListener(
  "change",
  validateYear
);

document
  .querySelectorAll('input[name="gender"]')
  .forEach(function (radio) {
    radio.addEventListener("change", validateGender);
  });

termsInput.addEventListener(
  "change",
  validateTerms
);

//submit validation
//const registrationForm = document.getElementById("registrationform");

registrationForm.addEventListener("submit", function(event) {

    event.preventDefault();

    let valid = true;

    // Name
    let nameValue = name.value.trim();

    if (nameValue === "") {
        nameError.textContent = "Full name is required";
        valid = false;
    }
    else if (!namePattern.test(nameValue)) {
        nameError.textContent =
            "Name should contain characters only";
        valid = false;
    }
    else {
        nameError.textContent = "";
    }


    // Email
    let emailValue = email.value.trim();

    if (emailValue === "") {
        emailError.textContent = "Email is required";
        valid = false;
    }
    else if (!emailPattern.test(emailValue)) {
        emailError.textContent =
            "Enter a valid email address";
        valid = false;
    }
    else {
        emailError.textContent = "";
    }


    // Password
    let passwordValue = password.value;

    if (passwordValue === "") {
        passwordError.textContent =
            "Password is required";
        valid = false;
    }
    else if (!passwordPattern.test(passwordValue)) {
        passwordError.textContent =
            "Password must contain 8 characters, letters and numbers";
        valid = false;
    }
    else {
        passwordError.textContent = "";
    }


    // Confirm Password
    let cpasswordValue = cpassword.value;

    if (cpasswordValue === "") {
        cpasswordError.textContent =
            "Confirm password is required";
        valid = false;
    }
    else if (cpasswordValue !== passwordValue) {
        cpasswordError.textContent =
            "Passwords do not match";
        valid = false;
    }
    else {
        cpasswordError.textContent = "";
    }

  let courseValid = validateCourse();
  let yearValid = validateYear();
  let genderValid = validateGender();
  let termsValid = validateTerms();

 if ( !courseValid || !yearValid || !genderValid || !termsValid ) 
    { valid = false; } 
 // ---------- Final Message ---------- 
 if (valid) {

    document.getElementById("finalMessage").textContent =
      "Registration successful!";

    document.getElementById("finalMessage").style.color =
      "green";

      window.location.href = "login.html";

  } else {

    document.getElementById("finalMessage").textContent =
      "Please correct the errors.";

    document.getElementById("finalMessage").style.color =
      "red";
  }
});

  // Reset Form
registrationForm.addEventListener("reset", function () {

  setTimeout(function () {

    const errors =
      document.querySelectorAll(".error");

    errors.forEach(function (error) {
      error.textContent = "";
    });

    document.getElementById("strength").textContent = "-";

    document.getElementById("finalMessage").textContent = "";

  }, 0);
});