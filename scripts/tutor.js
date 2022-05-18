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

function submitOrder() {
    // TODO: implement fields check
    let email = document.getElementById("sh-email").value;
    $.ajax({
        url: 'https://reepit.it/api/stripe/paymentengine.php',
        type: 'POST',
        data: { email: email},
        dataType: 'text',
        success: function(data, textStatus, xhr){
            if(xhr.status == 200){
                // Get userID from data
                console.log(data);
                const obj = JSON.parse(data);
                if(parseInt(obj.error) == 0) {
                    let stripeSession = obj.id;
                    var stripe = Stripe("pk_test_51IdL9kEB9H1DUjR0LUh1UdOCCAeQANjlJ3snEkJuzZqQEgkpFvVlgUnMl7dZznfJHWUiPIdzF0gsJUKEQY1fwLRj00JVSZaU6s");
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