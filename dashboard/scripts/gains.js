function checkWithdraw() {
    let importShown = document.getElementById("gains-lbl").innerText.slice(0, -1);
    if(parseFloat(importShown) > 0) {
        $.ajax({
            url: 'api/sendwithdrawrequest.php',
            type: 'POST',
            success: function(data, textStatus, xhr) {
                if(xhr.status === 200){
                    // Answer received
                    console.log(data);
                    const obj = JSON.parse(data);
                    if(parseInt(obj.error) === 0) {
                        // No errors, done
                        location.reload();
                    }
                    else {
                        // Some error occurred
                        // TODO: Handle the error
                        alert(obj.error_msg);
                    }
                }
                else {
                    // TODO: Handle the status error
                    alert("There was an error withdrawing your funds");
                }
            }
        });
    }
    else {
        alert("Importo non sufficiente per un prelievo");
    }
}