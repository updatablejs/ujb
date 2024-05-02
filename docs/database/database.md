

<h1>Database</h1>

<div class="example">
<h3>Database object instantiation</h3>
<p></p>
<pre>
use ujb\database\Database,
	ujb\database\adapters\pdo\Driver;

$database = new Database(new Driver([
	'host' => '',
	'username' => '',
	'password' => '',
	'name' => ''
]));

// To close the connection we use the "close" method.
// $database->close();
</pre>
</div>


<div class="example">
<h3>A simple query</h3>
<pre>
// We get a "ujb\database\statement\Statement" object.
$statement = $database->prepare('SELECT * FROM albums');
	
// We execute the query and obtain a "ujb\database\result\Result" object.
$result = $statement->execute();

// The result can be accessed like this.
while ($entity = $result->fetch()) {
	printr($entity);
}

// Or using a foreach loop 
foreach ($result as $key => $entity) {
	printr($key);
	printr($entity);
}

// We can transform the entire result into an array like this.
$array = $result->fetchAll();

// We can also execute a query directly without having to call the "prepare" and "execute" methods.
$result = $database->query('SELECT * FROM albums');
</pre>
</div>


<div class="example">
<h3>Using parameters</h3>
<pre>
// It is possible to set the parameters in the "prepare" method call.
$statement = $database->prepare('SELECT * FROM albums WHERE album_id = :id', ['id' => 1]);

// We can also set parameters using the "setParams" or "setParam" functions.
$statement = $database->prepare('SELECT * FROM albums WHERE album_id = :id');
$statement->setParam('id', 1);

// If you want, you can also use question mark placeholders.
$statement = $database->prepare('SELECT * FROM albums WHERE album_id = ?', [1]);

// Set parameter using "setParam" function.
$statement = $database->prepare('SELECT * FROM albums WHERE album_id = ?');
$statement->setParam(1);

// Multiple parameters can be used
$statement = $database->prepare(
	'SELECT * FROM albums 
		WHERE name = :name AND date_created = :date_created');
		
$statement->setParams([
	'name' => '',
	'date_created' => ''
]);

//  Multiple parameters with question mark placeholders.
$statement = $database->prepare(
	'SELECT * FROM albums 
		WHERE name = ? AND date_created = ?');
		
$statement->setParams([
	'name', 'date_created'
]);
</pre>
</div>


<div class="example">
<h3>Reusing the statement object</h3>
<pre>
$statement = $database->prepare('SELECT * FROM albums WHERE album_id = ?');

// Select the first album.
$statement->setParams([1]);
$result = $statement->execute()->fetchAll();
Array
(
    [0] => Array
        (
            [album_id] => 1
            [name] => The Wild Places
            [date_created] => 2024-03-01
        )

)

// Select the second album.
$statement->setParams([2]);
$result = $statement->execute()->fetchAll();
Array
(
    [0] => Array
        (
            [album_id] => 1
            [name] => The Wild Places
            [date_created] => 2024-03-01
        )

)
</pre>
</div>


<div class="example">
<h3>Get last insert Id</h3>
<pre>
$statement = $database->prepare('INSERT INTO albums (name) VALUES (?)');
$statement->setParams(['On the Road']);
$result = $statement->execute();
$lastInsertId = $database->getLastInsertId();

// To check if the query was successful, we can use the "count" method of the "result" object
if ($result->count()) {

}
</pre>
</div>


<div class="example">
<h3>Query information</h3>
<pre>
$database->query('SELECT * FROM albums');
$database->query('SELECT * FROM users');
$database->query('SELECT * FROM photos');

// You can get the total execution time of all queries.
$database->getExecutionTime();

// Show all queries.
$queries = $database->getQueries();

Array
(
    [0] => ujb\database\statement\QueryInfo Object
        (
            [query] => SELECT * FROM albums
            [executionTime] => 0.0099999904632568
            [fromCache] => 
        )

    [1] => ujb\database\statement\QueryInfo Object
        (
            [query] => SELECT * FROM users
            [executionTime] => 0
            [fromCache] => 
        )

    [2] => ujb\database\statement\QueryInfo Object
        (
            [query] => SELECT * FROM photos WHERE 1 =1 
            [executionTime] => 0
            [fromCache] => 
        )

)
</pre>
</div>


<div class="example">
<h3>Transactions</h3>
<p>To perform a transaction, the "begin Transaction", "rollBack", "commit" methods of the "ujb\database\Database" class can be used. Another possibility to perform a transaction is the "transaction" method together with a callback function.</p>
<pre>
$database->transaction(function() {
	// ...
});
</pre>
</div>
