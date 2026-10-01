//register
// Get elements
const registrationForm=document.getElementById("registrationform");
if(registrationForm){
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
}



//event 
const eventContainer = document.querySelector("#eventContainer");
const eventFilter = document.querySelector("#eventFilter");
const eventSort = document.querySelector("#eventSort");

if (eventContainer) {

    const next = document.querySelector("#next");
    const prev = document.querySelector("#prev");
    const slideNumber = document.querySelector("#slideNumber");

    let events = [];
    let allEvents = [];
    let currentSlide = 0;

    fetch("event.json")
        .then(response => response.json())
        .then(data => {

            events = data;
             allEvents = data;
            showEvent();

        })
        .catch(error => {

            console.log("Error loading events:", error);

        });


    function showEvent() {

        eventContainer.innerHTML = "";

        const event = events[currentSlide];

        console.log(event);

        const slide = document.createElement("div");

        slide.className = "";

        slide.innerHTML = `
            <h2>${event.title}</h2>
            <p>${event.date}</p>
            <p>${event.category}</p>
        `;

        eventContainer.appendChild(slide);

        slideNumber.textContent =
            (currentSlide + 1) + "/" + events.length;
    }

    eventFilter.addEventListener("change", function() {

    const selectedCategory = eventFilter.value;

    if (selectedCategory === "all") {
        events = allEvents;
    }

    else {
        events = allEvents.filter(function(event) {
            return event.category === selectedCategory;
        });
    }

    currentSlide = 0;
    showEvent();

});

eventSort.addEventListener("change", function() {

    const selectedSort = eventSort.value;

    if (selectedSort === "none") {

        events = allEvents;

    }

    else if (selectedSort === "title") {

        events = [...events].sort(function(a, b) {
            return a.title.localeCompare(b.title);
        });

    }

    else if (selectedSort === "date") {

        events = [...events].sort(function(a, b) {
            return new Date(a.date) - new Date(b.date);
        });

    }

    currentSlide = 0;
    showEvent();

});


    next.addEventListener("click", function() {

        currentSlide++;

        if (currentSlide >= events.length) {
            currentSlide = 0;
        }

        showEvent();

    });


    prev.addEventListener("click", function() {

        currentSlide--;

        if (currentSlide < 0) {
            currentSlide = events.length - 1;
        }

        showEvent();

    });

}


//profile
const studentName = document.querySelector("#studentname");

if (studentName) {

    fetch("students.json")
        .then(response => response.json())
        .then(data => {

            const student = data[0];

            document.querySelector("#studentname").value = student.studentname;
            document.querySelector("#idno").value = student.idno;
            document.querySelector("#gender").value = student.gender;
            document.querySelector("#bloodgroup").value = student.bloodgroup;
            document.querySelector("#birthdate").value = student.birthdate;
            document.querySelector("#birthplace").value = student.birthplace;
            document.querySelector("#nationality").value = student.nationality;
            document.querySelector("#mothertongue").value = student.mothertongue;
            document.querySelector("#mobileno").value = student.mobileno;

        })
        .catch(error => {
            console.log("Error loading student data:", error);
        });

}


// FAQ

const faqContainer = document.querySelector("#faqContainer");
const faqSearch = document.querySelector("#faqSearch");

if (faqContainer) {

let faqs = [];
let currentPage = 1;
let itemsPerPage = 2;

    fetch("FAQ.json")
        .then(response => response.json())

    .then(data => {

    faqs = data;

    showFAQs(faqs);

})
.catch(error => {

            console.log("Error loading FAQs:", error);

        });

   function showFAQs(data) {

    faqContainer.innerHTML = "";

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    const pageFAQs = data.slice(start, end);

    pageFAQs.forEach(faq => {

        const faqItem = document.createElement("div");
        faqItem.className = "faq-item";

        faqItem.innerHTML = `
            <button class="faq-que" type="button" aria-expanded="false">
                ${faq.question}
            </button>

            <div class="faq-ans" hidden>
                ${faq.answer}
            </div>
        `;

        faqContainer.appendChild(faqItem);

    });

    const totalPages = Math.ceil(data.length / itemsPerPage);

    document.querySelector("#faqPageNumber").textContent =
        currentPage + "/" + totalPages;

}   

const faqPrev = document.querySelector("#faqPrev");
const faqNext = document.querySelector("#faqNext");

faqNext.addEventListener("click", function() {

    if (currentPage < Math.ceil(faqs.length / itemsPerPage)) {

        currentPage++;
        showFAQs(faqs);

    }

});

faqPrev.addEventListener("click", function() {

    if (currentPage > 1) {

        currentPage--;
        showFAQs(faqs);

    }

});

faqSearch.addEventListener("input", function() {

    const searchText = faqSearch.value.toLowerCase();

    currentPage = 1;
    const filteredFAQs = faqs.filter(function(faq) {

        return faq.question.toLowerCase().includes(searchText) ||
               faq.answer.toLowerCase().includes(searchText);

    });

    showFAQs(filteredFAQs);

});

    // FAQ open and close

    faqContainer.addEventListener("click", function(event) {

        const que = event.target.closest(".faq-que");

        if (!que) {
            return;
        }

        const ans = que.nextElementSibling;

        const isOpen = que.getAttribute("aria-expanded") == "true";

        que.setAttribute("aria-expanded", String(!isOpen));

        ans.hidden = isOpen;

    });

}