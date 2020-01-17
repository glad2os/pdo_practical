<?php

namespace Database;

use Exception\DbConnectionException;
use Exception\DbException;
use Config\Database;
use Config\Books;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class PDO extends \PDO
{
    /*
     * Alphabet for token generation
     */
    private const alphabet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    /**
     * MySQLi constructor.
     * @throws DbConnectionException in case of SQL connection error
     */
    public function __construct()
    {
        try {
            parent::__construct(Database::DSN, Database::USER, Database::PASSWORD);
        } catch (\PDOException $e) {
            throw new DbConnectionException('Could not connect to Database server. ' . $e->getMessage());
        }
    }

    /*
     * Books Table
     */

    /**
     * Get Books
     * @param $page
     * @return array (associative)
     * @throws DbException in case of SQL error
     */
    public function getBooks($page)
    {
        $offset = $page * Books\BOOKS_ON_PAGE; // fuck this php!!!
        $limit = Books\BOOKS_ON_PAGE; // fuck this php!!!
        $stmt = $this->prepare("select * from books limit :offset, :limit");
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get Book
     * @param $id
     * @return array (associative)
     * @throws DbException in case of SQL error
     */
    public function getBook($id)
    {
        $stmt = $this->prepare("select * from books where id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get All Books
     * @return array (associative)
     * @throws DbException in case of SQL error
     */
    public function getAllBooks()
    {
        $stmt = $this->prepare("select * from books");
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get Books count
     * @return int
     * @throws DbException in case of SQL error
     */
    public function countOfBooks()
    {
        $stmt = $this->prepare("select count(id) from books");
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
        return $stmt->fetch(\PDO::FETCH_NUM)[0];
    }

    /**
     * Add book
     * @param $title
     * @param $isbn
     * @param $price
     * @return int (book id)
     * @throws DbException in case of SQL error
     */
    public function addBook($title, $isbn, $price)
    {
        $stmt = $this->prepare("insert into books (title, isbn, price) values (:title, :isbn, :price)");
        $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
        $stmt->bindParam(':isbn', $isbn, \PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, \PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
        return $this->lastInsertId();
    }

    /**
     * Update book
     * @param $title
     * @param $id
     * @param $isbn
     * @param $price
     * @throws DbException in case of SQL error
     */
    public function updateBook($id, $title, $isbn, $price)
    {
        $stmt = $this->prepare("update books set title = :title, isbn = :isbn, price = :price where id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
        $stmt->bindParam(':isbn', $isbn, \PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, \PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
    }

    /*
     * Authors Table
     */

    /**
     * Get Authors for Book
     * @param $bookId
     * @return array (associative)
     * @throws DbException in case of SQL error
     */
    public function getAuthorsOfBook($bookId)
    {
        $stmt = $this->prepare("select id, name from authors a left join authors_links al on a.id = al.author_id where al.book_id = :book_id");
        $stmt->bindParam(':book_id', $bookId, \PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get Authors for Book
     * @param $bookId
     * @return array
     * @throws DbException in case of SQL error
     */
    public function getAuthorsIdsOfBook($bookId)
    {
        $stmt = $this->prepare("select author_id from authors_links where book_id = :book_id");
        $stmt->bindParam(':book_id', $bookId, \PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
        $fetch = $stmt->fetchAll(\PDO::FETCH_NUM);
        $result = array();
        array_walk_recursive($fetch, function ($a) use (&$result) {
            $result[] = (int)$a;
        });
        return $result;
    }

    /**
     * Get All Authors
     * @return array (associative)
     * @throws DbException in case of SQL error
     */
    public function getAuthors()
    {
        $stmt = $this->prepare("select * from authors");
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Add author
     * @param $name
     * @throws DbException in case of SQL error
     */
    public function addAuthor($name)
    {
        $stmt = $this->prepare("insert into authors (name) values (:name)");
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
    }

    /**
     * Check author for exist
     * @param $name
     * @return true if exists
     * @throws DbException in case of SQL error
     */
    public function checkAuthor($name)
    {
        $stmt = $this->prepare("select count(id) from authors where name = :name");
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
        return (bool)$stmt->fetch(\PDO::FETCH_NUM)[0];
    }

    /**
     * Update author
     * @param $old
     * @param $new
     * @throws DbException in case of SQL error
     */
    public function updateAuthor($old, $new)
    {
        $stmt = $this->prepare("update authors set name = :new where name = :old");
        $stmt->bindParam(':old', $old, \PDO::PARAM_STR);
        $stmt->bindParam(':new', $new, \PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
    }

    /**
     * Link author to book
     * @param $authorId
     * @param $bookId
     * @throws DbException in case of SQL error
     */
    public function linkAuthor($authorId, $bookId)
    {
        $stmt = $this->prepare("insert into authors_links (author_id, book_id) values (:author_id, :book_id)");
        $stmt->bindParam(':author_id', $authorId, \PDO::PARAM_INT);
        $stmt->bindParam(':book_id', $bookId, \PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
    }

    /**
     * UnLink author to book
     * @param $authorId
     * @param $bookId
     * @throws DbException in case of SQL error
     */
    public function unLinkAuthor($authorId, $bookId)
    {
        $stmt = $this->prepare("delete from authors_links where author_id = :author_id and book_id = :book_id");
        $stmt->bindParam(':author_id', $authorId, \PDO::PARAM_INT);
        $stmt->bindParam(':book_id', $bookId, \PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->errorCode() != '00000') throw new DbException($stmt->errorInfo()[2], $stmt->errorCode());
    }
}