

<h1>Assembler</h1>

<p>
	The structure can be of the form: "table1.table2*|junction". The entities from table 2 will be added to the entity from table 1 in the field named "table2". If it is desired for these entities (from table 2) to be added into a different field, then a new location can be added as follows: 'table1.table2*(location)'.
</p>
<p>
	The * character indicates that there are multiple entities from table 2. These entities will be added to the entity from table 1 in an array with entities. Without the * character, only a single entity from table 2 will be added to the entity from table 1 and not an array with entities.
</p>
<p>
	If a junction is used in the union between table 1 and table 2, then the junction table name will be added as follows: 'table1.table2*(location)|junction'
</p>
<p>
	Multiple structures can be added: ['user.salaries(salary)', 'user.photos(avatar)']
</p>

<pre>
$assembler = [
	'structure' => 'table1.table2',
	'structure' => 'table1.table2*',
	'structure' => 'table1.table2*|junction',
	'structure' => 'table1.table2*(location)|junction',
	'structure' => 'table1.table2*(location)|junction.table3',
	'structure' => ['table1.table2*(location)|junction', ...]
			
		
	// For each unique field we will have an entity.	
	'groups' => string field, 
		
	// For each unique field we will have either an entity or an array of entities depending on the isCollection value.
	'groups' => [string field, bool isColection], 
				
	// For each unique value returned by the function we will have an entity.
	'groups' => function($values) {
		return value;
	},
		
	// For each unique value returned by the function we will have either an entity or an array of entities depending on the isCollection value.		
	'groups' => [function($values) {
		return value;
	}, bool isColection]
		
	// If the query uses several tables, we set the groups like this. 
	// The character * indicates that we have an array with entities.
	'groups' => [
		table* => string field, // * isColection 
					
		table => string field,
					
		table => function($values) {
			return value;
		}
	],
			
			
	'interceptors' => function($values) {				
		return $values;
	},
			
	'interceptors' => [
		table => function($values) {				
			return $values;
		}
	]
];
</pre>

<h2>Examples</h2>

<p>You can find the database used in these examples in the assembler.sql file in the same directory.</p>

<pre>
// This file contains some global functions
require('ujb/functions/common.php');

// You can use your own autoloader
spl_autoload_register(function ($class) {
	require $class . '.php';
});

use ujb\database\Database,
	ujb\database\adapters\pdo\Driver;

$database = new Database(new Driver([
	'host' => '',
	'username' => '',
	'password' => '',
	'name' => ''
]));
</pre>

<p>If you don't want to use this class "ujb\database\Database" you can use the "\PDO" class, there is an example at the end of this page.</p>

<div>
<h3>Group albums by name in collections</h3>
<p>For each name we will have a list of albums.</p>
<pre>
$result = $database->prepare('SELECT * FROM albums')
	->setAssembler([
		'groups' => ['name', true],
	])

	->execute();

printr($result->fetchAll());

/*
while ($entity = $result->fetch()) {
	printr($entity);
}

foreach ($result as $key => $entity) {
	printr($key);
	printr($entity);
}
*/
</pre>

<pre>
Array
(
    [The Wild Places] => Array
        (
            [0] => Array
                (
                    [album_id] => 1
                    [name] => The Wild Places
                    [date_created] => 2024-03-01
                )

        )

    [Wherever We Go] => Array
        (
            [0] => Array
                (
                    [album_id] => 2
                    [name] => Wherever We Go
                    [date_created] => 2024-03-02
                )

            [1] => Array
                (
                    [album_id] => 7
                    [name] => Wherever We Go
                    [date_created] => 2024-03-04
                )

        )

    [On the Road] => Array
        (
            [0] => Array
                (
                    [album_id] => 3
                    [name] => On the Road
                    [date_created] => 2024-03-03
                )

        )
)	
</pre>
</div>


<div>
<h3>Group albums by name</h3>
<p>For each name we will have an album.</p>
<pre>
$database->prepare('SELECT * FROM albums')
	->setAssembler([
		'groups' => 'name',
	])

	->execute();
</pre>

<pre>
Array
(
    [The Wild Places] => Array
        (
            [album_id] => 1
            [name] => The Wild Places
            [date_created] => 2024-03-01
        )

    [Wherever We Go] => Array
        (
            [album_id] => 7
            [name] => Wherever We Go
            [date_created] => 2024-03-04
        )

    [On the Road] => Array
        (
            [album_id] => 3
            [name] => On the Road
            [date_created] => 2024-03-03
        )
}
</pre>
</div>


