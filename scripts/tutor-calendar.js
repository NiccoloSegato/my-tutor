let today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();
let selectYear = document.getElementById("year");
let selectMonth = document.getElementById("month");

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
                let cell = document.createElement("td");
                let cellText = document.createTextNode("");
                cell.appendChild(cellText);
                row.appendChild(cell);
            }
            else if (date > daysInMonth) {
                break;
            }

            else {
                let cell = document.createElement("td");
                let cellText = document.createTextNode(date);
                if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                    cell.classList.add("bg-info");
                } // color today's date

                cell.onclick = showSlots;
                cell.appendChild(cellText);
                row.appendChild(cell);
                date++;
            }


        }

        tbl.appendChild(row); // appending each row into calendar body.
    }

}

function showSlots(event) {
    clearSection();
    document.getElementById("date-box").style.display = "block";
    let d = new Date(Date.UTC(selectedYear, selectedMonth, event.target.childNodes[0].data));
    const mysqlDate = d.toJSON().slice(0, 10);
    $.ajax({
        url: 'api/getlessonsfromdate.php',
        type: 'GET',
        data: { id: selectedSubject, date: mysqlDate },
        success: function(data, textStatus, xhr) {
            if(xhr.status === 200){
                // Answer received
                console.log(data);
                const obj = JSON.parse(data);
                if(parseInt(obj.error) === 0) {
                    // No errors
                    if(parseInt(obj.lessons_count) > 0) {
                        for (let i = 0; i < parseInt(obj.lessons_count); i++) {
                            let lesson = {
                                'id' : obj.lessons[i].id,
                                'subject' : obj.lessons[i].subject,
                                'starting_date' : obj.lessons[i].starting_date,
                                'duration' : obj.lessons[i].duration,
                                'price' : obj.lessons[i].price
                            }
                            let lessonDiv = document.createElement("div");
                            lessonDiv.classList.add("date-obj");
                            lessonDiv.onclick = function() {
                                renderSummary(lesson.id);
                            }
                
                            let lessonDate = document.createElement("p");
                            lessonDate.innerText = lesson.starting_date.slice(-8).substring(0, 5);
                            lessonDate.classList.add("lesson_starting_date");

                            let lessonPrice = document.createElement("p");
                            lessonPrice.innerText = (lesson.price / 100) + "€";

                            let lessonDuration = document.createElement("p");
                            lessonDuration.innerText = 'Durata: ' + lesson.duration + ' minuti';
                            lessonDuration.classList.add("lesson-duration-label");
                
                            lessonDiv.append(lessonDate);
                            lessonDiv.append(lessonDuration);
                            lessonDiv.append(lessonPrice);
                            document.getElementById("date-selector").append(lessonDiv);
                        }
                    }
                    else {
                        // No lessons
                        let lessonDiv = document.createElement("div");
                        lessonDiv.classList.add("date-obj");
                
                        let lessonDate = document.createElement("p");
                        lessonDate.innerHTML = "Nessuna lezione disponibile</br>per questa data";
                
                        lessonDiv.append(lessonDate);
                        document.getElementById("date-selector").append(lessonDiv);
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

function renderSummary(lessonId) {
    $.ajax({
        url: 'api/get-lesson.php',
        type: 'GET',
        data: { id: lessonId },
        success: function(data, textStatus, xhr) {
            if(xhr.status === 200){
                // Answer received
                console.log(data);
                const obj = JSON.parse(data);
                if(parseInt(obj.error) === 0) {
                    // No errors
                    let lesson = {
                        'id' : obj.id,
                        'subject' : obj.subject,
                        'subject_name' : obj.subject_name,
                        'grade' : obj.grade,
                        'tutor': obj.teacher_name,
                        'datetime' : obj.starting_date,
                        'duration' : obj.duration,
                        'price' : obj.price
                    }
                    document.getElementById("sum-lesson-name").innerText = lesson.subject_name;
                    switch (lesson.grade) {
                        case 0:
                            document.getElementById("sum-grade-name").innerText = "Elementari";
                            break;
                        case 1:
                            document.getElementById("sum-grade-name").innerText = "Medie";
                            break;
                        case 2:
                            document.getElementById("sum-grade-name").innerText = "Superiori";
                            break;
                        case 3:
                            document.getElementById("sum-grade-name").innerText = "Università";
                            break;
                        default:
                            break;
                    }
                    document.getElementById("sum-tutor-name").innerText = lesson.tutor;
                    document.getElementById("sum-duration-name").innerText = 'Durata: ' + lesson.duration + ' minuti';
                    // Split timestamp into [ Y, M, D, h, m, s ]
                    var t = lesson.datetime.split(/[- :]/);
                    var d = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));
                    document.getElementById("sum-date-name").innerHTML = "Il <strong>" + d.getDate() + " " + months[d.getMonth()] + " " + d.getFullYear() + "</strong> dalle <strong>" + d.getHours() + ":" + d.getMinutes() + "</strong>";
                    document.getElementById("sum-lesson-price").innerText = (lesson.price / 100) + '€ - Prezzo della lezione';
                    document.getElementById("sum-commission-price").innerText = "2€ - Commissioni";
                    document.getElementById("sum-total-price").innerText = ((lesson.price + 200) / 100) + "€ - Totale";
                    document.getElementById("sum-confirm-btn").onclick = function() {
                        $.ajax({
                            url: 'api/set-reservation.php',
                            type: 'POST',
                            data: { lessonid: lessonId },
                            success: function(data, textStatus, xhr) {
                                if(xhr.status === 200){
                                    // Answer received
                                    console.log(data);
                                    const obj = JSON.parse(data);
                                    if(parseInt(obj.error) === 0) {
                                        window.location.href = "confirm-reservation.php?id=" + obj.reservation_id;
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
                    document.getElementById("summary-slot").style.display = "block";
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