<html>

<head>
    <title>Todo List</title>
    <style type="text/css">
        body {
            padding: 10px;
        }
    </style>
</head>

<body>
    <h1>Todo List</h1>
    <div style="padding-bottom: 10px;">
        <div class="form-group">
            <label for="email">名稱:</label>
            <input type="text" id="t_title" placeholder="請輸入名稱" class="form-control">
        </div>
        <div class="form-group">
            <label for="pwd">內容:</label>
            <input type="text" id="t_content" placeholder="請輸入內容" class="form-control">
        </div>
        <button id="submitButton" class="btn btn-warning my-2">新增</button>
    </div>
    <table id="my-table" class="table table-striped">
        <thead>
            <tr>
                <th>Key</th>
                <th>Step</th>
                <th>Description</th>
                <th></th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div class="row">
        <div class="col-5">
            <div class="input-group">
                <input type="file" class="form-control" id="fileInput" aria-describedby="inputGroupFileAddon04" aria-label="Upload" axxept=".mp3,.mkv,.wma" multiple="multiple">
                <!-- <button class=" btn btn-primary" type="button" id="upload">Button</button> -->
            </div>
        </div>
        <audio class="col-5" id="audio" controls>
            <source src="" id="src" />
        </audio>
    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios@1.1.2/dist/axios.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
<script type=text/javascript>
    $(document).ready(function() {
        todoComponent.index();
    });
    let arr_tmp = [];

    submitButton.addEventListener('click', function() {
        todoComponent.create();
    });

    fileInput.addEventListener('change', function() {
        var file = document.getElementById('fileInput').files[0];
        const source = document.querySelector("source");
        source.setAttribute("src", URL.createObjectURL(file));
        document.getElementById("audio").load();
        // for (var i = 0; i < file.length; i++) {
        //     console.log(111);
        //     var path = URL.createObjectURL(file[i]);
        //     console.log(path);
        //     var audio = document.createElement('audio');
        //     audio.src = path;
        //     audio.controls = true;
        //     audio.volume = '0.2';
        //     audio1.appendChild(audio);

        // }

    });
    let todoComponent = {
        index: function() {
            axios.get('http://localhost:8080/todo')
                .then((response) => {
                    const data = response.data.data;
                    const tableBody = document.querySelector('#my-table tbody');

                    // 清空表格
                    tableBody.innerHTML = '';

                    // data.forEach(item => {
                    for (var i = 0; i < data.length; i++) {
                        const row = document.createElement('tr');
                        const column1 = document.createElement('td');
                        const column2 = document.createElement('td');
                        const column3 = document.createElement('td');
                        const edit = document.createElement('td');
                        column1.innerText = data[i].t_key;
                        column2.innerText = data[i].t_title;
                        column3.innerText = data[i].t_content;

                        const editbutton = document.createElement('button');
                        editbutton.innerText = '編輯';
                        editbutton.className = 'editButton';
                        editbutton.className = 'btn btn-primary mx-2';
                        editbutton.id = data[i].t_key;

                        const deletebutton = document.createElement('button');
                        deletebutton.innerText = '刪除';
                        deletebutton.className = 'deleteButton';
                        deletebutton.className = 'btn btn-danger mx-2';
                        deletebutton.id = data[i].t_key;

                        row.appendChild(column1);
                        row.appendChild(column2);
                        row.appendChild(column3);

                        edit.appendChild(editbutton);
                        edit.appendChild(deletebutton);
                        row.appendChild(edit);

                        // 將行插入到表格的tbody中
                        tableBody.appendChild(row);

                    }
                    let uButtons = document.getElementsByClassName("editButton");
                    for (let i = 0; i < uButtons.length; i++) {
                        uButtons[i].addEventListener("click", (e) => {
                            // console.log(uButtons[i].id);
                            let title = prompt("請輸入名稱:");
                            if (title == null || title == "") {
                                alert("名稱不能為空!");
                            } else {
                                let content = prompt("請輸入內容:");
                                if (content == null || content == "") {
                                    alert("內容不能為空!");
                                } else {
                                    let data = {
                                        "title": title,
                                        "content": content
                                    };
                                    todoComponent.update(uButtons[i].id, data);
                                    alert("更新成功!");
                                    window.location.reload();
                                }
                            }
                        })
                    }
                    let dButtons = document.getElementsByClassName("deleteButton");
                    for (let i = 0; i < dButtons.length; i++) {
                        dButtons[i].addEventListener("click", (e) => {
                            // console.log(dButtons[i].id);
                            todoComponent.delete(dButtons[i].id);
                        })
                    }
                    // });
                })
            // .catch((error) => console.log(error))
        },
        show: function(key) {
            axios.get('http://localhost:8080/todo/' + key)
                .then((response) => console.log(response))
                .catch((error) => console.log(error.response.data.messages.error))
        },
        create: function(data) {
            const t_title = document.getElementById('t_title').value;
            const t_content = document.getElementById('t_content').value;
            data = {
                "title": t_title,
                "content": t_content
            };

            axios.post('http://localhost:8080/todo', JSON.stringify(data))
                .then((response) => {
                    alert('新增成功');
                    window.location.reload();
                })
                .catch((error) => console.log(error.response.data.messages.error))
        },
        update: function(key, data) {
            data = {
                "title": data.title,
                "content": data.content
            };

            axios.put('http://localhost:8080/todo/' + key, JSON.stringify(data))
                .then((response) => console.log(response))
                .catch((error) => console.log(error.response.data.messages.error))
        },
        delete: function(key) {
            axios.delete('http://localhost:8080/todo/' + key)
                .then((response) => {
                    alert('刪除成功');
                    window.location.reload();
                })
                .catch((error) => console.log(error.response.data.messages.error))
        }

    }
</script>

</html>