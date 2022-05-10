function submitData() {
    let bio = document.getElementById("tutor-bio").value;
    let vat = document.getElementById("tutor-vat").value;
    let iban = document.getElementById("tutor-iban").value;

    if(bio.length > 0 && vat.length > 0 && iban.length > 0) {
        $.ajax({
            url: 'api/updateprofile.php',
            type: 'POST',
            data: { bio: bio, vat: vat, iban: iban },
            success: function(data, textStatus, xhr) {
                if(xhr.status === 200){
                    // Answer received
                    const obj = JSON.parse(data);
                    if(parseInt(obj.error) === 0) {
                        // No errors, done
                        window.location.href = "index.php";
                    }
                    else {
                        // Some error occurred
                        // TODO: Handle the error
                        alert(obj.error_msg);
                    }
                }
                else {
                    // TODO: Handle the status error
                    alert("There was an error deleting the subject");
                }
            }
        });
    }
    else {
        alert("Compila tutti i campi");
    }
}

function submitImage() {
    let form = document.getElementById("changeimage-cont");
    $.ajax({
        url: "api/settutorimage.php",
        type: "POST",
        data: new FormData(form),
        contentType: false,
        cache: false,
        processData:false,
        success: function(result){
            console.log(result);
            const obj = JSON.parse(result);
            if(parseInt(obj.error) === 0) {
                // No error
                receivedToken(obj.token);
            }
            else {
                alert(obj.error_msg);
            }
        },
        error: function(e) {
            $("#err").html(e).fadeIn();
        }
    });
}