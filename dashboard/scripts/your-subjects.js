function deleteSubject(id) {
    $.ajax({
        url: 'api/deletesubject.php',
        type: 'POST',
        data: { id: id },
        success: function(data, textStatus, xhr) {
            if(xhr.status === 200){
                // Answer received
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
                alert("There was an error deleting the subject");
            }
        }
    });
}