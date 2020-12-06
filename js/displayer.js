
let table_id = 0;

function displayCard(matrix, id) {
    let table = document.querySelector("#main_table");


    let container = document.createElement("div");
    container.id = id;

    for (let i = 0; i < 3; i++) {

        let tr = document.createElement("tr");

        for (let j = 0; j < 9; j++) {
            let td = document.createElement("td");

            let h1 = document.createElement("h1");

            if (matrix[i][j] == "null") {
                h1.innerHTML = "  ";
            }
            else h1.innerHTML = matrix[i][j];
            td.classList.add("main_td");
            td.id = "td_id_" + table_id;

            td.appendChild(h1);

            tr.appendChild(td);

            table_id++;
        }

        container.appendChild(tr);

    }

    table.appendChild(container);

}

function crossOutNumber(ids) {
    let table = document.querySelector("#main_table");
    let td;

    ids.forEach(id => {
        // let text = td.innerHTML;
        let string = `#td_id_${id}`;
        td = document.querySelector(string);
        // td.appendChild(text);
    });
    // console.log(cosa);
}



