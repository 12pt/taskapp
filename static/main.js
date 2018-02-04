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
function addTask(event) {
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
            insertArea.appendChild(makeTaskElement(JSON.parse(response)));
        }
    }, false);
}

function showEditDialog(id) {
    // convert the task submission form to edit form
    var form = document.querySelector("#input");
    var saveButton = form.querySelector("#magicButton");
    form.removeChild(saveButton);

    var edit = document.createElement("input");
    edit.type = "button";
    edit.setAttribute("value", "Save");
    form.appendChild(edit);

    var task = findTask(TASKS, id);
    var iTitle = form.querySelector("#titleInput");
    var iContent = form.querySelector("#contentInput");

    iTitle.value = task["title"];
    iContent.value = task["content"];
    iContent.placeholder = "Edit your task description here";

    edit.addEventListener("click", function() {
        editTask(id, iTitle, iContent);
        form.removeChild(edit);
        form.appendChild(saveButton);
    });
}

function makeTaskElement(task) {
    var container = document.createElement("div");
    container.className = "task";
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

    title.innerText = task["title"];
    content.innerText = task["content"];
    creationTime.innerText = task["date_created"];

    return container;
}

function deleteChildren(node) {
    while(node.firstChild) {
        node.removeChild(node.firstChild);
    }
}

function loadTasks() {
    ajax.get("http://localhost:8000/api.php/", {}, function(data) {
        var warning = document.querySelector("#warning");
        data = JSON.parse(data);

        if(!data.error) {
            warning.style.visibility = "hidden";
            warning.style.display = "none";

            insertArea = document.querySelector("#tasklist");
            deleteChildren(insertArea);
            TASKS = data;
            if(TASKS.length > 0) {
                for(var task of TASKS) {
                    var container = makeTaskElement(task);
                    insertArea.appendChild(container);
                }
            } else {
                warning.querySelector("p").innerText = "No tasks! Make some?";
            }
        } else {
            // turn on the warning div
            if(warning) {
                warning.querySelector("p").innerText = "An error occured when trying to load your tasks. Are you connected to the database?";
                warning.style.visibility = "show";
                warning.style.display = "block";
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
    document.querySelector("#magicButton").addEventListener("click", addTask);
}, false);
