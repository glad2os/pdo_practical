function request(target, body, callback) {
    const request = new XMLHttpRequest();
    request.open("POST", "/api/" + target, true);
    request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
    request.onreadystatechange = function () {
        if (request.readyState === XMLHttpRequest.DONE) {
            callback(request.status, request.responseText);
        }
    };
    request.send(JSON.stringify(body));
}

function addBook(form) {
    const authors = [].map.call(form.getElementsByClassName("ui label"), function (e) {
        return Number(e.getAttribute('data-value'))
    });
    request('book/add',
        {
            ['title']: form.title.value,
            ['isbn']: form.isbn.value,
            ['price']: form.price.value,
            ['authors']: authors
        },
        function (status, responseText) {
            if (status === 200) {
                window.location.href = '/admin';
            } else {
                console.log(responseText);
                alert(JSON.parse(responseText)['issueMessage']);
            }
        });
}

function deleteBook(form) {

    request('book/delete',
        {
            ['id']: books2.options[books2.selectedIndex].value
        },
        function (status, responseText) {
            if (status === 200) {
                window.location.href = '/admin';
            } else {
                console.log(responseText);
                alert(JSON.parse(responseText)['issueMessage']);
            }
        });
}

function updateBook(form) {
    let authors = [];
    for (let i = 0; i < form.authors.options.length; ++i) {
        if (form.authors.options[i].selected) {
            authors.push(Number.parseInt(form.authors.options[i].value));
        }
    }
    request('book/update',
        {
            ['id']: form.id.value,
            ['title']: form.title.value,
            ['isbn']: form.isbn.value,
            ['price']: form.price.value,
            ['authors']: authors
        },
        function (status, responseText) {
            if (status === 200) {
                window.location.href = '/admin';
            } else {
                console.log(responseText);
                alert(JSON.parse(responseText)['issueMessage']);
            }
        });
}

function addAuthor(form) {
    request('author/add',
        {
            ['name']: form.name.value
        },
        function (status, responseText) {
            if (status === 200) {
                window.location.href = '/admin';
            } else {
                console.log(responseText);
                alert(JSON.parse(responseText)['issueMessage']);
            }
        });
}

function deleteAuthor() {
    let author = $('#test').dropdown('get text');
    request('author/delete',
        {
            ['name']: author
        },
        function (status, responseText) {
            if (status === 200) {
                window.location.href = '/admin';
            } else {
                console.log(responseText);
                alert(JSON.parse(responseText)['issueMessage']);
            }
        });
}

function updateAuthor(form) {
    request('author/update',
        {
            ['old']: form.old.value,
            ['new']: form.new.value
        },
        function (status, responseText) {
            if (status === 200) {
                window.location.href = '/admin';
            } else {
                console.log(responseText);
                alert(JSON.parse(responseText)['issueMessage']);
            }
        });
}

