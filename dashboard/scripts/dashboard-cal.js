let today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();
let selectYear = document.getElementById("year");
let selectMonth = document.getElementById("month");

let lessons = [];

var selectedMonth = 0;
var selectedYear = 2022;

let months = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];

let monthAndYear = document.getElementById("monthAndYear");
showCalendar(currentMonth, currentYear);


function next() {
    currentYear = (currentMonth === 11) ? currentYear + 1 : currentYear;
    currentMonth = (currentMonth + 1) % 12;
    showCalendar(currentMonth, currentYear);
    selectedMonth = currentMonth;
    selectedYear= currentYear;
}

function previous() {
    currentYear = (currentMonth === 0) ? currentYear - 1 : currentYear;
    currentMonth = (currentMonth === 0) ? 11 : currentMonth - 1;
    showCalendar(currentMonth, currentYear);
    selectedMonth = currentMonth;
    selectedYear= currentYear;
}

function showCalendar(month, year) {

    selectedMonth = month;
    selectedYear = year;

    let firstDay = (new Date(year, month)).getDay();
    let daysInMonth = 32 - new Date(year, month, 32).getDate();

    let tbl = document.getElementById("calendar-body"); // body of the calendar

    // clearing all previous cells
    tbl.innerHTML = "";

    // filing data about month and in the page via DOM.
    monthAndYear.innerHTML = months[month] + " " + year;
    monthAndYear.classList.add("month-and-year");

    // creating all cells
    let date = 1;
    for (let i = 0; i < 6; i++) {
        // creates a table row
        let row = document.createElement("tr");

        //creating individual cells, filing them up with data.
        for (let j = 0; j < 7; j++) {
            if (i === 0 && j < firstDay) {
                // Dates before 1st day of month
                let cell = document.createElement("td");
                let cellText = document.createTextNode("");
                cell.appendChild(cellText);
                row.appendChild(cell);
            }
            else if (date > daysInMonth) {
                // Dates after last day of the month
                break;
            }

            else {
                let cell = document.createElement("td");
                let cellText = document.createElement("div");
                cellText.id = "container-" + date;
                cellText.classList.add("container-box");
                let dateP = document.createElement("p");
                dateP.innerText = date;
                cellText.append(dateP);

                if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                    cell.classList.add("bg-info");
                } // color today's date

                cell.appendChild(cellText);
                row.appendChild(cell);
                date++;
            }


        }

        tbl.appendChild(row); // appending each row into calendar body.
    }

    lessons = [];
    parseEvents(month, year);

}

function parseEvents(month, year) {
    let date = new Date(year, month, 1, 0, 0, 0, 0).toISOString().slice(0, 10);
    $.ajax({
        url: 'api/gettutorevents.php',
        type: 'GET',
        data: { date: date },
        success: function(data, textStatus, xhr) {
            if(xhr.status === 200){
                // Answer received
                const obj = JSON.parse(data);
                if(parseInt(obj.error) === 0) {
                    // No errors
                    if(parseInt(obj.lessons_count) > 0) {
                        for (let i = 0; i < parseInt(obj.lessons_count); i++) {
                            let lesson = {
                                'id' : obj.lessons[i].id,
                                'subject' : obj.lessons[i].subject,
                                'subject_name' : obj.lessons[i].subject_name,
                                'starting_date' : obj.lessons[i].starting_date,
                                'duration' : obj.lessons[i].duration,
                                'price' : obj.lessons[i].price,
                                'reservation' : {
                                    'id' : obj.lessons[i].reservation.id
                                }
                            }
                            lessons.push(lesson);
                        }
                        renderEvents();
                    }
                }
                else {
                    // Some error occurred
                    // TODO: Handle the error
                    alert(obj.error_msg);
                }
            }
            else {
                // TODO: Handle the status error
                alert("There was an error retrieving the menu");
            }
        }
    });
}

function renderEvents() {
    lessons.forEach(element => {
        let lessonDiv = document.createElement("div");
        lessonDiv.classList.add("lesson-container");
        lessonDiv.onclick = function() {
            showInfoLesson(element.id);
        }
        let lessonName = document.createElement("p");
        lessonName.innerHTML = '<strong>' + element.starting_date.slice(11, 16) + '</strong> ' + element.subject_name;
        lessonDiv.append(lessonName);
        document.getElementById("container-" + parseInt(element.starting_date.slice(8, 10))).append(lessonDiv);
    })
}

function showInfoLesson(lessonId) {
    $.ajax({
        url: 'api/getlesson.php',
        type: 'GET',
        data: { id: lessonId },
        success: function(data, textStatus, xhr) {
            if(xhr.status === 200){
                // Answer received
                const obj = JSON.parse(data);
                if(parseInt(obj.error) === 0) {
                    // No errors
                    let lesson = {
                        'id' : obj.id,
                        'subject' : obj.subject,
                        'subject_name' : obj.subject_name,
                        'starting_date' : obj.starting_date,
                        'duration' : obj.duration,
                        'price' : obj.price,
                        'reservation' : {
                            'isreserved' : obj.reservation.isreserved
                        }
                    }
                    if(lesson.reservation.isreserved == "0") {
                        // no reservations for this lesson
                        document.getElementById("ex-res-name").innerText = "🟡 Nessuna prenotazione per questa lezione...";
                        document.getElementById("del-les").onclick = function () {
                            deleteLesson(obj.id);
                        }
                        document.getElementById("del-les").style.display = "inline-block";
                    }
                    else {
                        // the lesson is reserved
                        lesson.reservation = {
                            'isreserved' : obj.reservation.isreserved,
                            'id' : obj.reservation.resid,
                            'buyer_email' : obj.reservation.buyer_email,
                            'buyer_phone' : obj.reservation.buyer_phone,
                            'buyer_description' : obj.reservation.buyer_description
                        }
                        document.getElementById("ex-res-name").innerText = "🟢 Prenotazione effettuata da " + lesson.reservation.buyer_email;
                        document.getElementById("ex-res-phone").innerText = lesson.reservation.buyer_phone;
                        document.getElementById("ex-res-desc").innerText = "\"" + lesson.reservation.buyer_description + "\"";
                        document.getElementById("del-les").style.display = "none";
                    }
                    document.getElementById("ex-subject-name").innerHTML = "<strong>" + lesson.subject_name + "</strong>";
                    document.getElementById("ex-date-dur").innerText = lesson.starting_date + " - " + lesson.duration + "min";
                    document.getElementById("ex-price").innerText = (parseInt(lesson.price) / 100) + "€";
                }
                else {
                    // Some error occurred
                    // TODO: Handle the error
                    alert(obj.error_msg);
                }
            }
            else {
                // TODO: Handle the status error
                alert("There was an error retrieving the menu");
            }
        }
    });
    document.getElementById("shadow").style.display = "block";
    document.getElementById("exist-infobox").style.display = "block";
}

function addEvent() {
    document.getElementById("shadow").style.display = "block";
    document.getElementById("event-infobox").style.display = "block";
}

function goToNewLesson() {
    window.location.href = "new-lesson.php";
}

function closeInfoBox() {
    document.getElementById("shadow").style.display = "none";
    //document.getElementById("event-infobox").style.display = "none";
    document.getElementById("exist-infobox").style.display = "none";
}

function openSubjects() {
    window.location.href = "your-subjects.php";
}

function openGains() {
    window.location.href = "gains.php";
}

function openProfile() {
    window.location.href = "profile.php";
}

function deleteLesson(id) {
    $.ajax({
        url: 'api/deletelesson.php',
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
                alert("There was an error retrieving the lesson");
            }
        }
    });
}