<div>
<h3>Group albums in collections using a function</h3>
<p>For each unique result of the function we will have a list of albums.</p>
<pre>
$database->prepare('SELECT * FROM albums')
	->setAssembler([
		'groups' => [function($values) {
			return str_replace('-', '/', $values['date_created']);
		}, true],
	])

	->execute();
</pre>

<pre>
Array
(
    [2024/03/01] => Array
        (
            [0] => Array
                (
                    [album_id] => 1
                    [name] => The Wild Places
                    [date_created] => 2024-03-01
                )

            [1] => Array
                (
                    [album_id] => 4
                    [name] => See the World
                    [date_created] => 2024-03-01
                )

            [2] => Array
                (
                    [album_id] => 5
                    [name] => Lessons From a Passport
                    [date_created] => 2024-03-01
                )

        )

    [2024/03/02] => Array
        (
            [0] => Array
                (
                    [album_id] => 2
                    [name] => Wherever We Go
                    [date_created] => 2024-03-02
                )

            [1] => Array
                (
                    [album_id] => 6
                    [name] => A Weekend in Colorado
                    [date_created] => 2024-03-02
                )

        )
		
    [2024/03/04] => Array
        (
            [0] => Array
                (
                    [album_id] => 7
                    [name] => Wherever We Go
                    [date_created] => 2024-03-04
                )

        )
)
</pre>
</div>


<div>
<h3>Select albums and change their values using an interceptor</h3>
<p></p>
<pre>
$database->prepare('SELECT * FROM albums')
	->setAssembler([
		'interceptors' => function($values) {
			$values['name'] = strtoupper($values['name']);
			
			return $values;
		}
	])

	->execute();
</pre>

<pre>
Array
(
    [0] => Array
        (
            [album_id] => 1
            [name] => THE WILD PLACES
            [date_created] => 2024-03-01
        )

    [1] => Array
        (
            [album_id] => 2
            [name] => WHEREVER WE GO
            [date_created] => 2024-03-02
        )

    [2] => Array
        (
            [album_id] => 3
            [name] => ON THE ROAD
            [date_created] => 2024-03-03
        )
)
</pre>
</div>


<div>
<h3>Using structure in a one to many relationship</h3>
<p>We are using this structure "albums.photos*" to indicate that an album has many photos.</p>
<pre>
$database->prepare(
		'SELECT * FROM albums 
			LEFT JOIN photos ON albums.album_id = photos.album_id'
	)
	
	->setAssembler([
		'structure' => 'albums.photos*',
	])

	->execute();
</pre>

<pre>
Array
(
    [0] => Array
        (
            [album_id] => 1
            [name] => The Wild Places
            [date_created] => 2024-03-01
            [photos] => Array
                (
                    [0] => Array
                        (
                            [photo_id] => 1
                            [album_id] => 1
                            [file] => the_wild_places_1.jpeg
                        )

                    [1] => Array
                        (
                            [photo_id] => 2
                            [album_id] => 1
                            [file] => the_wild_places_2.jpeg
                        )

                    [2] => Array
                        (
                            [photo_id] => 3
                            [album_id] => 1
                            [file] => the_wild_places_3.jpeg
                        )

                )

        )

    [1] => Array
        (
            [album_id] => 2
            [name] => Wherever We Go
            [date_created] => 2024-03-02
            [photos] => Array
                (
                    [0] => Array
                        (
                            [photo_id] => 4
                            [album_id] => 2
                            [file] => wherever_we_go_1.jpeg
                        )

                    [1] => Array
                        (
                            [photo_id] => 5
                            [album_id] => 2
                            [file] => wherever_we_go_2.jpeg
                        )

                )

        )

    [2] => Array
        (
            [album_id] => 3
            [name] => On the Road
            [date_created] => 2024-03-03
            [photos] => Array
                (
                    [0] => Array
                        (
                            [photo_id] => 6
                            [album_id] => 3
                            [file] => on_the_road_1.jpeg
                        )

                )

        )

    [3] => Array
        (
            [album_id] => 4
            [name] => See the World
            [date_created] => 2024-03-01
            [photos] => Array
                (
                )

        )
)
</pre>
</div>


<div>
<h3>Structure, groups and interceptors in a one to many relationship</h3>
<p>We use the same example as above, but now we add some additional grouping specifications and an interceptor. We are grouping albums by name in collections (* indicate that for each unique album name we have a collection of albums) and we group photos by file name. We are also using an interceptor to change the photo file name.
</p>
<pre>
$database->prepare(
		'SELECT * FROM albums 
			LEFT JOIN photos ON albums.album_id = photos.album_id'
	)
	
	->setAssembler([
		'structure' => 'albums.photos*',
		
		'groups' => [
			'albums*' => 'name',
			'photos' => 'file'
		], 
		
		'interceptors' => [
			'photos' => function($values) {
				$values['file'] = 'images/' . $values['file'];
				
				return $values;
			}
		]
	])

	->execute();
