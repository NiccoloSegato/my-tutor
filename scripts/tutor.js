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