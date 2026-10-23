
function validateForm() {
    const name = document.getElementById("name").value.trim();
    const gender = document.getElementById("gender").value;
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const course = document.getElementById("course").value.trim();
    const year = document.getElementById("year").value;

    let message = "";

    if (
        name === "" || gender === "" || email === "" ||
        password === "" || confirmPassword === "" ||
        course === "" || year === ""
    ) {
        message = "Please fill in all fields.";
    } else if (password.length < 8) {
        message = "Password must be at least 8 characters long.";
    } else if (password !== confirmPassword) {
        message = "Passwords do not match.";
    }

    if (message !== "") {
        let box = document.getElementById("formMessage");

        if (!box) {
            box = document.createElement("p");
            box.id = "formMessage";
            box.className = "error";

            const form = document.querySelector("form");
            form.parentNode.insertBefore(box, form);
        }

        box.textContent = message;
        return false;
    }

    return true;
}
