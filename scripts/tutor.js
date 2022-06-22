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
    let email = document.getElementById("sh-email").value;
    let phone = document.getElementById("sh-phone").value;
    let description = document.getElementById("sh-description").value;
    if(email.length > 5 && phone.length > 5) {
        // Change button UI
        let stripeButton = document.getElementById("sum-confirm-btn");
        stripeButton.classList.add("loadingbtn");
        stripeButton.onclick = null;
        stripeButton.innerText = "Caricamento...";

        $.ajax({
            url: '../api/stripe/paymentengine.php',
            type: 'POST',
            data: { email: email, phone: phone, description: description, lessonId: lessonId },
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
        alert('Assicurati di inserire tutte le informazioni richieste');
    }
}