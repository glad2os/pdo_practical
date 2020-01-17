const authorsSelects = document.getElementsByName('authors');

request('author/get_all', null, function (status, responseText) {
    if (status === 200) {
        const authors = JSON.parse(responseText);
        authorsSelects.forEach(select => {
            authors.forEach(entry => {
                let option = document.createElement('option');
                option.value = entry.id;
                option.innerText = entry.name;
                select.appendChild(option);
            })
        });
    } else {
        alert(JSON.parse(responseText)['issueMessage']);
    }
});

/* не смогли
const authors_ = document.getElementById('authors');
authors_.parentElement.addEventListener("click",() => {
    document.getElementById('author_name').innerText = authors_.options[authors_.selectedIndex].value;
});
 */

const books = document.getElementById('books');
request('book/get_all', null, function (status, responseText) {
    if (status === 200) {
        JSON.parse(responseText).forEach(entry => {
            let option = document.createElement('option');
            option.value = entry.id;
            option.innerText = entry.title;
            books.appendChild(option);
        });
    } else {
        alert(JSON.parse(responseText)['issueMessage']);
    }
});

// смогли
const authors__ = document.getElementById('book_authors');
books.parentElement.addEventListener("click", () => {
    for (let i = 0; i < authors__.options.length; ++i) {
        authors__.options[i].removeAttribute('selected');
    }
    request('book/get', {
        ['id']: books.options[books.selectedIndex].value
    }, function (status, responseText) {
        if (status === 200) {
            const book = JSON.parse(responseText);
            document.getElementById('book_id').value = book.id;
            document.getElementById('book_title').value = book.title;
            document.getElementById('book_isbn').value = book.isbn;
            document.getElementById('book_price').value = book.price;
            book.authors.forEach(entry => {
                for (let i = 0; i < authors__.options.length; ++i) {
                    if (authors__.options[i].value == entry) {
                        authors__.options[i].setAttribute('selected', 'true');
                    }
                }
            });
        } else {
            alert(JSON.parse(responseText)['issueMessage']);
        }
    });
});
