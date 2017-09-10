<?php
include_once("IModel.php");
include_once("Book.php");

/** The Model is the class holding data about a collection of books.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */
class DBModel implements IModel
{
    /**
      * The PDO object for interfacing the database
      *
      */
    protected $db = null;

    /**
	 * @throws PDOException
     */
    public function __construct($db = null)
    {
	    if ($db)
		{
			$this->db = $db;
		}
		else {
      $this->db = new PDO('mysql:host=localhost;dbname=oblig1;charset=utf8mb4', 'root', '');
		}
    }

    /** Function returning the complete list of books in the collection. Books are
     * returned in order of id.
     * @return Book[] An array of book objects indexed and ordered by their id.
	 * @throws PDOException
     */
    public function getBookList()
    {
      $bokliste = [];

      $prepared = $this->db->prepare('SELECT * FROM bokhylle');
      $prepared->execute();
      while ($row = $prepared->fetchObject()) {
        $bokliste[] = new Book($row->Title, $row->Author, $row->Description, $row->id);

      }
      return $bokliste;

    }

    /** Function retrieving information about a given book in the collection.
     * @param integer $id the id of the book to be retrieved
     * @return Book|null The book matching the $id exists in the collection; null otherwise.
	 * @throws PDOException
     */
    public function getBookById($id)
    {
    if(!is_numeric($id))
      return null;

		$book = null;
    $prepared = $this->db->prepare('SELECT *
       FROM bokhylle
       WHERE id=:id');
    $prepared->execute([':id'=>$id]);

    $row = $prepared->fetchObject();

    if($row)
      $book = new Book($row->Title, $row->Author, $row->Description, $row->id);

    return $book;
    }

    /** Adds a new book to the collection.
     * @param $book Book The book to be added - the id of the book will be set after successful insertion.
	 * @throws PDOException
     */
    public function addBook($book)
    {
      if(empty($book->title) || empty($book->author))
        throw new Exception("Author and Title must be filled in");

      $prepared = $this->db->prepare('INSERT INTO bokhylle (Title, Author, Description)
        VALUES(:Title, :Author, :Description )');


      $prepared->execute([

        ':Title' => $book->title,
        ':Author' => $book->author,
        ':Description' => $book->description
      ]);
      $book->id = $this->db->lastInsertId();

    }

    /** Modifies data related to a book in the collection.
     * @param $book Book The book data to be kept.
     * @todo Implement function using PDO and a real database.
     */
    public function modifyBook($book)
    {
      if(empty($book->title) || empty($book->author))
        throw new Exception("Author and Title must be filled in");
      $prepared = $this->db->prepare('UPDATE bokhylle
        SET
          Title=:Title,
          Author=:Author,
          Description=:Description
        WHERE id=:id');

      $prepared->execute([

        ':Title' => $book->title,
        ':Author' => $book->author,
        ':Description' => $book->description,
        ':id' => $book->id
      ]);
    }

    /** Deletes data related to a book from the collection.
     * @param $id integer The id of the book that should be removed from the collection.
     */
    public function deleteBook($id)
    {
      if(!is_numeric($id))
        return null;
      $prepared = $this->db->prepare('DELETE FROM bokhylle
        WHERE id=?');
      $prepared->execute([$id]);
    }

}

?>
