function checkFields() {
    if(document.getElementById("signup-check-field").checked) {
        // Policies accepted
        let name = document.getElementById("signup-name-field").value;
        let surname = document.getElementById("signup-surname-field").value;
        let city = document.getElementById("signup-city-field").value;
        let birthday = document.getElementById("signup-birthday-field").value;
        let email = document.getElementById("signup-email-field").value;
        let password = document.getElementById("signup-psw-field").value;

        if(name.length > 0 && surname.length > 0 && city.length > 0 && birthday.length > 0 && email.length > 0 && password.length > 0) {
            // Length check passed
            document.getElementById("signup-form").submit();
        }
    }
}