</pre>

<pre>
Array
(
    [The Wild Places] => Array
        (
            [0] => Array
                (
                    [album_id] => 1
                    [name] => The Wild Places
                    [date_created] => 2024-03-01
                    [photos] => Array
                        (
                            [the_wild_places_1.jpeg] => Array
                                (
                                    [photo_id] => 1
                                    [album_id] => 1
                                    [file] => images/the_wild_places_1.jpeg
                                )

                            [the_wild_places_2.jpeg] => Array
                                (
                                    [photo_id] => 2
                                    [album_id] => 1
                                    [file] => images/the_wild_places_2.jpeg
                                )

                            [the_wild_places_3.jpeg] => Array
                                (
                                    [photo_id] => 3
                                    [album_id] => 1
                                    [file] => images/the_wild_places_3.jpeg
                                )

                        )

                )

        )

    [Wherever We Go] => Array
        (
            [0] => Array
                (
                    [album_id] => 2
                    [name] => Wherever We Go
                    [date_created] => 2024-03-02
                    [photos] => Array
                        (
                            [wherever_we_go_1.jpeg] => Array
                                (
                                    [photo_id] => 4
                                    [album_id] => 2
                                    [file] => images/wherever_we_go_1.jpeg
                                )

                            [wherever_we_go_2.jpeg] => Array
                                (
                                    [photo_id] => 5
                                    [album_id] => 2
                                    [file] => images/wherever_we_go_2.jpeg
                                )

                        )

                )

            [1] => Array
                (
                    [album_id] => 7
                    [name] => Wherever We Go
                    [date_created] => 2024-03-04
                    [photos] => Array
                        (
                        )

                )

        )

    [On the Road] => Array
        (
            [0] => Array
                (
                    [album_id] => 3
                    [name] => On the Road
                    [date_created] => 2024-03-03
                    [photos] => Array
                        (
                            [on_the_road_1.jpeg] => Array
                                (
                                    [photo_id] => 6
                                    [album_id] => 3
                                    [file] => images/on_the_road_1.jpeg
                                )

                        )

                )

        )

    [See the World] => Array
        (
            [0] => Array
                (
                    [album_id] => 4
                    [name] => See the World
                    [date_created] => 2024-03-01
                    [photos] => Array
                        (
                        )

                )

        )
)
</pre>
</div>


<div>
<h3>Many to many relationship</h3>
<p>We are using this structure 'users.albums*|users_albums' to indicate that a user has may albums. In this query we are useing a junction table "users_albums". Each user can have many albums and each album can belong to many users.</p>
<pre>
$database->prepare(
		'SELECT * FROM users 
			LEFT JOIN users_albums ON users.user_id = users_albums.user_id 
			LEFT JOIN albums ON users_albums.album_id = albums.album_id'
	)
	
	->setAssembler([
		'structure' => 'users.albums*|users_albums',
	])

	->execute();
</pre>

<pre>
Array
(
    [0] => Array
        (
            [user_id] => 1
            [first_name] => Daleyza 
            [last_name] => Waller
            [albums] => Array
                (
                    [0] => Array
                        (
                            [album_id] => 1
                            [name] => The Wild Places
                            [date_created] => 2024-03-01
                            [junction] => Array
                                (
                                    [user_id] => 1
                                    [album_id] => 1
                                )

                        )

                    [1] => Array
                        (
                            [album_id] => 2
                            [name] => Wherever We Go
                            [date_created] => 2024-03-02
                            [junction] => Array
                                (
                                    [user_id] => 1
                                    [album_id] => 2
                                )

                        )

                )

        )

    [1] => Array
        (
            [user_id] => 2
            [first_name] => Marley
            [last_name] => Bowen
            [albums] => Array
                (
                    [0] => Array
                        (
                            [album_id] => 1
                            [name] => The Wild Places
                            [date_created] => 2024-03-01
                            [junction] => Array
                                (
                                    [user_id] => 2
                                    [album_id] => 1
                                )

                        )

                    [1] => Array
                        (
                            [album_id] => 2
                            [name] => Wherever We Go
                            [date_created] => 2024-03-02
                            [junction] => Array
                                (
                                    [user_id] => 2
                                    [album_id] => 2
                                )

                        )

                    [2] => Array
                        (
                            [album_id] => 3
                            [name] => On the Road
                            [date_created] => 2024-03-03
                            [junction] => Array
                                (
                                    [user_id] => 2
                                    [album_id] => 3
                                )

                        )

                    [3] => Array
                        (
                            [album_id] => 4
                            [name] => See the World
                            [date_created] => 2024-03-01
                            [junction] => Array
                                (
                                    [user_id] => 2
                                    [album_id] => 4
                                )

                        )

                )

        )
)
</pre>
</div>


