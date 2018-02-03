var TASKS; 

function findTask(tasks, id) {
    if(id > 0 && tasks.length > 0) {
        for(var task of tasks) {
            if(task["id"] == id) {
                return task;
            }
        }
    }
    return null;
}

function deleteTask(id, event) {
    ajax.del("http://localhost:8000/api.php/" + id, {}, function(response) {
        if(response.error) {
            console.error("Couldn't delete the task with id " + id);
        } else {
            event.target.parentNode.parentNode.removeChild(event.target.parentNode);
            console.log("Removed task " + id);
        }
    });
}

function editTask(id, titleInput, contentInput) {
    ajax.put("http://localhost:8000/api.php/" + id,
             {"title": titleInput.value, "content": contentInput.value},
             function(response) {
                 if(!response.error) {
                     var taskContainer = document.querySelector("[data-id=\"" + id + "\"]").parentNode;
                     taskContainer.querySelector("h2").innerText = titleInput.value;
                     taskContainer.querySelector("p").innerText = contentInput.value;

                     titleInput.value = "New Task";
                     contentInput.value = "";
                 }
             }, false);
}

/**
 * Called when the Add button is pressed.
 */
function addTask() {
    var form = document.querySelector("#input");
    var iTitle = form.querySelector("#titleInput");
    var iContent = form.querySelector("#contentInput");

    ajax.post("http://localhost:8000/api.php/", {title: iTitle.value, content: iContent.value}, function(response) {
        if(response.error) {
            console.warn("Error when trying to post a new task: " + response);
        } else {
            iTitle.value = "New Task";
            iContent.value = "";

            var insertArea = document.querySelector("#tasklist");
            console.log(response);
            insertArea.appendChild(makeTaskElement(JSON.parse(response)));
        }
    }, false);
}

function showEditDialog(id) {
    // convert the task submission form to edit form
    var form = document.querySelector("#input");
    var saveButton = form.querySelector("input[type=button]");
    saveButton.setAttribute("value", "Save");

    var task = findTask(TASKS, id);
    var iTitle = form.querySelector("#titleInput");
    var iContent = form.querySelector("#contentInput");

    iTitle.value = task["title"];
    iContent.value = task["content"];
    iContent.placeholder = "Edit your task description here";

    saveButton.onclick = function(event) {
        editTask(id, iTitle, iContent);
        // convert save button back to add button
    };
}

function makeTaskElement(task) {
    var container = document.createElement("div");
    var title = document.createElement("h2");
    var content = document.createElement("p");
    var creationTime = document.createElement("p");

    var delButton = document.createElement("input");
    delButton.setAttribute("value", "delete");
    delButton.setAttribute("data-id", task["id"]);
    delButton.type = "button";

    delButton.addEventListener("click", function(event) {
        var id = event.target.getAttribute("data-id");
        deleteTask(id, event);
    }, false);

    var editButton = document.createElement("input");
    editButton.className = "editButton";
    editButton.setAttribute("value", "edit");
    editButton.setAttribute("data-id", task["id"]);
    editButton.type = "button";

    editButton.addEventListener("click", function(event) {
        var id = event.target.getAttribute("data-id");
        showEditDialog(id);
    });

    container.appendChild(title);
    container.appendChild(content);
    container.appendChild(creationTime);
    container.appendChild(delButton);
    container.appendChild(editButton);

    title.innerText = task["id"] + " | " + task["title"];
    content.innerText = task["content"];
    creationTime.innerText = task["date_created"];

    return container;
}

function loadTasks() {
    ajax.get("http://localhost:8000/api.php/", {}, function(data) {
        if(!data.error) {
            insertArea = document.querySelector("#tasklist");
            TASKS = JSON.parse(data);
            for(var task of TASKS) {
                var container = makeTaskElement(task);
                insertArea.appendChild(container);
            }
        }
    }, false);
}

document.addEventListener("DOMContentLoaded", () => {
    let nojs = document.querySelector("#nojs");
    if(nojs.style == null) {
        nojs.style = {};
    }
    nojs.style.visibility = "hidden";

    loadTasks();
    document.querySelector("#magicButton").addEventListener("click", function(event) {
        addTask();
    });
}, false);
