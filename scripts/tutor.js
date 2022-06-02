var selectedSubject = 1;

function selectSubject(id) {
    selectedSubject = id;
    clearSection();
    document.getElementById("calendar-box").style.display = "block";
}

function clearSection() {
    document.getElementById("date-selector").innerHTML = "";
    document.getElementById("date-box").style.display = "none";
    document.getElementById("summary-slot").style.display = "none";
}

function submitOrder(lessonId) {
    let email = document.getElementById("popup-email").value;
    let cell = document.getElementById("popup-cell").value;
    let description = document.getElementById("popup-description").value;
    if(email.length > 5 && cell.length > 5) {
        $.ajax({
            url: './api/stripe/paymentengine.php',
            type: 'POST',
            data: { email: email, phone: cell, description: description, lessonId: lessonId },
            dataType: 'text',
            success: function(data, textStatus, xhr){
                if(xhr.status == 200){
                    // Get userID from data
                    console.log(data);
                    const obj = JSON.parse(data);
                    if(parseInt(obj.error) == 0) {
                        let stripeSession = obj.id;
                        var stripe = Stripe("pk_test_51L1CLOLQYnoKNATEfUDYgDkYgz0lfNBZSfX4aAMpyP7VvPU55SkKoVotBtVeFI7pjkRiMGcHv8HIb5tzQgLidAET00lCtr1Qbs");
                        stripe.redirectToCheckout({ sessionId: stripeSession });
                    }
                    else {
                        alert(obj.error_msg);
                    }
                }
                else {
                    // TODO: handle error
                    alert("Internal error");
                }
            }
        })
    }
    else {
        alert("Email e numero di telefono sono campi obbligatori");
    }
}