<div>
<h3>One to one relationship</h3>
<p>We are using this structure "users.salaries(salary)" to indicate that each user has one salary. We change the name of the field where the entity of the table "salaries" will be added, from "salaries" to "salary". This change is not necessary, we can leave things unchanged like this "users.salaries"</p>
<pre>
$database->prepare(
		'SELECT * FROM users 
			LEFT JOIN salaries ON users.user_id = salaries.user_id'
	)
	
	->setAssembler([
		'structure' => 'users.salaries(salary)',
	])

	->execute();
</pre>

<pre>
Array
(
    [0] => Array
        (
            [user_id] => 1
            [first_name] => Daleyza 
            [last_name] => Waller
            [salary] => Array
                (
                    [salary_id] => 1
                    [user_id] => 1
                    [salary] => 50
                )

        )

    [1] => Array
        (
            [user_id] => 2
            [first_name] => Marley
            [last_name] => Bowen
            [salary] => Array
                (
                    [salary_id] => 2
                    [user_id] => 2
                    [salary] => 100
                )

        )

    [2] => Array
        (
            [user_id] => 3
            [first_name] => Sasha
            [last_name] => Duffy
            [salary] => Array
                (
                    [salary_id] => 3
                    [user_id] => 3
                    [salary] => 150
                )

        )

    [3] => Array
        (
            [user_id] => 4
            [first_name] => Robin
            [last_name] => Soto
            [salary] => 
        )
)
</pre>
</div>


<div>
<h3>Using multiple structures</h3>
<p></p>
<pre>
$database->prepare(
		'SELECT * FROM users 
			
			LEFT JOIN salaries ON users.user_id = salaries.user_id
			
			LEFT JOIN users_albums ON users.user_id = users_albums.user_id 
			LEFT JOIN albums ON users_albums.album_id = albums.album_id
			
			LEFT JOIN photos ON albums.album_id = photos.album_id'
	)
	
	->setAssembler([
		'structure' => ['users.salaries(salary)', 'users.albums*|users_albums.photos*'],
	])

	->execute();
</pre>

<pre>
Array
(
    [0] => Array
        (
            [user_id] => 1
            [first_name] => Daleyza 
            [last_name] => Waller
            [salary] => Array
                (
                    [salary_id] => 1
                    [user_id] => 1
                    [salary] => 50
                )

            [albums] => Array
                (
                    [0] => Array
                        (
                            [album_id] => 1
                            [name] => The Wild Places
                            [date_created] => 2024-03-01
                            [junction] => Array
                                (
                                    [user_id] => 1
                                    [album_id] => 1
                                )

                            [photos] => Array
                                (
                                    [0] => Array
                                        (
                                            [photo_id] => 1
                                            [album_id] => 1
                                            [file] => the_wild_places_1.jpeg
                                        )

                                    [1] => Array
                                        (
                                            [photo_id] => 2
                                            [album_id] => 1
                                            [file] => the_wild_places_2.jpeg
                                        )

                                    [2] => Array
                                        (
                                            [photo_id] => 3
                                            [album_id] => 1
                                            [file] => the_wild_places_3.jpeg
                                        )

                                )

                        )

                    [1] => Array
                        (
                            [album_id] => 2
                            [name] => Wherever We Go
                            [date_created] => 2024-03-02
                            [junction] => Array
                                (
                                    [user_id] => 1
                                    [album_id] => 2
                                )

                            [photos] => Array
                                (
                                    [0] => Array
                                        (
                                            [photo_id] => 4
                                            [album_id] => 2
                                            [file] => wherever_we_go_1.jpeg
                                        )

                                    [1] => Array
                                        (
                                            [photo_id] => 5
                                            [album_id] => 2
                                            [file] => wherever_we_go_2.jpeg
                                        )

                                )

                        )

                )

        )

    [1] => Array
        (
            [user_id] => 2
            [first_name] => Marley
            [last_name] => Bowen
            [salary] => Array
                (
                    [salary_id] => 2
                    [user_id] => 2
                    [salary] => 100
                )

            [albums] => Array
                (
                    [0] => Array
                        (
                            [album_id] => 1
                            [name] => The Wild Places
                            [date_created] => 2024-03-01
                            [junction] => Array
                                (
                                    [user_id] => 2
                                    [album_id] => 1
                                )

                            [photos] => Array
                                (
                                    [0] => Array
                                        (
                                            [photo_id] => 1
                                            [album_id] => 1
                                            [file] => the_wild_places_1.jpeg
                                        )

                                    [1] => Array
                                        (
                                            [photo_id] => 2
                                            [album_id] => 1
                                            [file] => the_wild_places_2.jpeg
                                        )

                                    [2] => Array
                                        (
                                            [photo_id] => 3
                                            [album_id] => 1
                                            [file] => the_wild_places_3.jpeg
                                        )

                                )

                        )

                    [1] => Array
                        (
                            [album_id] => 2
                            [name] => Wherever We Go
                            [date_created] => 2024-03-02
                            [junction] => Array
                                (
                                    [user_id] => 2
                                    [album_id] => 2
                                )

                            [photos] => Array
                                (
                                    [0] => Array
                                        (
                                            [photo_id] => 4
                                            [album_id] => 2
                                            [file] => wherever_we_go_1.jpeg
                                        )

                                    [1] => Array
                                        (
                                            [photo_id] => 5
                                            [album_id] => 2
                                            [file] => wherever_we_go_2.jpeg
                                        )

                                )

                        )

                    [2] => Array
                        (
                            [album_id] => 3
                            [name] => On the Road
                            [date_created] => 2024-03-03
                            [junction] => Array
                                (
                                    [user_id] => 2
                                    [album_id] => 3
                                )

                            [photos] => Array
                                (
                                    [0] => Array
                                        (
                                            [photo_id] => 6
                                            [album_id] => 3
                                            [file] => on_the_road_1.jpeg
                                        )

                                )

                        )

                    [3] => Array
                        (
                            [album_id] => 4
                            [name] => See the World
                            [date_created] => 2024-03-01
                            [junction] => Array
                                (
                                    [user_id] => 2
                                    [album_id] => 4
                                )

                            [photos] => Array
                                (
                                )

                        )

                )

        )

    [2] => Array
        (
            [user_id] => 3
            [first_name] => Sasha
            [last_name] => Duffy
            [salary] => Array
                (
                    [salary_id] => 3
                    [user_id] => 3
                    [salary] => 150
                )

            [albums] => Array
                (
                    [0] => Array
                        (
                            [album_id] => 5
                            [name] => Lessons From a Passport
                            [date_created] => 2024-03-01
                            [junction] => Array
                                (
                                    [user_id] => 3
                                    [album_id] => 5
                                )

                            [photos] => Array
                                (
                                )

                        )

                    [1] => Array
                        (
                            [album_id] => 6
                            [name] => A Weekend in Colorado
                            [date_created] => 2024-03-02
                            [junction] => Array
                                (
                                    [user_id] => 3
                                    [album_id] => 6
                                )

                            [photos] => Array
                                (
                                )

                        )

                )

        )

    [3] => Array
        (
            [user_id] => 4
            [first_name] => Robin
            [last_name] => Soto
            [salary] => 
            [albums] => Array
                (
                    [0] => Array
                        (
                            [album_id] => 7
                            [name] => Wherever We Go
                            [date_created] => 2024-03-04
                            [junction] => Array
                                (
                                    [user_id] => 4
                                    [album_id] => 7
                                )

                            [photos] => Array
                                (
                                )

                        )

                )

        )

    [4] => Array
        (
            [user_id] => 5
            [first_name] => Bryan
            [last_name] => Buck
            [salary] => 
            [albums] => Array
                (
                )

        )
)
</pre>
</div>


<div>
<h3>Assembler with pdo</h3>
<p></p>
<pre>
use ujb\database\adapters\pdo\Statement, 
	ujb\database\adapters\pdo\Source,
	ujb\database\assembler\AssemblerFactory,
	ujb\database\result\Result;
	

// Connection
	
$dsn = 'mysql:dbname=test;host=127.0.0.1';
$user = 'root';
$password = '';

$db = new \PDO($dsn, $user, $password);
$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);			


// Result

$PDOStatement = $db->query('SELECT * FROM albums');
$statement = new Statement($PDOStatement); // We need this object to get the result metadata.
$source = new Source($PDOStatement, $statement->getMetadata());
$result = new Result($source);


// Assembler

$assembler = AssemblerFactory::create([
	'groups' => ['name', true],
]);

$result = $assembler->assemble($result);
</pre>

<pre></pre>
</div>
